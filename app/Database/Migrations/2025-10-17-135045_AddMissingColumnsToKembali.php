<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingColumnsToKembali extends Migration
{
    public function up()
    {
        // Menambahkan kolom-kolom yang diperlukan
        try {
            // Tambah kolom nomor_sip
            $this->forge->addColumn('kembali', [
                'nomor_sip' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'berita_acara_pengembalian'
                ]
            ]);
        } catch (\Exception $e) {
            // Kolom mungkin sudah ada
            log_message('info', 'Column nomor_sip might already exist: ' . $e->getMessage());
        }
        
        try {
            // Tambah kolom kondisi_kembali
            $this->forge->addColumn('kembali', [
                'kondisi_kembali' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'nomor_sip'
                ]
            ]);
        } catch (\Exception $e) {
            log_message('info', 'Column kondisi_kembali might already exist: ' . $e->getMessage());
        }
        
        try {
            // Tambah kolom foto_pengembalian
            $this->forge->addColumn('kembali', [
                'foto_pengembalian' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'kondisi_kembali'
                ]
            ]);
        } catch (\Exception $e) {
            log_message('info', 'Column foto_pengembalian might already exist: ' . $e->getMessage());
        }

        try {
            // Tambah kolom alamat_rumah
            $this->forge->addColumn('kembali', [
                'alamat_rumah' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true
                ]
            ]);
        } catch (\Exception $e) {
            log_message('info', 'Column alamat_rumah might already exist: ' . $e->getMessage());
        }
        
        try {
            // Tambah kolom no_ktp
            $this->forge->addColumn('kembali', [
                'no_ktp' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true
                ]
            ]);
        } catch (\Exception $e) {
            log_message('info', 'Column no_ktp might already exist: ' . $e->getMessage());
        }
        
        // Ubah kolom surat_pengembalian menjadi nullable
        $this->db->query('ALTER TABLE kembali ALTER COLUMN surat_pengembalian DROP NOT NULL');
        
        // Ubah kolom berita_acara_pengembalian menjadi nullable
        $this->db->query('ALTER TABLE kembali ALTER COLUMN berita_acara_pengembalian DROP NOT NULL');
    }

    public function down()
    {
        // Hapus kolom-kolom yang ditambahkan
        $this->forge->dropColumn('kembali', 'nomor_sip');
        $this->forge->dropColumn('kembali', 'kondisi_kembali');
        $this->forge->dropColumn('kembali', 'foto_pengembalian');
        $this->forge->dropColumn('kembali', 'alamat_rumah');
        $this->forge->dropColumn('kembali', 'no_ktp');
        
        // Kembalikan kolom surat_pengembalian ke NOT NULL
        $this->db->query('ALTER TABLE kembali ALTER COLUMN surat_pengembalian SET NOT NULL');
        
        // Kembalikan kolom berita_acara_pengembalian ke NOT NULL
        $this->db->query('ALTER TABLE kembali ALTER COLUMN berita_acara_pengembalian SET NOT NULL');
    }
}