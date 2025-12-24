<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUnitKerjaToPinjamRuangan extends Migration
{
    public function up()
    {
        // Menambahkan kolom unit_kerja ke tabel pinjam_ruangan
        $this->forge->addColumn('pinjam_ruangan', [
            'unit_kerja' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Unit kerja dari unit organisasi yang dipilih'
            ]
        ]);

        // Menambahkan index untuk performance
        $this->db->query('CREATE INDEX idx_pinjam_ruangan_unit_kerja ON pinjam_ruangan(unit_kerja)');
        
        // Menambahkan comment untuk dokumentasi
        $this->db->query("COMMENT ON COLUMN pinjam_ruangan.unit_kerja IS 'Unit kerja spesifik dari unit organisasi (contoh: Biro Perencanaan, Sekretariat Ditjen SDA, dll)'");
    }

    public function down()
    {
        // Drop index terlebih dahulu
        $this->db->query('DROP INDEX IF EXISTS idx_pinjam_ruangan_unit_kerja');
        
        // Drop kolom unit_kerja
        $this->forge->dropColumn('pinjam_ruangan', 'unit_kerja');
    }
}