<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKdfTteColumns extends Migration
{
    public function up()
    {
        // Add TTE KDF columns to pinjam table
        $fields = [
            'is_kdf_tte_signed' => [
                'type'       => 'SMALLINT',   // PostgreSQL tidak punya TINYINT
                'default'    => 0,
                'null'       => false,
                'comment'    => 'Apakah surat penanggung jawab KDF sudah ditandatangani dengan TTE (0=belum, 1=sudah)',
            ],
            'kdf_tte_signed_at' => [
                'type'       => 'TIMESTAMP',
                'null'       => true,
                'comment'    => 'Waktu ketika surat penanggung jawab KDF ditandatangani dengan TTE',
            ],
            'kdf_tte_signer_nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'comment'    => 'NIK penandatangan TTE untuk surat penanggung jawab KDF',
            ],
        ];

        $this->forge->addColumn('pinjam', $fields);

        // Add indexes (PostgreSQL ignores index name parameter)
        $this->forge->addKey('is_kdf_tte_signed');
        $this->forge->addKey('kdf_tte_signed_at');

        // Actually create the indexes
        $this->forge->processIndexes('pinjam');

        log_message('info', 'Migration AddKdfTteColumns: columns added');
    }

    public function down()
{
    $db = \Config\Database::connect();

    // Drop column safely (no error if column does not exist)
    $db->query('ALTER TABLE pinjam DROP COLUMN IF EXISTS is_kdf_tte_signed;');
    $db->query('ALTER TABLE pinjam DROP COLUMN IF EXISTS kdf_tte_signed_at;');
    $db->query('ALTER TABLE pinjam DROP COLUMN IF EXISTS kdf_tte_signer_nik;');

    // Drop index safely
    $db->query('DROP INDEX IF EXISTS idx_pinjam_is_kdf_tte_signed;');
    $db->query('DROP INDEX IF EXISTS idx_pinjam_kdf_tte_signed_at;');

    log_message('info', 'Migration AddKdfTteColumns: rollback executed safely');
}

}
