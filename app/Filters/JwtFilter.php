<?php

/**
 * ============================================================================
 *  FILE BARU — milik aplikasi API (mapu/api/)
 *  Letakkan : mapu/api/app/Filters/JwtFilter.php
 *  Daftar   : Alias 'jwt' di mapu/api/app/Config/Filters.php
 *  Tujuan   : Memeriksa bearer token pada header Authorization (LANGKAH 2).
 * ============================================================================
 */

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\ApiToken;

class JwtFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = service('response');

        $jwtSecret = (string) env('api.jwtSecret');
        if ($jwtSecret === '') {
            return $response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Server belum dikonfigurasi (.env: api.jwtSecret).',
            ]);
        }

        $authHeader = $request->getHeaderLine('Authorization');

        if (stripos($authHeader, 'Bearer ') !== 0) {
            return $response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'message' => 'Unauthorized. Header Authorization: Bearer <token> diperlukan.',
            ]);
        }

        $token   = trim(substr($authHeader, 7));
        $payload = ApiToken::verify($token, $jwtSecret);

        if ($payload === null) {
            return $response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'message' => 'Token tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        // Identitas pemakai token (opsional dipakai controller)
        $request->userId   = $payload['sub'] ?? null;
        $request->username = $payload['username'] ?? null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
