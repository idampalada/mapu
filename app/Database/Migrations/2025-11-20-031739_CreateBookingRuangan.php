<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBookingRuangan extends Migration
{
    public function up()
    {
        // Membuat tabel booking_ruangan dengan sintaks PostgreSQL yang benar
        $this->forge->addField([
            'id' => [
                'type'           => 'SERIAL',
                'auto_increment' => true,
            ],
            'ruangan_id' => [
                'type'       => 'INT',
                'null'       => false,
            ],
            'user_id' => [
                'type'       => 'INT',
                'null'       => false,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'waktu_mulai' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'waktu_selesai' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'keperluan' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'nama_penanggung_jawab' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            // FIELD BARU: Nomor HP Penanggung Jawab
            'nomor_hp_penanggung_jawab' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'comment'    => 'Nomor HP Penanggung Jawab'
            ],
            'unit_organisasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'jumlah_peserta' => [
                'type'       => 'INT',
                'null'       => false,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'aktif',
                'null'       => false,
            ],
            // Timestamp fields tanpa default di sini - akan ditambah manual
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true, // Sementara null, nanti diubah
            ],
            'updated_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true, // Sementara null, nanti diubah
            ],
        ]);

        $this->forge->addKey('id', true);
        
        // Membuat tabel terlebih dahulu
        $this->forge->createTable('booking_ruangan');

        // Sekarang alter table untuk menambahkan default values yang benar
        $this->db->query('ALTER TABLE booking_ruangan ALTER COLUMN created_at SET DEFAULT CURRENT_TIMESTAMP');
        $this->db->query('ALTER TABLE booking_ruangan ALTER COLUMN updated_at SET DEFAULT CURRENT_TIMESTAMP');
        $this->db->query('ALTER TABLE booking_ruangan ALTER COLUMN created_at SET NOT NULL');
        $this->db->query('ALTER TABLE booking_ruangan ALTER COLUMN updated_at SET NOT NULL');

        // Menambahkan foreign key constraints
        $this->db->query('ALTER TABLE booking_ruangan ADD CONSTRAINT fk_booking_ruangan_ruangan_id FOREIGN KEY (ruangan_id) REFERENCES ruangan(id) ON DELETE CASCADE');
        $this->db->query('ALTER TABLE booking_ruangan ADD CONSTRAINT fk_booking_ruangan_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');

        // Menambahkan constraints
        $this->db->query('ALTER TABLE booking_ruangan ADD CONSTRAINT chk_waktu_booking CHECK (waktu_selesai > waktu_mulai)');
        $this->db->query('ALTER TABLE booking_ruangan ADD CONSTRAINT chk_jumlah_peserta CHECK (jumlah_peserta > 0)');
        $this->db->query("ALTER TABLE booking_ruangan ADD CONSTRAINT chk_status CHECK (status IN ('aktif', 'selesai', 'dibatalkan'))");

        // Menambahkan indexes untuk performance
        $this->db->query('CREATE INDEX idx_booking_ruangan_tanggal ON booking_ruangan(tanggal)');
        $this->db->query('CREATE INDEX idx_booking_ruangan_ruangan_id ON booking_ruangan(ruangan_id)');
        $this->db->query('CREATE INDEX idx_booking_ruangan_user_id ON booking_ruangan(user_id)');
        $this->db->query('CREATE INDEX idx_booking_ruangan_status ON booking_ruangan(status)');
        $this->db->query('CREATE INDEX idx_booking_ruangan_waktu ON booking_ruangan(tanggal, waktu_mulai, waktu_selesai)');
        $this->db->query('CREATE INDEX idx_booking_availability ON booking_ruangan(ruangan_id, tanggal, waktu_mulai, waktu_selesai, status)');

        // Membuat function untuk auto update timestamp
        $this->db->query('
            CREATE OR REPLACE FUNCTION update_booking_ruangan_updated_at()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Membuat trigger untuk auto update timestamp
        $this->db->query('
            CREATE TRIGGER trigger_booking_ruangan_updated_at
                BEFORE UPDATE ON booking_ruangan
                FOR EACH ROW
                EXECUTE FUNCTION update_booking_ruangan_updated_at();
        ');

        // Menambahkan comment untuk dokumentasi
        $this->db->query("COMMENT ON TABLE booking_ruangan IS 'Tabel untuk menyimpan data booking ruangan langsung (tanpa perlu approval admin)'");
        $this->db->query("COMMENT ON COLUMN booking_ruangan.status IS 'Status booking: aktif, selesai, dibatalkan'");
        $this->db->query("COMMENT ON COLUMN booking_ruangan.keperluan IS 'Penjelasan keperluan penggunaan ruangan'");
        $this->db->query("COMMENT ON COLUMN booking_ruangan.nama_penanggung_jawab IS 'Nama penanggung jawab acara/kegiatan'");
        $this->db->query("COMMENT ON COLUMN booking_ruangan.nomor_hp_penanggung_jawab IS 'Nomor HP penanggung jawab acara/kegiatan'");
        $this->db->query("COMMENT ON COLUMN booking_ruangan.unit_organisasi IS 'Unit kerja atau organisasi pengguna'");
    }

    public function down()
    {
        // Drop trigger dan function
        $this->db->query('DROP TRIGGER IF EXISTS trigger_booking_ruangan_updated_at ON booking_ruangan');
        $this->db->query('DROP FUNCTION IF EXISTS update_booking_ruangan_updated_at()');
        
        // Drop tabel
        $this->forge->dropTable('booking_ruangan');
    }
}