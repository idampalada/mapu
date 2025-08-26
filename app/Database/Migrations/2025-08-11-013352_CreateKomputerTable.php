<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKomputerTable extends Migration
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
            'jns_processor' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'processor' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'memori' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'hardisk' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'monitor' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'spek_lain' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
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
        
        $this->forge->createTable('komputer');

        // Set default values menggunakan ALTER TABLE untuk PostgreSQL
        $this->db->query("ALTER TABLE komputer ALTER COLUMN created_at SET DEFAULT CURRENT_TIMESTAMP");
        $this->db->query("ALTER TABLE komputer ALTER COLUMN updated_at SET DEFAULT CURRENT_TIMESTAMP");

        // Add comments to table and columns
        $this->db->query("COMMENT ON TABLE komputer IS 'Tabel untuk menyimpan data komputer dan peralatan komputer'");
        $this->db->query("COMMENT ON COLUMN komputer.kelompok IS 'Kelompok: KOMPUTER UNIT atau PERALATAN KOMPUTER'");
        $this->db->query("COMMENT ON COLUMN komputer.kondisi IS 'Kondisi: Baik, Rusak Ringan, Rusak Berat'");
        $this->db->query("COMMENT ON COLUMN komputer.processor IS 'Spesifikasi processor komputer'");
        $this->db->query("COMMENT ON COLUMN komputer.memori IS 'Spesifikasi RAM/memori'");
        $this->db->query("COMMENT ON COLUMN komputer.hardisk IS 'Spesifikasi storage/hardisk'");
        $this->db->query("COMMENT ON COLUMN komputer.monitor IS 'Spesifikasi monitor jika ada'");
        $this->db->query("COMMENT ON COLUMN komputer.spek_lain IS 'Spesifikasi lainnya yang tidak tercakup kolom lain'");

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

        // Create trigger for komputer table
        $this->db->query("
            CREATE TRIGGER update_komputer_updated_at 
                BEFORE UPDATE ON komputer 
                FOR EACH ROW 
                EXECUTE FUNCTION update_updated_at_column();
        ");

        // Insert sample data untuk testing
        $sampleData = [
            [
                'kode_barang' => '3100102001',
                'nama_barang' => 'P.C Unit',
                'kelompok' => 'KOMPUTER UNIT',
                'sub_kelompok' => 'PERSONAL KOMPUTER',
                'kondisi' => 'Baik',
                'kuantitas' => 1,
                'merk' => 'Dell',
                'processor' => 'Intel Core i5',
                'memori' => '8GB',
                'hardisk' => '500GB',
                'status_penggunaan' => 'Digunakan sendiri untuk operasional',
                'nilai_perolehan' => 11500000,
                'tanggal_perolehan' => '2015-06-05',
                'nup' => '17',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode_barang' => '3100201001',
                'nama_barang' => 'Printer Laser',
                'kelompok' => 'PERALATAN KOMPUTER',
                'sub_kelompok' => 'PRINTER',
                'kondisi' => 'Baik',
                'kuantitas' => 1,
                'merk' => 'HP',
                'processor' => '',
                'memori' => '',
                'hardisk' => '',
                'status_penggunaan' => 'Digunakan sendiri untuk operasional',
                'nilai_perolehan' => 2500000,
                'tanggal_perolehan' => '2020-03-15',
                'nup' => '25',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode_barang' => '3100102002',
                'nama_barang' => 'Laptop',
                'kelompok' => 'KOMPUTER UNIT',
                'sub_kelompok' => 'LAPTOP',
                'kondisi' => 'Baik',
                'kuantitas' => 1,
                'merk' => 'Lenovo',
                'processor' => 'Intel Core i7',
                'memori' => '16GB',
                'hardisk' => '1TB SSD',
                'status_penggunaan' => 'Digunakan sendiri untuk operasional',
                'nilai_perolehan' => 15000000,
                'tanggal_perolehan' => '2022-08-20',
                'nup' => '42',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('komputer')->insertBatch($sampleData);
    }

    public function down()
    {
        // Drop trigger first
        $this->db->query("DROP TRIGGER IF EXISTS update_komputer_updated_at ON komputer");
        
        // Drop the table
        $this->forge->dropTable('komputer');
        
        // Drop the trigger function (optional, karena bisa digunakan tabel lain)
        // $this->db->query("DROP FUNCTION IF EXISTS update_updated_at_column()");
    }
}