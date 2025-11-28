<?php

namespace App\Controllers;

use App\Models\PinjamModel;
use App\Models\AsetModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class SuratJalanController extends BaseController
{
    protected $pinjamModel;
    protected $asetModel;

    public function __construct()
    {
        $this->pinjamModel = new PinjamModel();
        $this->asetModel = new AsetModel();
    }

    public function generate()
    {
        if (!in_groups(['admin', 'admin_gedungutama'])) {
            return $this->response->setJSON(['error' => 'Unauthorized Access']);
        }

        $pinjamId = $this->request->getPost('pinjam_id');
        $tanggal_mulai = $this->request->getPost('tanggal_mulai');
        $jam_mulai = $this->request->getPost('jam_mulai');
        $tanggal_selesai = $this->request->getPost('tanggal_selesai');
        $jam_selesai = $this->request->getPost('jam_selesai');
        $urusan_kedinasan = $this->request->getPost('urusan_kedinasan');
        
        // Tambahkan pengambilan nilai dari form
        $nama_pemegang_surat = $this->request->getPost('nama_pemegang_surat');
        $nip_pemegang_surat = $this->request->getPost('nip_pemegang_surat');

        // Validasi field yang diperlukan
        if (!$pinjamId || !$tanggal_mulai || !$jam_mulai || !$tanggal_selesai || !$jam_selesai || !$urusan_kedinasan) {
            return $this->response->setJSON(['error' => 'Semua field harus diisi']);
        }

        // Ambil data peminjaman
        $pinjam = $this->pinjamModel->find($pinjamId);
        if (!$pinjam) {
            return $this->response->setJSON(['error' => 'Data peminjaman tidak ditemukan']);
        }

        // Ambil data kendaraan
        $kendaraan = $this->asetModel->find($pinjam['kendaraan_id']);
        if (!$kendaraan) {
            return $this->response->setJSON(['error' => 'Data kendaraan tidak ditemukan']);
        }

        try {
            // Include logo converter
            if (file_exists(FCPATH . 'logo_converter_final.php')) {
                require_once FCPATH . 'logo_converter_final.php';
            }

            // Generate HTML dengan logo integration
            $html = $this->generateSuratJalanHtml($pinjam, $kendaraan, [
                'tanggal_mulai' => $tanggal_mulai,
                'jam_mulai' => $jam_mulai,
                'tanggal_selesai' => $tanggal_selesai,
                'jam_selesai' => $jam_selesai,
                'urusan_kedinasan' => $urusan_kedinasan,
                'nama_pemegang_surat' => $nama_pemegang_surat,
                'nip_pemegang_surat' => $nip_pemegang_surat
            ]);

            // Setup DOMPDF dengan konfigurasi yang sama seperti surat permohonan
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Times-Roman');
            $options->set('chroot', FCPATH);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Generate filename dan simpan
            $filename = 'surat_jalan_' . time() . '_' . uniqid() . '.pdf';
            $filePath = ROOTPATH . 'public/uploads/documents/' . $filename;
            
            // Pastikan direktori ada
            $dir = ROOTPATH . 'public/uploads/documents/';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            
            // Simpan file PDF
            file_put_contents($filePath, $dompdf->output());
            @chmod($filePath, 0644);

            // Update database dengan file yang dihasilkan
            $this->pinjamModel->update($pinjamId, [
                'surat_jalan_admin' => $filename,
                'status' => 'disetujui'
            ]);

            // Update status kendaraan
            $this->asetModel->update($pinjam['kendaraan_id'], [
                'status_pinjam' => 'Dipinjam'
            ]);

            // Kirim notifikasi email
            $userData = $this->getUserData($pinjam['user_id']);
            $notifData = array_merge($pinjam, [
                'user_email' => $userData->email,
                'user_fullname' => $userData->fullname,
                'merk' => $kendaraan['merk'],
                'no_polisi' => $kendaraan['no_polisi'],
                'status' => 'disetujui',
                'keterangan' => 'Peminjaman kendaraan disetujui',
                'surat_jalan_admin' => $filename
            ]);
            
            // Memanggil fungsi notifikasi yang sudah ada
            sendPeminjamanNotification($notifData, 'verified');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Surat Jalan berhasil dibuat dan peminjaman disetujui',
                'file_url' => base_url('/uploads/documents/' . $filename)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error generating Surat Jalan: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'error' => 'Terjadi kesalahan saat membuat Surat Jalan: ' . $e->getMessage()
            ]);
        }
    }
    
    protected function generateSuratJalanHtml($pinjam, $kendaraan, $additionalData)
    {
        // Include logo converter
        if (file_exists(FCPATH . 'logo_converter_final.php')) {
            require_once FCPATH . 'logo_converter_final.php';
        }

        // Format tanggal untuk tampilan
        $tanggalMulaiFormatted = date('d/m/Y', strtotime($additionalData['tanggal_mulai']));
        $tanggalSelesaiFormatted = date('d/m/Y', strtotime($additionalData['tanggal_selesai']));
        
        // Data untuk template
        $data = [
            'nama' => esc($pinjam['nama_penanggung_jawab']),
            'nip' => esc($pinjam['nip_nrp']),
            'pangkat' => esc($pinjam['pangkat_golongan']),
            'jabatan' => esc($pinjam['jabatan']),
            'unitOrganisasi' => esc($pinjam['unit_organisasi'] ?? 'Setjen Kementerian PUPR'),
            'urusan' => esc($additionalData['urusan_kedinasan']),
            'kodeBarang' => esc($kendaraan['kode_barang']),
            'noPolisi' => esc($kendaraan['no_polisi']),
            'merk' => esc($kendaraan['merk']),
            'tanggalMulaiFormatted' => $tanggalMulaiFormatted,
            'tanggalSelesaiFormatted' => $tanggalSelesaiFormatted,
            'jamMulai' => esc($additionalData['jam_mulai']),
            'jamSelesai' => esc($additionalData['jam_selesai']),
            'namaPemegangSurat' => esc($additionalData['nama_pemegang_surat'] ?? 'Pak Udin'),
            'nipPemegangSurat' => esc($additionalData['nip_pemegang_surat'] ?? '12345678')
        ];

        // Logo processing (SAMA SEPERTI SURAT PERMOHONAN)
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
        }

        // Generate HTML dengan template baru yang ada logo
        return view('templates/surat_jalan', $data);
    }

    // Method helper untuk fallback logo
    private function createSimpleFallbackLogo()
    {
        $svg = '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                <rect width="200" height="200" fill="#FFC107" stroke="#1A237E" stroke-width="3"/>
                <text x="100" y="70" text-anchor="middle" fill="#1A237E" font-size="36px" font-weight="bold">PU</text>
                <text x="100" y="110" text-anchor="middle" fill="#1A237E" font-size="14px">REPUBLIK</text>
                <text x="100" y="130" text-anchor="middle" fill="#1A237E" font-size="14px">INDONESIA</text>
                <text x="100" y="160" text-anchor="middle" fill="#1A237E" font-size="14px">KEMENTERIAN PUPR</text>
               </svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    protected function getUserData($userId)
    {
        $userModel = model('UserModel');
        return $userModel->find($userId);
    }
}