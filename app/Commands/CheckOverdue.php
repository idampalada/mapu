<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Controllers\CronJob;

class CheckOverdue extends BaseCommand
{
    protected $group = 'Cron';
    protected $name = 'cron:check-reminder';
    protected $description = 'Mengirim pengingat pengembalian kendaraan';

    public function run(array $params)
    {
        $cronJob = new CronJob();
        $db = \Config\Database::connect();

        try {
            CLI::write('================================================', 'white');
            CLI::write('PENGIRIMAN REMINDER PENGEMBALIAN KENDARAAN', 'yellow');
            CLI::write('================================================', 'white');
            CLI::write('Waktu Mulai: ' . date('Y-m-d H:i:s'), 'cyan');
            CLI::write('------------------------------------------------', 'white');

            $tomorrow = date('Y-m-d', strtotime('+1 day'));

            $builder = $db->table('pinjam');
            $builder->select('pinjam.*, users.email as user_email, users.fullname, assets.merk, assets.no_polisi');
            $builder->join('users', 'users.id = pinjam.user_id');
            $builder->join('assets', 'assets.id = pinjam.kendaraan_id');
            $builder->where('pinjam.tanggal_kembali', $tomorrow);
            $builder->where('pinjam.status', 'disetujui');
            $builder->where('pinjam.is_returned', false);
            $builder->where('pinjam.deleted_at IS NULL');
            
            $dueTomorrow = $builder->get()->getResultArray();

            if (empty($dueTomorrow)) {
                CLI::write('✓ Tidak ada kendaraan yang harus dikembalikan besok', 'green');
            } else {
                CLI::write('! Ditemukan ' . count($dueTomorrow) . ' kendaraan yang harus dikembalikan besok:', 'yellow');

                foreach ($dueTomorrow as $pinjam) {
                    CLI::write('------------------------------------------------', 'white');
                    CLI::write('Kendaraan: ' . $pinjam['merk'] . ' (' . $pinjam['no_polisi'] . ')', 'yellow');
                    CLI::write('Peminjam: ' . $pinjam['fullname'], 'yellow');
                    CLI::write('Tanggal Kembali: ' . date('d/m/Y', strtotime($pinjam['tanggal_kembali'])), 'yellow');
                    
                    $emailSent = $this->sendReminderEmail($pinjam);
                    CLI::write('Status Notifikasi: ' . ($emailSent ? 'Terkirim' : 'Gagal'), 'cyan');
                }
            }

            CLI::write('------------------------------------------------', 'white');
            CLI::write('Status: Pengiriman Reminder Selesai!', 'green');
            CLI::write('Waktu Selesai: ' . date('Y-m-d H:i:s'), 'cyan');
            CLI::write('================================================', 'white');

            return EXIT_SUCCESS;

        } catch (\Exception $e) {
            CLI::write('================================================', 'white');
            CLI::write('TERJADI KESALAHAN!', 'red');
            CLI::write('------------------------------------------------', 'white');
            CLI::error($e->getMessage());
            CLI::write('------------------------------------------------', 'white');
            CLI::write('Waktu Error: ' . date('Y-m-d H:i:s'), 'cyan');
            CLI::write('File: ' . $e->getFile(), 'yellow');
            CLI::write('Line: ' . $e->getLine(), 'yellow');
            CLI::write('================================================', 'white');

            return EXIT_ERROR;
        }
    }

    private function sendReminderEmail($data)
    {
        $email = \Config\Services::email();
        $sapaan = $this->getSapaan();

        $subject = "Pengingat Pengembalian Kendaraan";
        
        $content = "
            <p>Selamat {$sapaan}, {$data['fullname']}.</p>
            
            <p>Ini adalah pengingat untuk pengembalian kendaraan <strong>{$data['merk']}</strong> dengan nomor polisi <strong>{$data['no_polisi']}</strong>.</p>
            
            <div style='background-color: #fff3cd; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                <strong>Informasi Pengembalian:</strong>
                <ul style='margin: 10px 0; padding-left: 20px;'>
                    <li>Tanggal Pengembalian: " . date('d F Y', strtotime($data['tanggal_kembali'])) . "</li>
                </ul>
            </div>

            <div style='background-color: #e8f4fe; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                <strong>Hal yang perlu dipersiapkan:</strong>
                <ol style='margin: 10px 0; padding-left: 20px;'>
                    <li>Surat Pengembalian</li>
                    <li>Berita Acara Pengembalian</li>
                    <li>Kondisi kendaraan sesuai saat peminjaman</li>
                </ol>
            </div>

            <p>Jika ada pertanyaan lebih lanjut, mohon hubungi:<br>
            081578732756 - <strong>Domenico Adi Nugroho</strong></p>
        ";

        $message = getEmailTemplate($content);

        try {
            $email->setFrom('noreply@pu.go.id', 'Manajemen Aset PUPR');
            $email->setTo($data['user_email']);
            $email->setSubject($subject);
            $email->setMessage($message);
            
            return $email->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send reminder email: ' . $e->getMessage());
            return false;
        }
    }

    private function getSapaan()
    {
        $hour = (int) date('H');
        if ($hour >= 0 && $hour < 10) {
            return 'pagi';
        } elseif ($hour >= 10 && $hour < 15) {
            return 'siang'; 
        } elseif ($hour >= 15 && $hour < 18) {
            return 'sore';
        } else {
            return 'malam';
        }
    }
}