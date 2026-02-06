<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\PinjamBarangModel;
use Myth\Auth\Models\UserModel;

class Barang extends BaseController
{
    protected $barangModel;
    protected $pinjamBarangModel;
    protected $userModel;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->pinjamBarangModel = new PinjamBarangModel();
        $this->userModel = new UserModel();
    }

    /**
     * Dashboard barang - menampilkan data dari API
     */
    public function dashboard()
    {
        // Ambil data tanah dari API
        $client = \Config\Services::curlrequest();
        $response = $client->get('http://apigw.pu.go.id/v1/siman/tanah?api_key=c877acaa0de297a9e3b8bbdb101dd254d33a92a0444b979d599e04fdeaccdbc5');
        $tanahList = [];

        if ($response->getStatusCode() == 20) {
            $result = json_decode($response->getBody(), true);
            $tanahList = $result['resource'] ?? [];
        }

        return view('user/barang/dashboardbarang', [
            'tanahList' => $tanahList
        ]);
    }

    /**
     * Ajukan peminjaman barang (dari scan QR)
     */
    public function pinjam()
    {
        try {
            $userId = user_id();
            $barangId = $this->request->getPost('barang_id');

            // Validate barang exists
            $barang = $this->barangModel->find($barangId);
            if (!$barang) {
                throw new \Exception('Barang tidak ditemukan');
            }

            // Prepare data
            $data = [
                'barang_id' => $barangId,
                'user_id' => $userId,
                'nama_peminjam' => $this->request->getPost('nama_peminjam'),
                'tanggal' => $this->request->getPost('tanggal'),
                'waktu_mulai' => $this->request->getPost('waktu_mulai'),
                'waktu_selesai' => $this->request->getPost('waktu_selesai'),
                'keperluan' => $this->request->getPost('keperluan'),
                'status' => 'diajukan'
            ];

            $db = \Config\Database::connect();
            $db->transStart();

            // Insert peminjaman
            if (!$this->pinjamBarangModel->insert($data)) {
                throw new \Exception('Gagal menyimpan data peminjaman');
            }

            // Send email notification if helper exists
            if (function_exists('sendBarangPeminjamanNotification')) {
                $emailData = [
                    'user_email' => user()->email,
                    'user_fullname' => user()->fullname,
                    'nama_barang' => $barang['nama_barang'],
                    'tanggal' => $data['tanggal'],
                    'waktu_mulai' => $data['waktu_mulai'],
                    'waktu_selesai' => $data['waktu_selesai'],
                    'keperluan' => $data['keperluan']
                ];
                
                helper('email');
                sendBarangPeminjamanNotification($emailData, 'new');
            }

            // Update barang status
            $this->barangModel->update($barangId, [
                'status' => 'Menunggu Verifikasi',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Transaksi database gagal');
            }

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Peminjaman berhasil diajukan dan masuk ke dashboard admin untuk verifikasi'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in pinjam: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Verifikasi peminjaman (admin) - akan muncul di dashboard
     */
/**
     * Verifikasi peminjaman barang - VERSI SIMPLE tanpa cek barang
     */
    public function verifikasiPeminjaman()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        try {
            $pinjam_id = $this->request->getPost('pinjam_id');
            $status = $this->request->getPost('status');
            $keterangan = $this->request->getPost('keterangan') ?? '';

            if (!$pinjam_id || !$status) {
                throw new \Exception('Data tidak lengkap');
            }

            // Ambil data peminjaman
            $peminjaman = $this->pinjamBarangModel->find($pinjam_id);
            if (!$peminjaman) {
                throw new \Exception('Data peminjaman tidak ditemukan');
            }

            // SIMPLE: Langsung update status tanpa cek barang
            $updateData = [
                'status' => ($status === 'disetujui') ? 'dipinjam' : 'ditolak',
                'keterangan_status' => $keterangan,
                'verified_at' => date('Y-m-d H:i:s'),
                'verified_by' => user()->id
            ];

            $updated = $this->pinjamBarangModel->update($pinjam_id, $updateData);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate status peminjaman');
            }

            $message = ($status === 'disetujui') 
                ? 'Peminjaman barang telah disetujui' 
                : 'Peminjaman barang ditolak';

            log_message('info', 'Verifikasi peminjaman berhasil (simple): ID ' . $pinjam_id . ' status ' . $status);

            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in verifikasiPeminjaman simple: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Kembalikan barang by ID
     */
    public function kembalikanById()
    {
        try {
            $request = $this->request->getJSON(true);
            $barangId = $request['barang_id'] ?? null;
            $userId = user_id();

            if (!$barangId || !$userId) {
                throw new \Exception('ID barang atau User ID tidak ditemukan.');
            }

            // Find active peminjaman
            $data = $this->pinjamBarangModel
                ->where('barang_id', $barangId)
                ->where('user_id', $userId)
                ->whereIn('status', ['diajukan', 'disetujui', 'dipinjam'])
                ->orderBy('created_at', 'DESC')
                ->first();

            // If not found, check for rejected returns
            if (!$data) {
                $data = $this->pinjamBarangModel
                    ->where('barang_id', $barangId)
                    ->where('user_id', $userId)
                    ->where('status', 'ditolak')
                    ->orderBy('created_at', 'DESC')
                    ->first();
            }

            if (!$data) {
                throw new \Exception('Data peminjaman tidak ditemukan.');
            }

            $db = \Config\Database::connect();
            $db->transStart();

            // Reset status if previously rejected
            if ($data['status'] === 'ditolak') {
                $this->pinjamBarangModel->update($data['id'], [
                    'status' => 'dipinjam',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Update to return process
            $tanggalKembali = date('Y-m-d H:i:s');
            $this->pinjamBarangModel->update($data['id'], [
                'status' => 'proses_pengembalian',
                'tanggal_kembali' => $tanggalKembali
            ]);

            // Send email notification if helper exists
            if (function_exists('sendBarangPengembalianNotification')) {
                $barang = $this->barangModel->find($barangId);
                $user = user();

                $emailData = [
                    'user_email' => $user->email,
                    'user_fullname' => $user->fullname,
                    'nama_barang' => $barang['nama_barang'] ?? 'Barang Tidak Dikenal',
                    'tanggal_kembali' => $tanggalKembali,
                    'status' => 'proses_pengembalian',
                    'tanggal' => $data['tanggal'] ?? null,
                    'waktu_selesai' => $data['waktu_selesai'] ?? null,
                    'keperluan' => $data['keperluan'] ?? null
                ];

                helper('email');
                sendBarangPengembalianNotification($emailData, 'new');
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Transaksi database gagal');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengajuan pengembalian berhasil dan masuk ke dashboard admin untuk verifikasi.'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in kembalikanById: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Verifikasi pengembalian (admin) - akan muncul di dashboard
     */
    public function verifikasiPengembalian()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        try {
            $pinjam_id = $this->request->getPost('id');
            $status = $this->request->getPost('status');
            $keterangan = $this->request->getPost('keterangan') ?? '';

            if (!$pinjam_id || !$status) {
                throw new \Exception('Data tidak lengkap');
            }

            // Ambil data peminjaman
            $peminjaman = $this->pinjamBarangModel->find($pinjam_id);
            if (!$peminjaman) {
                throw new \Exception('Data peminjaman tidak ditemukan');
            }

            // Cek status harus 'proses_pengembalian'
            if ($peminjaman['status'] !== 'proses_pengembalian') {
                throw new \Exception('Status peminjaman tidak valid untuk verifikasi pengembalian');
            }

            // SIMPLE: Langsung update status tanpa ribet validasi barang
            $newStatus = ($status === 'disetujui') ? 'selesai' : 'dipinjam';
            
            $updateData = [
                'status' => $newStatus,
                'keterangan_status' => $keterangan,
                'verified_at' => date('Y-m-d H:i:s'),
                'verified_by' => user()->id
            ];

            $updated = $this->pinjamBarangModel->update($pinjam_id, $updateData);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate status pengembalian');
            }

            $message = ($status === 'disetujui') 
                ? 'Pengembalian barang telah disetujui' 
                : 'Pengembalian barang ditolak, status kembali ke dipinjam';

            log_message('info', 'Verifikasi pengembalian berhasil: ID ' . $pinjam_id . ' status ' . $status);

            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in verifikasiPengembalian: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    // =====================================================
    // ADMIN METHODS FOR DASHBOARD - TAMBAHAN BARU
    // =====================================================

    /**
     * Get pending peminjaman untuk dashboard admin
     */
/**
     * Get pending peminjaman SIMPLE - tanpa JOIN untuk testing
     */
/**
     * Get pending peminjaman untuk dashboard admin - FINAL CORRECT VERSION
     */
    public function getPendingScan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            // FINAL: JOIN yang benar dengan field mapping yang tepat
            $pendingData = $this->pinjamBarangModel
                ->select('pinjam_barang.*, 
                          users.fullname as nama_peminjam, 
                          users.email as email_peminjam,
                          komputer.merk as nama_barang,
                          komputer.kondisi as kondisi_barang,
                          komputer.id as komputer_id')
                ->join('users', 'users.id = pinjam_barang.user_id', 'left')
                ->join('komputer', 'komputer.id = pinjam_barang.barang_id', 'left')
                ->where('pinjam_barang.status', 'diajukan')
                ->orderBy('pinjam_barang.created_at', 'DESC')
                ->findAll();
            
            // Debug untuk memastikan field mapping benar
            foreach ($pendingData as &$item) {
                log_message('debug', 'Item ID ' . $item['id'] . ': barang_id=' . $item['barang_id'] . ', nama_barang=' . ($item['nama_barang'] ?? 'NULL'));
                
                // Fallback jika JOIN gagal
                if (empty($item['nama_barang'])) {
                    $item['nama_barang'] = 'Barang ID ' . $item['barang_id'];
                    log_message('warning', 'Fallback nama_barang for ID ' . $item['barang_id']);
                }
                
                // Pastikan field yang dibutuhkan ada
                $item['merk'] = $item['nama_barang'] ?? 'Unknown';
                $item['type'] = $item['kondisi_barang'] ?? 'Unknown';
            }
            
            log_message('info', 'getPendingScan berhasil: ' . count($pendingData) . ' items');
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $pendingData
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getPendingScan: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get dipinjam items untuk dashboard admin
     */
    public function getDipinjamScan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $dipinjamData = $this->pinjamBarangModel
                ->select('pinjam_barang.*, 
                          users.fullname as nama_peminjam, 
                          users.email as email_peminjam,
                          komputer.nama as nama_barang,
                          komputer.merk,
                          komputer.type')
                ->join('users', 'users.id = pinjam_barang.user_id')
                ->join('komputer', 'komputer.id = pinjam_barang.barang_id')
                ->where('pinjam_barang.status', 'dipinjam')
                ->orderBy('pinjam_barang.created_at', 'DESC')
                ->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $dipinjamData
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting dipinjam scan: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ]);
        }
    }

    /**
     * Get proses pengembalian untuk dashboard admin
     */
/**
     * Get pengembalian barang untuk admin dashboard
     */
    public function getPengembalianScan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            // Debug log untuk melihat request
            log_message('debug', 'getPengembalianScan called by user: ' . user()->id);
            
            // Get pengembalian dengan status 'proses_pengembalian'
            $pengembalianData = $this->pinjamBarangModel
                ->select('pinjam_barang.*, 
                          users.fullname as nama_peminjam, 
                          users.email as email_peminjam,
                          komputer.merk as nama_barang,
                          komputer.kondisi as kondisi_barang,
                          komputer.id as komputer_id')
                ->join('users', 'users.id = pinjam_barang.user_id', 'left')
                ->join('komputer', 'komputer.id = pinjam_barang.barang_id', 'left')
                ->where('pinjam_barang.status', 'proses_pengembalian')
                ->orderBy('pinjam_barang.tanggal_kembali', 'DESC')
                ->findAll();
            
            // Debug log untuk hasil query
            log_message('debug', 'getPengembalianScan found: ' . count($pengembalianData) . ' items');
            log_message('debug', 'Sample pengembalian data: ' . json_encode($pengembalianData[0] ?? 'No data'));
            
            // Fallback untuk nama_barang jika JOIN gagal
            foreach ($pengembalianData as &$item) {
                if (empty($item['nama_barang'])) {
                    $item['nama_barang'] = 'Barang ID ' . $item['barang_id'];
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $pengembalianData,
                'debug' => [
                    'count' => count($pengembalianData),
                    'query_status' => 'OK'
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getPengembalianScan: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ]);
        }
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStatistikScan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $stats = [
                'total' => $this->pinjamBarangModel->countAll(),
                'pending' => $this->pinjamBarangModel->where('status', 'diajukan')->countAllResults(false),
                'dipinjam' => $this->pinjamBarangModel->where('status', 'dipinjam')->countAllResults(false),
                'proses_pengembalian' => $this->pinjamBarangModel->where('status', 'proses_pengembalian')->countAllResults(false),
                'selesai' => $this->pinjamBarangModel->where('status', 'selesai')->countAllResults(false),
                'ditolak' => $this->pinjamBarangModel->where('status', 'ditolak')->countAllResults(false)
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting statistics scan: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ]);
        }
    }

    /**
     * Riwayat peminjaman untuk user - dengan tombol kembalikan
     */
    public function riwayat()
    {
        $data = [
            'title' => 'Riwayat Peminjaman Saya',
            'riwayat' => $this->getRiwayatPeminjaman()
        ];

        return view('user/barang/riwayat', $data);
    }

    /**
     * Get riwayat peminjaman user dengan JOIN ke tabel komputer
     */
    private function getRiwayatPeminjaman()
    {
        try {
            $userId = user()->id;
            
            $riwayat = $this->pinjamBarangModel
                ->select('pinjam_barang.*, 
                          komputer.merk as nama_barang,
                          komputer.kondisi as kondisi_barang,
                          komputer.id as komputer_id')
                ->join('komputer', 'komputer.id = pinjam_barang.barang_id', 'left')
                ->where('pinjam_barang.user_id', $userId)
                ->where('pinjam_barang.deleted_at', null)
                ->orderBy('pinjam_barang.created_at', 'DESC')
                ->findAll();
            
            log_message('debug', 'Riwayat found for user ' . $userId . ': ' . count($riwayat) . ' items');
            
            return $riwayat;
            
        } catch (\Exception $e) {
            log_message('error', 'Error getting riwayat: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Proses pengembalian barang oleh user
     */
    public function kembalikan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        try {
            $pinjam_id = $this->request->getPost('pinjam_id');
            
            if (!$pinjam_id) {
                throw new \Exception('ID peminjaman tidak valid');
            }

            // Cek apakah peminjaman milik user yang login
            $peminjaman = $this->pinjamBarangModel
                ->where('id', $pinjam_id)
                ->where('user_id', user()->id)
                ->first();
                
            if (!$peminjaman) {
                throw new \Exception('Peminjaman tidak ditemukan atau bukan milik Anda');
            }
            
            // Cek status harus 'dipinjam'
            if ($peminjaman['status'] !== 'dipinjam') {
                throw new \Exception('Status peminjaman tidak valid untuk pengembalian');
            }

            // Update status ke 'proses_pengembalian'
            $updateData = [
                'status' => 'proses_pengembalian',
                'tanggal_kembali' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updated = $this->pinjamBarangModel->update($pinjam_id, $updateData);
            
            if (!$updated) {
                throw new \Exception('Gagal memproses pengembalian');
            }

            log_message('info', 'User ' . user()->id . ' returned barang, pinjam_id: ' . $pinjam_id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengembalian berhasil diajukan. Menunggu verifikasi admin.'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in kembalikan: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    /**
     * Pengembalian barang dengan form upload foto dan kondisi - UPDATED TIMESTAMP
     */
    public function kembalikanWithForm()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $pinjam_id = $this->request->getPost('pinjam_id');
            $kondisi_barang = $this->request->getPost('kondisi_barang');
            $keterangan = $this->request->getPost('keterangan') ?? '';
            
            if (!$pinjam_id || !$kondisi_barang) {
                throw new \Exception('Data tidak lengkap');
            }

            // Cek apakah peminjaman milik user yang login
            $peminjaman = $this->pinjamBarangModel
                ->where('id', $pinjam_id)
                ->where('user_id', user()->id)
                ->first();
                
            if (!$peminjaman) {
                throw new \Exception('Peminjaman tidak ditemukan atau bukan milik Anda');
            }
            
            // Cek status harus 'dipinjam'
            if ($peminjaman['status'] !== 'dipinjam') {
                throw new \Exception('Status peminjaman tidak valid untuk pengembalian');
            }

            // Handle upload foto barang
            $uploadedFiles = [];
            $fotoFiles = $this->request->getFiles();
            
            if (isset($fotoFiles['foto_barang'])) {
                foreach ($fotoFiles['foto_barang'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        // Generate unique filename dengan timestamp
                        $fileName = 'pengembalian_barang_' . $pinjam_id . '_' . date('YmdHis') . '_' . uniqid() . '.' . $file->getExtension();
                        
                        // Move file to uploads/barang_returns/
                        if (!is_dir(WRITEPATH . 'uploads/barang_returns/')) {
                            mkdir(WRITEPATH . 'uploads/barang_returns/', 0777, true);
                        }
                        
                        if ($file->move(WRITEPATH . 'uploads/barang_returns/', $fileName)) {
                            $uploadedFiles[] = $fileName;
                            log_message('debug', 'Foto barang uploaded: ' . $fileName);
                        } else {
                            log_message('error', 'Failed to upload foto: ' . $file->getName());
                        }
                    }
                }
            }

            if (empty($uploadedFiles)) {
                throw new \Exception('Minimal upload 1 foto barang');
            }

            // TIMESTAMP OTOMATIS - Tanggal dan jam kembali saat ini
            $timestampKembali = date('Y-m-d H:i:s');
            
            // Update status ke 'proses_pengembalian' dengan timestamp otomatis
            $updateData = [
                'status' => 'proses_pengembalian',
                'tanggal_kembali' => $timestampKembali, // TIMESTAMP OTOMATIS
                'kondisi_pengembalian' => $kondisi_barang,
                'keterangan' => $keterangan,
                'foto_pengembalian' => json_encode($uploadedFiles),
                'updated_at' => $timestampKembali
            ];

            $updated = $this->pinjamBarangModel->update($pinjam_id, $updateData);
            
            if (!$updated) {
                throw new \Exception('Gagal memproses pengembalian');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            // Format timestamp untuk response
            $tanggalKembaliFormatted = date('d/m/Y H:i:s', strtotime($timestampKembali));
            
            log_message('info', 'User ' . user()->id . ' returned barang with form, pinjam_id: ' . $pinjam_id . ', kondisi: ' . $kondisi_barang . ', timestamp: ' . $timestampKembali . ', foto: ' . count($uploadedFiles));

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengembalian berhasil diajukan dengan ' . count($uploadedFiles) . ' foto dokumentasi.',
                'data' => [
                    'pinjam_id' => $pinjam_id,
                    'kondisi' => $kondisi_barang,
                    'foto_count' => count($uploadedFiles),
                    'tanggal_kembali' => $timestampKembali,
                    'tanggal_kembali_formatted' => $tanggalKembaliFormatted
                ]
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            
            // Delete uploaded files if transaction failed
            if (!empty($uploadedFiles)) {
                foreach ($uploadedFiles as $fileName) {
                    $filePath = WRITEPATH . 'uploads/barang_returns/' . $fileName;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
            
            log_message('error', 'Error in kembalikanWithForm: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Serve foto pengembalian barang - untuk admin dashboard
     */
    public function getFoto($filename)
    {
        try {
            $filePath = WRITEPATH . 'uploads/barang_returns/' . $filename;
            
            // Check if file exists
            if (!file_exists($filePath)) {
                throw new \Exception('File not found');
            }
            
            // Get file info
            $fileInfo = pathinfo($filePath);
            $mimeType = 'image/jpeg'; // default
            
            // Set proper mime type
            switch (strtolower($fileInfo['extension'])) {
                case 'jpg':
                case 'jpeg':
                    $mimeType = 'image/jpeg';
                    break;
                case 'png':
                    $mimeType = 'image/png';
                    break;
                case 'gif':
                    $mimeType = 'image/gif';
                    break;
                case 'webp':
                    $mimeType = 'image/webp';
                    break;
            }
            
            // Security: Only allow image files
            if (!in_array(strtolower($fileInfo['extension']), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                throw new \Exception('Invalid file type');
            }
            
            // Security: Sanitize filename (prevent path traversal)
            if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
                throw new \Exception('Invalid filename');
            }
            
            // Set headers
            $this->response->setHeader('Content-Type', $mimeType);
            $this->response->setHeader('Content-Length', filesize($filePath));
            $this->response->setHeader('Cache-Control', 'public, max-age=3600'); // Cache 1 hour
            
            // Output file
            $this->response->setBody(file_get_contents($filePath));
            
            return $this->response;
            
        } catch (\Exception $e) {
            log_message('error', 'Error serving foto: ' . $e->getMessage());
            
            // Return 404 or placeholder image
            return $this->response->setStatusCode(404)->setBody('File not found');
        }
    }/**
     * Get data barang dengan status peminjaman untuk semua kategori
     */
/**
 * Simple approach untuk testing PostgreSQL compatibility
 */
/**
 * Advanced Query Builder with LEFT JOIN - One Query Approach
 * Lebih efisien tapi sedikit lebih complex
 */
/**
 * Get barang dengan status peminjaman menggunakan CodeIgniter Query Builder
 * Simple approach yang mudah dan reliable
 */
public function getBarangWithStatus($kelompok = null)
{

    log_message('debug', '🔥 getBarangWithStatus CALLED with kelompok: ' . ($kelompok ?? 'NULL'));
    $db = \Config\Database::connect();
    $builder = $db->table('komputer k');
    
    // Step 1: Build basic query
    $builder->select('k.*');
    
    if ($kelompok) {
        $builder->where('k.kelompok', $kelompok);
    }
    
    $builder->orderBy('k.id', 'ASC');
    
    // Get komputer data first
    $komputers = $builder->get()->getResultArray();
    
    // Step 2: For each komputer, get loan status using Query Builder
    foreach ($komputers as &$komputer) {
        $loanBuilder = $db->table('pinjam_barang');
        $loanBuilder->select('status, nama_peminjam, tanggal');
        $loanBuilder->where('barang_id', $komputer['id']);
        $loanBuilder->whereIn('status', ['dipinjam', 'diajukan', 'proses_pengembalian']);
        $loanBuilder->orderBy('created_at', 'DESC');
        $loanBuilder->limit(1);
        
        $loan = $loanBuilder->get()->getRowArray();
        
        // DEBUG: Log untuk ID 95 specifically
        if ($komputer['id'] == 95) {
            log_message('debug', 'ID 95 LOAN QUERY RESULT: ' . json_encode($loan));
            log_message('debug', 'ID 95 LOAN STATUS: ' . ($loan['status'] ?? 'NULL'));
        }
        
        if ($loan) {
            // Determine status based on loan data
            if ($loan['status'] === 'dipinjam') {
                $komputer['status_peminjaman'] = 'In Use';
            } elseif (in_array($loan['status'], ['diajukan', 'proses_pengembalian'])) {
                $komputer['status_peminjaman'] = 'Pending';
                
                // DEBUG: Confirm Pending assignment for ID 95
                if ($komputer['id'] == 95) {
                    log_message('debug', 'ID 95 ASSIGNED STATUS: Pending (from status: ' . $loan['status'] . ')');
                }
            } else {
                $komputer['status_peminjaman'] = 'Available';
            }
            
            $komputer['dipinjam_oleh'] = $loan['nama_peminjam'];
            $komputer['tanggal_pinjam'] = $loan['tanggal'];
            $komputer['raw_status'] = $loan['status']; // For debugging
        } else {
            // DEBUG: Log when no loan found for ID 95
            if ($komputer['id'] == 95) {
                log_message('debug', 'ID 95 NO LOAN FOUND - Setting to Available');
            }
            
            $komputer['status_peminjaman'] = 'Available';
            $komputer['dipinjam_oleh'] = null;
            $komputer['tanggal_pinjam'] = null;
            $komputer['raw_status'] = null;
        }
    }
    
    // Debug log
    log_message('debug', 'Query Builder getBarangWithStatus result count: ' . count($komputers));
    
    return $komputers;
}

public function index($kelompok = null)
{
    if ($kelompok) {
        $kelompok = urldecode($kelompok);
    }
    
    // FORCE DEBUG LANGSUNG
    echo "<div style='background:red;color:white;padding:10px;'>CONTROLLER DEBUG: Method index() called</div>";
    
    // Test method langsung
    $testData = $this->getBarangWithStatus($kelompok);
    
    // Find ID 95
    foreach ($testData as $item) {
        if ($item['id'] == 95) {
            echo "<div style='background:red;color:white;padding:10px;'>CONTROLLER DEBUG ID 95: " . json_encode($item) . "</div>";
            break;
        }
    }
    
    $data = [
        'title' => $kelompok ? $kelompok : 'Semua Barang',
        'kelompok' => $kelompok,
        'komputerList' => $testData
    ];

    return view('user/barang/peralatandanmesin/komputer/index', $data);
}
    
    /**
     * Update method index untuk include status peminjaman
     */
    
    /**
     * DEBUG METHOD - Tambah method ini sementara di controller untuk debug
     */
    public function debugStatus()
    {
        $db = \Config\Database::connect();
        
        echo "<h3>🔍 DEBUG STATUS PEMINJAMAN</h3>";
        
        // 1. Cek data komputer ASUS ZENBOOK
        echo "<h4>1. Data Komputer ASUS ZENBOOK:</h4>";
        $komputer = $db->query("SELECT id, merk, kelompok, qr_code FROM komputer WHERE merk LIKE '%ASUS ZENBOOK PRO OLEDS911%'")->getResultArray();
        echo "<pre>";
        print_r($komputer);
        echo "</pre>";
        
        if (!empty($komputer)) {
            $komputer_id = $komputer[0]['id'];
            
            // 2. Cek data peminjaman untuk komputer ini
            echo "<h4>2. Data Peminjaman untuk ID {$komputer_id}:</h4>";
            $peminjaman = $db->query("SELECT * FROM pinjam_barang WHERE barang_id = {$komputer_id} ORDER BY created_at DESC")->getResultArray();
            echo "<pre>";
            print_r($peminjaman);
            echo "</pre>";
            
            // 3. Test JOIN query
            echo "<h4>3. Test JOIN Query:</h4>";
            $joinQuery = "SELECT k.id, k.merk, k.kelompok,
                         CASE 
                           WHEN pb.status = 'dipinjam' THEN 'In Use'
                           WHEN pb.status IN ('diajukan', 'proses_pengembalian') THEN 'Pending' 
                           ELSE 'Available'
                         END as status_peminjaman,
                         pb.nama_peminjam as dipinjam_oleh,
                         pb.status as raw_status,
                         pb.tanggal as tanggal_pinjam
                      FROM komputer k
                      LEFT JOIN pinjam_barang pb ON pb.barang_id = k.id 
                        AND pb.status IN ('dipinjam', 'diajukan', 'proses_pengembalian')
                      WHERE k.merk LIKE '%ASUS ZENBOOK PRO OLEDS911%'";
                      
            $joinResult = $db->query($joinQuery)->getResultArray();
            echo "<pre>";
            print_r($joinResult);
            echo "</pre>";
        }
        
        // 4. Cek semua peminjaman aktif
        echo "<h4>4. Semua Peminjaman Aktif:</h4>";
        $activePeminjaman = $db->query("SELECT k.merk, pb.status, pb.nama_peminjam, pb.created_at, pb.barang_id
                                      FROM pinjam_barang pb
                                      JOIN komputer k ON k.id = pb.barang_id
                                      WHERE pb.status IN ('dipinjam', 'diajukan', 'proses_pengembalian')
                                      ORDER BY pb.created_at DESC")->getResultArray();
        echo "<pre>";
        print_r($activePeminjaman);
        echo "</pre>";
        
        // 5. Test method getBarangWithStatus
        echo "<h4>5. Test getBarangWithStatus Method:</h4>";
        $result = $this->getBarangWithStatus('KOMPUTER UNIT');
        foreach ($result as $item) {
            if (strpos($item['merk'], 'ASUS ZENBOOK') !== false) {
                echo "<strong>ASUS ZENBOOK Data:</strong><br>";
                echo "ID: " . $item['id'] . "<br>";
                echo "Merk: " . $item['merk'] . "<br>";
                echo "Status Peminjaman: " . $item['status_peminjaman'] . "<br>";
                echo "Dipinjam Oleh: " . ($item['dipinjam_oleh'] ?? 'NULL') . "<br>";
                echo "Raw Status: " . ($item['raw_status'] ?? 'NULL') . "<br>";
                break;
            }
        }
        
        die(); // Stop execution untuk debug
    }
    
}