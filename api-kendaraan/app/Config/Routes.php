<?php

/**
 * ============================================================================
 *  FILE DIGANTI — milik aplikasi API (mapu/api/)
 *  Letakkan : mapu/api/app/Config/Routes.php  (timpa bawaan appstarter)
 *
 *  CATATAN: Ini Routes.php milik APLIKASI API BARU, BUKAN milik mapu.
 *           Routes.php milik mapu tidak disentuh sama sekali.
 * ============================================================================
 */

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Auto-route dimatikan: semua endpoint didaftarkan eksplisit di bawah.
$routes->setAutoRoute(false);

// Health check sederhana (terbuka) — memastikan API hidup
$routes->get('/', static function () {
    return service('response')->setJSON([
        'status'  => 'success',
        'message' => 'API Kendaraan aktif.',
        'version' => 'v1',
    ]);
});

$routes->group('api/v1', static function ($routes) {

    // ---- LANGKAH 1: ambil token (TERBUKA, tanpa filter jwt) ----
    $routes->post('auth/token', 'Auth::token');

    // ---- LANGKAH 2: data kendaraan (WAJIB bearer token) ----
    $routes->group('', ['filter' => 'jwt'], static function ($routes) {

        // Route KHUSUS di ATAS agar "statistik" tidak dianggap {id}
        $routes->get('kendaraan/statistik', 'Kendaraan::statistik');

        $routes->get('kendaraan',           'Kendaraan::index');
        $routes->get('kendaraan/(:num)',    'Kendaraan::show/$1');
        $routes->post('kendaraan',          'Kendaraan::create');
    });
});
