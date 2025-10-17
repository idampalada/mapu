<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdditionalFieldsToKomputer extends Migration
{
    public function up()
    {
        // Tambahkan kolom baru ke tabel komputer
        $fields = [
            'pengguna_sebelumnya' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Pengguna sebelumnya dari perangkat'
            ],
            'pengguna_sekarang' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true, 
                'comment'    => 'Pengguna saat ini dari perangkat'
            ],
            'status_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Status barang: Tersedia, Dipinjam, Dalam Perbaikan, dll'
            ],
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Keterangan tambahan atau catatan'
            ],
            'bidang' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'comment'    => 'Bidang/departemen tempat perangkat digunakan'
            ]
        ];

        $this->forge->addColumn('komputer', $fields);

        // Tambahkan indeks untuk meningkatkan performa pencarian
        $this->db->query("CREATE INDEX idx_komputer_pengguna_sekarang ON komputer(pengguna_sekarang)");
        $this->db->query("CREATE INDEX idx_komputer_status_barang ON komputer(status_barang)");
        $this->db->query("CREATE INDEX idx_komputer_bidang ON komputer(bidang)");
    }

    public function down()
    {
        // Hapus indeks terlebih dahulu
        $this->db->query("DROP INDEX IF EXISTS idx_komputer_pengguna_sekarang");
        $this->db->query("DROP INDEX IF EXISTS idx_komputer_status_barang");
        $this->db->query("DROP INDEX IF EXISTS idx_komputer_bidang");
        
        // Hapus kolom yang ditambahkan
        $this->forge->dropColumn('komputer', 'pengguna_sebelumnya');
        $this->forge->dropColumn('komputer', 'pengguna_sekarang');
        $this->forge->dropColumn('komputer', 'status_barang');
        $this->forge->dropColumn('komputer', 'keterangan');
        $this->forge->dropColumn('komputer', 'bidang');
    }
}