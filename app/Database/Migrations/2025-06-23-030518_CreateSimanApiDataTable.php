<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSimanApiDataTable extends Migration
{
    public function up()
    {
        // Drop table jika ada untuk clean start
        $this->db->query("DROP TABLE IF EXISTS siman_api_data CASCADE");
        
        // Buat tabel dengan SQL raw untuk PostgreSQL
        $sql = "
            CREATE TABLE siman_api_data (
                id BIGSERIAL PRIMARY KEY,
                kategori_api VARCHAR(100) NOT NULL,
                data_json JSONB,
                api_id VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        
        $this->db->query($sql);
        
        // Tambahkan indexes
        $this->db->query("CREATE INDEX idx_siman_api_data_kategori_api ON siman_api_data (kategori_api)");
        $this->db->query("CREATE INDEX idx_siman_api_data_created_at ON siman_api_data (created_at)");
        $this->db->query("CREATE INDEX idx_siman_api_data_data_json ON siman_api_data USING GIN (data_json)");
        $this->db->query("CREATE INDEX idx_siman_api_data_api_id ON siman_api_data (api_id)");
        
        // Comment untuk dokumentasi
        $this->db->query("COMMENT ON TABLE siman_api_data IS 'Tabel untuk menyimpan data dari SIMAN API dengan dynamic columns'");
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS siman_api_data CASCADE");
    }
}