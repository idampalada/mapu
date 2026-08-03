<?php

/**
 * ============================================================================
 *  FILE BARU — milik aplikasi API (mapu/api/)
 *  Letakkan : mapu/api/app/Models/UserModel.php
 *  Tabel    : users (HANYA DIBACA — untuk verifikasi login vendor)
 *  Catatan  : App ini tidak memakai Myth\Auth. Verifikasi password dibuat
 *             KOMPATIBEL dengan hash Myth\Auth (lihat Controllers/Auth.php).
 *             Tabel users tidak pernah ditulis/diubah oleh API ini.
 * ============================================================================
 */

namespace App\Models\Api;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    // API ini TIDAK menulis ke tabel users
    protected $allowedFields = [];

    protected $useTimestamps = false;

    /**
     * Cari user berdasarkan email ATAU username (otomatis dideteksi).
     * Mengembalikan array data user, atau null bila tidak ditemukan.
     */
    public function findByLogin(string $login): ?array
    {
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $row = $this->builder()
                    ->select('id, username, email, fullname, password_hash, active, role')
                    ->where($field, $login)
                    ->where('deleted_at IS NULL', null, false)
                    ->get()
                    ->getRowArray();

        return $row ?: null;
    }
}
