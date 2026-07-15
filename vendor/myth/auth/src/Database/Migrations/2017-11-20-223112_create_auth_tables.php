<?php

namespace Myth\Auth\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthTables extends Migration
{
    public function up()
    {
        // Users
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'fullname' => ['type' => 'varchar', 'constraint' => 255, 'default' => ''],
            'email' => ['type' => 'varchar', 'constraint' => 255],
            'username' => ['type' => 'varchar', 'constraint' => 30, 'null' => true],
            'unit_organisasi' => ['type' => 'varchar', 'constraint' => 255, 'default' => ''],
            'unit_kerja' => ['type' => 'varchar', 'constraint' => 255, 'default' => ''],
            'password_hash' => ['type' => 'varchar', 'constraint' => 255],
            'reset_hash' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'reset_at' => ['type' => 'datetime', 'null' => true],
            'reset_expires' => ['type' => 'datetime', 'null' => true],
            'activate_hash' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'status' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'status_message' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'active' => ['type' => 'tinyint', 'constraint' => 1, 'null' => 0, 'default' => 0],
            'force_pass_reset' => ['type' => 'tinyint', 'constraint' => 1, 'null' => 0, 'default' => 0],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
            'role' => ['type' => 'varchar', 'constraint' => 50, 'default' => 'user'],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('users', true);

        // ASET
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'kategori_id' => ['type' => 'varchar', 'constraint' => 255],
            'gambar_mobil' => ['type' => 'varchar', 'constraint' => 255],
            'no_sk_psp' => ['type' => 'varchar', 'constraint' => 50, 'unique' => true],
            'kode_barang' => ['type' => 'varchar', 'constraint' => 50, 'unique' => true],
            'merk' => ['type' => 'varchar', 'constraint' => 100],
            'tahun_pembuatan' => ['type' => 'varchar', 'constraint' => 4],
            'kapasitas' => ['type' => 'varchar', 'constraint' => 2],
            'no_polisi' => ['type' => 'varchar', 'constraint' => 20],
            'no_bpkb' => ['type' => 'varchar', 'constraint' => 50],
            'no_stnk' => ['type' => 'varchar', 'constraint' => 50],
            'no_rangka' => ['type' => 'varchar', 'constraint' => 50],
            'kondisi' => ['type' => 'varchar', 'constraint' => 20],
            'status_pinjam' => ['type' => 'varchar', 'constraint' => 100, 'default' => 'Tersedia'],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
        $this->forge->createTable('assets', true);

        // RUANGAN
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_ruangan' => ['type' => 'varchar', 'constraint' => 255],
            'lokasi' => ['type' => 'varchar', 'constraint' => 255],
            'kapasitas' => ['type' => 'int', 'constraint' => 11],
            'status' => ['type' => 'varchar', 'constraint' => 20, 'default' => 'Tersedia'],
            'keterangan' => ['type' => 'text', 'null' => true],
            'fasilitas' => ['type' => 'text', 'null' => true],
            'foto_ruangan' => ['type' => 'text', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('ruangan', true);

        // PINJAM RUANGAN
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'ruangan_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'nama_penanggung_jawab' => ['type' => 'varchar', 'constraint' => 255],
            'unit_organisasi' => ['type' => 'varchar', 'constraint' => 255],
            'keperluan' => ['type' => 'text'],
            'tanggal' => ['type' => 'date'],
            'waktu_mulai' => ['type' => 'time'],
            'waktu_selesai' => ['type' => 'time'],
            'jumlah_peserta' => ['type' => 'int', 'constraint' => 11],
            'surat_permohonan' => ['type' => 'varchar', 'constraint' => 255],
            'dokumen_tambahan' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'status' => ['type' => 'varchar', 'constraint' => 20, 'default' => 'pending'],
            'keterangan' => ['type' => 'text', 'null' => true],
            'verified_at' => ['type' => 'datetime', 'null' => true],
            'verified_by' => ['type' => 'int', 'unsigned' => true, 'null' => true],
            'keterangan_status' => ['type' => 'text', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('ruangan_id', 'ruangan', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('verified_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('pinjam_ruangan', true);

        // PINJEM
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'kode_barang' => ['type' => 'varchar', 'constraint' => 50],
            'kendaraan_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'nama_penanggung_jawab' => ['type' => 'varchar', 'constraint' => 255],
            'nip_nrp' => ['type' => 'varchar', 'constraint' => 255],
            'pangkat_golongan' => ['type' => 'varchar', 'constraint' => 255],
            'jabatan' => ['type' => 'varchar', 'constraint' => 255],
            'unit_organisasi' => ['type' => 'varchar', 'constraint' => 255],
            'surat_permohonan' => ['type' => 'varchar', 'constraint' => 255],
            'surat_jalan_admin' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'dokumen_tambahan' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'pengemudi' => ['type' => 'varchar', 'constraint' => 255],
            'no_hp' => ['type' => 'varchar', 'constraint' => 50],
            'tanggal_pinjam' => ['type' => 'date'],
            'tanggal_kembali' => ['type' => 'date'],
            'urusan_kedinasan' => ['type' => 'varchar', 'constraint' => 255],
            'status' => ['type' => 'varchar', 'constraint' => 20, 'default' => 'pending'],
            'is_returned' => ['type' => 'boolean', 'default' => false],
            'keterangan' => ['type' => 'text', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('kode_barang', 'assets', 'kode_barang', '', 'CASCADE');
        $this->forge->createTable('pinjam', true);

        // BALIKIN
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'kode_barang' => ['type' => 'varchar', 'constraint' => 50],
            'kendaraan_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'pinjam_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nama_penanggung_jawab' => ['type' => 'varchar', 'constraint' => 255],
            'nip_nrp' => ['type' => 'varchar', 'constraint' => 255],
            'pangkat_golongan' => ['type' => 'varchar', 'constraint' => 255],
            'jabatan' => ['type' => 'varchar', 'constraint' => 255],
            'unit_organisasi' => ['type' => 'varchar', 'constraint' => 255],
            'surat_pengembalian' => ['type' => 'varchar', 'constraint' => 255],
            'berita_acara_pengembalian' => ['type' => 'varchar', 'constraint' => 255],
            'dokumen_tambahan' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'no_hp' => ['type' => 'varchar', 'constraint' => 50],
            'tanggal_pinjam' => ['type' => 'date'],
            'tanggal_kembali' => ['type' => 'date'],
            'kondisi_kendaraan' => ['type' => 'text', 'null' => true],
            'status' => ['type' => 'varchar', 'constraint' => 20, 'default' => 'pending'],
            'keterangan' => ['type' => 'text', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('kode_barang', 'assets', 'kode_barang', '', 'CASCADE');
        $this->forge->addForeignKey('kendaraan_id', 'assets', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('pinjam_id', 'pinjam', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('kembali', true);

        // KEMBALI RUANGAN (NEW TABLE)
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'ruangan_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'pinjam_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'tanggal_pinjam' => ['type' => 'date', 'null' => true],
            'tanggal_kembali' => ['type' => 'date', 'null' => true],
            'surat_pengembalian' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'berita_acara_pengembalian' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'dokumen_tambahan' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'status' => ['type' => 'varchar', 'constraint' => 50, 'default' => 'pending'],
            'keterangan' => ['type' => 'text', 'null' => true],
            'verified_by' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true, 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
            'is_active' => ['type' => 'boolean', 'default' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('ruangan_id', 'ruangan', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('pinjam_id', 'pinjam_ruangan', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('verified_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('kembali_ruangan', true);

        // BARANG (NEW TABLE)
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_barang' => ['type' => 'varchar', 'constraint' => 100, 'null' => true],
            'kategori' => ['type' => 'varchar', 'constraint' => 50, 'null' => true],
            'kondisi' => ['type' => 'varchar', 'constraint' => 50, 'null' => true],
            'lokasi' => ['type' => 'varchar', 'constraint' => 100, 'null' => true],
            'status' => ['type' => 'varchar', 'constraint' => 50, 'null' => true],
            'kode_barang' => ['type' => 'varchar', 'constraint' => 50, 'null' => true],
            'gambar' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_barang');
        $this->forge->createTable('barang', true);

        // PINJAM BARANG (NEW TABLE)
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'barang_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'nama_peminjam' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'tanggal' => ['type' => 'date'],
            'waktu_mulai' => ['type' => 'time'],
            'waktu_selesai' => ['type' => 'time'],
            'keperluan' => ['type' => 'text', 'null' => true],
            'status' => ['type' => 'varchar', 'constraint' => 50, 'default' => 'Menunggu'],
            'created_at' => ['type' => 'datetime', 'null' => true, 'default' => 'CURRENT_TIMESTAMP'],
            'updated_at' => ['type' => 'datetime', 'null' => true, 'default' => 'CURRENT_TIMESTAMP'],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
            'keterangan' => ['type' => 'text', 'null' => true],
            'keterangan_status' => ['type' => 'text', 'null' => true],
            'verified_at' => ['type' => 'datetime', 'null' => true],
            'verified_by' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'tanggal_kembali' => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('barang_id', 'barang', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('verified_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('pinjam_barang', true);

        // TANAH (NEW TABLE)
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kode_barang' => ['type' => 'varchar', 'constraint' => 100],
            'nama_barang' => ['type' => 'varchar', 'constraint' => 255],
            'alamat' => ['type' => 'text', 'null' => true],
            'kelompok' => ['type' => 'varchar', 'constraint' => 100],
            'luas_tanah_seluruhnya' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'status_penggunaan' => ['type' => 'varchar', 'constraint' => 100, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tanah', true);

        // LOG AKSES (NEW TABLE)
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ip' => ['type' => 'varchar', 'constraint' => 45, 'null' => true],
            'lokasi' => ['type' => 'JSON', 'null' => true],
            'url' => ['type' => 'text', 'null' => true],
            'user_agent' => ['type' => 'text', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true, 'default' => 'CURRENT_TIMESTAMP'],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('log_akses', true);

        // PEMELIHARAAN RUTIN
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kendaraan_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'jenis_pemeliharaan' => ['type' => 'varchar', 'constraint' => 100],
            'tanggal_terjadwal' => ['type' => 'date'],
            'status' => ['type' => 'varchar', 'constraint' => 50, 'default' => 'Pending'],
            'bengkel' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'biaya' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'keterangan' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kendaraan_id', 'assets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pemeliharaan_rutin');

        // LAPORAN
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kendaraan_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'jenis_laporan' => ['type' => 'varchar', 'constraint' => 100],
            'tanggal_kejadian' => ['type' => 'date'],
            'lokasi_kejadian' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'keterangan' => ['type' => 'text', 'null' => true],
            'bukti_foto' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'status' => ['type' => 'varchar', 'constraint' => 20, 'default' => 'pending'],
            'tindak_lanjut' => ['type' => 'text', 'null' => true],
            'ditindaklanjuti_oleh' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'tanggal_tindak_lanjut' => ['type' => 'datetime', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kendaraan_id', 'assets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('ditindaklanjuti_oleh', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('laporan');

        // Auth Login Attempts
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ip_address' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'email' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'date' => ['type' => 'datetime'],
            'success' => ['type' => 'tinyint', 'constraint' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('email');
        $this->forge->addKey('user_id');
        $this->forge->createTable('auth_logins', true);

        // Auth Tokens
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'selector' => ['type' => 'varchar', 'constraint' => 255],
            'hashedValidator' => ['type' => 'varchar', 'constraint' => 255],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'expires' => ['type' => 'datetime'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('selector');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
        $this->forge->createTable('auth_tokens', true);

        // Password Reset Table
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email' => ['type' => 'varchar', 'constraint' => 255],
            'ip_address' => ['type' => 'varchar', 'constraint' => 255],
            'user_agent' => ['type' => 'varchar', 'constraint' => 255],
            'token' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => false],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('auth_reset_attempts', true);

        // Activation Attempts Table
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ip_address' => ['type' => 'varchar', 'constraint' => 255],
            'user_agent' => ['type' => 'varchar', 'constraint' => 255],
            'token' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => false],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('auth_activation_attempts', true);

        // Groups Table
        $fields = [
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'varchar', 'constraint' => 255],
            'description' => ['type' => 'varchar', 'constraint' => 255],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('auth_groups', true);

        // Permissions Table
        $fields = [
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'varchar', 'constraint' => 255],
            'description' => ['type' => 'varchar', 'constraint' => 255],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('auth_permissions', true);

        // Groups/Permissions Table
        $fields = [
            'group_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'permission_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey(['group_id', 'permission_id']);
        $this->forge->addForeignKey('group_id', 'auth_groups', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'auth_permissions', 'id', '', 'CASCADE');
        $this->forge->createTable('auth_groups_permissions', true);

        // Users/Groups Table
        $fields = [
            'group_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey(['group_id', 'user_id']);
        $this->forge->addForeignKey('group_id', 'auth_groups', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
        $this->forge->createTable('auth_groups_users', true);

        // Users/Permissions Table
        $fields = [
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'permission_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey(['user_id', 'permission_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'auth_permissions', 'id', '', 'CASCADE');
        $this->forge->createTable('auth_users_permissions', true);
    }

    //--------------------------------------------------------------------

    public function down()
    {
        // drop constraints first to prevent errors
        if ($this->db->DBDriver !== 'SQLite3') { // @phpstan-ignore-line
            // Drop foreign keys for new tables
            $this->forge->dropForeignKey('kembali_ruangan', 'kembali_ruangan_verified_by_foreign');
            $this->forge->dropForeignKey('kembali_ruangan', 'kembali_ruangan_pinjam_id_foreign');
            $this->forge->dropForeignKey('kembali_ruangan', 'kembali_ruangan_ruangan_id_foreign');
            $this->forge->dropForeignKey('kembali_ruangan', 'kembali_ruangan_user_id_foreign');
            
            $this->forge->dropForeignKey('pinjam_barang', 'pinjam_barang_verified_by_foreign');
            $this->forge->dropForeignKey('pinjam_barang', 'pinjam_barang_user_id_foreign');
            $this->forge->dropForeignKey('pinjam_barang', 'pinjam_barang_barang_id_foreign');

            // Existing foreign key drops
            $this->forge->dropForeignKey('pinjam_ruangan', 'pinjam_ruangan_verified_by_foreign');
            $this->forge->dropForeignKey('pinjam_ruangan', 'pinjam_ruangan_user_id_foreign');
            $this->forge->dropForeignKey('pinjam_ruangan', 'pinjam_ruangan_ruangan_id_foreign');
            $this->forge->dropForeignKey('laporan', 'laporan_user_id_foreign');
            $this->forge->dropForeignKey('laporan', 'laporan_ditindaklanjuti_oleh_foreign');
            $this->forge->dropForeignKey('laporan', 'laporan_kendaraan_id_foreign');
            $this->forge->dropForeignKey('pemeliharaan_rutin', 'pemeliharaan_rutin_kendaraan_id_foreign');
            $this->forge->dropForeignKey('kembali', 'kembali_kendaraan_id_foreign');
            $this->forge->dropForeignKey('kembali', 'kembali_user_id_foreign');
            $this->forge->dropForeignKey('kembali', 'kembali_kode_barang_foreign');
            $this->forge->dropForeignKey('kembali', 'kembali_pinjam_id_foreign');
            $this->forge->dropForeignKey('pinjam', 'pinjam_kode_barang_foreign');
            $this->forge->dropForeignKey('pinjam', 'pinjam_user_id_foreign');
            $this->forge->dropForeignKey('assets', 'assets_user_id_foreign');
            $this->forge->dropForeignKey('auth_tokens', 'auth_tokens_user_id_foreign');
            $this->forge->dropForeignKey('auth_groups_permissions', 'auth_groups_permissions_group_id_foreign');
            $this->forge->dropForeignKey('auth_groups_permissions', 'auth_groups_permissions_permission_id_foreign');
            $this->forge->dropForeignKey('auth_groups_users', 'auth_groups_users_group_id_foreign');
            $this->forge->dropForeignKey('auth_groups_users', 'auth_groups_users_user_id_foreign');
            $this->forge->dropForeignKey('auth_users_permissions', 'auth_users_permissions_user_id_foreign');
            $this->forge->dropForeignKey('auth_users_permissions', 'auth_users_permissions_permission_id_foreign');
        }

        // Drop new tables
        $this->forge->dropTable('log_akses', true);
        $this->forge->dropTable('tanah', true);
        $this->forge->dropTable('pinjam_barang', true);
        $this->forge->dropTable('barang', true);
        $this->forge->dropTable('kembali_ruangan', true);
        
        // Drop existing tables
        $this->forge->dropTable('auth_logins', true);
        $this->forge->dropTable('auth_tokens', true);
        $this->forge->dropTable('auth_reset_attempts', true);
        $this->forge->dropTable('auth_activation_attempts', true);
        $this->forge->dropTable('auth_groups', true);
        $this->forge->dropTable('auth_permissions', true);
        $this->forge->dropTable('auth_groups_permissions', true);
        $this->forge->dropTable('auth_groups_users', true);
        $this->forge->dropTable('auth_users_permissions', true);
        $this->forge->dropTable('laporan', true);
        $this->forge->dropTable('pemeliharaan_rutin', true);
        $this->forge->dropTable('kembali', true);
        $this->forge->dropTable('pinjam', true);
        $this->forge->dropTable('pinjam_ruangan', true);
        $this->forge->dropTable('assets', true);
        $this->forge->dropTable('ruangan', true);
        $this->forge->dropTable('users', true);
    }
}