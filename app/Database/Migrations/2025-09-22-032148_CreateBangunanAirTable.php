<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBangunanAirTable extends Migration
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
                'comment'    => 'Tanggal tarik data dari API',
            ],
            'nama_kl' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama Kementerian/Lembaga',
            ],
            'nama_kpknl' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama KPKNL',
            ],
            'nama_satker' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama Satuan Kerja',
            ],
            'kode_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
                'comment'    => 'Kode barang (wajib diisi)',
            ],
            'nama_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'comment'    => 'Nama barang (wajib diisi)',
            ],
            'nilai_perolehan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'default'    => 0,
                'comment'    => 'Nilai perolehan aset',
            ],
            'nilai_penyusutan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'default'    => 0,
                'comment'    => 'Nilai penyusutan aset',
            ],
            'nilai_buku' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'default'    => 0,
                'comment'    => 'Nilai buku aset',
            ],
            'nup' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nomor Unit Pengguna',
            ],
            'tanggal_perolehan' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Tanggal perolehan aset',
            ],
            'kondisi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Kondisi aset: BAIK, RUSAK RINGAN, RUSAK BERAT',
            ],
            'merk' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Merk/kontraktor aset',
            ],
            'kuantitas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 1,
                'comment'    => 'Jumlah unit aset',
            ],
            'status_penggunaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'comment'    => 'Status penggunaan aset',
            ],
            // Field khusus untuk Bangunan Air
            'luas_dasar' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'comment'    => 'Luas dasar bangunan (m2)',
            ],
            'luas_bangunan' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'comment'    => 'Luas bangunan (m2)',
            ],
'kelompok' => [
    'type'       => 'VARCHAR',
    'constraint' => 200, // Dari 100 jadi 200
    'null'       => false,
    'comment'    => 'Kelompok aset dari API',
],
'sub_kelompok' => [
    'type'       => 'VARCHAR',
    'constraint' => 150, // Dari 100 jadi 150
    'null'       => true,
    'comment'    => 'Sub kelompok aset dari API',
],
            'kategori_utama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'default'    => 'BANGUNAN AIR',
                'comment'    => 'Kategori utama untuk klasifikasi internal',
            ],
            'kategori_detail' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Kategori detail berdasarkan mapping kelompok API',
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
        $this->forge->addKey('kategori_utama');
        $this->forge->addKey('kategori_detail');
        $this->forge->createTable('bangunan_air');
    }

    public function down()
    {
        $this->forge->dropTable('bangunan_air');
    }
}