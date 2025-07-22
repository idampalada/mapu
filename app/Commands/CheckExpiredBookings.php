<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckExpiredBookings extends BaseCommand
{
    protected $group = 'Ruangan';
    protected $name = 'ruangan:check-expired';
    protected $description = 'Check for expired room bookings';

    public function run(array $params)
    {
        try {
            CLI::write('================================================', 'white');
            CLI::write('PENGECEKAN PEMINJAMAN RUANGAN YANG TELAH BERAKHIR', 'yellow');
            CLI::write('================================================', 'white');
            CLI::write('Waktu Mulai: ' . date('Y-m-d H:i:s'), 'cyan');
            CLI::write('------------------------------------------------', 'white');

            $ruanganController = new \App\Controllers\User\Ruangan();
            $result = $ruanganController->checkExpiredBookings();

            if (empty($result['expiredCount'])) {
                CLI::write('✓ Tidak ada peminjaman ruangan yang telah berakhir', 'green');
                CLI::write('Total peminjaman aktif: ' . $result['totalActive'] . ' ruangan', 'white');
            } else {
                CLI::write('! Ditemukan ' . $result['expiredCount'] . ' peminjaman yang telah berakhir:', 'yellow');

                foreach ($result['expiredDetails'] as $detail) {
                    CLI::write('------------------------------------------------', 'white');
                    CLI::write('Ruangan: ' . $detail['nama_ruangan'], 'yellow');
                    CLI::write('Peminjam: ' . $detail['nama_penanggung_jawab'], 'yellow');
                    CLI::write('Waktu Selesai: ' . $detail['tanggal'] . ' ' . $detail['waktu_selesai'], 'cyan');
                    CLI::write('Status Update: ' . ($detail['updated'] ? 'Berhasil' : 'Gagal'), 'cyan');
                }
            }

            CLI::write('------------------------------------------------', 'white');
            CLI::write('Status: Pengecekan Selesai!', 'green');
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
}