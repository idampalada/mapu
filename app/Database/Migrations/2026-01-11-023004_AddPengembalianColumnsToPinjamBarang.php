<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPengembalianColumnsToPinjamBarang extends Migration
{
    public function up()
    {
        // Menambahkan kolom untuk form pengembalian barang
        $fields = [
            'kondisi_pengembalian' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Kondisi barang saat pengembalian (Baik, Rusak Ringan, Rusak Berat)'
            ],
            'foto_pengembalian' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'JSON array berisi nama file foto pengembalian'
            ]
        ];

        $this->forge->addColumn('pinjam_barang', $fields);
        
        // Tambahkan index untuk kondisi_pengembalian untuk query yang lebih cepat
        $this->forge->addKey(['kondisi_pengembalian'], false, false, 'idx_pinjam_barang_kondisi_pengembalian');
    }

    public function down()
    {
        // Drop index terlebih dahulu
        $this->forge->dropKey('pinjam_barang', 'idx_pinjam_barang_kondisi_pengembalian');
        
        // Kemudian drop kolom
        $this->forge->dropColumn('pinjam_barang', ['kondisi_pengembalian', 'foto_pengembalian']);
    }
}