<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLateReturnFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('kembali', [
            'alasan_keterlambatan' => [
                'type'           => 'TEXT',
                'null'           => true,
                'after'          => 'no_ktp'
            ],
            'foto_keterlambatan' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => true,
                'after'          => 'alasan_keterlambatan'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('kembali', 'alasan_keterlambatan');
        $this->forge->dropColumn('kembali', 'foto_keterlambatan');
    }
}