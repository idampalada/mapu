<?php

namespace App\Controllers;

use App\Models\AsetModel;
use App\Models\KembaliModel;
use App\Models\PinjamModel;
use Dompdf\Dompdf;
use Dompdf\Options;

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
    // Perbarui daftar field yang digunakan
    $fields = [
        'kategori_id',
        'kode_barang',
        'merk',
        'tahun_pembuatan',
        'kapasitas',
        'no_polisi',
        'no_rangka',
        'kondisi',
        'warna',        // Field baru
        'nomor_mesin',  // Field baru
        'nup',          // Field baru
        'no_stnk',      // Field STNK baru
        'no_bpkb'       // Field BPKB baru
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

public function generateSuratJalan()
{
    $pinjamId = $this->request->getPost('pinjam_id');
    $tanggalMulai = $this->request->getPost('tanggal_mulai');
    $jamMulai = $this->request->getPost('jam_mulai');
    $tanggalSelesai = $this->request->getPost('tanggal_selesai');
    $jamSelesai = $this->request->getPost('jam_selesai');
    $urusanKedinasan = $this->request->getPost('urusan_kedinasan');
    
    $model = new PinjamModel();
    $asetModel = new AsetModel();
    $userModel = new \Myth\Auth\Models\UserModel(); // Tambahkan model user
    
    // Ambil data pinjam
    $pinjam = $model->find($pinjamId);
    if (!$pinjam) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Data peminjaman tidak ditemukan'
        ]);
    }
    
    // Ambil data aset
    $asset = $asetModel->find($pinjam['kendaraan_id']);
    if (!$asset) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Data kendaraan tidak ditemukan'
        ]);
    }
    
    // Ambil data user
    $userData = $userModel->find($pinjam['user_id']);
    
    // Buat data untuk PDF
    $pdfData = [
    'nomor_surat' => 'SURAT/JALAN/' . date('Y/m') . '/' . sprintf('%04d', $pinjamId),
    'nama_penanggung_jawab' => $pinjam['nama_penanggung_jawab'],
    'nip_nrp' => $pinjam['nip_nrp'],
    'pangkat_golongan' => $pinjam['pangkat_golongan'],
    'jabatan' => $pinjam['jabatan'],
    'unit_organisasi' => $pinjam['unit_organisasi'],
    'urusan_kedinasan' => $urusanKedinasan,
    'tanggal_mulai' => $tanggalMulai,
    'jam_mulai' => $jamMulai,
    'tanggal_selesai' => $tanggalSelesai,
    'jam_selesai' => $jamSelesai,
    'kode_barang' => $asset['kode_barang'],
    'nup' => $asset['nup'] ?? '-',
    'no_polisi' => $asset['no_polisi'],
    'merk' => $asset['merk'],
    'kategori' => $asset['kategori_id'],
    'tanggal_terbit' => date('Y-m-d'),
    'penanggung_jawab' => 'Pak Solihin',
    'nip_penanggung_jawab' => '123123',
    // Ubah 2 baris berikut agar mengambil nilai dari form
    'pemegang_surat' => $this->request->getPost('nama_pemegang_surat') ?? 'Pak Udin',
    'nip_pemegang_surat' => $this->request->getPost('nip_pemegang_surat') ?? '12345678',
    'lokasi_terbit' => 'Jakarta'
];
    
    // Generate PDF surat jalan
    $suratJalanName = $this->generateSuratJalanPdf($pdfData);
    
    // Update status peminjaman
    $model->update($pinjamId, [
        'status' => PinjamModel::STATUS_DISETUJUI,
        'surat_jalan_admin' => $suratJalanName,
    ]);
    
    // Update status kendaraan
    $asetModel->update($pinjam['kendaraan_id'], [
        'status_pinjam' => 'Dipinjam'
    ]);
    
    // Persiapkan data untuk notifikasi
    $notifData = [
        'user_email' => $userData ? $userData->email : '',
        'user_fullname' => $userData ? $userData->fullname : '',
        'merk' => $asset['merk'],
        'no_polisi' => $asset['no_polisi'],
        'nama_penanggung_jawab' => $pinjam['nama_penanggung_jawab'],
        'nip_nrp' => $pinjam['nip_nrp'],
        'jabatan' => $pinjam['jabatan'],
        'unit_organisasi' => $pinjam['unit_organisasi'],
        'tanggal_pinjam' => $pinjam['tanggal_pinjam'],
        'tanggal_kembali' => $pinjam['tanggal_kembali'],
        'status' => 'disetujui',
        'surat_jalan_admin' => $suratJalanName,
        'surat_permohonan' => $pinjam['surat_permohonan'] ?? null
    ];
    
    // Kirim notifikasi persetujuan peminjaman
    sendPeminjamanNotification($notifData, 'verified');
    
    return $this->response->setJSON([
        'success' => true,
        'message' => 'Peminjaman berhasil disetujui',
        'file_name' => $suratJalanName
    ]);
}

// Method untuk generate PDF surat jalan
private function generateSuratJalanPdf($data)
{
    // Include logo converter jika tersedia
    if (file_exists(FCPATH . 'logo_converter_final.php')) {
        require_once FCPATH . 'logo_converter_final.php';
    }

    helper('dompdf');

    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Times-Roman');
    $options->set('chroot', FCPATH);

    $dompdf = new \Dompdf\Dompdf($options);

    // === LOGO PROCESSING (SAMA SEPERTI SURAT LAIN) ===
    $logoPath = FCPATH . 'assets/images/logo-pu.svg';

    if (class_exists('LogoConverter')) {
        $logoResult = \LogoConverter::getLogoForDompdf($logoPath);

        if ($logoResult['success'] && strlen($logoResult['data']) > 1000) {
            $data['logo_data'] = $logoResult['data'];
            $data['logo_method'] = $logoResult['method'];
            $data['logo_found'] = true;
        } else {
            $data['logo_data'] = $this->createSimpleFallbackLogo();
            $data['logo_method'] = 'fallback';
            $data['logo_found'] = false;
        }
    } else {
        $data['logo_data'] = $this->createSimpleFallbackLogo();
        $data['logo_method'] = 'no_converter';
        $data['logo_found'] = false;
    }

    // Load template header baru
    $html = view('templates/surat_jalan', $data);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $output = $dompdf->output();

    $timestamp = time();
    $cleanName = str_replace(' ', '_', strtolower($data['nama_penanggung_jawab']));
    $fileName = "surat_jalan_{$timestamp}_{$cleanName}.pdf";

    $filePath = ROOTPATH . 'public/uploads/documents/' . $fileName;

    if (!is_dir(ROOTPATH . 'public/uploads/documents/')) {
        mkdir(ROOTPATH . 'public/uploads/documents/', 0777, true);
    }

    file_put_contents($filePath, $output);
    @chmod($filePath, 0644);

    return $fileName;
}


// Method untuk upload file surat jalan
public function uploadSuratJalan()
{
    $pinjamId = $this->request->getPost('pinjam_id');
    
    $model = new PinjamModel();
    $asetModel = new AsetModel();
    
    // Ambil data pinjam
    $pinjam = $model->find($pinjamId);
    if (!$pinjam) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Data peminjaman tidak ditemukan'
        ]);
    }
    
    // Ambil file surat jalan
    $suratJalan = $this->request->getFile('surat_jalan_admin');
    if (!$suratJalan->isValid()) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'File surat jalan tidak valid'
        ]);
    }
    
    // Validasi file
    if ($suratJalan->getSize() > 2 * 1024 * 1024) { // Max 2MB
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Ukuran file melebihi batas maksimal (2MB)'
        ]);
    }
    
    if ($suratJalan->getClientMimeType() != 'application/pdf') {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'File harus berformat PDF'
        ]);
    }
    
    // Simpan file
    $timestamp = time();
    $newName = "surat_jalan_{$timestamp}_{$pinjam['nama_penanggung_jawab']}.pdf";
    $suratJalan->move(ROOTPATH . 'public/uploads/documents', $newName);
    
    // Update status peminjaman
    $model->update($pinjamId, [
        'status' => PinjamModel::STATUS_DISETUJUI,
        'surat_jalan_admin' => $newName,
    ]);
    
    // Update status kendaraan
    $asetModel->update($pinjam['kendaraan_id'], [
        'status_pinjam' => 'Dipinjam'
    ]);
    
    return $this->response->setJSON([
        'success' => true,
        'message' => 'Peminjaman berhasil disetujui',
        'file_name' => $newName
    ]);
}

// Method untuk mendapatkan data peminjaman
public function getPeminjamanData()
{
    $pinjamId = $this->request->getPost('pinjam_id');
    
    $model = new PinjamModel();
    $asetModel = new AsetModel();
    
    // Ambil data pinjam
    $pinjam = $model->find($pinjamId);
    if (!$pinjam) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Data peminjaman tidak ditemukan'
        ]);
    }
    
    // Ambil data aset
    $asset = $asetModel->find($pinjam['kendaraan_id']);
    if (!$asset) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Data kendaraan tidak ditemukan'
        ]);
    }
    
    return $this->response->setJSON([
        'success' => true,
        'pinjam' => $pinjam,
        'asset' => $asset
    ]);
}

public function getPeminjamanForKembali($kendaraanId = null)
{
    if (!$kendaraanId) {
        return $this->response->setJSON([
            'error' => 'ID Kendaraan tidak valid'
        ]);
    }
    
    $pinjamModel = new PinjamModel();
    $asetModel = new AsetModel();
    
    // Ambil data peminjaman aktif
    $pinjam = $pinjamModel->where([
        'kendaraan_id' => $kendaraanId,
        'status' => 'disetujui',
        'is_returned' => false,
        'deleted_at' => null
    ])->first();
    
    if (!$pinjam) {
        return $this->response->setJSON([
            'error' => 'Data peminjaman tidak ditemukan'
        ]);
    }
    
    // Ambil data aset
    $asset = $asetModel->find($kendaraanId);
    if (!$asset) {
        return $this->response->setJSON([
            'error' => 'Data kendaraan tidak ditemukan'
        ]);
    }
    
    // Gabungkan data untuk respons
    $response = array_merge($pinjam, [
        'merk' => $asset['merk'],
        'no_polisi' => $asset['no_polisi'],
        'kategori_id' => $asset['kategori_id'],
        'kode_barang' => $asset['kode_barang'],
        'nup' => $asset['nup'] ?? '-',
        'tahun_pembuatan' => $asset['tahun_pembuatan'] ?? '-',
        'warna' => $asset['warna'] ?? '-',
        'nomor_mesin' => $asset['nomor_mesin'] ?? '-',
        'nomor_rangka' => $asset['no_rangka'] ?? '-',
        // Tambahkan field STNK dan BPKB
        'no_stnk' => $asset['no_stnk'] ?? '',
        'no_bpkb' => $asset['no_bpkb'] ?? ''
    ]);
    
    return $this->response->setJSON([
        'success' => true,
        'data' => $response
    ]);
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
            'kode_barang' => $this->request->getPost('kode_barang'),
            'merk' => $this->request->getPost('merk'),
            'warna' => $this->request->getPost('warna'),
            'tahun_pembuatan' => $this->request->getPost('tahun_pembuatan'),
            'kapasitas' => $this->request->getPost('kapasitas'),
            'no_polisi' => $this->request->getPost('no_polisi'),
            'nup' => $this->request->getPost('nup'),
            'nomor_mesin' => $this->request->getPost('nomor_mesin'),
            'no_rangka' => $this->request->getPost('no_rangka'),
            'kondisi' => $this->request->getPost('kondisi'),
            'status_pinjam' => 'Tersedia',
            'created_at' => date('Y-m-d H:i:s'),
            'gambar_mobil' => json_encode($fileNames),
            // Tambahkan field no_stnk dan no_bpkb
            'no_stnk' => $this->request->getPost('no_stnk'),
            'no_bpkb' => $this->request->getPost('no_bpkb')
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
    $no_ktp = $this->request->getPost('no_ktp'); // Field baru
    $alamat_rumah = $this->request->getPost('alamat_rumah'); // Field baru
    $pangkat_golongan = $this->request->getPost('pangkat_golongan');
    $jabatan = $this->request->getPost('jabatan');
    $unit_organisasi = $this->request->getPost('unit_organisasi');
    $kendaraan_id = $this->request->getPost('kendaraan_id');
    $pengemudi = $this->request->getPost('pengemudi');
    $no_hp = $this->request->getPost('no_hp');
    $tanggal_pinjam = $this->request->getPost('tanggal_pinjam');
    $tanggal_kembali = $this->request->getPost('tanggal_kembali');
    $urusan_kedinasan = $this->request->getPost('urusan_kedinasan');

    
    $nama_penanggung_jawab_kendaraan = $this->request->getPost('nama_penanggung_jawab_kendaraan');
    $nip_penanggung_jawab_kendaraan = $this->request->getPost('nip_penanggung_jawab_kendaraan');
    $nama_kepala_satuan_kerja = $this->request->getPost('nama_kepala_satuan_kerja');
    $nip_kepala_satuan_kerja = $this->request->getPost('nip_kepala_satuan_kerja');
    
    $validationRules = [
        'nama_penanggung_jawab' => 'required',
        'nip_nrp' => 'required',
        'no_ktp' => 'required', 
        'alamat_rumah' => 'required',
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
            return $this->response->setJSON([
                'error' => ucwords(str_replace('_', ' ', $field)) . ' harus diisi.'
            ]);
        }
    }

    $asset = $asetModel->find($kendaraan_id);
    if (!$asset) {
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
        return $this->response->setJSON([
            'error' => 'Kendaraan ini sedang dalam proses verifikasi peminjaman.'
        ]);
    }

    $db->transStart();

    try {
        // Konversi kategori_id ke jenis kendaraan
        $jenisKendaraan = "Tidak Diketahui";
        switch($asset['kategori_id']) {
            case "KDJ":
                $jenisKendaraan = "Kendaraan Dinamis Jalan (KDJ)";
                break;
            case "KDO":
                $jenisKendaraan = "Kendaraan Dinamis Off-road (KDO)";
                break;
            case "KDF":
                $jenisKendaraan = "Kendaraan Dinamis Fasilitas (KDF)";
                break;
            default:
                $jenisKendaraan = $asset['kategori_id'] ?? "Tidak Diketahui";
        }
        
        // Buat data yang akan dikirim ke template PDF
                $pdfData = [
            'nama_penanggung_jawab' => $nama_penanggung_jawab,
            'nip_nrp' => $nip_nrp,
            'no_ktp' => $no_ktp,
            'alamat_rumah' => $alamat_rumah,
            'pangkat_golongan' => $pangkat_golongan,
            'jabatan' => $jabatan,
            'unit_organisasi' => $unit_organisasi,
            'pengemudi' => $pengemudi,
            'no_hp' => $no_hp,
            'tanggal_pinjam' => $tanggal_pinjam,
            'tanggal_kembali' => $tanggal_kembali,
            'urusan_kedinasan' => $urusan_kedinasan,
            'jenis_kendaraan' => $jenisKendaraan,
            'merk' => $asset['merk'],
            'no_polisi' => $asset['no_polisi'],
            'warna' => $asset['warna'] ?? '-',
            'nomor_mesin' => $asset['nomor_mesin'] ?? '-',
            'no_rangka' => $asset['no_rangka'] ?? '-',
            'nup' => $asset['nup'] ?? '-',
            'kode_barang' => $asset['kode_barang'],
            'tahun_pembuatan' => $asset['tahun_pembuatan'] ?? '-',
            'tanggal_pengajuan' => date('Y-m-d'),
    'nama_kepala_satuan_kerja' => '',
    'nip_kepala_satuan_kerja' => ''
        ];
        
        // Generate surat permohonan PDF
        $suratPermohonanName = $this->generateSuratPermohonan($pdfData);
        
                $data = [
            'user_id' => $userId,
            'nama_penanggung_jawab' => $nama_penanggung_jawab,
            'nip_nrp' => $nip_nrp,
            'no_ktp' => $no_ktp,
            'alamat_rumah' => $alamat_rumah,
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
            'created_at' => date('Y-m-d H:i:s'),
            // Simpan juga data penanda tangan ke database (opsional, jika ingin menyimpannya)
    'nama_penanggung_jawab_kendaraan' => '', // Simpan nilai kosong
    'nip_penanggung_jawab_kendaraan' => '', 
    'nama_kepala_satuan_kerja' => '',
    'nip_kepala_satuan_kerja' => ''
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
            // Hapus file jika transaksi gagal
            @unlink(ROOTPATH . 'public/uploads/documents/' . $suratPermohonanName);
            return $this->response->setJSON([
                'error' => 'Gagal menyimpan data: Terjadi kesalahan pada transaksi database'
            ]);
        }

        sendPeminjamanNotification($data, 'new');

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data peminjaman berhasil disimpan'
        ]);

    } catch (\Exception $e) {
        $db->transRollback();
        if (isset($suratPermohonanName)) {
            @unlink(ROOTPATH . 'public/uploads/documents/' . $suratPermohonanName);
        }
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
    $photo_data = $this->request->getPost('photo_data');
    $kondisi_kembali = $this->request->getPost('kondisi_kembali');
    $nomor_sip = $this->request->getPost('nomor_sip');
    $alamat_rumah = $this->request->getPost('alamat_rumah');
    $no_ktp = $this->request->getPost('no_ktp');
    $rating_pengguna = $this->request->getPost('rating_pengguna');
    
    // Data keterlambatan
    $is_late_return = $this->request->getPost('is_late_return') === 'true';
    $days_late = (int)$this->request->getPost('days_late');
    $alasan_keterlambatan = $is_late_return ? $this->request->getPost('alasan_keterlambatan') : null;
    
    
    // Ambil data Pihak Kedua yang diedit dari form
    $pihak_kedua_nama = $this->request->getPost('pihak_kedua_nama') ?? 'Pak Udin';
    $pihak_kedua_nip = $this->request->getPost('pihak_kedua_nip') ?? '12345678';
    $pihak_kedua_jabatan = $this->request->getPost('pihak_kedua_jabatan') ?? 'Kepala Satuan Kerja Selaku Kuasa Pengguna Barang';

    if (empty($kendaraan_id)) {
        return $this->response->setJSON([
            'error' => 'Data kendaraan tidak valid'
        ]);
    }
    
    if (empty($nomor_sip)) {
        return $this->response->setJSON([
            'error' => 'Nomor SIP / Surat Penanggung Jawab harus diisi'
        ]);
    }
    
    if (empty($rating_pengguna)) {
        return $this->response->setJSON([
            'error' => 'Rating penggunaan kendaraan harus dipilih'
        ]);
    }

    // Validasi data keterlambatan jika terlambat
    if ($is_late_return && empty($alasan_keterlambatan)) {
        return $this->response->setJSON([
            'error' => 'Alasan keterlambatan wajib diisi untuk pengembalian yang terlambat'
        ]);
    }

    $asset = $asetModel->find($kendaraan_id);
    if (!$asset) {
        return $this->response->setJSON(['error' => 'Kendaraan tidak ditemukan dalam database.']);
    }

    // PERBAIKAN: Ubah is_returned dari true menjadi false
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
    log_message('debug', 'Rating pengguna      : ' . $rating_pengguna);
    if ($is_late_return) {
        log_message('debug', 'Keterlambatan: Ya, ' . $days_late . ' hari');
        log_message('debug', 'Alasan keterlambatan: ' . $alasan_keterlambatan);
    }

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

    // Proses foto yang diambil
    $photoFileName = null;
    if (!empty($photo_data)) {
        log_message('debug', '📷 Memproses foto dari kamera...');
        $photo_data = str_replace('data:image/jpeg;base64,', '', $photo_data);
        $photo_data = str_replace(' ', '+', $photo_data);
        $imageData = base64_decode($photo_data);
        $photoFileName = 'foto_pengembalian_' . time() . '.jpg';
        $filePath = ROOTPATH . 'public/uploads/images/' . $photoFileName;
        
        try {
            // Pastikan direktori ada
            $imagesDir = ROOTPATH . 'public/uploads/images/';
            if (!is_dir($imagesDir)) {
                mkdir($imagesDir, 0777, true);
            }
            
            file_put_contents($filePath, $imageData);
            chmod($filePath, 0644);
            log_message('debug', '✅ Foto pengembalian berhasil disimpan: ' . $photoFileName);
        } catch (\Exception $e) {
            log_message('error', '❌ Gagal menyimpan foto: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Gagal menyimpan foto: ' . $e->getMessage()]);
        }
    } else {
        log_message('debug', '❌ Tidak ada foto yang diambil');
        return $this->response->setJSON(['error' => 'Foto kendaraan diperlukan untuk pengembalian']);
    }

    // Foto keterlambatan tidak lagi diproses - hanya alasan yang diperlukan
$fotoKeterlambatanFileName = null;
if ($is_late_return) {
    log_message('debug', '📝 Keterlambatan terdeteksi, hanya memproses alasan: ' . $alasan_keterlambatan);
    // Tidak ada pemrosesan foto keterlambatan
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
        'tanggal_kembali' => $tanggal_kembali,
        'kondisi_kembali' => $kondisi_kembali,
        'nomor_sip' => $nomor_sip,
        'rating_pengguna' => $rating_pengguna
    ];

    foreach ($requiredFields as $field => $value) {
    if (empty($value)) {
        log_message('debug', '❌ Field kosong: ' . $field);
        if ($photoFileName) {
            @unlink(ROOTPATH . 'public/uploads/images/' . $photoFileName);
        }
        // HAPUS: cleanup foto keterlambatan karena tidak ada lagi
        return $this->response->setJSON(['error' => ucwords(str_replace('_', ' ', $field)) . ' harus diisi.']);
    }
}

    log_message('debug', '✅ Semua field valid, mulai transaksi database...');
    
    // Pastikan direktori uploads/documents ada
    $docsDir = ROOTPATH . 'public/uploads/documents/';
    if (!is_dir($docsDir)) {
        mkdir($docsDir, 0777, true);
        log_message('debug', '✅ Direktori documents dibuat: ' . $docsDir);
    }

    try {
        // Generate berita acara PDF
        $timestamp = time();
        $beritaAcaraPdfName = "berita_acara_pengembalian_{$timestamp}.pdf";
        
        // Data untuk PDF
        $pdfData = [
            'nomor_surat' => 'PENGEMBALIAN/' . date('Y/m') . '/' . sprintf('%04d', $pinjam['id']),
            'nama_penanggung_jawab' => $nama_penanggung_jawab,
            'nip_nrp' => $nip_nrp,
            'pangkat_golongan' => $pangkat_golongan,
            'jabatan' => $jabatan,
            'unit_organisasi' => $unit_organisasi,
            'alamat_rumah' => $alamat_rumah ?? '',
            'no_hp' => $no_hp,
            'no_ktp' => $no_ktp ?? '',
            'no_polisi' => $asset['no_polisi'],
            'merk' => $asset['merk'],
            'kode_barang' => $asset['kode_barang'],
            'no_rangka' => $asset['no_rangka'] ?? '',  // Gunakan no_rangka karena itu nama di DB
            'warna' => $asset['warna'] ?? '',
            'nomor_mesin' => $asset['nomor_mesin'] ?? '',
            'nup' => $asset['nup'] ?? '',
            'tahun_pembuatan' => $asset['tahun_pembuatan'] ?? '',
            'kategori_id' => $asset['kategori_id'],
            'tanggal_pengembalian' => date('Y-m-d'),
            'kondisi_kembali' => $kondisi_kembali,
            'foto_pengembalian' => $photoFileName,
            'nomor_sip' => $nomor_sip,
            'pihak_kedua_nama' => $pihak_kedua_nama,
            'pihak_kedua_nip' => $pihak_kedua_nip,
            'pihak_kedua_jabatan' => $pihak_kedua_jabatan,
            'no_stnk' => $asset['no_stnk'] ?? '',
            'no_bpkb' => $asset['no_bpkb'] ?? '',
            'rating_pengguna' => $rating_pengguna,
            // Data keterlambatan
    'alasan_keterlambatan' => $alasan_keterlambatan,
    'foto_keterlambatan' => null, // UBAH: Set null karena tidak ada foto
    'daysLate' => $days_late
        ];

        // Log data STNK, BPKB, dan Rangka untuk debugging
        log_message('debug', 'Data kendaraan untuk PDF: ' . json_encode([
            'no_stnk' => $asset['no_stnk'] ?? 'tidak ada',
            'no_bpkb' => $asset['no_bpkb'] ?? 'tidak ada',
            'no_rangka' => $asset['no_rangka'] ?? 'tidak ada',
            'rating_pengguna' => $rating_pengguna
        ]));

        if ($is_late_return) {
            log_message('debug', 'Data keterlambatan untuk PDF: ' . json_encode([
                'alasan_keterlambatan' => $alasan_keterlambatan,
                'foto_keterlambatan' => $fotoKeterlambatanFileName,
                'days_late' => $days_late
            ]));
        }

        // Generate PDF
        helper('dompdf');
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $html = view('templates/berita_acara_pengembalian', $pdfData);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Simpan PDF ke folder
        $output = $dompdf->output();
        $filePath = $docsDir . $beritaAcaraPdfName;
        
        if (!file_put_contents($filePath, $output)) {
            throw new \Exception('Gagal menyimpan berita acara pengembalian');
        }
        
        chmod($filePath, 0644);
        log_message('debug', '✅ Berita acara berhasil dibuat: ' . $beritaAcaraPdfName);

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
            'foto_pengembalian' => $photoFileName,
            'nomor_sip' => $nomor_sip,
            'alamat_rumah' => $alamat_rumah,
            'no_ktp' => $no_ktp,
            'status' => KembaliModel::STATUS_PENDING,
            'keterangan' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'berita_acara_pengembalian' => $beritaAcaraPdfName,
            'surat_pengembalian' => null,
            'pihak_kedua_nama' => $pihak_kedua_nama,
            'pihak_kedua_nip' => $pihak_kedua_nip,
            'pihak_kedua_jabatan' => $pihak_kedua_jabatan,
            'rating_pengguna' => $rating_pengguna,
            // Data keterlambatan
            'alasan_keterlambatan' => $alasan_keterlambatan
        ];

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
            'berita_acara_pdf' => $beritaAcaraPdfName ?? '',
            'foto_pengembalian' => $photoFileName ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'pihak_kedua_nama' => $pihak_kedua_nama,
            'pihak_kedua_nip' => $pihak_kedua_nip,
            'pihak_kedua_jabatan' => $pihak_kedua_jabatan,
            'rating_pengguna' => $rating_pengguna,
            // Data keterlambatan untuk notifikasi
            'is_late_return' => $is_late_return,
            'days_late' => $days_late,
            'alasan_keterlambatan' => $alasan_keterlambatan
        ];
        
        if (function_exists('sendPengembalianNotification')) {
            sendPengembalianNotification($notifData, 'new');
            log_message('debug', '✅ Notifikasi berhasil dikirim');
        }

        log_message('debug', '🎉 Proses pengembalian selesai dengan sukses!');

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data pengembalian berhasil disimpan'
        ]);

    } catch (\Exception $e) {
    if (isset($db) && method_exists($db, 'transRollback')) {
        $db->transRollback();
    }
    if (isset($photoFileName) && !empty($photoFileName)) {
        @unlink(ROOTPATH . 'public/uploads/images/' . $photoFileName);
    }
    // HAPUS: cleanup foto keterlambatan karena tidak ada lagi
    // Hapus juga file berita acara jika ada error
    if (isset($beritaAcaraPdfName) && !empty($beritaAcaraPdfName)) {
        @unlink(ROOTPATH . 'public/uploads/documents/' . $beritaAcaraPdfName);
    }
    
    log_message('error', 'Error in return process: ' . $e->getMessage());
    log_message('error', 'Stack trace: ' . $e->getTraceAsString());

    return $this->response->setJSON([
        'error' => 'Gagal menyimpan data: ' . $e->getMessage()
    ]);
}
}


private function generateBeritaAcara($data)
{
    // Setup DOMPDF
    helper('dompdf');
    
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    
    $dompdf = new \Dompdf\Dompdf($options);
    
    // Pastikan data STNK dan BPKB tersedia untuk template
    if (!isset($data['no_stnk'])) {
        $data['no_stnk'] = '-';
    }
    
    if (!isset($data['no_bpkb'])) {
        $data['no_bpkb'] = '-';
    }
    
    // HTML template berita acara
    $html = view('templates/berita_acara_pengembalian', $data);
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // Simpan PDF ke folder
    $output = $dompdf->output();
    
    $timestamp = time();
    $fileName = "berita_acara_pengembalian_{$timestamp}.pdf";
    
    // Pastikan direktori ada
    $dir = ROOTPATH . 'public/uploads/documents/';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $filePath = $dir . $fileName;
    log_message('debug', 'Menyimpan berita acara: ' . $filePath);
    
    // Simpan file dengan error handling
    try {
        if (file_put_contents($filePath, $output)) {
            log_message('debug', 'Berita acara berhasil disimpan: ' . $fileName);
            @chmod($filePath, 0644); // Set izin file
            return $fileName;
        } else {
            log_message('error', 'Gagal menyimpan berita acara ke: ' . $filePath);
            return null;
        }
    } catch (\Exception $e) {
        log_message('error', 'Exception saat menyimpan berita acara: ' . $e->getMessage());
        return null;
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
    $rating_admin = $this->request->getPost('rating_admin'); // Ambil rating admin
    $dokumenTambahan = $this->request->getFile('dokumen_tambahan');

    // Validasi alasan penolakan jika status ditolak
    if ($status === 'ditolak' && empty($keterangan)) {
        return $this->response->setJSON(['error' => 'Alasan penolakan harus diisi']);
    }

    // Validasi rating admin jika status disetujui
    if ($status === 'disetujui' && empty($rating_admin)) {
        return $this->response->setJSON(['error' => 'Rating admin harus diisi']);
    }

    // Validasi dokumen tambahan saat penolakan
    if ($status === 'ditolak' && (!$dokumenTambahan || !$dokumenTambahan->isValid())) {
        return $this->response->setJSON(['error' => 'Dokumen tambahan harus diupload untuk menolak pengembalian']);
    }

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

    // Tambahkan rating admin jika disetujui
    if ($status === 'disetujui') {
        $updateData['rating_admin'] = $rating_admin;
    }

    if ($dokumenTambahan && $dokumenTambahan->isValid()) {
        // Mendukung format PDF, JPG, dan PNG
        $validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        $fileType = $dokumenTambahan->getClientMimeType();
        
        if (!in_array($fileType, $validTypes)) {
            return $this->response->setJSON(['error' => 'Format file Dokumen Tambahan harus PDF, JPG, atau PNG']);
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

    // Cari data peminjaman terkait
    $pinjam = $pinjamModel->find($kembali['pinjam_id']);

    if (!$pinjam) {
        return $this->response->setJSON(['error' => 'Data peminjaman terkait tidak ditemukan']);
    }

    $db->transStart();

    try {
        $model->update($kembaliId, $updateData);

        if ($status === 'disetujui') {
            $asetModel->update($kembali['kendaraan_id'], [
                'status_pinjam' => 'Tersedia'
            ]);

            $pinjamModel->update($pinjam['id'], [
                'status' => 'selesai',
                'is_returned' => false
            ]);

        } else if ($status === 'ditolak') {
            $asetModel->update($kembali['kendaraan_id'], [
                'status_pinjam' => 'Dipinjam'
            ]);

            $pinjamModel->update($pinjam['id'], [
                'is_returned' => false,
                'has_rejected_return' => true,
                'rejected_return_reason' => $keterangan,
                'rejected_return_date' => date('Y-m-d H:i:s')
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
            'dokumen_tambahan' => $kembali['dokumen_tambahan'] ?? '',
            'rating_pengguna' => $kembali['rating_pengguna'] ?? '',
            'rating_admin' => $rating_admin ?? ''
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
public function updateSurat()
{
    $pinjamId = $this->request->getPost('pinjam_id');
    $nomorSurat = $this->request->getPost('nomor_surat');
    $tanggalSurat = $this->request->getPost('tanggal_surat');
    $tempatSurat = $this->request->getPost('tempat_surat');
    
    // Hanya ambil data untuk Kepala Satuan Kerja
    $namaKepalaSatuanKerja = $this->request->getPost('nama_kepala_satuan_kerja');
    $nipKepalaSatuanKerja = $this->request->getPost('nip_kepala_satuan_kerja');
    
    $model = new PinjamModel();
    $asetModel = new AsetModel();
    
    // Ambil data pinjam
    $pinjam = $model->find($pinjamId);
    if (!$pinjam) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Data peminjaman tidak ditemukan'
        ]);
    }
    
    // Ambil data aset
    $asset = $asetModel->find($pinjam['kendaraan_id']);
    if (!$asset) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Data kendaraan tidak ditemukan'
        ]);
    }
    
    // Konversi kategori_id ke jenis kendaraan
    $jenisKendaraan = "Tidak Diketahui";
    switch($asset['kategori_id']) {
        case "KDJ":
            $jenisKendaraan = "Kendaraan Dinamis Jalan (KDJ)";
            break;
        case "KDO":
            $jenisKendaraan = "Kendaraan Dinamis Off-road (KDO)";
            break;
        case "KDF":
            $jenisKendaraan = "Kendaraan Dinamis Fasilitas (KDF)";
            break;
        default:
            $jenisKendaraan = $asset['kategori_id'] ?? "Tidak Diketahui";
    }
    
    // Siapkan data untuk PDF
    $pdfData = [
        'nama_penanggung_jawab' => $pinjam['nama_penanggung_jawab'],
        'nip_nrp' => $pinjam['nip_nrp'],
        'no_ktp' => $pinjam['no_ktp'],
        'alamat_rumah' => $pinjam['alamat_rumah'],
        'pangkat_golongan' => $pinjam['pangkat_golongan'],
        'jabatan' => $pinjam['jabatan'],
        'unit_organisasi' => $pinjam['unit_organisasi'],
        'pengemudi' => $pinjam['pengemudi'],
        'no_hp' => $pinjam['no_hp'],
        'tanggal_pinjam' => $pinjam['tanggal_pinjam'],
        'tanggal_kembali' => $pinjam['tanggal_kembali'],
        'urusan_kedinasan' => $pinjam['urusan_kedinasan'],
        'jenis_kendaraan' => $jenisKendaraan,
        'merk' => $asset['merk'],
        'no_polisi' => $asset['no_polisi'],
        'warna' => $asset['warna'] ?? '-',
        'nomor_mesin' => $asset['nomor_mesin'] ?? '-',
        'no_rangka' => $asset['no_rangka'] ?? '-',
        'nup' => $asset['nup'] ?? '-',
        'kode_barang' => $asset['kode_barang'],
        'tahun_pembuatan' => $asset['tahun_pembuatan'] ?? '-',
        'tanggal_pengajuan' => date('Y-m-d'),
        // Data kepala satuan kerja dari input admin
        'nama_kepala_satuan_kerja' => $namaKepalaSatuanKerja,
        'nip_kepala_satuan_kerja' => $nipKepalaSatuanKerja,
        // Data tambahan
        'nomor_surat' => $nomorSurat,
        'tanggal_surat' => $tanggalSurat,
        'tempat_surat' => $tempatSurat
    ];
    
    // Hapus file draft lama
    if (!empty($pinjam['surat_permohonan'])) {
        $oldPath = ROOTPATH . 'public/uploads/documents/' . $pinjam['surat_permohonan'];
        if (file_exists($oldPath)) {
            @unlink($oldPath);
        }
    }
    
    // Generate surat permohonan PDF yang final
    $suratPermohonanName = $this->generateSuratPermohonan($pdfData, true);
    
    // Update data peminjaman
    $model->update($pinjamId, [
        'surat_permohonan' => $suratPermohonanName,
        'nama_kepala_satuan_kerja' => $namaKepalaSatuanKerja,
        'nip_kepala_satuan_kerja' => $nipKepalaSatuanKerja
    ]);
    
    // Kembalikan response JSON untuk ditangani oleh JavaScript
    return $this->response->setJSON([
        'success' => true,
        'message' => 'Surat permohonan berhasil diperbarui',
        'file_name' => $suratPermohonanName
    ]);
}
public function generateSuratPenanggungJawabKdf()
{
    log_message('debug', '==== generateSuratPenanggungJawabKdf DIPANGGIL ====');
    log_message('debug', 'POST DATA: ' . json_encode($this->request->getPost()));
    
    $pinjamId = $this->request->getPost('pinjam_id');
    $nomorSurat = $this->request->getPost('nomor_surat');
    $tanggalSurat = $this->request->getPost('tanggal_surat');
    $tempatSurat = $this->request->getPost('tempat_surat');
    
    // Data untuk kedua penandatangan
    $namaPenanggungJawabKendaraan = $this->request->getPost('nama_penanggung_jawab_kendaraan');
    $nipPenanggungJawabKendaraan = $this->request->getPost('nip_penanggung_jawab_kendaraan');
    $namaKepalaSatuanKerja = $this->request->getPost('nama_kepala_satuan_kerja');
    $nipKepalaSatuanKerja = $this->request->getPost('nip_kepala_satuan_kerja');
    
    $model = new PinjamModel();
    $asetModel = new AsetModel();
    
    // Ambil data peminjaman
    $pinjam = $model->find($pinjamId);
    if (!$pinjam) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Data peminjaman tidak ditemukan'
        ]);
    }
    
    // Ambil data kendaraan
    $asset = $asetModel->find($pinjam['kendaraan_id']);
    if (!$asset) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Data kendaraan tidak ditemukan'
        ]);
    }
    
    // Konversi kategori_id ke jenis kendaraan
    $jenisKendaraan = "Tidak Diketahui";
    switch($asset['kategori_id']) {
        case "KDJ":
            $jenisKendaraan = "Kendaraan Dinamis Jalan (KDJ)";
            break;
        case "KDO":
            $jenisKendaraan = "Kendaraan Dinamis Off-road (KDO)";
            break;
        case "KDF":
            $jenisKendaraan = "Kendaraan Dinamis Fasilitas (KDF)";
            break;
        default:
            $jenisKendaraan = $asset['kategori_id'] ?? "Tidak Diketahui";
    }
    
    // Data untuk PDF surat penanggung jawab
    $pdfData = [
        'nama_penanggung_jawab' => $pinjam['nama_penanggung_jawab'],
        'nip_nrp' => $pinjam['nip_nrp'],
        'no_ktp' => $pinjam['no_ktp'] ?? '-',
        'alamat_rumah' => $pinjam['alamat_rumah'] ?? '-',
        'no_hp' => $pinjam['no_hp'] ?? '-', 
        'pangkat_golongan' => $pinjam['pangkat_golongan'],
        'jabatan' => $pinjam['jabatan'],
        'unit_organisasi' => $pinjam['unit_organisasi'],
        'jenis_kendaraan' => $jenisKendaraan,
        'merk' => $asset['merk'],
        'no_polisi' => $asset['no_polisi'],
        'warna' => $asset['warna'] ?? '-',
        'nomor_mesin' => $asset['nomor_mesin'] ?? '-',
        'no_rangka' => $asset['no_rangka'] ?? '-',
        'nup' => $asset['nup'] ?? '-',
        'kode_barang' => $asset['kode_barang'],
        'tahun_pembuatan' => $asset['tahun_pembuatan'] ?? '-',
        // Data penomoran surat
        'nomor_surat' => $nomorSurat,
        'tanggal_surat' => $tanggalSurat,
        'tempat_surat' => $tempatSurat,
        // Data kepala satuan kerja
        'nama_penanggung_jawab_kendaraan' => $namaPenanggungJawabKendaraan,
        'nip_penanggung_jawab_kendaraan' => $nipPenanggungJawabKendaraan,
        'nama_kepala_satuan_kerja' => $namaKepalaSatuanKerja,
        'nip_kepala_satuan_kerja' => $nipKepalaSatuanKerja,
    ];
    
    // Hapus file surat lama jika ada
    if (!empty($pinjam['surat_penanggung_jawab'])) {
        $oldPath = ROOTPATH . 'public/uploads/documents/' . $pinjam['surat_penanggung_jawab'];
        if (file_exists($oldPath)) {
            @unlink($oldPath);
        }
    }
    
    // Generate surat penanggung jawab PDF
    log_message('debug', '==== Memanggil generateSuratPenanggungJawab ====');
    $suratName = $this->generateSuratPenanggungJawab($pdfData);
    log_message('debug', '==== Hasil generateSuratPenanggungJawab: ' . $suratName . ' ====');
    
    // Update data peminjaman
    log_message('debug', '==== Mencoba update database dengan pinjam_id: ' . $pinjamId . ' ====');
    log_message('debug', 'Data update: ' . json_encode([
        'surat_penanggung_jawab' => $suratName,
        'nomor_surat' => $nomorSurat,
        'tanggal_surat' => $tanggalSurat,
        'nama_penanggung_jawab_kendaraan' => $namaPenanggungJawabKendaraan,
        'nip_penanggung_jawab_kendaraan' => $nipPenanggungJawabKendaraan,
        'nama_kepala_satuan_kerja' => $namaKepalaSatuanKerja,
        'nip_kepala_satuan_kerja' => $nipKepalaSatuanKerja
    ]));
    
    try {
        $result = $model->update($pinjamId, [
            'surat_penanggung_jawab' => $suratName,
            'nomor_surat' => $nomorSurat,
            'tanggal_surat' => $tanggalSurat,
            'tempat_surat' => $tempatSurat,
            'nama_penanggung_jawab_kendaraan' => $namaPenanggungJawabKendaraan,
            'nip_penanggung_jawab_kendaraan' => $nipPenanggungJawabKendaraan,
            'nama_kepala_satuan_kerja' => $namaKepalaSatuanKerja,
            'nip_kepala_satuan_kerja' => $nipKepalaSatuanKerja
        ]);
        
        log_message('debug', '==== Hasil update database: ' . ($result !== false ? 'Berhasil' : 'Gagal') . ' ====');
        
        // Verifikasi data telah tersimpan
        $updatedPinjam = $model->find($pinjamId);
        log_message('debug', 'Data setelah update: ' . json_encode([
            'surat_penanggung_jawab' => $updatedPinjam['surat_penanggung_jawab'] ?? null,
            'nomor_surat' => $updatedPinjam['nomor_surat'] ?? null
        ]));
        
        // Jika result false, tambahkan debug untuk error database
        if ($result === false) {
            log_message('error', 'Error Database: ' . print_r($model->errors(), true));
            log_message('error', 'SQL terakhir: ' . $model->db->getLastQuery());
        }
    } catch (\Exception $e) {
        log_message('error', 'Exception saat update database: ' . $e->getMessage());
          log_message('debug', 'Kolom yang diizinkan dalam model: ' . json_encode($model->allowedFields));
    }
    
    // Kembalikan response
    return $this->response->setJSON([
        'success' => true,
        'message' => 'Surat penanggung jawab berhasil dibuat',
        'file_name' => $suratName
    ]);
}

// Method untuk generate PDF surat penanggung jawab
// Method untuk generate PDF surat penanggung jawab
private function generateSuratPenanggungJawab($data)
{
    // Include logo converter jika tersedia
    if (file_exists(FCPATH . 'logo_converter_final.php')) {
        require_once FCPATH . 'logo_converter_final.php';
    }

    helper('dompdf');

    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Times-Roman');
    $options->set('chroot', FCPATH);

    $dompdf = new \Dompdf\Dompdf($options);

    // 🖼️ LOGO PROCESSING
    $logoPath = FCPATH . 'assets/images/logo-pu.svg';

    if (class_exists('LogoConverter')) {
        $logoResult = \LogoConverter::getLogoForDompdf($logoPath);

        if ($logoResult['success'] && strlen($logoResult['data']) > 1000) {
            $data['logo_data'] = $logoResult['data'];
            $data['logo_method'] = $logoResult['method'];
            $data['logo_found'] = true;
            $data['logo_message'] = $logoResult['message'];
        } else {
            $data['logo_data'] = $this->createSimpleFallbackLogo();
            $data['logo_method'] = 'fallback';
            $data['logo_found'] = false;
        }
    } else {
        $data['logo_data'] = $this->createSimpleFallbackLogo();
        $data['logo_method'] = 'no_converter';
        $data['logo_found'] = false;
    }

    // Load HTML template
    $html = view('templates/surat_penanggung_jawab', $data);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Simpan PDF
    $output = $dompdf->output();
    $timestamp = time();
    $cleanName = str_replace(' ', '_', strtolower($data['nama_penanggung_jawab']));
    $fileName = "surat_penanggung_jawab_{$timestamp}_{$cleanName}.pdf";

    $filePath = ROOTPATH . 'public/uploads/documents/' . $fileName;

    // Simpan file
    $dir = ROOTPATH . 'public/uploads/documents/';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($filePath, $output);
    @chmod($filePath, 0644);

    return $fileName;
}


// Perbarui method generateSuratPermohonan
private function generateSuratPermohonan($data, $isFinal = false)
{
    // Include logo converter jika tersedia
    if (file_exists(FCPATH . 'logo_converter_final.php')) {
        require_once FCPATH . 'logo_converter_final.php';
    }
    
    // Setup DOMPDF
    helper('dompdf');
    
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Times-Roman');
    $options->set('chroot', FCPATH);
    
    $dompdf = new \Dompdf\Dompdf($options);
    
    // 🖼️ LOGO PROCESSING
    $logoPath = FCPATH . 'assets/images/logo-pu.svg';
    
    if (class_exists('LogoConverter')) {
        $logoResult = \LogoConverter::getLogoForDompdf($logoPath);
        
        if ($logoResult['success'] && strlen($logoResult['data']) > 1000) {
            $data['logo_data'] = $logoResult['data'];
            $data['logo_method'] = $logoResult['method'];
            $data['logo_found'] = true;
            $data['logo_message'] = $logoResult['message'];
        } else {
            $data['logo_data'] = $this->createSimpleFallbackLogo();
            $data['logo_method'] = 'fallback';
            $data['logo_found'] = false;
        }
    } else {
        $data['logo_data'] = $this->createSimpleFallbackLogo();
        $data['logo_method'] = 'no_converter';
        $data['logo_found'] = false;
    }
    
    // Generate PDF (TIDAK BERUBAH)
    $html = view('templates/surat_permohonan', $data);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    $output = $dompdf->output();
    $timestamp = time();
    $cleanName = str_replace(' ', '_', strtolower($data['nama_penanggung_jawab']));
    
    $fileName = $isFinal ? "surat_permohonan_{$timestamp}_{$cleanName}.pdf" 
                         : "draft_surat_{$timestamp}_{$cleanName}.pdf";
    
    $filePath = ROOTPATH . 'public/uploads/documents/' . $fileName;
    
    $dir = ROOTPATH . 'public/uploads/documents/';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    file_put_contents($filePath, $output);
    @chmod($filePath, 0644);
    
    return $fileName;
}

// Tambahkan function ini ke controller AsetKendaraan
public function checkFile($filename = null)
{
    if (!$filename) {
        $files = [];
        $path = ROOTPATH . 'public/uploads/documents/';
        
        if (is_dir($path)) {
            $fileList = scandir($path);
            
            foreach ($fileList as $file) {
                if ($file != '.' && $file != '..') {
                    $files[] = [
                        'name' => $file,
                        'size' => filesize($path . $file),
                        'modified' => date('Y-m-d H:i:s', filemtime($path . $file)),
                        'path' => $path . $file
                    ];
                }
            }
        }
        
        return $this->response->setJSON([
            'directory' => $path,
            'exists' => is_dir($path),
            'writable' => is_dir($path) ? is_writable($path) : false,
            'files' => $files
        ]);
    } else {
        $path = ROOTPATH . 'public/uploads/documents/' . $filename;
        
        return $this->response->setJSON([
            'file' => $filename,
            'path' => $path,
            'exists' => file_exists($path),
            'size' => file_exists($path) ? filesize($path) : null,
            'modified' => file_exists($path) ? date('Y-m-d H:i:s', filemtime($path)) : null
        ]);
    }
}

public function getTimelineData($kendaraan_id = null)
{
    try {
        // Validate input
        if (!$kendaraan_id || !is_numeric($kendaraan_id)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'ID Kendaraan tidak valid'
            ]);
        }
        
        // Dapatkan user_id yang sedang login
        $userId = user_id();
        
        // Periksa apakah user adalah admin - cara yang lebih aman
        $isAdmin = false;
        
        // Cek apakah user adalah admin menggunakan Auth Groups
        $authGroupsModel = new \Myth\Auth\Models\GroupModel();
        $userGroups = $authGroupsModel->getGroupsForUser($userId);
        
        foreach ($userGroups as $group) {
            if ($group['name'] === 'admin' || $group['name'] === 'admin_gedungutama') {
                $isAdmin = true;
                break;
            }
        }
        
        // Log untuk debugging
        log_message('debug', 'User ID: ' . $userId . ', isAdmin: ' . ($isAdmin ? 'true' : 'false'));
        
        // Initialize models
        $pinjamModel = new \App\Models\PinjamModel();
        $kembaliModel = new \App\Models\KembaliModel();
        $asetModel = new \App\Models\AsetModel();
        
        // Get asset information
        $asset = $asetModel->find($kendaraan_id);
        if (!$asset) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Kendaraan tidak ditemukan'
            ]);
        }
        
        // Log untuk debugging
        log_message('info', 'Fetching data for kendaraan ID: ' . $kendaraan_id);
        
        // Get all peminjaman for this specific vehicle ID only
        $builder = $pinjamModel->builder()
            ->select('
                pinjam.*,
                users.username, 
                users.fullname,
                assets.merk as kendaraan_nama
            ')
            ->join('users', 'users.id = pinjam.user_id', 'left')
            ->join('assets', 'assets.id = pinjam.kendaraan_id', 'left')
            ->where('pinjam.kendaraan_id', $kendaraan_id) // Specific kendaraan_id
            ->where('pinjam.deleted_at IS NULL');
        
        // Jika bukan admin, filter berdasarkan user_id
        if (!$isAdmin) {
            $builder->where('pinjam.user_id', $userId);
        }
        
        $peminjaman = $builder->orderBy('pinjam.created_at', 'DESC')
            ->get()
            ->getResultArray();
            
        // Get all pengembalian for this specific vehicle ID only
        $kembaliBuilder = $kembaliModel->builder()
            ->select('
                kembali.*,
                users.username, 
                users.fullname,
                pinjam.nama_penanggung_jawab,
                pinjam.urusan_kedinasan,
                pinjam.tanggal_pinjam,
                pinjam.tanggal_kembali,
                pinjam.surat_permohonan,
                pinjam.surat_jalan_admin,
                pinjam.surat_penanggung_jawab,
                assets.merk as kendaraan_nama
            ')
            ->join('users', 'users.id = kembali.user_id', 'left')
            ->join('pinjam', 'pinjam.id = kembali.pinjam_id', 'left')
            ->join('assets', 'assets.id = kembali.kendaraan_id', 'left')
            ->where('kembali.kendaraan_id', $kendaraan_id) // Specific kendaraan_id
            ->where('kembali.deleted_at IS NULL');
        
        // Jika bukan admin, filter berdasarkan user_id
        if (!$isAdmin) {
            $kembaliBuilder->where('kembali.user_id', $userId);
        }
        
        $pengembalian = $kembaliBuilder->orderBy('kembali.created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        // Get all penolakan pengembalian for this specific vehicle ID only
        $penolakanBuilder = $kembaliModel->builder()
            ->select('
                kembali.*,
                users.username, 
                users.fullname,
                pinjam.nama_penanggung_jawab,
                pinjam.urusan_kedinasan,
                pinjam.tanggal_pinjam,
                pinjam.tanggal_kembali,
                pinjam.surat_permohonan,
                pinjam.surat_jalan_admin,
                pinjam.surat_penanggung_jawab,
                assets.merk as kendaraan_nama,
                assets.no_polisi
            ')
            ->join('users', 'users.id = kembali.user_id', 'left')
            ->join('pinjam', 'pinjam.id = kembali.pinjam_id', 'left')
            ->join('assets', 'assets.id = kembali.kendaraan_id', 'left')
            ->where('kembali.kendaraan_id', $kendaraan_id)
            ->where('kembali.status', 'ditolak')
            ->where('kembali.deleted_at IS NULL');
        
        // Jika bukan admin, filter berdasarkan user_id
        if (!$isAdmin) {
            $penolakanBuilder->where('kembali.user_id', $userId);
        }
        
        $penolakan = $penolakanBuilder->orderBy('kembali.created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        // Log jumlah data yang ditemukan
        log_message('info', 'Found ' . count($peminjaman) . ' peminjaman, ' . count($pengembalian) . ' pengembalian, and ' . count($penolakan) . ' penolakan for kendaraan ID: ' . $kendaraan_id);
        
        // Format peminjaman data
        $formattedPeminjaman = [];
        foreach ($peminjaman as $item) {
            $formattedPeminjaman[] = [
                'id' => $item['id'],
                'user_id' => $item['user_id'] ?? null,
                'username' => $item['username'] ?? '',
                'fullname' => $item['fullname'] ?? '',
                'nama_penanggung_jawab' => $item['nama_penanggung_jawab'] ?? 'Tidak Ada Nama',
                'tanggal' => $item['created_at'],
                'tanggal_formatted' => date('d/m/Y', strtotime($item['created_at'])),
                'tanggal_pinjam' => $item['tanggal_pinjam'],
                'tanggal_pinjam_formatted' => date('d/m/Y', strtotime($item['tanggal_pinjam'])),
                'tanggal_kembali' => $item['tanggal_kembali'],
                'tanggal_kembali_formatted' => date('d/m/Y', strtotime($item['tanggal_kembali'])),
                'status' => $item['status'] ?? 'pending',
                'keterangan' => $item['keterangan'] ?? '',
                'urusan_kedinasan' => $item['urusan_kedinasan'] ?? '',
                'surat_permohonan' => $item['surat_permohonan'] ?? '',
                'surat_jalan_admin' => $item['surat_jalan_admin'] ?? '',
                'surat_penanggung_jawab' => $item['surat_penanggung_jawab'] ?? '', // Menambahkan field surat_penanggung_jawab
                'dokumen_tambahan' => $item['dokumen_tambahan'] ?? '',
                'kendaraan_nama' => $item['kendaraan_nama'] ?? $asset['merk'] ?? 'Tidak Diketahui',
                'kendaraan_id' => $item['kendaraan_id'],
                'is_returned' => (bool)($item['is_returned'] ?? false)  // Pastikan field ini disertakan
            ];
        }

        // Format pengembalian data
        $formattedPengembalian = [];
        foreach ($pengembalian as $item) {
            $formattedPengembalian[] = [
                'id' => $item['id'],
                'pinjam_id' => $item['pinjam_id'],
                'user_id' => $item['user_id'] ?? null,
                'username' => $item['username'] ?? '',
                'fullname' => $item['fullname'] ?? '',
                'nama_penanggung_jawab' => $item['nama_penanggung_jawab'] ?? 'Tidak Ada Nama',
                'tanggal' => $item['created_at'],
                'tanggal_formatted' => date('d/m/Y', strtotime($item['created_at'])),
                'tanggal_pinjam' => $item['tanggal_pinjam'],
                'tanggal_pinjam_formatted' => date('d/m/Y', strtotime($item['tanggal_pinjam'])),
                'tanggal_kembali' => $item['tanggal_kembali'],
                'tanggal_kembali_formatted' => date('d/m/Y', strtotime($item['tanggal_kembali'])),
                'status' => $item['status'] ?? 'pending',
                'keterangan' => $item['keterangan'] ?? '',
                'urusan_kedinasan' => $item['urusan_kedinasan'] ?? '',
                'surat_permohonan' => $item['surat_permohonan'] ?? '',
                'surat_jalan_admin' => $item['surat_jalan_admin'] ?? '',
                'surat_penanggung_jawab' => $item['surat_penanggung_jawab'] ?? '', // Menambahkan field surat_penanggung_jawab
                'surat_pengembalian' => $item['surat_pengembalian'] ?? '',
                'berita_acara_pengembalian' => $item['berita_acara_pengembalian'] ?? '',
                'dokumen_tambahan' => $item['dokumen_tambahan'] ?? '',
                'kondisi_kembali' => $item['kondisi_kembali'] ?? '',
                'kendaraan_nama' => $item['kendaraan_nama'] ?? $asset['merk'] ?? 'Tidak Diketahui',
                'kendaraan_id' => $item['kendaraan_id']
            ];
        }
        
        // Format penolakan data
        $formattedPenolakan = [];
        foreach ($penolakan as $item) {
            $formattedPenolakan[] = [
                'id' => $item['id'],
                'pinjam_id' => $item['pinjam_id'],
                'user_id' => $item['user_id'] ?? null,
                'username' => $item['username'] ?? '',
                'fullname' => $item['fullname'] ?? '',
                'nama_penanggung_jawab' => $item['nama_penanggung_jawab'] ?? 'Tidak Ada Nama',
                'tanggal' => $item['created_at'],
                'tanggal_formatted' => date('d/m/Y', strtotime($item['created_at'])),
                'tanggal_pinjam' => $item['tanggal_pinjam'],
                'tanggal_pinjam_formatted' => date('d/m/Y', strtotime($item['tanggal_pinjam'])),
                'tanggal_kembali' => $item['tanggal_kembali'],
                'tanggal_kembali_formatted' => date('d/m/Y', strtotime($item['tanggal_kembali'])),
                'status' => $item['status'],
                'keterangan' => $item['keterangan'] ?? 'Tidak ada keterangan',
                'urusan_kedinasan' => $item['urusan_kedinasan'] ?? '',
                'surat_pengembalian' => $item['surat_pengembalian'] ?? '',
                'berita_acara_pengembalian' => $item['berita_acara_pengembalian'] ?? '',
                'dokumen_tambahan' => $item['dokumen_tambahan'] ?? '',
                'kondisi_kembali' => $item['kondisi_kembali'] ?? '',
                'kendaraan_nama' => $item['kendaraan_nama'] ?? $asset['merk'] ?? 'Tidak Diketahui',
                'kendaraan_id' => $item['kendaraan_id'],
                'no_polisi' => $item['no_polisi'] ?? ''
            ];
        }
        
        // Return formatted data
        return $this->response->setJSON([
            'success' => true,
            'asset' => $asset,
            'kendaraan_id' => $kendaraan_id, // Tambahkan ID kendaraan yang diminta
            'peminjaman' => $formattedPeminjaman,
            'pengembalian' => $formattedPengembalian,
            'penolakan' => $formattedPenolakan // Tambahkan data penolakan
        ]);
    } catch (\Exception $e) {
        log_message('error', 'Error in getTimelineData: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}
public function getPenolakanHistory($kendaraanId)
{
    // Validasi input
    if (!is_numeric($kendaraanId) || $kendaraanId <= 0) {
        return $this->response->setStatusCode(400)->setJSON(['error' => 'ID kendaraan tidak valid']);
    }

    try {
        $model = new KembaliModel();
        
        // Ambil data pengembalian yang ditolak
        $penolakan = $model->select('
                kembali.*, 
                pinjam.has_rejected_return, 
                pinjam.rejected_return_reason,
                pinjam.rejected_return_date,
                assets.merk, 
                assets.no_polisi
            ')
            ->join('pinjam', 'pinjam.id = kembali.pinjam_id')
            ->join('assets', 'assets.id = kembali.kendaraan_id')
            ->where([
                'kembali.kendaraan_id' => $kendaraanId,
                'kembali.status' => 'ditolak'
            ])
            ->orderBy('kembali.created_at', 'DESC')
            ->findAll();
        
        // Jika tidak ada data, kembalikan array kosong
        if (empty($penolakan)) {
            return $this->response->setJSON([]);
        }
        
        return $this->response->setJSON($penolakan);
        
    } catch (\Exception $e) {
        log_message('error', 'Error in getPenolakanHistory: ' . $e->getMessage());
        return $this->response->setStatusCode(500)
                             ->setJSON(['error' => 'Terjadi kesalahan saat mengambil data']);
    }
}


}