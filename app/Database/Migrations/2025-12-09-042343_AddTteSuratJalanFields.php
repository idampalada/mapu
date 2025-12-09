<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTteSuratJalanFieldsSafe extends Migration
{
    public function up()
    {
        // Cek apakah kolom sudah ada sebelum menambahkan
        $fields = $this->db->getFieldData('pinjam');
        $existingColumns = array_column($fields, 'name');
        
        // Array untuk kolom yang akan ditambahkan
        $columnsToAdd = [];
        
        // Cek dan tambahkan kolom jika belum ada
        if (!in_array('is_surat_jalan_tte_signed', $existingColumns)) {
            $columnsToAdd['is_surat_jalan_tte_signed'] = [
                'type'       => 'SMALLINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => 'Status TTE surat jalan (0=tidak, 1=sudah)'
            ];
        }
        
        if (!in_array('surat_jalan_tte_signed_at', $existingColumns)) {
            $columnsToAdd['surat_jalan_tte_signed_at'] = [
                'type' => 'TIMESTAMP',
                'null' => true,
                'comment' => 'Waktu TTE surat jalan ditandatangani'
            ];
        }
        
        if (!in_array('surat_jalan_tte_signer_nik', $existingColumns)) {
            $columnsToAdd['surat_jalan_tte_signer_nik'] = [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'null'       => true,
                'comment'    => 'NIK penandatangan TTE surat jalan'
            ];
        }
        
        // Tambahkan kolom yang belum ada
        if (!empty($columnsToAdd)) {
            $this->forge->addColumn('pinjam', $columnsToAdd);
            log_message('info', 'Added missing TTE Surat Jalan columns: ' . implode(', ', array_keys($columnsToAdd)));
        } else {
            log_message('info', 'All TTE Surat Jalan columns already exist');
        }

        // Cek dan tambahkan index jika belum ada
        try {
            // Cek apakah index sudah ada
            $indexExists = $this->db->query("SELECT 1 FROM pg_indexes WHERE indexname = 'idx_pinjam_surat_jalan_tte'")->getRow();
            
            if (!$indexExists) {
                $this->db->query('CREATE INDEX idx_pinjam_surat_jalan_tte ON pinjam (is_surat_jalan_tte_signed)');
                log_message('info', 'Created index: idx_pinjam_surat_jalan_tte');
            } else {
                log_message('info', 'Index idx_pinjam_surat_jalan_tte already exists');
            }
            
            $indexExists2 = $this->db->query("SELECT 1 FROM pg_indexes WHERE indexname = 'idx_pinjam_surat_jalan_tte_signed_at'")->getRow();
            
            if (!$indexExists2) {
                $this->db->query('CREATE INDEX idx_pinjam_surat_jalan_tte_signed_at ON pinjam (surat_jalan_tte_signed_at)');
                log_message('info', 'Created index: idx_pinjam_surat_jalan_tte_signed_at');
            } else {
                log_message('info', 'Index idx_pinjam_surat_jalan_tte_signed_at already exists');
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error creating indexes: ' . $e->getMessage());
            // Continue execution, indexes are not critical
        }
        
        log_message('info', 'TTE Surat Jalan migration completed successfully');
    }

    public function down()
    {
        // Hapus index terlebih dahulu jika ada
        try {
            $this->db->query('DROP INDEX IF EXISTS idx_pinjam_surat_jalan_tte');
            $this->db->query('DROP INDEX IF EXISTS idx_pinjam_surat_jalan_tte_signed_at');
            log_message('info', 'Dropped TTE Surat Jalan indexes');
        } catch (\Exception $e) {
            log_message('error', 'Error dropping indexes: ' . $e->getMessage());
        }
        
        // Cek kolom yang ada dan hapus jika ada
        try {
            $fields = $this->db->getFieldData('pinjam');
            $existingColumns = array_column($fields, 'name');
            
            $columnsToRemove = [];
            
            if (in_array('is_surat_jalan_tte_signed', $existingColumns)) {
                $columnsToRemove[] = 'is_surat_jalan_tte_signed';
            }
            if (in_array('surat_jalan_tte_signed_at', $existingColumns)) {
                $columnsToRemove[] = 'surat_jalan_tte_signed_at';
            }
            if (in_array('surat_jalan_tte_signer_nik', $existingColumns)) {
                $columnsToRemove[] = 'surat_jalan_tte_signer_nik';
            }
            
            if (!empty($columnsToRemove)) {
                $this->forge->dropColumn('pinjam', $columnsToRemove);
                log_message('info', 'Removed TTE Surat Jalan columns: ' . implode(', ', $columnsToRemove));
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error removing columns: ' . $e->getMessage());
        }
        
        log_message('info', 'TTE Surat Jalan rollback completed');
    }
}