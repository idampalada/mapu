<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRejectionFieldsToPinjam extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pinjam', [
            'has_rejected_return' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'null' => false,
            ],
            'rejected_return_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'rejected_return_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pinjam', 'has_rejected_return');
        $this->forge->dropColumn('pinjam', 'rejected_return_reason');
        $this->forge->dropColumn('pinjam', 'rejected_return_date');
    }
}