<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStnkBpkbToAssets extends Migration
{
    public function up()
    {
        // Add new columns to the assets table
        $fields = [
            'no_stnk' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nomor STNK kendaraan'
            ],
            'no_bpkb' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nomor BPKB kendaraan'
            ]
        ];

        $this->forge->addColumn('assets', $fields);

        // Add indexes for the new columns for better query performance
        $this->db->query("CREATE INDEX idx_assets_no_stnk ON assets(no_stnk)");
        $this->db->query("CREATE INDEX idx_assets_no_bpkb ON assets(no_bpkb)");
    }

    public function down()
    {
        // Remove indexes
        $this->db->query("DROP INDEX IF EXISTS idx_assets_no_stnk");
        $this->db->query("DROP INDEX IF EXISTS idx_assets_no_bpkb");
        
        // Remove columns
        $this->forge->dropColumn('assets', 'no_stnk');
        $this->forge->dropColumn('assets', 'no_bpkb');
    }
}