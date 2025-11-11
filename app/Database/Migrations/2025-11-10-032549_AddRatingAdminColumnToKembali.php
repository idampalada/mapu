<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRatingAdminColumnToKembali extends Migration
{
    public function up()
    {
        $this->forge->addColumn('kembali', [
            'rating_admin' => [
                'type'       => 'INT',
                'constraint' => 1,
                'null'       => true,
                'comment'    => 'Rating dari admin saat verifikasi pengembalian (1-5)',
                'after'      => 'rating_pengguna'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('kembali', 'rating_admin');
    }
}