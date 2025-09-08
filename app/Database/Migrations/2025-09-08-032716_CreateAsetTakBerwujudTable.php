<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAsetTakBerwujudTable extends Migration
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
            'kode_kl' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'comment'    => 'Kode Kementerian/Lembaga',
            ],
            'nama_kl' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama Kementerian/Lembaga',
            ],
            'kode_kpknl' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'comment'    => 'Kode KPKNL',
            ],
            'nama_kpknl' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama KPKNL',
            ],
            'kode_satker' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Kode Satuan Kerja',
            ],
            'nama_satker' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama Satuan Kerja',
            ],
            'kode_sub_satker' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Kode Sub Satuan Kerja',
            ],
            'nama_sub_satker' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama Sub Satuan Kerja',
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
            'nilai_perolehan_pertama' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'default'    => 0,
                'comment'    => 'Nilai perolehan pertama aset',
            ],
            'nilai_mutasi' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'default'    => 0,
                'comment'    => 'Nilai mutasi aset',
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
            'no_kib' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nomor KIB (Kartu Inventaris Barang)',
            ],
            'tanggal_perolehan' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Tanggal perolehan aset',
            ],
            'tgl_buku' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Tanggal buku aset',
            ],
            'tgl_rekam' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Tanggal rekam aset',
            ],
            'tgl_rekam_pertama' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Tanggal rekam pertama aset',
            ],
            'kondisi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Kondisi aset: BAIK, RUSAK RINGAN, RUSAK BERAT',
            ],
            'merk' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
            'no_dana' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nomor dana',
            ],
            'tgl_dana' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Tanggal dana',
            ],
            'dari' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Asal perolehan',
            ],
            'asl_perlh' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Asal peroleh',
            ],
            'tgl_perlh' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Tanggal peroleh',
            ],
            'no_psp' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nomor PSP',
            ],
            'tgl_psp' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Tanggal PSP',
            ],
            'jumlah_foto' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
                'comment'    => 'Jumlah foto aset',
            ],
            'status_pengelolaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Status pengelolaan aset',
            ],
            'status_penggunaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Status penggunaan aset',
            ],
            'jenis_pengguna' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Jenis pengguna aset',
            ],
            'kd_satker_pengguna' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Kode satker pengguna',
            ],
            'ur_satker_pengguna' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Uraian satker pengguna',
            ],
            'alamat_satker_pengguna' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Alamat satker pengguna',
            ],
            'kelompok' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'Kelompok aset: ASET TAK BERWUJUD, ASET TAK BERWUJUD DALAM PENYELESAIAN, ASET KEMITRAAN',
            ],
            'sub_kelompok' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Sub kelompok aset (opsional)',
            ],
            'dihentikan_yn' => [
                'type'       => 'VARCHAR',
                'constraint' => 1,
                'null'       => true,
                'default'    => 'N',
                'comment'    => 'Flag dihentikan (Y/N)',
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
        $this->forge->addKey('nup');                   // Index untuk pencarian NUP
        $this->forge->addKey('no_kib');                // Index untuk pencarian No KIB
        $this->forge->addKey('created_at');            // Index untuk sorting berdasarkan waktu
        
        // Unique constraint untuk kombinasi kode_barang + kelompok (mencegah duplikasi)
        $this->forge->addUniqueKey(['kode_barang', 'kelompok'], 'unique_kode_kelompok_aset_tak_berwujud');
        
        $this->forge->createTable('aset_tak_berwujud');
    }

    public function down()
    {
        $this->forge->dropTable('aset_tak_berwujud');
    }
}