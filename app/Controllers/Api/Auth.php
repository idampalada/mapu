<?php

/**
 * ============================================================================
 *  FILE BARU — milik aplikasi API (mapu/api/)
 *  Letakkan : mapu/api/app/Controllers/Auth.php
 *  Route    : POST /api/v1/auth/token   (TERBUKA, tanpa filter jwt)
 *
 *  Tujuan   : LANGKAH 1 — vendor menukar email/username + password menjadi
 *             bearer token.
 *
 *  PENTING  : App ini TIDAK memakai Myth\Auth, tetapi memverifikasi password
 *             dengan algoritma yang SAMA persis dengan Myth\Auth:
 *                 password_verify(base64_encode(hash('sha384', $pw, true)), $hash)
 *             Sehingga akun users milik mapu tetap bisa dipakai, tanpa
 *             mengubah apa pun di project mapu.
 * ============================================================================
 */

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Libraries\ApiToken;
use App\Models\Api\UserModel;

class Auth extends ResourceController
{
    protected $format = 'json';

    /**
     * POST /api/v1/auth/token
     * Body (JSON atau form):
     *   { "login": "email-atau-username", "password": "..." }
     */
    public function token()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $login    = trim((string) ($data['login'] ?? $data['email'] ?? $data['username'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        $jwtSecret = (string) env('api.jwtSecret');
        $ttl       = (int) (env('api.jwtTTL') ?: 3600);

        if ($jwtSecret === '') {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Server belum dikonfigurasi (.env: api.jwtSecret).',
            ], 500);
        }

        if ($login === '' || $password === '') {
            return $this->respond([
                'status'  => 'error',
                'message' => 'login (email/username) dan password wajib diisi.',
            ], 400);
        }

        $users = new UserModel();
        $user  = $users->findByLogin($login);

        // Verifikasi password dengan algoritma Myth\Auth
        $passwordOk = $user !== null && self::verifyMythPassword($password, $user['password_hash']);

        if (! $passwordOk) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Email/username atau password salah.',
            ], 401);
        }

        // Akun harus aktif
        if ((int) ($user['active'] ?? 0) !== 1) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Akun belum aktif. Hubungi admin.',
            ], 403);
        }

        $issued = ApiToken::issue([
            'sub'      => (int) $user['id'],
            'username' => $user['username'],
        ], $ttl, $jwtSecret);

        return $this->respond([
            'status'       => 'success',
            'token_type'   => 'Bearer',
            'access_token' => $issued['token'],
            'expires_in'   => $issued['expires_in'],
        ], 200);
    }

    /**
     * Verifikasi password kompatibel Myth\Auth.
     * Myth\Auth\Password::verify() melakukan:
     *     password_verify(base64_encode(hash('sha384', $password, true)), $hash)
     */
    private static function verifyMythPassword(string $password, ?string $hash): bool
    {
        if (empty($hash)) {
            return false;
        }

        $prepared = base64_encode(hash('sha384', $password, true));

        return password_verify($prepared, $hash);
    }
}
