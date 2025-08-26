<?php

namespace App\Controllers;

use App\Models\PinjamModel;
use App\Models\AsetModel;
use CodeIgniter\Controller;

class CronJob extends Controller
{
    public function testOverdueCheck()
    {
        if (ENVIRONMENT !== 'development') {
            return $this->response->setJSON([
                'error' => 'Method ini hanya tersedia di environment development'
            ]);
        }

        try {
            $result = $this->checkOverdueReturns();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengecekan keterlambatan berhasil dijalankan',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Gagal menjalankan pengecekan: ' . $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }
    public function checkOverdueReturns()
    {
        $pinjamModel = new PinjamModel();
        $asetModel = new AsetModel();

        $activePinjam = $pinjamModel->select('pinjam.*, assets.merk, assets.no_polisi, users.email, users.fullname')
            ->join('assets', 'assets.id = pinjam.kendaraan_id')
            ->join('users', 'users.id = pinjam.user_id')
            ->where('pinjam.status', PinjamModel::STATUS_DISETUJUI)
            ->where('pinjam.is_returned', false)
            ->where('pinjam.deleted_at', null)
            ->findAll();

        $today = new \DateTime();
        $overdueDetails = [];
        $overdueCount = 0;

        foreach ($activePinjam as $pinjam) {
            $tanggalKembali = new \DateTime($pinjam['tanggal_kembali']);

            if ($today > $tanggalKembali) {
                $interval = $today->diff($tanggalKembali);
                $daysDiff = $interval->days;

                $emailSent = $this->sendOverdueNotification($pinjam, $daysDiff);

                $overdueDetails[] = [
                    'merk' => $pinjam['merk'],
                    'no_polisi' => $pinjam['no_polisi'],
                    'fullname' => $pinjam['fullname'],
                    'daysLate' => $daysDiff,
                    'tanggal_kembali' => $pinjam['tanggal_kembali'],
                    'emailSent' => $emailSent
                ];

                $overdueCount++;

                log_message('info', "Keterlambatan pengembalian: User {$pinjam['fullname']} terlambat {$daysDiff} hari untuk kendaraan {$pinjam['merk']} ({$pinjam['no_polisi']})");
            }
        }

        return [
            'totalActive' => count($activePinjam),
            'overdueCount' => $overdueCount,
            'overdueDetails' => $overdueDetails
        ];
    }

    private function sendOverdueNotification($pinjam, $daysDiff)
    {
        try {
            $adminEmail = 'idampalada@if.uai.ac.id';
            $sapaan = getSapaan();

            $subject = "PENTING: Keterlambatan Pengembalian Kendaraan";
            $message = "
            <p>Selamat {$sapaan},</p>
            
            <p style='color: red; font-weight: bold;'>Terdapat keterlambatan pengembalian kendaraan selama {$daysDiff} hari.</p>
            
            <p><strong>Detail Peminjaman:</strong></p>
            <ul>
                <li>Kendaraan: {$pinjam['merk']}</li>
                <li>Nomor Polisi: {$pinjam['no_polisi']}</li>
                <li>Peminjam: {$pinjam['fullname']}</li>
                <li>Email Peminjam: {$pinjam['email']}</li>
                <li>Penanggung Jawab: {$pinjam['nama_penanggung_jawab']}</li>
                <li>NIP/NRP: {$pinjam['nip_nrp']}</li>
                <li>Unit Organisasi: {$pinjam['unit_organisasi']}</li>
                <li>Tanggal Pinjam: " . date('d/m/Y', strtotime($pinjam['tanggal_pinjam'])) . "</li>
                <li>Batas Tanggal Kembali: " . date('d/m/Y', strtotime($pinjam['tanggal_kembali'])) . "</li>
            </ul>

            <p><strong>Tindakan yang diperlukan:</strong></p>
            <ol>
                <li>Hubungi peminjam untuk konfirmasi status kendaraan</li>
                <li>Periksa kondisi dan keberadaan kendaraan</li>
                <li>Dokumentasikan keterlambatan ini</li>
                <li>Tindak lanjuti sesuai prosedur yang berlaku</li>
            </ol>

            <p>Silakan akses <a href='http://manajemenaset.pu.go.id/admin/dashboard'>dashboard admin</a> untuk informasi lebih detail.</p>
            <hr>
            <p><i>Email ini dikirim secara otomatis oleh sistem.</i></p>
        ";

            $adminEmailSent = sendEmail($adminEmail, $subject, $message);

            $userSubject = "Peringatan: Keterlambatan Pengembalian Kendaraan";
            $userMessage = "
            <p>Selamat {$sapaan}, {$pinjam['fullname']}.</p>
            
            <p style='color: red; font-weight: bold;'>Anda terlambat mengembalikan kendaraan selama {$daysDiff} hari.</p>
            
            <p><strong>Detail Peminjaman:</strong></p>
            <ul>
                <li>Kendaraan: {$pinjam['merk']}</li>
                <li>Nomor Polisi: {$pinjam['no_polisi']}</li>
                <li>Tanggal Pinjam: " . date('d/m/Y', strtotime($pinjam['tanggal_pinjam'])) . "</li>
                <li>Batas Tanggal Kembali: " . date('d/m/Y', strtotime($pinjam['tanggal_kembali'])) . "</li>
            </ul>

            <p><strong>Mohon segera:</strong></p>
            <ol>
                <li>Kembalikan kendaraan ke bagian aset</li>
                <li>Siapkan dokumen pengembalian yang diperlukan</li>
                <li>Hubungi admin aset untuk proses pengembalian</li>
            </ol>
            <hr>
            <p><i>Email ini dikirim secara otomatis oleh sistem.</i></p>
        ";

            $userEmailSent = sendEmail($pinjam['email'], $userSubject, $userMessage);

            log_message('info', "Notifikasi keterlambatan untuk {$pinjam['no_polisi']} - Admin: " .
                ($adminEmailSent ? 'Terkirim' : 'Gagal') . ", User: " .
                ($userEmailSent ? 'Terkirim' : 'Gagal'));

            return $adminEmailSent && $userEmailSent;

        } catch (\Exception $e) {
            log_message('error', "Error sending overdue notification: {$e->getMessage()}");
            log_message('error', "Stack trace: {$e->getTraceAsString()}");
            return false;
        }
    }
    public function cleanOldLogs()
{
    $db = \Config\Database::connect();
    $builder = $db->table('log_akses');

    // Ambil data yang lebih dari 1 hari
    $oldLogs = $builder->where('created_at <', date('Y-m-d H:i:s', strtotime('-1 days')))
                       ->get()
                       ->getResultArray();

    if (empty($oldLogs)) {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Tidak ada data log lama yang perlu dibersihkan.'
        ]);
    }

    // Simpan ke file backup
    $backupPath = WRITEPATH . 'backups/log_akses_backup_' . date('Ymd_His') . '.json';
    file_put_contents($backupPath, json_encode($oldLogs, JSON_PRETTY_PRINT));

    // Hapus dari database
    $ids = array_column($oldLogs, 'id');
    $builder->whereIn('id', $ids)->delete();

    return $this->response->setJSON([
        'success' => true,
        'message' => count($oldLogs) . ' data log lama telah dihapus dan dibackup.',
        'backup_file' => $backupPath
    ]);
}

}