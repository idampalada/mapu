<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPinjamColumns extends Migration
{
    public function up()
    {
        // Tambahkan kolom yang diperlukan untuk form peminjaman
        $fields = [
            'alamat_rumah' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Alamat rumah peminjam',
                'after'      => 'unit_organisasi'
            ],
            'no_ktp' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'comment'    => 'Nomor KTP peminjam',
                'after'      => 'alamat_rumah'
            ]
        ];

        $this->forge->addColumn('pinjam', $fields);
    }

    public function down()
    {
        // Hapus kolom yang ditambahkan
        $this->forge->dropColumn('pinjam', 'alamat_rumah');
        $this->forge->dropColumn('pinjam', 'no_ktp');
    }
}