<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTteColumnsToPinjamTable extends Migration
{
    public function up()
    {
        // Menambahkan kolom-kolom TTE ke tabel pinjam
        $fields = [
            'is_tte_signed' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'comment'    => 'Status apakah dokumen sudah ditandatangani elektronik (0=belum, 1=sudah)'
            ],
            'tte_signed_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'comment' => 'Waktu dokumen ditandatangani elektronik'
            ],
            'tte_signer_nik' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
                'comment'    => 'NIK penandatangan elektronik'
            ],
            'tte_signature_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'ID signature dari BSrE (untuk tracking)'
            ],
            'tte_verification_status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'comment'    => 'Status verifikasi TTE (VALID, INVALID, UNKNOWN)'
            ],
            'tte_error_message' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Pesan error jika TTE gagal'
            ]
        ];

        $this->forge->addColumn('pinjam', $fields);
    }

    public function down()
    {
        // Menghapus kolom-kolom TTE
        $this->forge->dropColumn('pinjam', [
            'is_tte_signed',
            'tte_signed_at',
            'tte_signer_nik',
            'tte_signature_id',
            'tte_verification_status',
            'tte_error_message'
        ]);
    }
}