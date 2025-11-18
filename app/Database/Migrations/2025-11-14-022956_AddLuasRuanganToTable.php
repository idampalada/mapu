<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLuasRuanganToTable extends Migration
{
    public function up()
    {
        // Tambahkan kolom luas_ruangan ke tabel ruangan
        $fields = [
            'luas_ruangan' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'comment'    => 'Luas ruangan dalam meter persegi (m²)',
                'after'      => 'kapasitas'
            ]
        ];

        $this->forge->addColumn('ruangan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('ruangan', 'luas_ruangan');
    }
}