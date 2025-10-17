<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifySuratPermohonanColumn extends Migration
{
    public function up()
    {
        // Ubah kolom surat_permohonan menjadi nullable
        $this->db->query("ALTER TABLE pinjam ALTER COLUMN surat_permohonan DROP NOT NULL");
    }

    public function down()
    {
        // Kembalikan NOT NULL constraint
        $this->db->query("ALTER TABLE pinjam ALTER COLUMN surat_permohonan SET NOT NULL");
    }
}