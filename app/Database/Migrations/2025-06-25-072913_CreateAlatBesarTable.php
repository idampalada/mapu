<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAlatBesarTable extends Migration
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
                'constraint' => 100,
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
                'comment'    => 'Merk/brand aset',
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
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Status penggunaan aset',
            ],
            'tahun_buat' => [
                'type'       => 'VARCHAR',
                'constraint' => 4,
                'null'       => true,
                'comment'    => 'Tahun pembuatan aset (format: YYYY)',
            ],
            'no_mesin' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nomor mesin aset',
            ],
            'no_rangka' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nomor rangka aset',
            ],
            'kelompok' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'default'    => 'ALAT BESAR DARAT',
                'comment'    => 'Kelompok aset: ALAT BESAR DARAT, ALAT BANTU, ALAT BESAR APUNG',
            ],
            'sub_kelompok' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Sub kelompok aset (opsional)',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Waktu pembuatan record',
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Waktu update terakhir record',
            ],
        ]);

        // Primary Key
        $this->forge->addKey('id', true);
        
        // Indexes untuk performa query
        $this->forge->addKey('kode_barang');           // Index untuk pencarian kode barang
        $this->forge->addKey('kelompok');              // Index untuk filter kelompok
        $this->forge->addKey(['kelompok', 'kondisi']); // Composite index untuk filter kelompok + kondisi
        $this->forge->addKey('nama_barang');           // Index untuk pencarian nama barang
        $this->forge->addKey('merk');                  // Index untuk pencarian merk
        $this->forge->addKey('created_at');            // Index untuk sorting berdasarkan waktu
        
        // Unique constraint untuk kombinasi kode_barang + kelompok (mencegah duplikasi)
        $this->forge->addUniqueKey(['kode_barang', 'kelompok'], 'unique_kode_kelompok');
        
        $this->forge->createTable('alat_besar');
        
        // REMOVED: CHECK CONSTRAINTS untuk menghindari error case sensitivity
        // Validasi data akan dilakukan di level aplikasi (Model/Controller)
    }

    public function down()
    {
        $this->forge->dropTable('alat_besar');
    }
}