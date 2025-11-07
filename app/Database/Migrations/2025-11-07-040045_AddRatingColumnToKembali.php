<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRatingColumnToKembali extends Migration
{
    public function up()
    {
        $this->forge->addColumn('kembali', [
            'rating_pengguna' => [
                'type'       => 'INT',
                'constraint' => 1,
                'null'       => true,
                'comment'    => 'Rating penggunaan kendaraan (1-5)',
                'after'      => 'kondisi_kembali' // Tambahkan setelah kolom kondisi_kembali
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('kembali', 'rating_pengguna');
    }
}
