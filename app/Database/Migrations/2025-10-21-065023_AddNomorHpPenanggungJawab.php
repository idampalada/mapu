<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNomorHpPenanggungJawab extends Migration
{
    public function up()
    {
        // Tambahkan kolom nomor_hp_penanggung_jawab
        $fields = [
            'nomor_hp_penanggung_jawab' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'comment'    => 'Nomor HP Penanggung Jawab',
                'after'      => 'nama_penanggung_jawab'
            ]
        ];

        $this->forge->addColumn('pinjam_ruangan', $fields);
    }

    public function down()
    {
        // Hapus kolom jika rollback
        $this->forge->dropColumn('pinjam_ruangan', 'nomor_hp_penanggung_jawab');
    }
}