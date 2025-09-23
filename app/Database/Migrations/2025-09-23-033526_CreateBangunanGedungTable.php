<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBangunanGedungTable extends Migration
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
                'constraint' => 50,
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
                'constraint' => 50,
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
            'kode_pos' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'comment'    => 'Kode pos lokasi',
            ],
            'jalan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Alamat jalan lokasi gedung',
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
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Tanggal perolehan aset',
            ],
            'tgl_buku' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Tanggal buku',
            ],
            'tgl_rekam' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Tanggal rekam',
            ],
            'tgl_rekam_pertama' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Tanggal rekam pertama',
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
                'comment'    => 'Merk/kontraktor aset',
            ],
            'kuantitas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 1,
                'comment'    => 'Jumlah unit aset',
            ],
            'sbsk' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'SBSK (Surat Bukti Status Kepemilikan)',
            ],
            'optimalisasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Status optimalisasi',
            ],
            'kd_kabkota' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'comment'    => 'Kode kabupaten/kota',
            ],
            'nm_kabkota' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama kabupaten/kota',
            ],
            'kode_provinsi' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'comment'    => 'Kode provinsi',
            ],
            'uraian_provinsi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama provinsi',
            ],
            'latitude' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Koordinat latitude',
            ],
            'longitude' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Koordinat longitude',
            ],
            // Field khusus untuk Bangunan Gedung
            'luas_bangunan' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'comment'    => 'Luas bangunan (m2)',
            ],
            'luas_dasar_bangunan' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'comment'    => 'Luas dasar bangunan (m2)',
            ],
            'jumlah_lantai' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 1,
                'comment'    => 'Jumlah lantai bangunan',
            ],
            // Field pendanaan
            'no_dana' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nomor dana',
            ],
            'tgl_dana' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Tanggal dana',
            ],
            'dari' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Sumber dana',
            ],
            'asl_perlh' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Asal perolehan',
            ],
            'tgl_perlh' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Tanggal perolehan',
            ],
            'no_sk_psp' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nomor SK PSP',
            ],
            'tgl_sk_psp' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Tanggal SK PSP',
            ],
            'jumlah_foto' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
                'comment'    => 'Jumlah foto aset',
            ],
            'jumlah_kib' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
                'comment'    => 'Jumlah KIB',
            ],
            'status_pengelolaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Status pengelolaan aset',
            ],
            // Field dokumen kepemilikan
            'jenis_sertifikat' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Jenis sertifikat kepemilikan',
            ],
            'no_dok_kepemilikan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nomor dokumen kepemilikan',
            ],
            'dok_kepemilikan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Dokumen kepemilikan',
            ],
            'jns_dok_kepemilikan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Jenis dokumen kepemilikan',
            ],
            'dokumen' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Dokumen terkait',
            ],
            'status_penggunaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'comment'    => 'Status penggunaan aset',
            ],
            // Field pengguna
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
            // Field status
            'status_sbsn' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Status SBSN',
            ],
            'status_idle' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Status idle aset',
            ],
            // Field tanah terkait
            'kd_brg_tanah' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Kode barang tanah',
            ],
            'ur_brg_tanah' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Uraian barang tanah',
            ],
            'nup_tanah' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'NUP tanah',
            ],
            'kd_satker_tanah' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Kode satker tanah',
            ],
            'nm_satker_tanah' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama satker tanah',
            ],
            'tot_pegawai' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
                'comment'    => 'Total pegawai',
            ],
            'dihentikan_yn' => [
                'type'       => 'VARCHAR',
                'constraint' => 1,
                'null'       => true,
                'comment'    => 'Status dihentikan (Y/N)',
            ],
            // Field klasifikasi
            'kelompok' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => false,
                'comment'    => 'Kelompok aset dari API',
            ],
            'sub_kelompok' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'comment'    => 'Sub kelompok aset dari API',
            ],
            'kategori_utama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'default'    => 'GEDUNG DAN BANGUNAN',
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
        $this->forge->addKey('nama_satker');
        $this->forge->addKey('nup');
        $this->forge->createTable('bangunan_gedung');
    }

    public function down()
    {
        $this->forge->dropTable('bangunan_gedung');
    }
}
            