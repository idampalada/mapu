<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyAssetsColumns extends Migration
{
    public function up()
    {
        // Hapus kolom yang tidak dibutuhkan
        $this->forge->dropColumn('assets', 'no_stnk');
        $this->forge->dropColumn('assets', 'no_sk_psp');
        $this->forge->dropColumn('assets', 'no_bpkb');

        // Tambahkan kolom baru
        $fields = [
            'nup' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nomor Unit Pengguna'
            ],
            'warna' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Warna kendaraan'
            ],
            'nomor_mesin' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nomor mesin kendaraan'
            ]
        ];

        $this->forge->addColumn('assets', $fields);

        // Tambahkan indeks untuk kolom baru
        $this->db->query("CREATE INDEX idx_assets_nup ON assets(nup)");
        $this->db->query("CREATE INDEX idx_assets_nomor_mesin ON assets(nomor_mesin)");
    }

    public function down()
    {
        // Hapus indeks terlebih dahulu
        $this->db->query("DROP INDEX IF EXISTS idx_assets_nup");
        $this->db->query("DROP INDEX IF EXISTS idx_assets_nomor_mesin");
        
        // Hapus kolom yang ditambahkan
        $this->forge->dropColumn('assets', 'nup');
        $this->forge->dropColumn('assets', 'warna');
        $this->forge->dropColumn('assets', 'nomor_mesin');

        // Kembalikan kolom yang dihapus
        $fields = [
            'no_stnk' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'no_sk_psp' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'no_bpkb' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ]
        ];

        $this->forge->addColumn('assets', $fields);
    }
}