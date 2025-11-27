<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateBookingRuanganStatusConstraint extends Migration
{
    public function up()
    {
        // Get the database connection
        $db = \Config\Database::connect();
        
        try {
            // Log the migration start
            log_message('info', 'Starting migration: UpdateBookingRuanganStatusConstraint');
            
            // Check if constraint exists first
            $constraintQuery = $db->query("
                SELECT conname 
                FROM pg_catalog.pg_constraint con
                INNER JOIN pg_catalog.pg_class rel ON rel.oid = con.conrelid
                INNER JOIN pg_catalog.pg_namespace nsp ON nsp.oid = connamespace
                WHERE nsp.nspname = 'public' 
                  AND rel.relname = 'booking_ruangan'
                  AND con.conname = 'chk_status'
            ");
            
            $constraintExists = $constraintQuery->getNumRows() > 0;
            
            if ($constraintExists) {
                log_message('info', 'Dropping existing chk_status constraint');
                // Drop existing constraint
                $db->query("ALTER TABLE public.booking_ruangan DROP CONSTRAINT chk_status");
            } else {
                log_message('info', 'No existing chk_status constraint found');
            }
            
            // Create new constraint that allows all necessary statuses
            log_message('info', 'Creating new chk_status constraint');
            $db->query("
                ALTER TABLE public.booking_ruangan 
                ADD CONSTRAINT chk_status 
                CHECK (status IN ('aktif', 'pending', 'disetujui', 'ditolak', 'selesai'))
            ");
            
            // Verify the constraint was created
            $verifyQuery = $db->query("
                SELECT 
                    conname AS constraint_name,
                    pg_catalog.pg_get_constraintdef(con.oid, true) AS constraint_definition
                FROM pg_catalog.pg_constraint con
                INNER JOIN pg_catalog.pg_class rel ON rel.oid = con.conrelid
                INNER JOIN pg_catalog.pg_namespace nsp ON nsp.oid = connamespace
                WHERE nsp.nspname = 'public' 
                  AND rel.relname = 'booking_ruangan'
                  AND con.conname = 'chk_status'
            ");
            
            $result = $verifyQuery->getRowArray();
            
            if ($result) {
                log_message('info', 'Migration successful. New constraint: ' . $result['constraint_definition']);
            } else {
                throw new \Exception('Failed to verify constraint creation');
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Migration failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function down()
    {
        // Get the database connection
        $db = \Config\Database::connect();
        
        try {
            log_message('info', 'Rolling back migration: UpdateBookingRuanganStatusConstraint');
            
            // Drop the constraint we created
            $db->query("ALTER TABLE public.booking_ruangan DROP CONSTRAINT IF EXISTS chk_status");
            
            // Note: We cannot restore the original constraint as we don't know what it was
            // This rollback will leave the table without status constraint
            log_message('warning', 'Rollback completed. Original constraint not restored (unknown original values)');
            
        } catch (\Exception $e) {
            log_message('error', 'Rollback failed: ' . $e->getMessage());
            throw $e;
        }
    }
}