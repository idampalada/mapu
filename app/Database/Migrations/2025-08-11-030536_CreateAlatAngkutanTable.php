<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAlatAngkutanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'SERIAL',
                'auto_increment' => true,
            ],
            'tgl_tarik' => [
                'type' => 'DATE',
                'null' => true,
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
                'constraint' => 500,
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
                'default'    => 0,
                'null'       => true,
            ],
            'nilai_penyusutan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'null'       => true,
            ],
            'nilai_buku' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'null'       => true,
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
                'type'    => 'INTEGER',
                'default' => 1,
                'null'    => true,
            ],
            'status_penggunaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
            ],
            'thn_buat' => [
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
            'no_polisi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'daya_mesin' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'bhn_bakar' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'kelompok' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'sub_kelompok' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        
        // Add indexes for better performance
        $this->forge->addKey('kode_barang');
        $this->forge->addKey('kelompok');
        $this->forge->addKey('nama_barang');
        $this->forge->addKey('kondisi');
        
        $this->forge->createTable('alat_angkutan');

        // Set default values menggunakan ALTER TABLE untuk PostgreSQL
        $this->db->query("ALTER TABLE alat_angkutan ALTER COLUMN created_at SET DEFAULT CURRENT_TIMESTAMP");
        $this->db->query("ALTER TABLE alat_angkutan ALTER COLUMN updated_at SET DEFAULT CURRENT_TIMESTAMP");

        // Add comments to table and columns
        $this->db->query("COMMENT ON TABLE alat_angkutan IS 'Tabel untuk menyimpan data alat angkutan'");
        $this->db->query("COMMENT ON COLUMN alat_angkutan.kelompok IS 'Kelompok: ALAT ANGKUTAN DARAT BERMOTOR, ALAT ANGKUTAN DARAT TAK BERMOTOR, dll'");
        $this->db->query("COMMENT ON COLUMN alat_angkutan.kondisi IS 'Kondisi: Baik, Rusak Ringan, Rusak Berat'");
        $this->db->query("COMMENT ON COLUMN alat_angkutan.no_polisi IS 'Nomor polisi kendaraan'");
        $this->db->query("COMMENT ON COLUMN alat_angkutan.daya_mesin IS 'Daya mesin kendaraan (CC, HP, dll)'");
        $this->db->query("COMMENT ON COLUMN alat_angkutan.bhn_bakar IS 'Jenis bahan bakar (BENSIN, SOLAR, dll)'");

        // Create trigger function for auto-updating updated_at
        $this->db->query("
            CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS \$\$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            \$\$ language 'plpgsql';
        ");

        // Create trigger for alat_angkutan table
        $this->db->query("
            CREATE TRIGGER update_alat_angkutan_updated_at 
                BEFORE UPDATE ON alat_angkutan 
                FOR EACH ROW 
                EXECUTE FUNCTION update_updated_at_column();
        ");

        // Insert sample data untuk testing
        $sampleData = [
            [
                'kode_barang' => '3020104001',
                'nama_barang' => 'Sepeda Motor',
                'kelompok' => 'ALAT ANGKUTAN DARAT BERMOTOR',
                'sub_kelompok' => 'KENDARAAN BERMOTOR BERODA DUA',
                'kondisi' => 'Baik',
                'kuantitas' => 1,
                'merk' => 'HONDA',
                'thn_buat' => '2007',
                'no_mesin' => '-',
                'no_rangka' => 'MH1JB52107K344891',
                'no_polisi' => 'AB 2834 IH',
                'daya_mesin' => '125 CC',
                'bhn_bakar' => 'BENSIN',
                'status_penggunaan' => 'Digunakan sendiri untuk operasional',
                'nilai_perolehan' => 14000000,
                'tanggal_perolehan' => '2007-06-26',
                'nup' => '108',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode_barang' => '3020201001',
                'nama_barang' => 'Sepeda',
                'kelompok' => 'ALAT ANGKUTAN DARAT TAK BERMOTOR',
                'sub_kelompok' => 'SEPEDA',
                'kondisi' => 'Baik',
                'kuantitas' => 1,
                'merk' => 'POLYGON',
                'thn_buat' => '2020',
                'no_mesin' => '',
                'no_rangka' => '',
                'no_polisi' => '',
                'daya_mesin' => '',
                'bhn_bakar' => '',
                'status_penggunaan' => 'Digunakan sendiri untuk operasional',
                'nilai_perolehan' => 2500000,
                'tanggal_perolehan' => '2020-03-15',
                'nup' => '25',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode_barang' => '3020301001',
                'nama_barang' => 'Perahu Motor',
                'kelompok' => 'ALAT ANGKUTAN APUNG BERMOTOR',
                'sub_kelompok' => 'KAPAL BERMOTOR',
                'kondisi' => 'Baik',
                'kuantitas' => 1,
                'merk' => 'YAMAHA',
                'thn_buat' => '2018',
                'no_mesin' => 'YM18001',
                'no_rangka' => '',
                'no_polisi' => '',
                'daya_mesin' => '40 HP',
                'bhn_bakar' => 'BENSIN',
                'status_penggunaan' => 'Digunakan sendiri untuk operasional',
                'nilai_perolehan' => 45000000,
                'tanggal_perolehan' => '2018-08-20',
                'nup' => '42',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('alat_angkutan')->insertBatch($sampleData);
    }

    public function down()
    {
        // Drop trigger first
        $this->db->query("DROP TRIGGER IF EXISTS update_alat_angkutan_updated_at ON alat_angkutan");
        
        // Drop the table
        $this->forge->dropTable('alat_angkutan');
        
        // Drop the trigger function (optional, karena bisa digunakan tabel lain)
        // $this->db->query("DROP FUNCTION IF EXISTS update_updated_at_column()");
    }
}