<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTtePengembalianFields extends Migration
{
    public function up()
    {
        // Tambahkan kolom TTE untuk pengembalian di tabel kembali
        $fields = [
            'is_tte_signed' => [
                'type'       => 'SMALLINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'comment'    => 'Status TTE pengembalian (0=tidak, 1=sudah)'
            ],
            'tte_signed_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'comment' => 'Waktu TTE pengembalian ditandatangani'
            ],
            'tte_signer_nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'null'       => true,
                'comment'    => 'NIK penandatangan TTE pengembalian'
            ],
            'verified_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'comment' => 'Waktu verifikasi pengembalian'
            ],
            'verified_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'ID admin yang melakukan verifikasi'
            ]
        ];

        // Cek field yang sudah ada sebelum menambahkan
        $existingFields = $this->db->getFieldData('kembali');
        $existingFieldNames = array_column($existingFields, 'name');

        $fieldsToAdd = [];
        foreach ($fields as $fieldName => $fieldConfig) {
            if (!in_array($fieldName, $existingFieldNames)) {
                $fieldsToAdd[$fieldName] = $fieldConfig;
            }
        }

        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('kembali', $fieldsToAdd);
            log_message('info', 'Added TTE pengembalian fields: ' . implode(', ', array_keys($fieldsToAdd)));
        }

        // Tambahkan index untuk performance (PostgreSQL syntax)
        try {
            $indexQueries = [
                'CREATE INDEX IF NOT EXISTS idx_kembali_tte_signed ON kembali (is_tte_signed)',
                'CREATE INDEX IF NOT EXISTS idx_kembali_tte_signed_at ON kembali (tte_signed_at)',
                'CREATE INDEX IF NOT EXISTS idx_kembali_verified_at ON kembali (verified_at)',
                'CREATE INDEX IF NOT EXISTS idx_kembali_verified_by ON kembali (verified_by)'
            ];

            foreach ($indexQueries as $query) {
                $this->db->query($query);
            }
            
            log_message('info', 'TTE pengembalian indexes created successfully');
        } catch (\Exception $e) {
            log_message('error', 'Error creating indexes: ' . $e->getMessage());
        }
        
        log_message('info', 'TTE pengembalian migration completed successfully');
    }

    public function down()
    {
        // Hapus index terlebih dahulu
        try {
            $indexesToDrop = [
                'idx_kembali_tte_signed',
                'idx_kembali_tte_signed_at', 
                'idx_kembali_verified_at',
                'idx_kembali_verified_by'
            ];

            foreach ($indexesToDrop as $index) {
                $this->db->query("DROP INDEX IF EXISTS {$index}");
            }
            
            log_message('info', 'TTE pengembalian indexes dropped successfully');
        } catch (\Exception $e) {
            log_message('error', 'Error dropping indexes: ' . $e->getMessage());
        }
        
        // Hapus kolom yang ada
        try {
            $existingFields = $this->db->getFieldData('kembali');
            $existingFieldNames = array_column($existingFields, 'name');
            
            $columnsToRemove = [];
            $possibleColumns = [
                'is_tte_signed',
                'tte_signed_at', 
                'tte_signer_nik',
                'verified_at',
                'verified_by'
            ];

            foreach ($possibleColumns as $column) {
                if (in_array($column, $existingFieldNames)) {
                    $columnsToRemove[] = $column;
                }
            }
            
            if (!empty($columnsToRemove)) {
                $this->forge->dropColumn('kembali', $columnsToRemove);
                log_message('info', 'Removed TTE pengembalian columns: ' . implode(', ', $columnsToRemove));
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error removing columns: ' . $e->getMessage());
        }
        
        log_message('info', 'TTE pengembalian rollback completed');
    }
}