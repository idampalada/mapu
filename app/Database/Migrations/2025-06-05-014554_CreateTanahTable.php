<?php

// File: app/Database/Migrations/2024-06-05_CreateTanahTable.php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTanahTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'SERIAL',
                'unsigned' => false,
                'null' => false,
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
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'kelompok' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'luas_tanah_seluruhnya' => [
                'type'       => 'NUMERIC',
                'constraint' => '12,2',
                'default'    => 0.00,
                'null'       => false,
            ],
            'status_penggunaan' => [
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
        $this->forge->createTable('tanah');
    }

    public function down()
    {
        $this->forge->dropTable('tanah');
    }
}
