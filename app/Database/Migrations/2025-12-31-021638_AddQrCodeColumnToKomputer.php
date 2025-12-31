<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQrCodeColumnToKomputer extends Migration
{
    public function up()
    {
        // Tambahkan kolom qr_code ke tabel komputer
        $this->forge->addColumn('komputer', [
            'qr_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'QR Code dari Excel, berisi text/kode unik untuk identifikasi'
            ]
        ]);

        // Tambahkan comment untuk kolom baru
        $this->db->query("COMMENT ON COLUMN komputer.qr_code IS 'QR Code text dari import Excel (misal: #E13457068SCC0185E0531661F20A295D)'");

        // Tambahkan index untuk performa pencarian QR Code
        $this->db->query("CREATE INDEX idx_komputer_qr_code ON komputer(qr_code) WHERE qr_code IS NOT NULL");

        log_message('info', 'Migration: Added qr_code column to komputer table');
    }

    public function down()
    {
        // Hapus index terlebih dahulu
        $this->db->query("DROP INDEX IF EXISTS idx_komputer_qr_code");
        
        // Hapus kolom qr_code
        $this->forge->dropColumn('komputer', 'qr_code');

        log_message('info', 'Migration: Removed qr_code column from komputer table');
    }
}