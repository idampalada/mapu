<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAlatBesarDaratTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tgl_tarik' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'nama_kl' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'nama_kpknl' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'nama_satker' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'kode_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'nama_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'nilai_perolehan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'default'    => 0,
            ],
            'nilai_penyusutan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'default'    => 0,
            ],
            'nilai_buku' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'default'    => 0,
            ],
            'nup' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tanggal_perolehan' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'kondisi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'merk' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'kuantitas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
            ],
            'status_penggunaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tahun_buat' => [
                'type'       => 'VARCHAR',
                'constraint' => 4,
                'null'       => true,
            ],
            'no_mesin' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'no_rangka' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'kelompok' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'default'    => 'ALAT BESAR DARAT',
            ],
            'sub_kelompok' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('kode_barang');
        $this->forge->addKey('kelompok');
        
        $this->forge->createTable('alat_besar_darat');
    }

    public function down()
    {
        $this->forge->dropTable('alat_besar_darat');
    }
}