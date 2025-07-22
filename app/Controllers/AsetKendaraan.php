<?php

namespace App\Controllers;

use App\Models\AsetModel;
use App\Models\KembaliModel;
use App\Models\PinjamModel;

class AsetKendaraan extends BaseController
{
    private function getUserData($userId)
    {
        $userModel = new \Myth\Auth\Models\UserModel();
        return $userModel->find($userId);
    }
    
    private function initCurlWithSSL($url, $isPost = true) 
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO, APPPATH . 'ThirdParty/cacert.pem');
        
        if ($isPost) {
            curl_setopt($ch, CURLOPT_POST, true);
        }
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        return $ch;
    }

    private function check_file_with_virustotal($file)
{
    // Daftar MIME types yang didukung
    $supportedMimeTypes = [
        'application/pdf',
        'image/png', 
        'image/jpeg',
        'image/jpg'
    ];
    
    // Cek apakah MIME type didukung
    if (!in_array($file->getMimeType(), $supportedMimeTypes)) {
        log_message('warning', 'MIME type tidak didukung untuk VirusTotal: ' . $file->getMimeType());
        return true; // Anggap tidak aman jika MIME type tidak didukung
    }
    
    // Cek ukuran file (maksimal 32MB untuk VirusTotal)
    if ($file->getSize() > 32 * 1024 * 1024) {
        log_message('warning', 'File terlalu besar untuk VirusTotal: ' . $file->getSize() . ' bytes');
        return true; // Anggap tidak aman jika file terlalu besar
    }

    $api_key = '964f15a6e58be968be71f229b33c52b56a9ba2ccfd8969df075e2700dc584d4a';
    $api_url_scan = 'https://www.virustotal.com/vtapi/v2/file/scan';
    $api_url_report = 'https://www.virustotal.com/vtapi/v2/file/report';

    try {
        // Tentukan MIME type untuk CURLFile berdasarkan file yang diupload
        $curlMimeType = $file->getMimeType();
        
        $post = [
            'apikey' => $api_key,
            'file' => new \CURLFile($file->getTempName(), $curlMimeType, $file->getName())
        ];

        log_message('debug', 'Uploading file to VirusTotal: ' . $file->getName() . ' (' . $curlMimeType . ')');
        
        $ch = $this->initCurlWithSSL($api_url_scan);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        
        $scan_response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            log_message('error', 'Curl error during VirusTotal scan: ' . curl_error($ch));
            curl_close($ch);
            return true; // Anggap tidak aman jika ada error curl
        }
        curl_close($ch);

        $scan_result = json_decode($scan_response, true);
        if (!isset($scan_result['scan_id'])) {
            log_message('error', 'Invalid scan response from VirusTotal: ' . json_encode($scan_result));
            return true; // Anggap tidak aman jika response tidak valid
        }

        log_message('debug', 'VirusTotal scan initiated, scan_id: ' . $scan_result['scan_id']);
        
        // Tunggu 5 detik untuk hasil scan
        sleep(5);

        $post = [
            'apikey' => $api_key,
            'resource' => $scan_result['scan_id']
        ];

        $ch = $this->initCurlWithSSL($api_url_report);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        
        $report_response = curl_exec($ch);
        curl_close($ch);

        $report_result = json_decode($report_response, true);

        if (!isset($report_result['response_code']) || $report_result['response_code'] === 0) {
            log_message('warning', 'File belum pernah di-scan sebelumnya di VirusTotal');
            return false; // Anggap aman jika belum pernah di-scan
        }

        $positives = $report_result['positives'] ?? 0;
        $total = $report_result['total'] ?? 0;
        
        log_message('debug', 'VirusTotal scan result: ' . $positives . '/' . $total . ' engines detected threats');
        
        // Return true jika ada deteksi positif
        return $positives > 0;

    } catch (\Exception $e) {
        log_message('error', 'Error checking file with VirusTotal: ' . $e->getMessage());
        return true; // Anggap tidak aman jika ada error
    }
}

    public function edit($id)
    {
        $model = new AsetModel();
        $aset = $model->find($id);

        if (!$aset) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Data aset tidak ditemukan'
            ]);
        }
        if (!in_groups(['admin', 'admin_gedungutama'])) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Unauthorized Access'
            ]);
        }

        $data = [];
        $fields = [
            'kategori_id',
            'no_sk_psp',
            'kode_barang',
            'merk',
            'tahun_pembuatan',
            'kapasitas',
            'no_polisi',
            'no_bpkb',
            'no_stnk',
            'no_rangka',
            'kondisi'
        ];

        foreach ($fields as $field) {
            $value = $this->request->getPost($field);
            if ($value !== null && $value !== '') {
                $data[$field] = $value;
            }
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $gambar_mobil = $this->request->getFile('gambar_mobil');
        if ($gambar_mobil && $gambar_mobil->isValid()) {
            if ($gambar_mobil->getSize() > 5 * 1024 * 1024) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Ukuran file gambar mobil tidak boleh lebih dari 5MB'
                ]);
            }

            if (!empty($aset['gambar_mobil'])) {
                $oldImagePath = ROOTPATH . 'public/uploads/images/' . $aset['gambar_mobil'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $newName = $gambar_mobil->getRandomName();
            if ($gambar_mobil->move(ROOTPATH . 'public/uploads/images', $newName)) {
                $data['gambar_mobil'] = $newName;
            }
        }

        if (empty($data)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Tidak ada data yang diubah'
            ]);
        }

        try {
            $model->update($id, $data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error updating asset: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Gagal memperbarui data: ' . $e->getMessage()
            ]);
        }
    }

    public function getAsetById($id)
    {
        try {
            if (!in_groups(['admin', 'admin_gedungutama'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Unauthorized Access'
                ]);
            }

            $model = new AsetModel();
            $aset = $model->find($id);

            if ($aset) {
                unset($aset['deleted_at']);

                return $this->response->setJSON([
                    'success' => true,
                    'data' => $aset
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'error' => 'Data tidak ditemukan'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getAsetById: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        $model = new AsetModel();

        try {
            $model->delete($id);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    protected $email;

    public function __construct()
    {
        helper(['auth', 'email']);
        $this->email = \Config\Services::email();

        $config = config('Email');
        $this->email->initialize($config);
    }

    public function getKendaraan()
    {
        $model = new AsetModel();
        $kendaraan = $model->findAll();
        return $this->response->setJSON($kendaraan);
    }

    public function getKendaraanDipinjam()
    {
        $model = new PinjamModel();
        $asetModel = new AsetModel();

        try {
            $pinjaman = $model->where('deleted_at', null)->findAll();
            log_message('debug', 'Data Pinjaman: ' . json_encode($pinjaman));

            if (empty($pinjaman)) {
                log_message('debug', 'Tidak ada data peminjaman aktif');
                return $this->response->setJSON([]);
            }

            $kendaraanIds = array_map('strval', array_column($pinjaman, 'kendaraan_id'));
            log_message('debug', 'ID Kendaraan: ' . json_encode($kendaraanIds));

            $builder = $asetModel->builder();
            $builder->select('assets.*, pinjam.tanggal_pinjam, pinjam.tanggal_kembali');
            $builder->join('pinjam', 'CAST(pinjam.kendaraan_id AS VARCHAR) = CAST(assets.id AS VARCHAR)', 'inner');
            $builder->whereIn('assets.id', $kendaraanIds);
            $builder->where('pinjam.deleted_at IS NULL');

            $kendaraan = $builder->get()->getResult();
            log_message('debug', 'Hasil Query: ' . json_encode($kendaraan));

            return $this->response->setJSON($kendaraan);
        } catch (\Exception $e) {
            log_message('error', 'Error in getKendaraanDipinjam: ' . $e->getMessage());
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function getPeminjamanData($kendaraanId)
    {
        $model = new PinjamModel();
        $asetModel = new AsetModel();
    
        $pinjam = $model->where([
            'kendaraan_id' => $kendaraanId,
            'status' => 'disetujui',
            'is_returned' => false,
            'deleted_at' => null
        ])
        ->orderBy('id', 'DESC')
        ->first();
    
        log_message('debug', '🔥 DEBUG getPeminjamanData()');
        log_message('debug', 'Logged in user_id: ' . user_id());
        log_message('debug', 'Data peminjaman user_id: ' . ($pinjam['user_id'] ?? 'NULL'));
        log_message('debug', 'Full pinjam data: ' . json_encode($pinjam));
    
        if (!$pinjam) {
            return $this->response->setJSON(['error' => 'Data peminjaman tidak ditemukan']);
        }
    
        if ((int)$pinjam['user_id'] !== (int)user_id()) {
            return $this->response->setJSON([
                'error' => 'Anda tidak memiliki akses untuk mengembalikan kendaraan ini'
            ]);
        }
    
        $pinjam['tanggal_pinjam'] = date('Y-m-d', strtotime($pinjam['tanggal_pinjam']));
    
        $kendaraan = $asetModel->find($kendaraanId);
        if ($kendaraan) {
            $pinjam['merk'] = $kendaraan['merk'];
            $pinjam['no_polisi'] = $kendaraan['no_polisi'];
        }
    
        return $this->response->setJSON($pinjam);
    }

    public function tambah()
{
    $model = new AsetModel();

    try {
        $userId = user_id();
        $files = $this->request->getFiles();
        
        if (!isset($files['gambar_mobil']) || empty($files['gambar_mobil'])) {
            throw new \Exception('Minimal 1 foto harus diunggah');
        }

        $uploadedFiles = $files['gambar_mobil'];
        
        if (count($uploadedFiles) > 5) {
            throw new \Exception('Maksimal 5 foto yang dapat diunggah');
        }

        $fileNames = [];
        
        // PERBAIKAN: Definisi MIME types dan ekstensi yang lebih lengkap
        $validMimeTypes = [
            'image/jpeg',
            'image/jpg',  // Beberapa sistem menggunakan image/jpg
            'image/pjpeg', // Progressive JPEG
            'image/png'
        ];
        
        $validExtensions = ['jpg', 'jpeg', 'png'];
        
        foreach ($uploadedFiles as $file) {
            if (!$file->isValid()) {
                continue;
            }
            
            // Validasi ukuran file
            if ($file->getSize() > 5 * 1024 * 1024) {
                throw new \Exception('Ukuran file melebihi 5MB: ' . $file->getName());
            }

            // PERBAIKAN: Validasi ekstensi file yang case-insensitive
            $fileExtension = strtolower($file->getClientExtension());
            if (!in_array($fileExtension, $validExtensions)) {
                throw new \Exception('Format file harus JPG, JPEG, atau PNG: ' . $file->getName());
            }

            // PERBAIKAN: Validasi MIME type yang lebih fleksibel
            $fileMimeType = $file->getClientMimeType();
            if (!in_array($fileMimeType, $validMimeTypes)) {
                // Log untuk debugging
                log_message('warning', 'MIME type tidak dikenali: ' . $fileMimeType . ' untuk file: ' . $file->getName());
                
                // Validasi ulang dengan finfo jika tersedia
                if (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $realMimeType = finfo_file($finfo, $file->getTempName());
                    finfo_close($finfo);
                    
                    if (!in_array($realMimeType, $validMimeTypes)) {
                        throw new \Exception('Format file tidak valid (MIME: ' . $realMimeType . '): ' . $file->getName());
                    }
                } else {
                    // Jika finfo tidak tersedia, hanya periksa ekstensi
                    if (!in_array($fileExtension, $validExtensions)) {
                        throw new \Exception('Format file harus JPG, JPEG, atau PNG: ' . $file->getName());
                    }
                }
            }

            // PERBAIKAN: Generate nama file yang aman
            $newName = 'kendaraan_' . time() . '_' . uniqid() . '.' . $fileExtension;
            
            // Pastikan direktori upload ada
            $uploadDir = ROOTPATH . 'public/uploads/images';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            if ($file->move($uploadDir, $newName)) {
                $fileNames[] = $newName;
                log_message('info', 'File berhasil diupload: ' . $newName);
            } else {
                throw new \Exception('Gagal mengupload file: ' . $file->getName());
            }
        }

        if (empty($fileNames)) {
            throw new \Exception('Minimal 1 foto harus diunggah');
        }

        $data = [
            'user_id' => $userId,
            'kategori_id' => $this->request->getPost('kategori_id'),
            'no_sk_psp' => $this->request->getPost('no_sk_psp'),
            'kode_barang' => $this->request->getPost('kode_barang'),
            'merk' => $this->request->getPost('merk'),
            'tahun_pembuatan' => $this->request->getPost('tahun_pembuatan'),
            'kapasitas' => $this->request->getPost('kapasitas'),
            'no_polisi' => $this->request->getPost('no_polisi'),
            'no_bpkb' => $this->request->getPost('no_bpkb'),
            'no_stnk' => $this->request->getPost('no_stnk'),
            'no_rangka' => $this->request->getPost('no_rangka'),
            'kondisi' => $this->request->getPost('kondisi'),
            'status_pinjam' => 'Tersedia',
            'created_at' => date('Y-m-d H:i:s'),
            'gambar_mobil' => json_encode($fileNames)
        ];

        log_message('info', 'Menyimpan data aset dengan gambar: ' . json_encode($fileNames));
        $model->insert($data);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data berhasil disimpan'
        ]);
        
    } catch (\Exception $e) {
        log_message('error', 'Error dalam proses tambah aset: ' . $e->getMessage());
        
        // Cleanup files jika terjadi error
        if (isset($fileNames) && is_array($fileNames)) {
            foreach ($fileNames as $fileName) {
                $filePath = ROOTPATH . 'public/uploads/images/' . $fileName;
                if (file_exists($filePath)) {
                    @unlink($filePath);
                    log_message('info', 'Menghapus file karena error: ' . $fileName);
                }
            }
        }
        
        return $this->response->setJSON([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

    public function pinjam()
    {
        $model = new PinjamModel();
        $asetModel = new AsetModel();
        $db = db_connect();

        $userId = user_id();
        $nama_penanggung_jawab = $this->request->getPost('nama_penanggung_jawab');
        $nip_nrp = $this->request->getPost('nip_nrp');
        $pangkat_golongan = $this->request->getPost('pangkat_golongan');
        $jabatan = $this->request->getPost('jabatan');
        $unit_organisasi = $this->request->getPost('unit_organisasi');
        $kendaraan_id = $this->request->getPost('kendaraan_id');
        $pengemudi = $this->request->getPost('pengemudi');
        $no_hp = $this->request->getPost('no_hp');
        $tanggal_pinjam = $this->request->getPost('tanggal_pinjam');
        $tanggal_kembali = $this->request->getPost('tanggal_kembali');
        $urusan_kedinasan = $this->request->getPost('urusan_kedinasan');
        $surat_permohonan = $this->request->getFile('surat_permohonan');

        if (!$surat_permohonan->isValid() || $surat_permohonan->getError() !== 0) {
            return $this->response->setJSON(['error' => 'Surat Permohonan tidak valid: ' . $surat_permohonan->getErrorString()]);
        }

        if ($surat_permohonan->getClientMimeType() !== 'application/pdf') {
            return $this->response->setJSON(['error' => 'Format file Surat Permohonan harus PDF']);
        }

        if ($surat_permohonan->getSize() > 2 * 1024 * 1024) {
            return $this->response->setJSON(['error' => 'Ukuran file Surat Permohonan tidak boleh lebih dari 2MB']);
        }

        if ($this->check_file_with_virustotal($surat_permohonan)) {
            return $this->response->setJSON(['error' => 'File Surat Permohonan terdeteksi tidak aman']);
        }

        if ($this->check_file_with_virustotal($surat_permohonan)) {
            return $this->response->setJSON([
                'error' => 'File Surat Permohonan terdeteksi tidak aman'
            ]);
        }

        $suratPermohonanName = $surat_permohonan->getRandomName();

        try {
            $surat_permohonan->move(ROOTPATH . 'public/uploads/documents', $suratPermohonanName);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Gagal mengupload file: ' . $e->getMessage()]);
        }

        $validationRules = [
            'nama_penanggung_jawab' => 'required',
            'nip_nrp' => 'required',
            'pangkat_golongan' => 'required',
            'jabatan' => 'required',
            'unit_organisasi' => 'required',
            'kendaraan_id' => 'required',
            'pengemudi' => 'required',
            'no_hp' => 'required',
            'tanggal_pinjam' => 'required',
            'tanggal_kembali' => 'required',
            'urusan_kedinasan' => 'required'
        ];

        foreach ($validationRules as $field => $rule) {
            $value = $this->request->getPost($field);
            if (empty($value)) {
                @unlink(ROOTPATH . 'public/uploads/documents/' . $suratPermohonanName);
                return $this->response->setJSON([
                    'error' => ucwords(str_replace('_', ' ', $field)) . ' harus diisi.'
                ]);
            }
        }

        $asset = $asetModel->find($kendaraan_id);
        if (!$asset) {
            @unlink(ROOTPATH . 'public/uploads/documents/' . $suratPermohonanName);
            return $this->response->setJSON([
                'error' => 'Kendaraan tidak ditemukan dalam database.'
            ]);
        }

        $existingPinjam = $model->where([
            'kendaraan_id' => $kendaraan_id,
            'status' => 'disetujui',
            'is_returned' => false,
            'deleted_at' => null
        ])->first();

        if ($existingPinjam) {
            @unlink(ROOTPATH . 'public/uploads/documents/' . $suratPermohonanName);
            return $this->response->setJSON([
                'error' => 'Kendaraan ini sedang dipinjam.'
            ]);
        }

        $pendingPinjam = $model->where([
            'kendaraan_id' => $kendaraan_id,
            'status' => 'pending',
            'deleted_at' => null
        ])->first();

        if ($pendingPinjam) {
            @unlink(ROOTPATH . 'public/uploads/documents/' . $suratPermohonanName);
            return $this->response->setJSON([
                'error' => 'Kendaraan ini sedang dalam proses verifikasi peminjaman.'
            ]);
        }

        $db->transStart();

        try {
            $data = [
                'user_id' => $userId,
                'nama_penanggung_jawab' => $nama_penanggung_jawab,
                'nip_nrp' => $nip_nrp,
                'pangkat_golongan' => $pangkat_golongan,
                'jabatan' => $jabatan,
                'unit_organisasi' => $unit_organisasi,
                'kendaraan_id' => $kendaraan_id,
                'pengemudi' => $pengemudi,
                'no_hp' => $no_hp,
                'tanggal_pinjam' => $tanggal_pinjam,
                'tanggal_kembali' => $tanggal_kembali,
                'urusan_kedinasan' => $urusan_kedinasan,
                'kode_barang' => $asset['kode_barang'],
                'surat_permohonan' => $suratPermohonanName,
                'surat_jalan_admin' => null,
                'status' => PinjamModel::STATUS_PENDING,
                'is_returned' => false,
                'keterangan' => null,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $model->insert($data);

            $data['merk'] = $asset['merk'];
            $data['no_polisi'] = $asset['no_polisi'];

            $userData = user()->toArray();
            $data['user_email'] = $userData['email'];
            $data['user_fullname'] = $userData['fullname'];

            $asetModel->update($kendaraan_id, [
                'status_pinjam' => 'Dalam Verifikasi'
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                @unlink(ROOTPATH . 'public/uploads/documents/' . $suratPermohonanName);
                return $this->response->setJSON([
                    'error' => 'Gagal menyimpan data: Terjadi kesalahan pada transaksi database'
                ]);
            }

            $userData = user()->toArray();
            $data['user_email'] = $userData['email'];
            $data['user_fullname'] = $userData['fullname'];
            $data['merk'] = $asset['merk'];
            $data['no_polisi'] = $asset['no_polisi'];
            sendPeminjamanNotification($data, 'new');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data peminjaman berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            @unlink(ROOTPATH . 'public/uploads/documents/' . $suratPermohonanName);
            log_message('error', 'Error in loan process: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    public function kembali()
{
    log_message('debug', '🔥 MASUK FUNCTION KEMBALI');
    $model = new KembaliModel();
    $pinjamModel = new PinjamModel();
    $asetModel = new AsetModel();
    $db = db_connect();

    $userId = user_id();
    $nama_penanggung_jawab = $this->request->getPost('nama_penanggung_jawab');
    $nip_nrp = $this->request->getPost('nip_nrp');
    $pangkat_golongan = $this->request->getPost('pangkat_golongan');
    $jabatan = $this->request->getPost('jabatan');
    $unit_organisasi = $this->request->getPost('unit_organisasi');
    $kendaraan_id = $this->request->getPost('kendaraan_id') ?? $this->request->getPost('kendaraan_id_hidden');
    $no_hp = $this->request->getPost('no_hp');
    $tanggal_pinjam = $this->request->getPost('tanggal_pinjam');
    $tanggal_kembali = $this->request->getPost('tanggal_kembali');
    $suratPengembalian = $this->request->getFile('surat_pengembalian');
    $beritaAcara = $this->request->getFile('berita_acara_pengembalian');
    $kondisi_kembali = $this->request->getPost('kondisi_kembali');

    if (empty($kendaraan_id)) {
        return $this->response->setJSON([
            'error' => 'Data kendaraan tidak valid'
        ]);
    }

    $asset = $asetModel->find($kendaraan_id);
    if (!$asset) {
        return $this->response->setJSON(['error' => 'Kendaraan tidak ditemukan dalam database.']);
    }

    $pinjam = $pinjamModel->where([
        'kendaraan_id' => $kendaraan_id,
        'status' => 'disetujui',
        'is_returned' => false,
        'deleted_at' => null
    ])->first();

    log_message('debug', '🔍 === DEBUG KEMBALI ===');
    log_message('debug', 'User login ID        : ' . $userId);
    log_message('debug', 'Kendaraan ID (request): ' . $kendaraan_id);
    log_message('debug', 'Hasil query pinjam    : ' . json_encode($pinjam));

    if (!$pinjam) {
        log_message('debug', '🚫 Tidak ditemukan peminjaman aktif dengan kondisi: kendaraan_id = ' . $kendaraan_id);
        return $this->response->setJSON(['error' => 'Tidak ada peminjaman aktif untuk kendaraan ini']);
    }

    log_message('debug', '✅ Data ditemukan, lanjut cek akses user...');
    log_message('debug', 'user_id di data pinjam: ' . $pinjam['user_id']);
    log_message('debug', 'user_id login         : ' . $userId);
    
    if ((int) $pinjam['user_id'] !== (int) $userId) {
        log_message('debug', '🚫 User bukan peminjam, ditolak.');
    
        return $this->response->setJSON([
            'error' => 'Anda tidak memiliki akses untuk mengembalikan kendaraan ini'
        ]);
    }

    // Daftar MIME types yang diizinkan
    $allowedMimeTypes = ['application/pdf', 'image/png', 'image/jpeg'];

    log_message('debug', '📁 Mulai proses upload file...');
    
    $suratPengembalianName = null;

    if ($suratPengembalian && $suratPengembalian->isValid()) {
        log_message('debug', '📎 Surat Pengembalian: ' . $suratPengembalian->getClientName() . ' (' . $suratPengembalian->getClientMimeType() . ')');
        if (!in_array($suratPengembalian->getClientMimeType(), $allowedMimeTypes)) {
            log_message('debug', '❌ Format surat pengembalian tidak valid: ' . $suratPengembalian->getClientMimeType());
            return $this->response->setJSON(['error' => 'Format file Surat Pengembalian harus PDF, PNG, atau JPEG']);
        }

        if ($suratPengembalian->getSize() > 2 * 1024 * 1024) {
            log_message('debug', '❌ Ukuran surat pengembalian terlalu besar: ' . $suratPengembalian->getSize());
            return $this->response->setJSON(['error' => 'Ukuran file Surat Pengembalian tidak boleh lebih dari 2MB']);
        }

        log_message('debug', '🦠 Scanning surat pengembalian dengan VirusTotal...');
        if ($this->check_file_with_virustotal($suratPengembalian)) {
            log_message('debug', '❌ Surat pengembalian terdeteksi tidak aman oleh VirusTotal');
            return $this->response->setJSON(['error' => 'File Surat Pengembalian terdeteksi tidak aman']);
        }

        $suratPengembalianName = $suratPengembalian->getRandomName();
        log_message('debug', '💾 Upload surat pengembalian: ' . $suratPengembalianName);
        try {
            $suratPengembalian->move(ROOTPATH . 'public/uploads/documents', $suratPengembalianName);
            log_message('debug', '✅ Surat pengembalian berhasil diupload');
        } catch (\Exception $e) {
            log_message('error', '❌ Gagal upload surat pengembalian: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Gagal mengupload surat pengembalian: ' . $e->getMessage()]);
        }
    } else {
        log_message('debug', '📎 Tidak ada surat pengembalian yang diupload');
    }

    log_message('debug', '📁 Proses berita acara...');
    $beritaAcaraName = null;

    if ($beritaAcara && $beritaAcara->isValid()) {
        log_message('debug', '📎 Berita Acara: ' . $beritaAcara->getClientName() . ' (' . $beritaAcara->getClientMimeType() . ')');
        if (!in_array($beritaAcara->getClientMimeType(), $allowedMimeTypes)) {
            log_message('debug', '❌ Format berita acara tidak valid: ' . $beritaAcara->getClientMimeType());
            if ($suratPengembalianName) {
                @unlink(ROOTPATH . 'public/uploads/documents/' . $suratPengembalianName);
            }
            return $this->response->setJSON(['error' => 'Format file Berita Acara harus PDF, PNG, atau JPEG']);
        }

        if ($beritaAcara->getSize() > 2 * 1024 * 1024) {
            log_message('debug', '❌ Ukuran berita acara terlalu besar: ' . $beritaAcara->getSize());
            if ($suratPengembalianName) {
                @unlink(ROOTPATH . 'public/uploads/documents/' . $suratPengembalianName);
            }
            return $this->response->setJSON(['error' => 'Ukuran file Berita Acara tidak boleh lebih dari 2MB']);
        }

        log_message('debug', '🦠 Scanning berita acara dengan VirusTotal...');
        if ($this->check_file_with_virustotal($beritaAcara)) {
            log_message('debug', '❌ Berita acara terdeteksi tidak aman oleh VirusTotal');
            if ($suratPengembalianName) {
                @unlink(ROOTPATH . 'public/uploads/documents/' . $suratPengembalianName);
            }
            return $this->response->setJSON(['error' => 'File Berita Acara terdeteksi tidak aman']);
        }
        
        $beritaAcaraName = $beritaAcara->getRandomName();
        log_message('debug', '💾 Upload berita acara: ' . $beritaAcaraName);
        try {
            $beritaAcara->move(ROOTPATH . 'public/uploads/documents', $beritaAcaraName);
            log_message('debug', '✅ Berita acara berhasil diupload');
        } catch (\Exception $e) {
            log_message('error', '❌ Gagal upload berita acara: ' . $e->getMessage());
            if ($suratPengembalianName) {
                @unlink(ROOTPATH . 'public/uploads/documents/' . $suratPengembalianName);
            }
            return $this->response->setJSON(['error' => 'Gagal mengupload berita acara: ' . $e->getMessage()]);
        }
    } else {
        log_message('debug', '📎 Tidak ada berita acara yang diupload');
    }

    log_message('debug', '✅ Upload file selesai, lanjut validasi field...');

    $requiredFields = [
        'nama_penanggung_jawab' => $nama_penanggung_jawab,
        'nip_nrp' => $nip_nrp,
        'pangkat_golongan' => $pangkat_golongan,
        'jabatan' => $jabatan,
        'unit_organisasi' => $unit_organisasi,
        'no_hp' => $no_hp,
        'tanggal_pinjam' => $tanggal_pinjam,
        'tanggal_kembali' => $tanggal_kembali
    ];

    foreach ($requiredFields as $field => $value) {
        if (empty($value)) {
            log_message('debug', '❌ Field kosong: ' . $field);
            $this->cleanupFiles($suratPengembalianName, $beritaAcaraName);
            return $this->response->setJSON(['error' => ucwords(str_replace('_', ' ', $field)) . ' harus diisi.']);
        }
    }

    log_message('debug', '✅ Semua field valid, mulai transaksi database...');

    try {
        $db->transStart();
        $data = [
            'user_id' => $userId,
            'nama_penanggung_jawab' => $nama_penanggung_jawab,
            'nip_nrp' => $nip_nrp,
            'pangkat_golongan' => $pangkat_golongan,
            'jabatan' => $jabatan,
            'unit_organisasi' => $unit_organisasi,
            'kendaraan_id' => $kendaraan_id,
            'kondisi_kembali' => $kondisi_kembali,
            'pinjam_id' => $pinjam['id'],
            'no_hp' => $no_hp,
            'tanggal_pinjam' => $tanggal_pinjam,
            'tanggal_kembali' => $tanggal_kembali,
            'kode_barang' => $asset['kode_barang'],
            'status' => KembaliModel::STATUS_PENDING,
            'keterangan' => null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($suratPengembalianName) {
            $data['surat_pengembalian'] = $suratPengembalianName;
        }
        if ($beritaAcaraName) {
            $data['berita_acara_pengembalian'] = $beritaAcaraName;
        }

        $result = $model->insert($data);

        if (!$result) {
            log_message('error', '❌ Gagal insert ke KembaliModel');
            throw new \Exception('Gagal menyimpan data pengembalian');
        }

        log_message('debug', '✅ Data pengembalian berhasil disimpan dengan ID: ' . $result);

        $pinjamModel->update($pinjam['id'], ['is_returned' => true]);
        log_message('debug', '✅ Status pinjaman berhasil diupdate');

        $asetModel->update($kendaraan_id, [
            'status_pinjam' => 'Dalam Verifikasi Pengembalian'
        ]);
        log_message('debug', '✅ Status aset berhasil diupdate');

        $db->transComplete();

        if ($db->transStatus() === false) {
            log_message('error', '❌ Transaksi database gagal');
            throw new \Exception('Terjadi kesalahan pada transaksi database');
        }

        log_message('debug', '✅ Semua transaksi database berhasil');
        log_message('debug', '📧 Mengirim notifikasi...');

        $userData = user()->toArray();
        $notifData = [
            'user_email' => $userData['email'] ?? '',
            'user_fullname' => $userData['fullname'] ?? '',
            'merk' => $asset['merk'] ?? '',
            'no_polisi' => $asset['no_polisi'] ?? '',
            'status' => 'pending',
            'keterangan' => '',
            'kondisi_kembali' => $kondisi_kembali ?? '-',
            'nama_penanggung_jawab' => $nama_penanggung_jawab ?? '',
            'nip_nrp' => $nip_nrp ?? '',
            'tanggal_pinjam' => $tanggal_pinjam ?? '',
            'tanggal_kembali' => $tanggal_kembali ?? '',
            'surat_pengembalian' => $suratPengembalianName ?? '',
            'berita_acara_pengembalian' => $beritaAcaraName ?? ''
        ];
        sendPengembalianNotification($notifData, 'new');
        log_message('debug', '✅ Notifikasi berhasil dikirim');

        log_message('debug', '🎉 Proses pengembalian selesai dengan sukses!');

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data pengembalian berhasil disimpan'
        ]);

    } catch (\Exception $e) {
        $db->transRollback();
        $this->cleanupFiles($suratPengembalianName, $beritaAcaraName);
        log_message('error', 'Error in return process: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());

        return $this->response->setJSON([
            'error' => 'Gagal menyimpan data: ' . $e->getMessage()
        ]);
    }
}

private function cleanupFiles($suratPengembalian = null, $beritaAcara = null)
{
    if ($suratPengembalian) {
        @unlink(ROOTPATH . 'public/uploads/documents/' . $suratPengembalian);
    }
    if ($beritaAcara) {
        @unlink(ROOTPATH . 'public/uploads/documents/' . $beritaAcara);
    }
}

    public function verifikasiPeminjaman()
    {
        if (!in_groups(['admin', 'admin_gedungutama'])) {
            return $this->response->setJSON(['error' => 'Unauthorized Access']);
        }

        $pinjamId = $this->request->getPost('pinjam_id');
        $status = $this->request->getPost('status');
        $keterangan = $this->request->getPost('keterangan');
        $surat_jalan_admin = $this->request->getFile('surat_jalan_admin');
        $dokumen_tambahan = $this->request->getFile('dokumen_tambahan');

        $model = new PinjamModel();
        $asetModel = new AsetModel();
        $db = db_connect();

        if (!in_array($status, [PinjamModel::STATUS_DISETUJUI, PinjamModel::STATUS_DITOLAK])) {
            return $this->response->setJSON(['error' => 'Status tidak valid']);
        }

        $pinjam = $model->find($pinjamId);
        if (!$pinjam) {
            return $this->response->setJSON(['error' => 'Data peminjaman tidak ditemukan']);
        }

        if ($status === 'disetujui') {
            if (!$surat_jalan_admin || !$surat_jalan_admin->isValid()) {
                return $this->response->setJSON(['error' => 'Surat Jalan harus diunggah untuk menyetujui peminjaman']);
            }

            if ($surat_jalan_admin->getClientMimeType() !== 'application/pdf') {
                return $this->response->setJSON(['error' => 'Format file Surat Jalan harus PDF']);
            }

            if ($surat_jalan_admin->getSize() > 2 * 1024 * 1024) {
                return $this->response->setJSON(['error' => 'Ukuran file Surat Jalan tidak boleh lebih dari 2MB']);
            }

            if ($this->check_file_with_virustotal($surat_jalan_admin)) {
                return $this->response->setJSON(['error' => 'File Surat Jalan terdeteksi tidak aman']);
            }
        }

        if ($status === 'ditolak') {
            if (!$dokumen_tambahan || !$dokumen_tambahan->isValid()) {
                return $this->response->setJSON(['error' => 'Dokumen Tambahan harus diunggah untuk menolak peminjaman']);
            }

            if ($dokumen_tambahan->getClientMimeType() !== 'application/pdf') {
                return $this->response->setJSON(['error' => 'Format file Dokumen Tambahan harus PDF']);
            }

            if ($dokumen_tambahan->getSize() > 2 * 1024 * 1024) {
                return $this->response->setJSON(['error' => 'Ukuran file Dokumen Tambahan tidak boleh lebih dari 2MB']);
            }

            if ($this->check_file_with_virustotal($dokumen_tambahan)) {
                return $this->response->setJSON(['error' => 'File Dokumen Tambahan terdeteksi tidak aman']);
            }
        }

        $db->transStart();

        try {
            $updateData = [
                'status' => $status,
                'keterangan' => $keterangan
            ];

            if ($status === 'disetujui') {
                $suratJalanName = $surat_jalan_admin->getRandomName();
                try {
                    if ($surat_jalan_admin->move(ROOTPATH . 'public/uploads/documents', $suratJalanName)) {
                        $updateData['surat_jalan_admin'] = $suratJalanName;
                    }
                } catch (\Exception $e) {
                    return $this->response->setJSON(['error' => 'Gagal mengupload surat jalan: ' . $e->getMessage()]);
                }
            }

            if ($status === 'ditolak') {
                $dokumenTambahanName = $dokumen_tambahan->getRandomName();
                try {
                    if ($dokumen_tambahan->move(ROOTPATH . 'public/uploads/documents', $dokumenTambahanName)) {
                        $updateData['dokumen_tambahan'] = $dokumenTambahanName;
                    }
                } catch (\Exception $e) {
                    return $this->response->setJSON(['error' => 'Gagal mengupload surat jalan: ' . $e->getMessage()]);
                }
            }

            $model->update($pinjamId, $updateData);

            $statusAset = $status === 'disetujui' ? 'Dipinjam' : 'Tersedia';
            $asetModel->update($pinjam['kendaraan_id'], ['status_pinjam' => $statusAset]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                if (isset($suratJalanName)) {
                    @unlink(ROOTPATH . 'public/uploads/documents/' . $suratJalanName);
                }
                return $this->response->setJSON(['error' => 'Terjadi kesalahan pada transaksi database']);
            }

            $asset = $asetModel->find($pinjam['kendaraan_id']);
            $userData = $this->getUserData($pinjam['user_id']);
            $notifData = array_merge($pinjam, [
                'user_email' => $userData->email,
                'user_fullname' => $userData->fullname,
                'merk' => $asset['merk'],
                'no_polisi' => $asset['no_polisi'],
                'status' => $status,
                'keterangan' => $keterangan,
                'surat_jalan_admin' => $suratJalanName ?? '',
                'surat_permohonan' => $pinjam['surat_permohonan'] ?? '',
                'dokumen_tambahan' => $pinjam['dokumen_tambahan'] ?? ''
            ]);
            sendPeminjamanNotification($notifData, 'verified');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Verifikasi peminjaman berhasil'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            if (isset($suratJalanName)) {
                @unlink(ROOTPATH . 'public/uploads/documents/' . $suratJalanName);
            }
            log_message('error', 'Error in verification: ' . $e->getMessage());
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function verifikasiPengembalian()
    {
        if (!in_groups(['admin', 'admin_gedungutama'])) {
            return $this->response->setJSON(['error' => 'Unauthorized Access']);
        }

        $kembaliId = $this->request->getPost('kembali_id');
        $status = $this->request->getPost('status');
        $keterangan = $this->request->getPost('keterangan');
        $dokumenTambahan = $this->request->getFile('dokumen_tambahan');

        $model = new KembaliModel();
        $pinjamModel = new PinjamModel();
        $asetModel = new AsetModel();
        $db = db_connect();

        if (!in_array($status, [KembaliModel::STATUS_DISETUJUI, KembaliModel::STATUS_DITOLAK])) {
            return $this->response->setJSON(['error' => 'Status tidak valid']);
        }

        $kembali = $model->find($kembaliId);
        if (!$kembali) {
            return $this->response->setJSON(['error' => 'Data pengembalian tidak ditemukan']);
        }

        $updateData = [
            'status' => $status,
            'keterangan' => $keterangan
        ];

        if ($dokumenTambahan && $dokumenTambahan->isValid()) {
            if ($dokumenTambahan->getClientMimeType() !== 'application/pdf') {
                return $this->response->setJSON(['error' => 'Format file Dokumen Tambahan harus PDF']);
            }

            if ($dokumenTambahan->getSize() > 2 * 1024 * 1024) {
                return $this->response->setJSON(['error' => 'Ukuran file Dokumen Tambahan tidak boleh lebih dari 2MB']);
            }
            
            if ($this->check_file_with_virustotal($dokumenTambahan)) {
                return $this->response->setJSON(['error' => 'File Dokumen Tambahan terdeteksi tidak aman']);
            }
    
            $newName = $dokumenTambahan->getRandomName();
            $dokumenTambahan->move(ROOTPATH . 'public/uploads/documents', $newName);
            $updateData['dokumen_tambahan'] = $newName;
        }

        $pinjam = $pinjamModel->where([
            'kendaraan_id' => $kembali['kendaraan_id'],
            'status' => 'disetujui',
            'is_returned' => true,
            'deleted_at' => null
        ])->first();

        if (!$pinjam) {
            return $this->response->setJSON(['error' => 'Data peminjaman terkait tidak ditemukan']);
        }

        $db->transStart();

        try {
            $model->update($kembaliId, [
                'status' => $status,
                'keterangan' => $keterangan
            ]);

            if ($status === 'disetujui') {
                $asetModel->update($kembali['kendaraan_id'], [
                    'status_pinjam' => 'Tersedia'
                ]);

                $pinjamModel->update($pinjam['id'], [
                    'status' => 'selesai',
                    'is_returned' => true
                ]);

            } else if ($status === 'ditolak') {
                $asetModel->update($kembali['kendaraan_id'], [
                    'status_pinjam' => 'Dipinjam'
                ]);

                $pinjamModel->update($pinjam['id'], [
                    'is_returned' => false
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['error' => 'Terjadi kesalahan pada transaksi database']);
            }

            $asset = $asetModel->find($kembali['kendaraan_id']);
            $userData = $this->getUserData($kembali['user_id']);
            $notifData = [
                'user_email' => $userData->email ?? '',
                'user_fullname' => $userData->fullname ?? '',
                'merk' => $asset['merk'] ?? '',
                'no_polisi' => $asset['no_polisi'] ?? '',
                'status' => $status ?? '',
                'keterangan' => $keterangan ?? '',
                'kondisi_kembali' => $kembali['kondisi_kembali'] ?? '-',
                'nama_penanggung_jawab' => $kembali['nama_penanggung_jawab'] ?? '',
                'nip_nrp' => $kembali['nip_nrp'] ?? '',
                'tanggal_pinjam' => $kembali['tanggal_pinjam'] ?? '',
                'tanggal_kembali' => $kembali['tanggal_kembali'] ?? '',
                'surat_pengembalian' => $kembali['surat_pengembalian'] ?? '',
                'berita_acara_pengembalian' => $kembali['berita_acara_pengembalian'] ?? '',
                'dokumen_tambahan' => $kembali['dokumen_tambahan'] ?? ''
            ];
            sendPengembalianNotification($notifData, 'verified');

            $message = $status === 'disetujui'
                ? 'Pengembalian kendaraan berhasil disetujui'
                : 'Pengembalian kendaraan ditolak. Status dikembalikan ke Dipinjam';

            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error in verification: ' . $e->getMessage());
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }
}