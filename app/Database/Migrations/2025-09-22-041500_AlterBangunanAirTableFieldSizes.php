<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterBangunanAirTableFieldSizes extends Migration
{
    public function up()
    {
        // Perbesar ukuran field yang sering bermasalah
        $this->forge->modifyColumn('bangunan_air', [
            'merk' => [
                'type'       => 'VARCHAR',
                'constraint' => 255, // Dari 100 jadi 255
                'null'       => true,
            ],
            'kelompok' => [
                'type'       => 'VARCHAR', 
                'constraint' => 150, // Dari 100 jadi 150
                'null'       => false,
            ],
            'sub_kelompok' => [
                'type'       => 'VARCHAR',
                'constraint' => 150, // Dari 100 jadi 150  
                'null'       => true,
            ],
            'kode_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 150, // Dari 100 jadi 150
                'null'       => false,
            ]
        ]);
    }

    public function down()
    {
        // Rollback ke ukuran semula
        $this->forge->modifyColumn('bangunan_air', [
            'merk' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'kelompok' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'sub_kelompok' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'kode_barang' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false]
        ]);
    }
}