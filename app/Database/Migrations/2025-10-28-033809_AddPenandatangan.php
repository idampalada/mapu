<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPenandatanganColumnsToPinjam extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pinjam', [
            'nama_penanggung_jawab_kendaraan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'urusan_kedinasan'
            ],
            'nip_penanggung_jawab_kendaraan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'nama_penanggung_jawab_kendaraan'
            ],
            'nama_kepala_satuan_kerja' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'nip_penanggung_jawab_kendaraan'
            ],
            'nip_kepala_satuan_kerja' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'nama_kepala_satuan_kerja'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pinjam', 'nama_penanggung_jawab_kendaraan');
        $this->forge->dropColumn('pinjam', 'nip_penanggung_jawab_kendaraan');
        $this->forge->dropColumn('pinjam', 'nama_kepala_satuan_kerja');
        $this->forge->dropColumn('pinjam', 'nip_kepala_satuan_kerja');
    }
}