<?php

/**
 * ============================================================================
 *  FILE BARU — milik aplikasi API (mapu/api/)
 *  Letakkan : mapu/api/app/Libraries/ApiToken.php
 *  Tujuan   : Membuat & memverifikasi token JWT (HMAC-SHA256).
 *             Stateless — tanpa tabel database & tanpa library luar.
 * ============================================================================
 */

namespace App\Libraries;

class ApiToken
{
    /**
     * Membuat token JWT yang ditandatangani dengan secret.
     *
     * @param array  $claims Data di dalam token (mis. ['sub' => 12, 'username' => 'vendor'])
     * @param int    $ttl    Umur token dalam detik
     * @param string $secret Kunci penandatangan (.env: api.jwtSecret)
     *
     * @return array{token:string, expires_in:int, expires_at:int}
     */
    public static function issue(array $claims, int $ttl, string $secret): array
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $now    = time();

        $payload = array_merge($claims, [
            'iat' => $now,
            'exp' => $now + $ttl,
        ]);

        $h   = self::b64urlEncode(json_encode($header));
        $p   = self::b64urlEncode(json_encode($payload));
        $sig = self::b64urlEncode(hash_hmac('sha256', "$h.$p", $secret, true));

        return [
            'token'      => "$h.$p.$sig",
            'expires_in' => $ttl,
            'expires_at' => $now + $ttl,
        ];
    }

    /**
     * Memverifikasi token. Mengembalikan payload bila valid, atau null.
     */
    public static function verify(string $jwt, string $secret): ?array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return null;
        }

        [$h, $p, $sig] = $parts;

        $expected = self::b64urlEncode(hash_hmac('sha256', "$h.$p", $secret, true));
        if (! hash_equals($expected, $sig)) {
            return null;
        }

        $payload = json_decode(self::b64urlDecode($p), true);
        if (! is_array($payload)) {
            return null;
        }

        if (isset($payload['exp']) && time() >= (int) $payload['exp']) {
            return null;
        }

        return $payload;
    }

    private static function b64urlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function b64urlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
