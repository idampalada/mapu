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
            // Generate PDF
            $html = $this->generateSuratJalanHtml($pinjam, $kendaraan, [
                'tanggal_mulai' => $tanggal_mulai,
                'jam_mulai' => $jam_mulai,
                'tanggal_selesai' => $tanggal_selesai,
                'jam_selesai' => $jam_selesai,
                'urusan_kedinasan' => $urusan_kedinasan
            ]);

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Generate random filename
            $filename = 'surat_jalan_' . time() . '_' . uniqid() . '.pdf';
            $filePath = ROOTPATH . 'public/uploads/documents/' . $filename;
            
            // Simpan file PDF
            file_put_contents($filePath, $dompdf->output());

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
        // Format tanggal untuk tampilan
        $tanggalMulaiFormatted = date('d/m/Y', strtotime($additionalData['tanggal_mulai']));
        $tanggalSelesaiFormatted = date('d/m/Y', strtotime($additionalData['tanggal_selesai']));
        
        // Tanggal saat ini untuk dokumen
        $currentDate = date('d-m-Y');

        // Escape data untuk HTML
        $nama = esc($pinjam['nama_penanggung_jawab']);
        $nip = esc($pinjam['nip_nrp']);
        $pangkat = esc($pinjam['pangkat_golongan']);
        $jabatan = esc($pinjam['jabatan']);
        $unitOrganisasi = esc($pinjam['unit_organisasi'] ?? 'PUPR');
        $urusan = esc($additionalData['urusan_kedinasan']);
        $kodeBarang = esc($kendaraan['kode_barang']);
        $noPolisi = esc($kendaraan['no_polisi']);
        $merk = esc($kendaraan['merk']);

        // Template HTML untuk PDF
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Surat Jalan Kendaraan Dinas</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12pt;
                    line-height: 1.5;
                    margin: 0;
                    padding: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .header h1 {
                    font-size: 14pt;
                    text-transform: uppercase;
                    margin: 0;
                }
                .header h2 {
                    font-size: 13pt;
                    text-transform: uppercase;
                    margin: 5px 0;
                }
                .content {
                    margin: 15px 0;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                table td {
                    padding: 5px;
                    vertical-align: top;
                }
                .permission-text {
                    font-weight: bold;
                    text-align: center;
                    margin: 15px 0;
                }
                .requirements {
                    margin: 20px 0;
                }
                .requirements ol {
                    margin-left: 20px;
                    padding-left: 0;
                }
                .requirements li {
                    margin-bottom: 5px;
                }
                .signature {
                    text-align: right;
                    margin-top: 50px;
                }
                .signature-space {
                    margin-top: 60px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>SURAT JALAN KENDARAAN DINAS FUNGSIONAL</h1>
                <h2>KEMENTERIAN PEKERJAAN UMUM DAN PERUMAHAN RAKYAT</h2>
                <p>NOMOR: ...................(2)</p>
            </div>
            
            <div class="content">
                <p>Dalam rangka penggunaan Kendaraan Dinas Fungsional pada Satuan Kerja {$unitOrganisasi} Kementerian PUPR, dengan ini:</p>
                <table>
                    <tr>
                        <td width="25%">Nama</td>
                        <td width="2%">:</td>
                        <td>{$nama}</td>
                    </tr>
                    <tr>
                        <td>NIP/NRP</td>
                        <td>:</td>
                        <td>{$nip}</td>
                    </tr>
                    <tr>
                        <td>Pangkat/Golongan</td>
                        <td>:</td>
                        <td>{$pangkat}</td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td>{$jabatan}</td>
                    </tr>
                </table>
            </div>
            
            <div class="permission-text">
                DIIZINKAN
            </div>
            
            <p>untuk memakai 1 (satu) unit kendaraan dinas fungsional dalam rangka melaksanakan tugas kedinasan {$urusan} mulai tanggal {$tanggalMulaiFormatted} Jam {$additionalData['jam_mulai']} sampai dengan tanggal {$tanggalSelesaiFormatted} jam {$additionalData['jam_selesai']}.</p>
            
            <div class="content">
                <p><strong>Data Kendaraan Dinas Fungsional:</strong></p>
                <table>
                    <tr>
                        <td width="25%">Kode Barang</td>
                        <td width="2%">:</td>
                        <td>{$kodeBarang}</td>
                    </tr>
                    <tr>
                        <td>NUP</td>
                        <td>:</td>
                        <td>{$kodeBarang}</td>
                    </tr>
                    <tr>
                        <td>Nomor Polisi</td>
                        <td>:</td>
                        <td>{$noPolisi}</td>
                    </tr>
                    <tr>
                        <td>Merk/Type</td>
                        <td>:</td>
                        <td>{$merk}</td>
                    </tr>
                </table>
            </div>
            
            <div class="requirements">
                <p><strong>Dengan ketentuan:</strong></p>
                <ol>
                    <li>Pemakai bertanggung jawab atas keamanan kendaraan selama pemakaian;</li>
                    <li>Pemakai bertanggung jawab atas kehilangan, bersedia dikenakan Tuntutan Ganti Rugi sesuai dengan ketentuan peraturan perundang-undangan;</li>
                    <li>Kendaraan Dinas Fungsional hanya untuk keperluan dinas/tugas, dan tidak dibenarkan untuk keperluan pribadi/keluarga;</li>
                    <li>Pemakai bersedia mengembalikan Kendaraan Dinas kepada Satuan Kerja selaku Kuasa Pengguna Barang.</li>
                </ol>
            </div>
            
            <div class="signature">
                <p>{$currentDate}</p>
                <div class="signature-space"></div>
                <p>........................(17), ......................(18)...20xx</p>
            </div>
        </body>
        </html>
        HTML;
    }

    protected function getUserData($userId)
    {
        // Dapatkan data pengguna dari model pengguna Anda
        $userModel = model('UserModel');
        return $userModel->find($userId);
    }
}