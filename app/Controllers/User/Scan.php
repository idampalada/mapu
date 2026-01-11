<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\PinjamBarangModel;
use App\Models\KomputerModel;

class Scan extends BaseController
{
    protected $pinjamBarangModel;
    protected $komputerModel;

    public function __construct()
    {
        $this->pinjamBarangModel = new PinjamBarangModel();
        $this->komputerModel = new KomputerModel();
    }

    public function index()
    {
        return view('user/scan/index');
    }

/**
     * Validate QR Code - DEBUG VERSION
     */
    public function validateQR()
    {
        try {
            $qrData = $this->request->getPost('qr_data');
            
            // Debug logging
            log_message('debug', 'validateQR called with qr_data: ' . ($qrData ?? 'NULL'));
            log_message('debug', 'Request method: ' . $this->request->getMethod());
            log_message('debug', 'All POST data: ' . json_encode($this->request->getPost()));
            
            // Cek apakah qr_data kosong
            if (empty($qrData)) {
                log_message('error', 'QR Code kosong atau tidak ditemukan dalam request');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'QR Code tidak boleh kosong',
                    'debug' => [
                        'qr_data' => $qrData,
                        'post_data' => $this->request->getPost()
                    ]
                ]);
            }

            // Cari barang berdasarkan QR code
            $barang = $this->komputerModel->where('qr_code', $qrData)->first();
            
            log_message('debug', 'Barang found: ' . ($barang ? 'YES' : 'NO'));
            if ($barang) {
                log_message('debug', 'Barang data: ' . json_encode($barang));
            }

            if (!$barang) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'QR Code tidak ditemukan atau tidak valid',
                    'debug' => [
                        'searched_qr' => $qrData
                    ]
                ]);
            }

            // Cek status barang
            if (isset($barang['kondisi']) && strtolower($barang['kondisi']) === 'dipinjam') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Barang sedang dipinjam oleh user lain'
                ]);
            }

            // Success response
            return $this->response->setJSON([
                'success' => true,
                'message' => 'QR Code valid',
                'barang' => $barang
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in validateQR: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function submitPinjam()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $barang_id = $this->request->getPost('barang_id');
            $tanggal_pinjam = $this->request->getPost('tanggal_pinjam');
            $tanggal_kembali = $this->request->getPost('tanggal_kembali');
            $keperluan = $this->request->getPost('keperluan');
            $penanggung_jawab = $this->request->getPost('penanggung_jawab');

            // Validasi input
            if (empty($barang_id) || empty($tanggal_pinjam) || empty($tanggal_kembali) || empty($keperluan)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Semua field wajib diisi'
                ]);
            }

            // Validasi tanggal
            if (strtotime($tanggal_pinjam) > strtotime($tanggal_kembali)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tanggal kembali tidak boleh lebih awal dari tanggal pinjam'
                ]);
            }

            // Cek apakah barang masih tersedia
            $barang = $this->komputerModel->find($barang_id);
            if (!$barang) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                ]);
            }

            // Cek apakah ada peminjaman yang bentrok
            $existingPinjam = $this->pinjamBarangModel
                ->where('barang_id', $barang_id)
                ->whereIn('status', ['diajukan', 'disetujui', 'dipinjam'])
                ->first();

            if ($existingPinjam) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Barang tidak tersedia untuk tanggal tersebut'
                ]);
            }

            // Siapkan data sesuai struktur database existing
            $data = [
                'user_id' => user_id(),
                'barang_id' => $barang_id,
                'nama_peminjam' => user()->fullname, // Ambil dari user yang login
                'tanggal' => $tanggal_pinjam, // Field 'tanggal' sesuai struktur DB
                'waktu_mulai' => '08:00:00', // Default waktu mulai
                'waktu_selesai' => '17:00:00', // Default waktu selesai
                'keperluan' => $keperluan,
                'status' => 'diajukan', // Status sesuai yang ada di DB existing
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Jika ada penanggung jawab, gunakan, jika tidak pakai nama user
            if (!empty($penanggung_jawab)) {
                $data['nama_peminjam'] = $penanggung_jawab;
            }

            if ($this->pinjamBarangModel->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Permohonan peminjaman berhasil diajukan. Menunggu persetujuan admin.'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data peminjaman'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error in submitPinjam: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get history peminjaman untuk user yang login - UPDATE dengan JOIN
     */
    public function getMyHistory()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        try {
            $userId = user()->id;
            
            // Get history dengan JOIN ke tabel komputer
            $history = $this->pinjamBarangModel
                ->select('pinjam_barang.*, 
                          komputer.merk as nama_barang,
                          komputer.kondisi as kondisi_barang,
                          komputer.id as komputer_id')
                ->join('komputer', 'komputer.id = pinjam_barang.barang_id', 'left')
                ->where('pinjam_barang.user_id', $userId)
                ->where('pinjam_barang.deleted_at', null)
                ->orderBy('pinjam_barang.created_at', 'DESC')
                ->findAll();
            
            // Fallback untuk nama_barang jika JOIN gagal
            foreach ($history as &$item) {
                if (empty($item['nama_barang'])) {
                    $item['nama_barang'] = 'Barang ID ' . $item['barang_id'];
                }
                // Set merk for consistency
                $item['merk'] = $item['nama_barang'];
            }
            
            log_message('debug', 'History loaded for user ' . $userId . ': ' . count($history) . ' items');
            
            return $this->response->setJSON([
                'success' => true,
                'history' => $history
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getMyHistory: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat riwayat: ' . $e->getMessage()
            ]);
        }
    }
}