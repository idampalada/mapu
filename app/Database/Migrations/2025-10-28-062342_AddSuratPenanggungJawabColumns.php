<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSuratPenanggungJawabColumns extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pinjam', [
            // Untuk menyimpan file surat penanggung jawab
            'surat_penanggung_jawab' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'surat_permohonan'
            ],
            
            // Data penomoran surat
            'nomor_surat' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'surat_penanggung_jawab'
            ],
            
            'tanggal_surat' => [
                'type'       => 'DATE',
                'null'       => true,
                'after'      => 'nomor_surat'
            ],
            
            'tempat_surat' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'tanggal_surat'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pinjam', 'surat_penanggung_jawab');
        $this->forge->dropColumn('pinjam', 'nomor_surat');
        $this->forge->dropColumn('pinjam', 'tanggal_surat');
        $this->forge->dropColumn('pinjam', 'tempat_surat');
    }
}