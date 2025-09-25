<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Beranda');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * @var RouteCollection $routes
 */

// Public Routes
$routes->get('/', 'Beranda::index');

$routes->group('auth', function($routes) {
    $routes->get('register', 'Auth::register');
    $routes->post('check-username', 'Auth::checkUsername');
    $routes->post('check-email', 'Auth::checkEmail');
    $routes->post('check-email-forgot', 'Auth::checkEmailForgot');
    $routes->get('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
});

$routes->group('', ['filter' => 'login'], function ($routes) {
    $routes->get('homepage', 'User\Homepage::index');
    $routes->get('mainpage', 'User\Mainpage::index');
    $routes->get('mainpage/status-kendaraan', 'User\Mainpage::statusKendaraan');


    $routes->get('mainpage/getPeminjamanKendaraanAPI', 'User\Mainpage::getPeminjamanKendaraanAPI');
    $routes->get('mainpage/getPeminjamanRuanganAPI', 'User\Mainpage::getPeminjamanRuanganAPI');
    $routes->get('mainpage/getStatusKendaraanAPI', 'User\Mainpage::getStatusKendaraanAPI');
    $routes->get('mainpage/getStatusRuanganAPI', 'User\Mainpage::getStatusRuanganAPI');
    $routes->get('mainpage/getStatistikKendaraanAPI', 'User\Mainpage::getStatistikKendaraanAPI');
    $routes->get('mainpage/getStatistikRuanganAPI', 'User\Mainpage::getStatistikRuanganAPI');

    $routes->get('user/riwayat', 'User\Riwayat::index');
    $routes->get('user/riwayat/detail/(:segment)/(:num)', 'User\Riwayat::getDetail/$1/$2');

    $routes->group('admin/riwayat', ['filter' => 'role:admin,admin_gedungutama'], function($routes) {
        $routes->get('', 'Admin\Riwayat::index');
        $routes->get('kendaraan', 'Admin\Riwayat::kendaraan');
        $routes->get('ruangan', 'Admin\Riwayat::ruangan');
        $routes->get('barang', 'Admin\Riwayat::barang');
    });
    

//     $routes->post('admin/barang/verifikasi', 'User\Barang::verifikasiPeminjaman', ['filter' => 'role:admin,admin_gedungutama']);
//     $routes->post('admin/barang/verifikasi', 'Admin\Barang::verifikasi', ['filter' => 'role:admin,admin_gedungutama']);
//     $routes->post('admin/barang/verifikasi/(:num)', 'User\Barang::verifikasiPeminjaman/$1', ['filter' => 'role:admin,admin_gedungutama']);
//     $routes->get('admin/barang/pending', 'Admin\Barang::getPendingBarang', ['filter' => 'role:admin,admin_gedungutama']);
//     $routes->get('user/barang', 'User\\Barang::index');
//     $routes->get('user/barang/kategori/(:segment)', 'User\\Barang::detail/$1');
//     $routes->post('user/barang/pinjam', 'User\\Barang::pinjam');
//     // Admin verifikasi peminjaman barang
// $routes->post('admin/User/Barang/verifikasiPeminjaman', 'User\Barang::verifikasiPeminjaman', ['filter' => 'role:admin,admin_gedungutama']);
// $routes->post('verifikasiPeminjaman', 'User\Ruangan::verifikasiPeminjaman');
// $routes->post('verifikasiPengembalianRuangan', 'User\Ruangan::verifikasiPengembalian');
// $routes->group('admin/User/Barang', ['filter' => 'role:admin,admin_gedungutama'], function($routes) {
//     $routes->post('verifikasiPeminjaman', 'User\Barang::verifikasiPeminjaman');
//     $routes->post('verifikasiPengembalian', 'User\Barang::verifikasiPengembalian');
//     $routes->post('admin/User/Barang/kembalikan', 'User\Barang::kembalikan');
//     $routes->post('admin/User/Barang/verifikasiPengembalian');
//     $routes->post('admin/User/Barang/verifikasiPengembalian', 'User\Barang::verifikasiPengembalian', ['filter' => 'role:admin,admin_gedungutama']);



    
// });
$routes->group('admin/User/Barang', ['filter' => 'role:admin,admin_gedungutama'], function($routes) {
    $routes->post('verifikasiPeminjaman', 'User\Barang::verifikasiPeminjaman');
    $routes->post('verifikasiPengembalian', 'User\Barang::verifikasiPengembalian');
    $routes->post('kembalikan', 'User\Barang::kembalikan');
    $routes->post('/user/barang/kembalikan', 'User\Barang::kembalikan');
    $routes->post('/admin/User/Barang/verifikasiPengembalian', 'User\Barang::verifikasiPengembalian');
    
});


$routes->group('user/barang', ['filter' => 'login'], function($routes) {
    $routes->get('', 'User\Barang::dashboard'); // halaman awal dashboardbarang.php
    $routes->get('index', 'User\Barang::index'); 
    $routes->get('kategori/(:segment)', 'User\Barang::detail/$1');
    $routes->post('pinjam', 'User\Barang::pinjam');
    $routes->post('kembalikan', 'User\Barang::kembalikan'); 
});

$routes->group('admin/barang', ['filter' => 'role:admin,admin_gedungutama'], function($routes) {
    $routes->post('verifikasi', 'Admin\Barang::verifikasi');
    $routes->post('verifikasi/(:num)', 'User\Barang::verifikasiPeminjaman/$1');
    $routes->get('pending', 'Admin\Barang::getPendingBarang');
    $routes->post('tambah', 'Admin\Barang::tambah');
});

$routes->group('User/Barang', ['filter' => 'role:admin,admin_gedungutama'], function($routes) {
    $routes->post('tambah', 'User\Barang::tambah');
});
$routes->post('admin/User/Barang/verifikasiPeminjaman', 'User\Barang::verifikasiPeminjaman', ['filter' => 'role:admin,admin_gedungutama']);
$routes->post('user/barang/kembalikanById', 'User\Barang::kembalikanById');

    $routes->group('user/ruangan', function($routes) {
    // API routes HARUS DI ATAS (:segment)
    $routes->get('getBookingByDate', 'User\Ruangan::getBookingByDate');
    $routes->post('getBookingByDate', 'User\Ruangan::getBookingByDate');
    $routes->get('checkAvailability', 'User\Ruangan::checkAvailability');
    
    // Form routes
    $routes->post('pinjam', 'User\Ruangan::pinjam');
    
    // General routes
    $routes->get('', 'User\Ruangan::index');
    
    // HARUS TERAKHIR - catch all
    $routes->get('(:segment)', 'User\Ruangan::detail/$1');
});

    $routes->group('AsetKendaraan', ['filter' => 'login'], function($routes) {
        $routes->get('getKendaraan', 'AsetKendaraan::getKendaraan');
        $routes->get('getKendaraanDipinjam', 'AsetKendaraan::getKendaraanDipinjam');
        $routes->post('pinjam', 'AsetKendaraan::pinjam');
        $routes->post('kembali', 'AsetKendaraan::kembali');
    });

    $routes->post('Ruangan/tambah', 'User\Ruangan::tambah');
});

$routes->get('admin/dashboard', 'Admin\Dashboard::index', ['filter' => 'role:admin,admin_gedungutama,admin_pusdatin,admin_binamarga,admin_ciptakarya,admin_sda,admin_gedungg,admin_heritage,admin_auditorium']);
$routes->get('user/ruangan/check-expired', 'User\Ruangan::checkExpiredBookings');
$routes->post('admin/users/changerole', 'Admin\Users::changeRole');
$routes->post('admin/users/deleteUser', 'Admin\Users::deleteUser');
// Verifikasi Ruangan hanya untuk admin dan admin gedung masing-masing
$routes->group('admin/verifikasi-ruangan', ['filter' => 'role:admin,admin_gedungutama,admin_pusdatin,admin_binamarga,admin_ciptakarya,admin_sda,admin_gedungg,admin_heritage,admin_auditorium'], function($routes) {
    $routes->post('verifikasiPeminjaman', 'User\Ruangan::verifikasiPeminjaman');
    $routes->post('verifikasiPengembalianRuangan', 'User\Ruangan::verifikasiPengembalian');
});

// Tambahan route API pengembalian ruangan untuk dashboard
$routes->get('admin/dashboard/getPengembalianRuanganAPI', 'Admin\Dashboard::getPengembalianRuanganAPI', ['filter' => 'role:admin,admin_gedungutama,admin_pusdatin,admin_binamarga,admin_ciptakarya,admin_sda,admin_gedungg,admin_heritage,admin_auditorium']);
// Statistik
$routes->get('/admin/dashboard/chart/peminjaman', 'Admin\Dashboard::chartPeminjaman');
$routes->get('/admin/dashboard/chart/peminjaman-bulanan', 'Admin\Dashboard::chartPeminjamanBulanan');
$routes->get('/admin/dashboard/chart/peminjaman-mingguan', 'Admin\Dashboard::chartPeminjamanMingguan');
$routes->get('/admin/dashboard/chart/peminjaman-harian', 'Admin\Dashboard::chartPeminjamanHarian');
$routes->get('/admin/dashboard/chart/pengembalian', 'Admin\Dashboard::chartPengembalian');
$routes->get('/admin/dashboard/chart/pengembalian-bulanan', 'Admin\Dashboard::chartPengembalianBulanan');
$routes->get('/admin/dashboard/chart/pengembalian-mingguan', 'Admin\Dashboard::chartPengembalianMingguan');
$routes->get('/admin/dashboard/chart/pengembalian-harian', 'Admin\Dashboard::chartPengembalianHarian');
$routes->get('/admin/dashboard/chart/peminjaman-barang-bulanan', 'Admin\Dashboard::chartPeminjamanBarangBulanan');
$routes->get('/admin/dashboard/chart/peminjaman-barang-mingguan', 'Admin\Dashboard::chartPeminjamanBarangMingguan');
$routes->get('/admin/dashboard/chart/peminjaman-barang-harian', 'Admin\Dashboard::chartPeminjamanBarangHarian');

$routes->get('tracking-api', 'Tracking::getTracking', ['filter' => 'login']);




#daftar pengguna edit user
$routes->group('admin', ['filter' => 'role:admin,admin_gedungutama'], function($routes) {
    $routes->get('users', 'Admin\Users::index');
    $routes->post('users/changerole', 'Admin\Users::changeRole');
    $routes->post('users/deleteUser', 'Admin\Users::deleteUser');
    $routes->get('users/edit/(:num)', 'Admin\Users::edit/$1');
    $routes->post('users/update/(:num)', 'Admin\Users::update/$1');
    
});


// Untuk admin routes 
$routes->group('admin', ['filter' => 'role:admin,admin_gedungutama,admin_pusdatin,admin_binamarga,admin_ciptakarya,admin_sda,admin_gedungg,admin_heritage,admin_auditorium'], function ($routes) {
    $routes->get('riwayat', 'Admin\Riwayat::index');
    $routes->get('daftar-pengguna', 'Admin\Users::index');
    $routes->post('barang/tambah', 'Admin\Barang::tambah');
    $routes->get('users/getActivity/(:num)', 'Admin\Users::getActivity/$1');
    $routes->get('daftar-aset', 'Admin\Aset::index');
    $routes->get('aset/getDetail/(:num)', 'Admin\Aset::getDetail/$1');
    
    // Routing ruangan untuk admin
    $routes->get('ruangan/getDetail/(:num)', 'User\Ruangan::getDetail/$1');
    $routes->post('ruangan/edit/(:num)', 'User\Ruangan::edit/$1');
    $routes->post('ruangan/delete/(:num)', 'User\Ruangan::delete/$1');
});
    $routes->group('User', ['filter' => 'role:admin,admin_gedungutama'], function($routes) {
        $routes->post('Barang/tambah', 'User\\Barang::tambah');
        
    });


$routes->group('admin', ['filter' => 'role:admin,admin_gedungutama,admin_pusdatin,admin_binamarga,admin_ciptakarya,admin_sda,admin_gedungg,admin_heritage,admin_auditorium'], function ($routes) {
    $routes->get('riwayat', 'Admin\Riwayat::index');
    $routes->get('daftar-pengguna', 'Admin\Users::index');
    $routes->get('users/getActivity/(:num)', 'Admin\Users::getActivity/$1');
    // $routes->post('User/Ruangan/verifikasiPeminjaman', 'User\Ruangan::verifikasiPeminjaman');
    
    $routes->group('daftar-aset', function($routes) {
        $routes->get('', 'Admin\Aset::index');
        $routes->get('detail/(:num)', 'Admin\Aset::getDetail/$1');
    });
    
    $routes->group('AsetKendaraan', ['filter' => 'login'], function($routes) {
        $routes->post('tambah', 'AsetKendaraan::tambah');
        $routes->delete('delete/(:num)', 'AsetKendaraan::delete/$1');
        $routes->post('verifikasiPeminjaman', 'AsetKendaraan::verifikasiPeminjaman');
        $routes->post('verifikasiPengembalianKendaraan', 'AsetKendaraan::verifikasiPengembalian');
        $routes->get('getAsetById/(:num)', 'AsetKendaraan::getAsetById/$1');
        $routes->post('edit/(:num)', 'AsetKendaraan::edit/$1');
    });

    $routes->group('User/Ruangan', function($routes) {
        $routes->post('verifikasiPeminjaman', 'User\Ruangan::verifikasiPeminjaman');
        $routes->post('verifikasiPengembalianRuangan', 'User\Ruangan::verifikasiPengembalian');
        $routes->post('edit/(:num)', 'User\Ruangan::edit/$1');
        $routes->post('delete/(:num)', 'User\Ruangan::delete/$1');
        $routes->post('verifikasi', 'User\Ruangan::verifikasiPeminjaman');
        $routes->get('detail/(:num)', 'User\Ruangan::getDetail/$1');

    });

    $routes->group('laporan', function($routes) {
        $routes->get('', 'Laporan::index');
        $routes->post('tambah', 'Admin\Laporan::tambah');
        $routes->get('get-laporan', 'Admin\Laporan::getLaporan');
        $routes->get('get-laporan/(:num)', 'Admin\Laporan::getLaporan/$1');
        $routes->post('update/(:num)', 'Admin\Laporan::update/$1');
        $routes->delete('delete/(:num)', 'Admin\Laporan::delete/$1');
        $routes->get('statistik', 'Admin\Laporan::getStatistik');
        $routes->get('pemeliharaan-rutin', 'Laporan::pemeliharaanRutin');
        $routes->get('kerusakan', 'Laporan::kerusakan');
        $routes->get('riwayat-pemeliharaan', 'Laporan::riwayatPemeliharaan');
        $routes->get('kepatuhan', 'Laporan::kepatuhan');
        $routes->get('insiden', 'Laporan::insiden');
        $routes->get('penertiban', 'Laporan::penertiban');
        $routes->get('statistik-aset', 'Laporan::statistikAset');
        $routes->get('analisis', 'Laporan::analisis');
    });

    $routes->group('pemeliharaan-rutin', function($routes) {
        $routes->get('', 'Admin\PemeliharaanRutin::index');
        $routes->get('get-pemeliharaan', 'Admin\PemeliharaanRutin::getPemeliharaan');
        $routes->get('get-kendaraan', 'Admin\PemeliharaanRutin::getKendaraan');
        $routes->post('tambah-jadwal', 'Admin\PemeliharaanRutin::tambahJadwal');
        $routes->delete('delete/(:num)', 'Admin\PemeliharaanRutin::delete/$1');
        $routes->get('get-pemeliharaan/(:num)', 'Admin\PemeliharaanRutin::getJadwalById/$1');
        $routes->post('update/(:num)', 'Admin\PemeliharaanRutin::update/$1');
        $routes->get('export-excel', 'Admin\PemeliharaanRutin::exportExcel');
        $routes->get('export-pdf', 'Admin\PemeliharaanRutin::exportPDF');
    });
});

$routes->group('verifikasi-ruangan', function($routes) {
    $routes->post('gedungutama', 'User\Ruangan::verifikasiGedungUtama', ['filter' => 'role:admin_gedungutama']);
    $routes->post('pusdatin', 'User\Ruangan::verifikasiPusdatin', ['filter' => 'role:admin_pusdatin']);
    $routes->post('binamarga', 'User\Ruangan::verifikasiBinaMarga', ['filter' => 'role:admin_binamarga']);
    $routes->post('ciptakarya', 'User\Ruangan::verifikasiCiptaKarya', ['filter' => 'role:admin_ciptakarya']);
    $routes->post('sda', 'User\Ruangan::verifikasiSDA', ['filter' => 'role:admin_sda']);
    $routes->post('gedungg', 'User\Ruangan::verifikasiGedungG', ['filter' => 'role:admin_gedungg']); 
    $routes->post('heritage', 'User\Ruangan::verifikasiHeritage', ['filter' => 'role:admin_heritage']);
    $routes->post('auditorium', 'User\Ruangan::verifikasiAuditorium', ['filter' => 'role:admin_auditorium']);
});
$routes->get('admin/users/pending', 'Admin\Users::pending', ['filter' => 'role:admin']);
$routes->post('admin/users/activate', 'Admin\Users::activate', ['filter' => 'role:admin']);
$routes->get('user/profile', 'User\Profile::index');
$routes->post('user/profile/update', 'User\Profile::update');
$routes->get('user/homepage', 'User\Homepage::index');




$routes->group('cron', function($routes) {
    $routes->cli('check-overdue', 'CronJob::checkOverdueReturns');
    $routes->get('check-overdue/(:any)', 'CronJob::checkOverdueReturns/$1', ['filter' => 'key-auth']);
});

if (ENVIRONMENT === 'development') {
    $routes->get('test-overdue', 'CronJob::testOverdueCheck');
}

$routes->get('uploads/images/(:any)', function ($filename) {
    $path = ROOTPATH . 'public/uploads/images/' . $filename;
    if (file_exists($path)) {
        $mime = mime_content_type($path);
        header('Content-Type: ' . $mime);
        readfile($path);
        exit;
    }
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
});

$routes->get('uploads/documents/(:any)', function ($filename) {
    $path = ROOTPATH . 'public/uploads/documents/' . $filename;
    if (file_exists($path)) {
        $mime = mime_content_type($path);
        header('Content-Type: ' . $mime);
        readfile($path);
        exit;
    }
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
});
// Tambahkan route langsung tanpa group (untuk debug)
// $routes->get('User/Ruangan/getBookingSaya', 'User\Ruangan::getBookingSaya');
$routes->get('User/Ruangan/getBookingPublik', 'User\Ruangan::getBookingPublik');
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    $routes->get('barangkategori', 'BarangKategori::index');
});

// Routes utama
$routes->get('user/tanah', 'User\Tanah::kelompokTanah');
$routes->get('user/tanah/kelompoktanah', 'User\Tanah::kelompokTanah');
$routes->get('user/tanah/kelompoktanah/(:segment)', 'User\Tanah::kelompokDetail/$1');

// CRUD
$routes->match(['GET', 'POST'], 'user/tanah/tambah', 'User\Tanah::tambahTanah');

// Import/Export
$routes->post('user/tanah/importFromApi', 'User\Tanah::importFromApi', ['filter' => 'login']);
$routes->post('user/tanah/resetData', 'User\Tanah::resetData', ['filter' => 'login']);
$routes->get('user/tanah/exportTanahList/(:segment)', 'User\Tanah::exportTanahList/$1', ['filter' => 'login']);

// Routes untuk Peralatan dan Mesin
// Halaman utama Peralatan dan Mesin
$routes->get('user/barang/peralatandanmesin', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::index');

// 3.01 Alat Besar
// Routes untuk Alat Besar Darat - CRUD Lengkap
// Path controller: app\Controllers\User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat.php
// Routes untuk Alat Besar Darat - CRUD Lengkap + Import
// Path controller: app\Controllers\User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat.php
// 3.01 Alat Besar
// Routes untuk Alat Besar Darat - CRUD Lengkap
// Path controller: app\Controllers\User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat.php

// 3.01 Alat Besar
// Routes untuk Alat Besar Darat - CRUD Lengkap
// Path controller: app\Controllers\User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat.php
// KATEGORI ALAT BESAR (VIEW UTAMA SEMUA SUB-KATEGORI)
$routes->get('user/barang/peralatandanmesin/alatbesar/kelompokalatbesar', 'User\Barang\PeralatanDanMesin\AlatBesar\KelompokAlatBesar::index');

// ========== ROUTES UNTUK ALAT BESAR (TERPUSAT) ==========

// Routes melalui PeralatanDanMesin (untuk redirect)
$routes->get('user/barang/peralatandanmesin/kelompokalatbesar', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::kelompokalatbesar');
$routes->get('user/barang/peralatandanmesin/kelompokalatbesar/(:segment)', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::kelompokalatbesar/$1');

// Routes langsung ke AlatBesar controller (yang sebenarnya memproses data)
// 1. Dashboard Alat Besar - Menampilkan data dari API
$routes->get('user/barang/peralatandanmesin/alatbesar/dashboard', 'User\Barang\PeralatanDanMesin\AlatBesar::dashboard');

// 2. Kelompok Alat Besar - Overview semua kategori (tanpa parameter)
$routes->get('user/barang/peralatandanmesin/alatbesar/kelompokalatbesar', 'User\Barang\PeralatanDanMesin\AlatBesar::kelompokAlatBesar');

// 3. Kelompok Detail - Menampilkan data per kategori dengan parameter
$routes->get('user/barang/peralatandanmesin/alatbesar/kelompokalatbesar/(:segment)', 'User\Barang\PeralatanDanMesin\AlatBesar::kelompokDetail/$1');

// 4. Form Tambah Alat Besar - POST handler untuk form tambah manual
$routes->post('user/barang/peralatandanmesin/alatbesar/tambah', 'User\Barang\PeralatanDanMesin\AlatBesar::tambah');

// 5. Import dari API - POST handler untuk import/sync data dari API
$routes->post('user/barang/peralatandanmesin/alatbesar/importFromApi', 'User\Barang\PeralatanDanMesin\AlatBesar::importFromApi');

// 6. Reset Data - POST handler untuk menghapus semua data
$routes->post('user/barang/peralatandanmesin/alatbesar/resetData', 'User\Barang\PeralatanDanMesin\AlatBesar::resetData');

// 7. Export Data - GET handler untuk export CSV per kategori
$routes->get('user/barang/peralatandanmesin/alatbesar/exportAlatBesarList/(:segment)', 'User\Barang\PeralatanDanMesin\AlatBesar::exportAlatBesarList/$1');

// 8. Statistik - GET handler untuk menampilkan statistik database
$routes->get('user/barang/peralatandanmesin/alatbesar/stats', 'User\Barang\PeralatanDanMesin\AlatBesar::stats');

// ========== ROUTES ALTERNATIF (UNTUK BACKWARD COMPATIBILITY) ==========
// Jika masih ada link lama yang mengarah ke controller terpisah, redirect ke yang baru

// Redirect dari route lama AlatBesarDarat ke yang baru
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesardarat', 'User\Barang\PeralatanDanMesin\AlatBesar::kelompokDetail/ALAT BESAR DARAT');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesardarat/(:any)', 'User\Barang\PeralatanDanMesin\AlatBesar::kelompokDetail/ALAT BESAR DARAT');

// Redirect dari route lama AlatBantu ke yang baru  
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbantu', 'User\Barang\PeralatanDanMesin\AlatBesar::kelompokDetail/ALAT BANTU');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbantu/(:any)', 'User\Barang\PeralatanDanMesin\AlatBesar::kelompokDetail/ALAT BANTU');

// Redirect dari route lama AlatBesarApung ke yang baru
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesarapung', 'User\Barang\PeralatanDanMesin\AlatBesar::kelompokDetail/ALAT BESAR APUNG');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesarapung/(:any)', 'User\Barang\PeralatanDanMesin\AlatBesar::kelompokDetail/ALAT BESAR APUNG');
// CRUD ROUTES (yang sudah ada)
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesardarat', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::index');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesardarat/detail/(:num)', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::detail/$1');
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/alatbesar/alatbesardarat/tambah', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::tambah');
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/alatbesar/alatbesardarat/edit/(:num)', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::edit/$1');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesardarat/hapus/(:num)', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::hapus/$1');

// IMPORT & EXPORT ROUTES (tambahkan ini)
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesardarat', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::index', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/alatbesar/alatbesardarat/tambah', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::tambah', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/alatbesar/alatbesardarat/importFromApi', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::importFromApi', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/alatbesar/alatbesardarat/resetData', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::resetData', ['filter' => 'login']);

$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesardarat/export', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::exportAlatBesarList');

// DEBUG & STATS ROUTES (opsional, bisa ditambahkan)
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/alatbesar/alatbesardarat/debug-form', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::debugForm');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesardarat/stats', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::stats');

// ROUTES UNTUK URL PENDEK (tambahkan ini untuk mengatasi 404)
$routes->get('user/alatbesardarat', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::index');
$routes->get('user/alatbesardarat/kelompokalatbesardarat', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::kelompokalatbesardarat');
$routes->get('user/alatbesardarat/kelompokalatbesardarat/(:segment)', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::kelompokDetail/$1');

// PARENT ROUTES (yang sudah ada)
$routes->get('user/barang/peralatandanmesin/alatbesar', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatbesar');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesardarat/test-api', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarDarat::testApi');

//ALAT BANTU
// ROUTES ALAT BANTU - MASUK DALAM KATEGORI ALAT BESAR

// CRUD ROUTES
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbantu', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::index');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbantu/detail/(:num)', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::detail/$1');
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/alatbesar/alatbantu/tambah', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::tambah');
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/alatbesar/alatbantu/edit/(:num)', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::edit/$1');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbantu/hapus/(:num)', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::hapus/$1');

// IMPORT & EXPORT ROUTES
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbantu', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::index', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/alatbesar/alatbantu/tambah', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::tambah', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/alatbesar/alatbantu/importFromApi', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::importFromApi', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/alatbesar/alatbantu/resetData', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::resetData', ['filter' => 'login']);

$routes->get('user/barang/peralatandanmesin/alatbesar/alatbantu/export', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::exportAlatBantuList');

// DEBUG & STATS ROUTES
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbantu/testDatabase', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::testDatabase');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbantu/debug', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::debug');
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/alatbesar/alatbantu/debug-form', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::debugForm');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbantu/stats', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::stats');

// ROUTES UNTUK URL PENDEK
$routes->get('user/alatbantu', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::index');
$routes->get('user/alatbantu/kelompokalatbantu', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::kelompokalatbantu');
$routes->get('user/alatbantu/kelompokalatbantu/(:segment)', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBantu::kelompokDetail/$1');


//ALAT BESAR APUNG
// ROUTES ALAT BESAR APUNG - MASUK DALAM KATEGORI ALAT BESAR

// CRUD ROUTES
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesarapung', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::index');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesarapung/detail/(:num)', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::detail/$1');
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/alatbesar/alatbesarapung/tambah', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::tambah');
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/alatbesar/alatbesarapung/edit/(:num)', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::edit/$1');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesarapung/hapus/(:num)', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::hapus/$1');

// IMPORT & EXPORT ROUTES
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesarapung', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::index', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/alatbesar/alatbesarapung/tambah', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::tambah', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/alatbesar/alatbesarapung/importFromApi', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::importFromApi', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/alatbesar/alatbesarapung/resetData', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::resetData', ['filter' => 'login']);

$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesarapung/export', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::exportAlatBesarApungList');

// DEBUG & STATS ROUTES
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesarapung/testDatabase', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::testDatabase');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesarapung/debug', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::debug');
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/alatbesar/alatbesarapung/debug-form', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::debugForm');
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesarapung/stats', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::stats');

// ROUTES UNTUK URL PENDEK
$routes->get('user/alatbesarapung', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::index');
$routes->get('user/alatbesarapung/kelompokalatbesarapung', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::kelompokalatbesarapung');
$routes->get('user/alatbesarapung/kelompokalatbesarapung/(:segment)', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::kelompokDetail/$1');

// TEST API ROUTE (opsional)
$routes->get('user/barang/peralatandanmesin/alatbesar/alatbesarapung/test-api', 'User\Barang\PeralatanDanMesin\AlatBesar\AlatBesarApung::testApi');
                                                            //ALAT ANGKUTAN
// 3.02 Alat Angkutan
$routes->get('user/barang/peralatandanmesin/alatangkutan', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatangkutan');
$routes->get('user/barang/peralatandanmesin/alatangkutan/daratbermotor', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatangkutandaratbermotor');
$routes->get('user/barang/peralatandanmesin/alatangkutan/darattakbermotor', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatangkutandarattakbermotor');
$routes->get('user/barang/peralatandanmesin/alatangkutan/apungbermotor', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatangkutanapungbermotor');
$routes->get('user/barang/peralatandanmesin/alatangkutan/apungtakbermotor', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatangkutanapungtakbermotor');
$routes->get('user/barang/peralatandanmesin/alatangkutan/bermotorudara', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatangkutanbermotorudara');

// ========== ROUTES UNTUK ALAT ANGKUTAN (TERPUSAT) ==========

// Routes melalui PeralatanDanMesin (untuk redirect)
$routes->get('user/barang/peralatandanmesin/kelompokalatangkutan', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::kelompokalatangkutan');
$routes->get('user/barang/peralatandanmesin/kelompokalatangkutan/(:segment)', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::kelompokalatangkutan/$1');

// Routes langsung ke AlatAngkutan controller (yang sebenarnya memproses data)
// 1. Dashboard Alat Angkutan - Menampilkan data dari API
$routes->get('user/barang/peralatandanmesin/alatangkutan/dashboard', 'User\Barang\PeralatanDanMesin\AlatAngkutan::dashboard');

// 2. Kelompok Alat Angkutan - Overview semua kategori (tanpa parameter)
$routes->get('user/barang/peralatandanmesin/alatangkutan/kelompokalatangkutan', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokAlatAngkutan');

// 3. Kelompok Detail - Menampilkan data per kategori dengan parameter
$routes->get('user/barang/peralatandanmesin/alatangkutan/kelompokalatangkutan/(:segment)', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokDetail/$1');

// 4. Form Tambah Alat Angkutan - POST handler untuk form tambah manual
$routes->post('user/barang/peralatandanmesin/alatangkutan/tambah', 'User\Barang\PeralatanDanMesin\AlatAngkutan::tambah');

// 5. Import dari API - POST handler untuk import/sync data dari API
$routes->post('user/barang/peralatandanmesin/alatangkutan/importFromApi', 'User\Barang\PeralatanDanMesin\AlatAngkutan::importFromApi');

// 6. Reset Data - POST handler untuk menghapus semua data
$routes->post('user/barang/peralatandanmesin/alatangkutan/resetData', 'User\Barang\PeralatanDanMesin\AlatAngkutan::resetData');

// 7. Export Data - GET handler untuk export CSV per kategori
$routes->get('user/barang/peralatandanmesin/alatangkutan/exportAlatAngkutanList/(:segment)', 'User\Barang\PeralatanDanMesin\AlatAngkutan::exportAlatAngkutanList/$1');

// 8. Statistik - GET handler untuk menampilkan statistik database
$routes->get('user/barang/peralatandanmesin/alatangkutan/stats', 'User\Barang\PeralatanDanMesin\AlatAngkutan::stats');

// 9. Test API - GET handler untuk test koneksi API
$routes->get('user/barang/peralatandanmesin/alatangkutan/test-api', 'User\Barang\PeralatanDanMesin\AlatAngkutan::testApi');

// ========== ROUTES ALTERNATIF (UNTUK BACKWARD COMPATIBILITY) ==========
// Jika masih ada link lama yang mengarah ke controller terpisah, redirect ke yang baru

// Redirect dari route lama Darat Bermotor ke yang baru
$routes->get('user/barang/peralatandanmesin/alatangkutan/daratbermotor', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokDetail/ALAT ANGKUTAN DARAT BERMOTOR');
$routes->get('user/barang/peralatandanmesin/alatangkutan/daratbermotor/(:any)', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokDetail/ALAT ANGKUTAN DARAT BERMOTOR');

// Redirect dari route lama Darat Tak Bermotor ke yang baru  
$routes->get('user/barang/peralatandanmesin/alatangkutan/darattakbermotor', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokDetail/ALAT ANGKUTAN DARAT TAK BERMOTOR');
$routes->get('user/barang/peralatandanmesin/alatangkutan/darattakbermotor/(:any)', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokDetail/ALAT ANGKUTAN DARAT TAK BERMOTOR');

// Redirect dari route lama Apung Bermotor ke yang baru
$routes->get('user/barang/peralatandanmesin/alatangkutan/apungbermotor', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokDetail/ALAT ANGKUTAN APUNG BERMOTOR');
$routes->get('user/barang/peralatandanmesin/alatangkutan/apungbermotor/(:any)', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokDetail/ALAT ANGKUTAN APUNG BERMOTOR');

// Redirect dari route lama Apung Tak Bermotor ke yang baru
$routes->get('user/barang/peralatandanmesin/alatangkutan/apungtakbermotor', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokDetail/ALAT ANGKUTAN APUNG TAK BERMOTOR');
$routes->get('user/barang/peralatandanmesin/alatangkutan/apungtakbermotor/(:any)', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokDetail/ALAT ANGKUTAN APUNG TAK BERMOTOR');

// Redirect dari route lama Bermotor Udara ke yang baru
$routes->get('user/barang/peralatandanmesin/alatangkutan/bermotorudara', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokDetail/ALAT ANGKUTAN BERMOTOR UDARA');
$routes->get('user/barang/peralatandanmesin/alatangkutan/bermotorudara/(:any)', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokDetail/ALAT ANGKUTAN BERMOTOR UDARA');

// ROUTES UNTUK URL PENDEK
$routes->get('user/alatangkutan', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokAlatAngkutan');
$routes->get('user/alatangkutan/kelompokalatangkutan', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokAlatAngkutan');
$routes->get('user/alatangkutan/kelompokalatangkutan/(:segment)', 'User\Barang\PeralatanDanMesin\AlatAngkutan::kelompokDetail/$1');
// 3.03 Alat Bengkel dan Alat Ukur
$routes->get('user/barang/peralatandanmesin/alatbengkelukur', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatbengkelukur');
$routes->get('user/barang/peralatandanmesin/alatbengkelukur/bengkelbermesin', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatbengkelbermesin');
$routes->get('user/barang/peralatandanmesin/alatbengkelukur/bengkeltakbermesin', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatbengkeltakbermesin');
$routes->get('user/barang/peralatandanmesin/alatbengkelukur/alatukur', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatukur');

// 3.04 Alat Pertanian
$routes->get('user/barang/peralatandanmesin/alatpertanian', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatpertanian');
$routes->get('user/barang/peralatandanmesin/alatpertanian/alatpengolahan', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatpengolahan');

// 3.05 Alat Kantor & Rumah Tangga
$routes->get('user/barang/peralatandanmesin/alatkantorrt', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatkantorrt');
$routes->get('user/barang/peralatandanmesin/alatkantorrt/alatkantor', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatkantor');
$routes->get('user/barang/peralatandanmesin/alatkantorrt/alatrumahTangga', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatrumahTangga');

// 3.06 Alat Studio, Komunikasi dan Pemancar
$routes->get('user/barang/peralatandanmesin/alatstudiokomunikasi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatstudiokomunikasi');
$routes->get('user/barang/peralatandanmesin/alatstudiokomunikasi/alatstudio', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatstudio');
$routes->get('user/barang/peralatandanmesin/alatstudiokomunikasi/alatkomunikasi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatkomunikasi');
$routes->get('user/barang/peralatandanmesin/alatstudiokomunikasi/peralatanpemancar', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::peralatanpemancar');
$routes->get('user/barang/peralatandanmesin/alatstudiokomunikasi/peralatankomunikasiNavigasi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::peralatankomunikasiNavigasi');

// 3.07 Alat Kedokteran dan Kesehatan
$routes->get('user/barang/peralatandanmesin/alatkedokterankesehatan', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatkedokterankesehatan');
$routes->get('user/barang/peralatandanmesin/alatkedokterankesehatan/alatkedokteran', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatkedokteran');
$routes->get('user/barang/peralatandanmesin/alatkedokterankesehatan/alatkesehatanumum', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatkesehatanumum');

// 3.08 Alat Laboratorium
$routes->get('user/barang/peralatandanmesin/alatlaboratorium', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatlaboratorium');
$routes->get('user/barang/peralatandanmesin/alatlaboratorium/unitalatlaboratorium', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::unitalatlaboratorium');
$routes->get('user/barang/peralatandanmesin/alatlaboratorium/unitalatlabkimiapelajar', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::unitalatlabkimiapelajar');
$routes->get('user/barang/peralatandanmesin/alatlaboratorium/alatlabfisikanuklir', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatlabfisikanuklir');
$routes->get('user/barang/peralatandanmesin/alatlaboratorium/alatproteksiRadiasi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatproteksiRadiasi');
$routes->get('user/barang/peralatandanmesin/alatlaboratorium/radiationApplication', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::radiationApplication');
$routes->get('user/barang/peralatandanmesin/alatlaboratorium/alatlablingkunganhidup', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatlablingkunganhidup');
$routes->get('user/barang/peralatandanmesin/alatlaboratorium/peralatanlabhydrodinamica', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::peralatanlabhydrodinamica');
$routes->get('user/barang/peralatandanmesin/alatlaboratorium/alatlabstandarisasikalibrasi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatlabstandarisasikalibrasi');

// 3.09 Alat Persenjataan
$routes->get('user/barang/peralatandanmesin/alatpersenjataan', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatpersenjataan');
$routes->get('user/barang/peralatandanmesin/alatpersenjataan/senjataapi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::senjataapi');
$routes->get('user/barang/peralatandanmesin/alatpersenjataan/persenjataannonsenjataapi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::persenjataannonsenjataapi');
$routes->get('user/barang/peralatandanmesin/alatpersenjataan/senjatasinar', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::senjatasinar');
$routes->get('user/barang/peralatandanmesin/alatpersenjataan/alatkhususkepolisian', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatkhususkepolisian');

// 3.10 Komputer
// 3.10 Komputer - ROUTES LENGKAP
$routes->get('user/barang/peralatandanmesin/komputer', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::komputer');
$routes->get('user/barang/peralatandanmesin/komputer/komputerunit', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::komputerunit');
$routes->get('user/barang/peralatandanmesin/komputer/peralatankomputer', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::peralatankomputer');

// ========== ROUTES UNTUK KOMPUTER (TERPUSAT) ==========

// Routes melalui PeralatanDanMesin (untuk redirect)
$routes->get('user/barang/peralatandanmesin/kelompokkomputer', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::kelompokkomputer');
$routes->get('user/barang/peralatandanmesin/kelompokkomputer/(:segment)', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::kelompokkomputer/$1');

// Routes langsung ke Komputer controller (yang sebenarnya memproses data)
// 1. Dashboard Komputer - Menampilkan data dari API
$routes->get('user/barang/peralatandanmesin/komputer/dashboard', 'User\Barang\PeralatanDanMesin\Komputer::dashboard');

// 2. Kelompok Komputer - Overview semua kategori (tanpa parameter)
$routes->get('user/barang/peralatandanmesin/komputer/kelompokkomputer', 'User\Barang\PeralatanDanMesin\Komputer::kelompokKomputer');

// 3. Kelompok Detail - Menampilkan data per kategori dengan parameter
$routes->get('user/barang/peralatandanmesin/komputer/kelompokkomputer/(:segment)', 'User\Barang\PeralatanDanMesin\Komputer::kelompokDetail/$1');

// 4. Form Tambah Komputer - POST handler untuk form tambah manual
$routes->post('user/barang/peralatandanmesin/komputer/tambah', 'User\Barang\PeralatanDanMesin\Komputer::tambah');

// 5. Import dari API - POST handler untuk import/sync data dari API
$routes->post('user/barang/peralatandanmesin/komputer/importFromApi', 'User\Barang\PeralatanDanMesin\Komputer::importFromApi');

// 6. Reset Data - POST handler untuk menghapus semua data
$routes->post('user/barang/peralatandanmesin/komputer/resetData', 'User\Barang\PeralatanDanMesin\Komputer::resetData');

// 7. Export Data - GET handler untuk export CSV per kategori
$routes->get('user/barang/peralatandanmesin/komputer/exportKomputerList/(:segment)', 'User\Barang\PeralatanDanMesin\Komputer::exportKomputerList/$1');

// 8. Statistik - GET handler untuk menampilkan statistik database
$routes->get('user/barang/peralatandanmesin/komputer/stats', 'User\Barang\PeralatanDanMesin\Komputer::stats');

// ========== ROUTES ALTERNATIF (UNTUK BACKWARD COMPATIBILITY) ==========
// Jika masih ada link lama yang mengarah ke controller terpisah, redirect ke yang baru

// Redirect dari route lama KomputerUnit ke yang baru
$routes->get('user/barang/peralatandanmesin/komputer/komputerunit', 'User\Barang\PeralatanDanMesin\Komputer::kelompokDetail/KOMPUTER UNIT');
$routes->get('user/barang/peralatandanmesin/komputer/komputerunit/(:any)', 'User\Barang\PeralatanDanMesin\Komputer::kelompokDetail/KOMPUTER UNIT');

// Redirect dari route lama PeralatanKomputer ke yang baru  
$routes->get('user/barang/peralatandanmesin/komputer/peralatankomputer', 'User\Barang\PeralatanDanMesin\Komputer::kelompokDetail/PERALATAN KOMPUTER');
$routes->get('user/barang/peralatandanmesin/komputer/peralatankomputer/(:any)', 'User\Barang\PeralatanDanMesin\Komputer::kelompokDetail/PERALATAN KOMPUTER');

// CRUD ROUTES (yang sudah ada)
$routes->get('user/barang/peralatandanmesin/komputer/komputerunit', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::index');
$routes->get('user/barang/peralatandanmesin/komputer/komputerunit/detail/(:num)', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::detail/$1');
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/komputer/komputerunit/tambah', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::tambah');
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/komputer/komputerunit/edit/(:num)', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::edit/$1');
$routes->get('user/barang/peralatandanmesin/komputer/komputerunit/hapus/(:num)', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::hapus/$1');

// IMPORT & EXPORT ROUTES (tambahkan ini)
$routes->get('user/barang/peralatandanmesin/komputer/komputerunit', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::index', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/komputer/komputerunit/tambah', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::tambah', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/komputer/komputerunit/importFromApi', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::importFromApi', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/komputer/komputerunit/resetData', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::resetData', ['filter' => 'login']);

$routes->get('user/barang/peralatandanmesin/komputer/komputerunit/export', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::exportKomputerList');

// DEBUG & STATS ROUTES (opsional, bisa ditambahkan)
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/komputer/komputerunit/debug-form', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::debugForm');
$routes->get('user/barang/peralatandanmesin/komputer/komputerunit/stats', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::stats');

// ROUTES UNTUK URL PENDEK (tambahkan ini untuk mengatasi 404)
$routes->get('user/komputerunit', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::index');
$routes->get('user/komputerunit/kelompokkomputerunit', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::kelompokkomputerunit');
$routes->get('user/komputerunit/kelompokkomputerunit/(:segment)', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::kelompokDetail/$1');

// PARENT ROUTES (yang sudah ada)
$routes->get('user/barang/peralatandanmesin/komputer', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::komputer');
$routes->get('user/barang/peralatandanmesin/komputer/komputerunit/test-api', 'User\Barang\PeralatanDanMesin\Komputer\KomputerUnit::testApi');

//PERALATAN KOMPUTER
// ROUTES PERALATAN KOMPUTER - MASUK DALAM KATEGORI KOMPUTER

// CRUD ROUTES
$routes->get('user/barang/peralatandanmesin/komputer/peralatankomputer', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::index');
$routes->get('user/barang/peralatandanmesin/komputer/peralatankomputer/detail/(:num)', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::detail/$1');
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/komputer/peralatankomputer/tambah', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::tambah');
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/komputer/peralatankomputer/edit/(:num)', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::edit/$1');
$routes->get('user/barang/peralatandanmesin/komputer/peralatankomputer/hapus/(:num)', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::hapus/$1');

// IMPORT & EXPORT ROUTES
$routes->get('user/barang/peralatandanmesin/komputer/peralatankomputer', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::index', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/komputer/peralatankomputer/tambah', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::tambah', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/komputer/peralatankomputer/importFromApi', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::importFromApi', ['filter' => 'login']);
$routes->post('user/barang/peralatandanmesin/komputer/peralatankomputer/resetData', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::resetData', ['filter' => 'login']);

$routes->get('user/barang/peralatandanmesin/komputer/peralatankomputer/export', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::exportPeralatanKomputerList');

// DEBUG & STATS ROUTES
$routes->get('user/barang/peralatandanmesin/komputer/peralatankomputer/testDatabase', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::testDatabase');
$routes->get('user/barang/peralatandanmesin/komputer/peralatankomputer/debug', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::debug');
$routes->match(['GET', 'POST'], 'user/barang/peralatandanmesin/komputer/peralatankomputer/debug-form', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::debugForm');
$routes->get('user/barang/peralatandanmesin/komputer/peralatankomputer/stats', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::stats');

// ROUTES UNTUK URL PENDEK
$routes->get('user/peralatankomputer', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::index');
$routes->get('user/peralatankomputer/kelompokperalatankomputer', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::kelompokperalatankomputer');
$routes->get('user/peralatankomputer/kelompokperalatankomputer/(:segment)', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::kelompokDetail/$1');

// TEST API ROUTE (opsional)
$routes->get('user/barang/peralatandanmesin/komputer/peralatankomputer/test-api', 'User\Barang\PeralatanDanMesin\Komputer\PeralatanKomputer::testApi');
// 3.11 Alat Eksplorasi
$routes->get('user/barang/peralatandanmesin/alateksplorasi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alateksplorasi');
$routes->get('user/barang/peralatandanmesin/alateksplorasi/alateksplorasitopografi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alateksplorasitopografi');
$routes->get('user/barang/peralatandanmesin/alateksplorasi/alateksplorasigeofisika', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alateksplorasigeofisika');

// 3.12 Alat Pengeboran
$routes->get('user/barang/peralatandanmesin/alatpengeboran', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatpengeboran');
$routes->get('user/barang/peralatandanmesin/alatpengeboran/alatpengeboran_mesin', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatpengeboran_mesin');
$routes->get('user/barang/peralatandanmesin/alatpengeboran/alatpengeboran_nonmesin', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatpengeboran_nonmesin');

// 3.13 Alat Produksi, Pengolahan dan Pemurnian
$routes->get('user/barang/peralatandanmesin/alatproduksipengolahan', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatproduksipengolahan');
$routes->get('user/barang/peralatandanmesin/alatproduksipengolahan/sumur', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::sumur');
$routes->get('user/barang/peralatandanmesin/alatproduksipengolahan/produksi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::produksi');
$routes->get('user/barang/peralatandanmesin/alatproduksipengolahan/pengolahanpemurnian', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::pengolahanpemurnian');

// 3.14 Alat Bantu Eksplorasi
$routes->get('user/barang/peralatandanmesin/alatbantueksplorasi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatbantueksplorasi');
$routes->get('user/barang/peralatandanmesin/alatbantueksplorasi/alatbantueksplorasi_detail', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatbantueksplorasi_detail');
$routes->get('user/barang/peralatandanmesin/alatbantueksplorasi/alatbantuproduksi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatbantuproduksi');

// 3.15 Alat Keselamatan Kerja
$routes->get('user/barang/peralatandanmesin/alatkeselamatankerja', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatkeselamatankerja');
$routes->get('user/barang/peralatandanmesin/alatkeselamatankerja/alatdeteksi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatdeteksi');
$routes->get('user/barang/peralatandanmesin/alatkeselamatankerja/alatpelindung', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatpelindung');
$routes->get('user/barang/peralatandanmesin/alatkeselamatankerja/alatsar', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatsar');
$routes->get('user/barang/peralatandanmesin/alatkeselamatankerja/alatkerjaPenerbangan', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatkerjaPenerbangan');

// 3.16 Alat Peraga
$routes->get('user/barang/peralatandanmesin/alatperaga', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatperaga');
$routes->get('user/barang/peralatandanmesin/alatperaga/alatperagapelatihanpercontohan', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::alatperagapelatihanpercontohan');

// 3.17 Peralatan Profesi/Produksi
$routes->get('user/barang/peralatandanmesin/peralatanprofesiproduksi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::peralatanprofesiproduksi');
$routes->get('user/barang/peralatandanmesin/peralatanprofesiproduksi/unitperalatanprosesproduksi', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::unitperalatanprosesproduksi');

// 3.18 Rambu-Rambu
$routes->get('user/barang/peralatandanmesin/ramburambu', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::ramburambu');
$routes->get('user/barang/peralatandanmesin/ramburambu/rambulalulintas_darat', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::rambulalulintas_darat');
$routes->get('user/barang/peralatandanmesin/ramburambu/rambulalulintas_udara', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::rambulalulintas_udara');

// 3.19 Peralatan Olahraga
$routes->get('user/barang/peralatandanmesin/peralatanolahraga', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::peralatanolahraga');
$routes->get('user/barang/peralatandanmesin/peralatanolahraga/peralatanolahraga_detail', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::peralatanolahraga_detail');

// ========== ROUTES UNTUK GEDUNG DAN BANGUNAN ==========
// Halaman utama Gedung dan Bangunan
$routes->get('user/barang/gedungdanbangunan', 'User\Barang\GedungDanBangunan\GedungDanBangunan::index');

// ========== 4.01 BANGUNAN GEDUNG ==========
$routes->get('user/barang/gedungdanbangunan/bangunangedung', 'User\Barang\GedungDanBangunan\GedungDanBangunan::bangunangedung');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/bangunangedungtempatkerja', 'User\Barang\GedungDanBangunan\GedungDanBangunan::bangunangedungtempatkerja');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/bangunangedungtempattinggal', 'User\Barang\GedungDanBangunan\GedungDanBangunan::bangunangedungtempattinggal');

//BANGUNAN GEDUNG
// 4.01 Bangunan Gedung
$routes->get('user/barang/gedungdanbangunan/bangunangedung', 'User\Barang\GedungDanBangunan\GedungDanBangunan::bangunangedung');

// ========== ROUTES UNTUK BANGUNAN GEDUNG (TERPUSAT) ==========
// Routes langsung ke BangunanGedung controller (yang sebenarnya memproses data)
$routes->get('user/barang/gedungdanbangunan/bangunangedung/dashboard', 'User\Barang\GedungDanBangunan\BangunanGedung::dashboard');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokBangunanGedung');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung/(:segment)/(:segment)', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokDetailSegments/$1/$2');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung/(:any)', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokDetail/$1');

// CRUD Operations
$routes->post('user/barang/gedungdanbangunan/bangunangedung/tambah', 'User\Barang\GedungDanBangunan\BangunanGedung::tambah');
$routes->post('user/barang/gedungdanbangunan/bangunangedung/importFromApi', 'User\Barang\GedungDanBangunan\BangunanGedung::importFromApi');
$routes->post('user/barang/gedungdanbangunan/bangunangedung/resetData', 'User\Barang\GedungDanBangunan\BangunanGedung::resetData');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/exportBangunanGedungList/(:segment)', 'User\Barang\GedungDanBangunan\BangunanGedung::exportBangunanGedungList/$1');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/stats', 'User\Barang\GedungDanBangunan\BangunanGedung::stats');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/test-api', 'User\Barang\GedungDanBangunan\BangunanGedung::testApi');

// ========== ROUTES ALTERNATIF (BACKWARD COMPATIBILITY) ==========
// Redirect dari route lama ke yang baru - LETAKKAN DI AKHIR
$routes->get('user/barang/gedungdanbangunan/bangunangedung/tempatkerja', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokDetail/BANGUNAN GEDUNG TEMPAT KERJA');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/tempattinggal', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokDetail/BANGUNAN GEDUNG TEMPAT TINGGAL');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/candi', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokDetail/CANDI');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/tuguperingatan', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokDetail/TUGU PERINGATAN');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/prasasti', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokDetail/PRASASTI');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/monumen', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokDetail/MONUMEN');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/menaraperambuan', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokDetail/BANGUNAN MENARA PERAMBUAN');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/menarapengawas', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokDetail/MENARA PENGAWAS');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/menara', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokDetail/MENARA');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/tugubatas', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokDetail/TUGU BATAS');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/tandabatas', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokDetail/TANDA BATAS');
$routes->get('user/barang/gedungdanbangunan/bangunangedung/tugukontrol', 'User\Barang\GedungDanBangunan\BangunanGedung::kelompokDetail/TUGU TITIK KONTROL');

// ========== 4.02 MONUMEN ==========
// ========== 4.02 MONUMEN ==========
// Routes dari GedungDanBangunan controller (redirect)
$routes->get('user/barang/gedungdanbangunan/monumen', 'User\Barang\GedungDanBangunan\GedungDanBangunan::monumen');
$routes->get('user/barang/gedungdanbangunan/monumen/candituguperingatan', 'User\Barang\GedungDanBangunan\GedungDanBangunan::candituguperingatan');

// ========== ROUTES UNTUK MONUMEN (TERPUSAT) ==========
// Routes langsung ke Monumen controller (yang sebenarnya memproses data)
$routes->get('user/barang/gedungdanbangunan/monumen/dashboard', 'User\Barang\GedungDanBangunan\Monumen::dashboard');
$routes->get('user/barang/gedungdanbangunan/monumen/kelompokmonumen', 'User\Barang\GedungDanBangunan\Monumen::kelompokMonumen');

// CRUD Operations untuk Monumen
$routes->post('user/barang/gedungdanbangunan/monumen/tambah', 'User\Barang\GedungDanBangunan\Monumen::tambah');
$routes->post('user/barang/gedungdanbangunan/monumen/importFromApi', 'User\Barang\GedungDanBangunan\Monumen::importFromApi');
$routes->post('user/barang/gedungdanbangunan/monumen/resetData', 'User\Barang\GedungDanBangunan\Monumen::resetData');
$routes->get('user/barang/gedungdanbangunan/monumen/exportMonumenList/(:segment)', 'User\Barang\GedungDanBangunan\Monumen::exportMonumenList/$1');
$routes->get('user/barang/gedungdanbangunan/monumen/stats', 'User\Barang\GedungDanBangunan\Monumen::stats');
$routes->get('user/barang/gedungdanbangunan/monumen/test-api', 'User\Barang\GedungDanBangunan\Monumen::testApi');
$routes->get('user/barang/gedungdanbangunan/monumen/search', 'User\Barang\GedungDanBangunan\Monumen::search');

// ========== 4.03 BANGUNAN MENARA ==========
$routes->get('user/barang/gedungdanbangunan/bangunanmenara', 'User\Barang\GedungDanBangunan\GedungDanBangunan::bangunanmenara');
$routes->get('user/barang/gedungdanbangunan/bangunanmenara/bangunanmenaraperambuan', 'User\Barang\GedungDanBangunan\GedungDanBangunan::bangunanmenaraperambuan');

// ========== ROUTES UNTUK TUGU TITIK KONTROL/PASTI (4.04) ==========
// Routes dari GedungDanBangunan controller (redirect)
$routes->get('user/barang/gedungdanbangunan/tugutitikkontrol', 'User\Barang\GedungDanBangunan\GedungDanBangunan::tugutitikkontrol');
$routes->get('user/barang/gedungdanbangunan/tugutitikkontrol/tugutandabatas', 'User\Barang\GedungDanBangunan\GedungDanBangunan::tugutandabatas');

// ========== ROUTES UNTUK TUGU TITIK KONTROL (TERPUSAT) ==========
// Routes langsung ke TuguTitikKontrol controller (yang sebenarnya memproses data)
$routes->get('user/barang/gedungdanbangunan/tugutitikkontrol/dashboard', 'User\Barang\GedungDanBangunan\TuguTitikKontrol::dashboard');
$routes->get('user/barang/gedungdanbangunan/tugutitikkontrol/kelompoktugutitikkontrol', 'User\Barang\GedungDanBangunan\TuguTitikKontrol::kelompokTuguTitikKontrol');

// CRUD Operations untuk TuguTitikKontrol
$routes->post('user/barang/gedungdanbangunan/tugutitikkontrol/tambah', 'User\Barang\GedungDanBangunan\TuguTitikKontrol::tambah');
$routes->post('user/barang/gedungdanbangunan/tugutitikkontrol/importFromApi', 'User\Barang\GedungDanBangunan\TuguTitikKontrol::importFromApi');
$routes->post('user/barang/gedungdanbangunan/tugutitikkontrol/resetData', 'User\Barang\GedungDanBangunan\TuguTitikKontrol::resetData');
$routes->get('user/barang/gedungdanbangunan/tugutitikkontrol/exportTuguTitikKontrolList/(:segment)', 'User\Barang\GedungDanBangunan\TuguTitikKontrol::exportTuguTitikKontrolList/$1');
$routes->get('user/barang/gedungdanbangunan/tugutitikkontrol/stats', 'User\Barang\GedungDanBangunan\TuguTitikKontrol::stats');
$routes->get('user/barang/gedungdanbangunan/tugutitikkontrol/test-api', 'User\Barang\GedungDanBangunan\TuguTitikKontrol::testApi');
$routes->get('user/barang/gedungdanbangunan/tugutitikkontrol/search', 'User\Barang\GedungDanBangunan\TuguTitikKontrol::search');


// ========== ROUTES UNTUK JALAN, IRIGASI DAN JARINGAN ==========
// Halaman utama Jalan, Irigasi dan Jaringan
$routes->get('user/barang/jalanirigasijaringan', 'User\Barang\JalanIrigasiJaringan\JalanIrigasiJaringan::index');

// ========== 5.01 JALAN DAN JEMBATAN (TERPUSAT) ==========
// Route untuk halaman utama Jalan dan Jembatan
$routes->get('user/barang/jalanirigasijaringan/jalandanjembatan', 'User\Barang\JalanIrigasiJaringan\JalanIrigasiJaringan::jalandanjembatan');

// Routes langsung ke JalanDanJembatan controller (yang sebenarnya memproses data)
$routes->get('user/barang/jalanirigasijaringan/jalandanjembatan/dashboard', 'User\Barang\JalanIrigasiJaringan\JalanDanJembatan::dashboard');
$routes->get('user/barang/jalanirigasijaringan/jalandanjembatan/kelompokjalandanjembatan', 'User\Barang\JalanIrigasiJaringan\JalanDanJembatan::kelompokJalanDanJembatan');
$routes->get('user/barang/jalanirigasijaringan/jalandanjembatan/kelompokjalandanjembatan/(:any)', 'User\Barang\JalanIrigasiJaringan\JalanDanJembatan::kelompokDetail/$1');

// CRUD Operations untuk JalanDanJembatan
$routes->post('user/barang/jalanirigasijaringan/jalandanjembatan/tambah', 'User\Barang\JalanIrigasiJaringan\JalanDanJembatan::tambah');
$routes->post('user/barang/jalanirigasijaringan/jalandanjembatan/importFromApi', 'User\Barang\JalanIrigasiJaringan\JalanDanJembatan::importFromApi');
$routes->post('user/barang/jalanirigasijaringan/jalandanjembatan/resetData', 'User\Barang\JalanIrigasiJaringan\JalanDanJembatan::resetData');
$routes->get('user/barang/jalanirigasijaringan/jalandanjembatan/exportJalanDanJembatanList/(:segment)', 'User\Barang\JalanIrigasiJaringan\JalanDanJembatan::exportJalanDanJembatanList/$1');
$routes->get('user/barang/jalanirigasijaringan/jalandanjembatan/exportJalanDanJembatanList', 'User\Barang\JalanIrigasiJaringan\JalanDanJembatan::exportJalanDanJembatanList');
$routes->get('user/barang/jalanirigasijaringan/jalandanjembatan/stats', 'User\Barang\JalanIrigasiJaringan\JalanDanJembatan::stats');
$routes->get('user/barang/jalanirigasijaringan/jalandanjembatan/test-api', 'User\Barang\JalanIrigasiJaringan\JalanDanJembatan::testApi');
$routes->get('user/barang/jalanirigasijaringan/jalandanjembatan/search', 'User\Barang\JalanIrigasiJaringan\JalanDanJembatan::search');

// ========== ROUTES ALTERNATIF (BACKWARD COMPATIBILITY) ==========
// Redirect dari route lama ke yang baru - LETAKKAN DI AKHIR
$routes->get('user/barang/jalanirigasijaringan/jalan', 'User\Barang\JalanIrigasiJaringan\JalanDanJembatan::kelompokDetail/JALAN');
$routes->get('user/barang/jalanirigasijaringan/jembatan', 'User\Barang\JalanIrigasiJaringan\JalanDanJembatan::kelompokDetail/JEMBATAN');

// Route untuk Jembatan (nanti)
// $routes->get('user/barang/jalanirigasijaringan/jembatan', 'User\Barang\JalanIrigasiJaringan\Jembatan::index');
//BANGUNAN AIR
// 5.02 Bangunan Air
$routes->get('user/barang/jalanirigasijaringan/bangunanair', 'User\Barang\JalanIrigasiJaringan\JalanIrigasiJaringan::bangunanair');

// ========== ROUTES UNTUK BANGUNAN AIR (TERPUSAT) ==========
// Routes langsung ke BangunanAir controller (yang sebenarnya memproses data)
$routes->get('user/barang/jalanirigasijaringan/bangunanair/dashboard', 'User\Barang\JalanIrigasiJaringan\BangunanAir::dashboard');
$routes->get('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair', 'User\Barang\JalanIrigasiJaringan\BangunanAir::kelompokBangunanAir');
$routes->get('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair/(:segment)/(:segment)', 'User\Barang\JalanIrigasiJaringan\BangunanAir::kelompokDetailSegments/$1/$2');
$routes->get('user/barang/jalanirigasijaringan/bangunanair/kelompokbangunanair/(:any)', 'User\Barang\JalanIrigasiJaringan\BangunanAir::kelompokDetail/$1');

// CRUD Operations
$routes->post('user/barang/jalanirigasijaringan/bangunanair/tambah', 'User\Barang\JalanIrigasiJaringan\BangunanAir::tambah');
$routes->post('user/barang/jalanirigasijaringan/bangunanair/importFromApi', 'User\Barang\JalanIrigasiJaringan\BangunanAir::importFromApi');
$routes->post('user/barang/jalanirigasijaringan/bangunanair/resetData', 'User\Barang\JalanIrigasiJaringan\BangunanAir::resetData');
$routes->get('user/barang/jalanirigasijaringan/bangunanair/exportBangunanAirList/(:segment)', 'User\Barang\JalanIrigasiJaringan\BangunanAir::exportBangunanAirList/$1');
$routes->get('user/barang/jalanirigasijaringan/bangunanair/stats', 'User\Barang\JalanIrigasiJaringan\BangunanAir::stats');
$routes->get('user/barang/jalanirigasijaringan/bangunanair/test-api', 'User\Barang\JalanIrigasiJaringan\BangunanAir::testApi');

// ========== ROUTES ALTERNATIF (BACKWARD COMPATIBILITY) ==========
// Redirect dari route lama ke yang baru - LETAKKAN DI AKHIR
$routes->get('user/barang/jalanirigasijaringan/bangunanair/irigasi', 'User\Barang\JalanIrigasiJaringan\BangunanAir::kelompokDetail/BANGUNAN AIR IRIGASI');
$routes->get('user/barang/jalanirigasijaringan/bangunanair/pasangsurut', 'User\Barang\JalanIrigasiJaringan\BangunanAir::kelompokDetail/BANGUNAN PENGAIRAN PASANG SURUT');
$routes->get('user/barang/jalanirigasijaringan/bangunanair/rawa', 'User\Barang\JalanIrigasiJaringan\BangunanAir::kelompokDetail/BANGUNAN PENGEMBANGAN RAWA DAN POLDER');
$routes->get('user/barang/jalanirigasijaringan/bangunanair/pengaman', 'User\Barang\JalanIrigasiJaringan\BangunanAir::kelompokDetail/BANGUNAN PENGAMAN SUNGAI PANTAI & PENANGGULAN BENCANA ALAM');
$routes->get('user/barang/jalanirigasijaringan/bangunanair/sumberair', 'User\Barang\JalanIrigasiJaringan\BangunanAir::kelompokDetail/BANGUNAN PENGEMBANGAN SUMBER AIR DAN AIR TANAH');
$routes->get('user/barang/jalanirigasijaringan/bangunanair/airbersih', 'User\Barang\JalanIrigasiJaringan\BangunanAir::kelompokDetail/BANGUNAN AIR BERSIH/AIR BAKU');
$routes->get('user/barang/jalanirigasijaringan/bangunanair/airkotor', 'User\Barang\JalanIrigasiJaringan\BangunanAir::kelompokDetail/BANGUNAN AIR KOTOR');


// ========== 5.03 INSTALASI ==========
// Route utama instalasi (redirect ke kelompokinstalasi)
$routes->get('user/barang/jalanirigasijaringan/instalasi', 'User\Barang\JalanIrigasiJaringan\JalanIrigasiJaringan::instalasi');

// Route khusus untuk Instalasi Air Bersih (karena ada slash)
$routes->get('user/barang/jalanirigasijaringan/instalasi/airBersih', 'User\Barang\JalanIrigasiJaringan\Instalasi::airBersih');

// ========== ROUTES UNTUK INSTALASI (TERPUSAT) ==========
// Routes langsung ke Instalasi controller (yang sebenarnya memproses data)
$routes->get('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi', 'User\Barang\JalanIrigasiJaringan\Instalasi::kelompokInstalasi');
$routes->get('user/barang/jalanirigasijaringan/instalasi/kelompokinstalasi/(:any)', 'User\Barang\JalanIrigasiJaringan\Instalasi::kelompokDetail/$1');

// CRUD Operations
$routes->post('user/barang/jalanirigasijaringan/instalasi/tambah', 'User\Barang\JalanIrigasiJaringan\Instalasi::tambah');
$routes->post('user/barang/jalanirigasijaringan/instalasi/importFromApi', 'User\Barang\JalanIrigasiJaringan\Instalasi::importFromApi');
$routes->post('user/barang/jalanirigasijaringan/instalasi/resetData', 'User\Barang\JalanIrigasiJaringan\Instalasi::resetData');
$routes->get('user/barang/jalanirigasijaringan/instalasi/exportInstalasiList/(:segment)', 'User\Barang\JalanIrigasiJaringan\Instalasi::exportInstalasiList/$1');
$routes->get('user/barang/jalanirigasijaringan/instalasi/stats', 'User\Barang\JalanIrigasiJaringan\Instalasi::stats');
$routes->get('user/barang/jalanirigasijaringan/instalasi/test-api', 'User\Barang\JalanIrigasiJaringan\Instalasi::testApi');

// ========== ROUTES ALTERNATIF (BACKWARD COMPATIBILITY) ==========
// Redirect dari route lama ke yang baru - LETAKKAN DI AKHIR
$routes->get('user/barang/jalanirigasijaringan/instalasi/airbersih', 'User\Barang\JalanIrigasiJaringan\Instalasi::airBersih');
$routes->get('user/barang/jalanirigasijaringan/instalasi/airkotor', 'User\Barang\JalanIrigasiJaringan\Instalasi::kelompokDetail/INSTALASI AIR KOTOR');
$routes->get('user/barang/jalanirigasijaringan/instalasi/sampah', 'User\Barang\JalanIrigasiJaringan\Instalasi::kelompokDetail/INSTALASI PENGOLAHAN SAMPAH');
$routes->get('user/barang/jalanirigasijaringan/instalasi/bahanbangunan', 'User\Barang\JalanIrigasiJaringan\Instalasi::kelompokDetail/INSTALASI PENGOLAHAN BAHAN BANGUNAN');
$routes->get('user/barang/jalanirigasijaringan/instalasi/listrik', 'User\Barang\JalanIrigasiJaringan\Instalasi::kelompokDetail/INSTALASI PEMBANGKIT LISTRIK');
$routes->get('user/barang/jalanirigasijaringan/instalasi/gardu', 'User\Barang\JalanIrigasiJaringan\Instalasi::kelompokDetail/INSTALASI GARDU LISTRIK');
$routes->get('user/barang/jalanirigasijaringan/instalasi/pertahanan', 'User\Barang\JalanIrigasiJaringan\Instalasi::kelompokDetail/INSTALASI PERTAHANAN');
$routes->get('user/barang/jalanirigasijaringan/instalasi/gas', 'User\Barang\JalanIrigasiJaringan\Instalasi::kelompokDetail/INSTALASI GAS');
$routes->get('user/barang/jalanirigasijaringan/instalasi/pengaman', 'User\Barang\JalanIrigasiJaringan\Instalasi::kelompokDetail/INSTALASI PENGAMAN');
$routes->get('user/barang/jalanirigasijaringan/instalasi/lain', 'User\Barang\JalanIrigasiJaringan\Instalasi::kelompokDetail/INSTALASI LAIN');
// ========== 5.04 JARINGAN ==========
// Route utama jaringan (redirect ke kelompokjaringan)
$routes->get('user/barang/jalanirigasijaringan/jaringan', 'User\Barang\JalanIrigasiJaringan\JalanIrigasiJaringan::jaringan');

// ========== ROUTES UNTUK JARINGAN (TERPUSAT) ==========
// Routes langsung ke Jaringan controller (yang sebenarnya memproses data)
$routes->get('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan', 'User\Barang\JalanIrigasiJaringan\Jaringan::kelompokJaringan');
$routes->get('user/barang/jalanirigasijaringan/jaringan/kelompokjaringan/(:any)', 'User\Barang\JalanIrigasiJaringan\Jaringan::kelompokDetail/$1');

// CRUD Operations
$routes->post('user/barang/jalanirigasijaringan/jaringan/tambah', 'User\Barang\JalanIrigasiJaringan\Jaringan::tambah');
$routes->post('user/barang/jalanirigasijaringan/jaringan/importFromApi', 'User\Barang\JalanIrigasiJaringan\Jaringan::importFromApi');
$routes->post('user/barang/jalanirigasijaringan/jaringan/resetData', 'User\Barang\JalanIrigasiJaringan\Jaringan::resetData');
$routes->get('user/barang/jalanirigasijaringan/jaringan/exportJaringanList/(:segment)', 'User\Barang\JalanIrigasiJaringan\Jaringan::exportJaringanList/$1');
$routes->get('user/barang/jalanirigasijaringan/jaringan/stats', 'User\Barang\JalanIrigasiJaringan\Jaringan::stats');
$routes->get('user/barang/jalanirigasijaringan/jaringan/test-api', 'User\Barang\JalanIrigasiJaringan\Jaringan::testApi');

// ========== ROUTES ALTERNATIF (BACKWARD COMPATIBILITY) ==========
// Redirect dari route lama ke yang baru - LETAKKAN DI AKHIR
$routes->get('user/barang/jalanirigasijaringan/jaringan/airminum', 'User\Barang\JalanIrigasiJaringan\Jaringan::kelompokDetail/JARINGAN AIR MINUM');
$routes->get('user/barang/jalanirigasijaringan/jaringan/listrik', 'User\Barang\JalanIrigasiJaringan\Jaringan::kelompokDetail/JARINGAN LISTRIK');
$routes->get('user/barang/jalanirigasijaringan/jaringan/telepon', 'User\Barang\JalanIrigasiJaringan\Jaringan::kelompokDetail/JARINGAN TELEPON');
$routes->get('user/barang/jalanirigasijaringan/jaringan/gas', 'User\Barang\JalanIrigasiJaringan\Jaringan::kelompokDetail/JARINGAN GAS');

// ========== ROUTES UNTUK BAHAN PERPUSTAKAAN (TERPUSAT) ==========
// Routes melalui AsetTetapLainnya (untuk redirect)
$routes->get('user/barang/asettetaplainnya/kelompokbahanperpustakaan', 'User\Barang\AsetTetapLainnya\AsetTetapLainnya::kelompokbahanperpustakaan');
$routes->get('user/barang/asettetaplainnya/kelompokbahanperpustakaan/(:segment)', 'User\Barang\AsetTetapLainnya\AsetTetapLainnya::kelompokbahanperpustakaan/$1');

// Routes langsung ke BahanPerpustakaan controller (yang sebenarnya memproses data)
$routes->get('user/barang/asettetaplainnya/bahanperpustakaan/dashboard', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::dashboard');
$routes->get('user/barang/asettetaplainnya/bahanperpustakaan/kelompokbahanperpustakaan', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::kelompokBahanPerpustakaan');
$routes->get('user/barang/asettetaplainnya/bahanperpustakaan/kelompokbahanperpustakaan/(:segment)', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::kelompokDetail/$1');
$routes->post('user/barang/asettetaplainnya/bahanperpustakaan/tambah', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::tambah');
$routes->post('user/barang/asettetaplainnya/bahanperpustakaan/importFromApi', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::importFromApi');
$routes->post('user/barang/asettetaplainnya/bahanperpustakaan/resetData', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::resetData');
$routes->get('user/barang/asettetaplainnya/bahanperpustakaan/exportBahanPerpustakaanList/(:segment)', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::exportBahanPerpustakaanList/$1');

// 6.01 Bahan Perpustakaan - redirect ke kelompok
$routes->get('user/barang/asettetaplainnya/kelompokbahanperpustakaan', 'User\Barang\AsetTetapLainnya\AsetTetapLainnya::kelompokbahanperpustakaan');
$routes->get('user/barang/asettetaplainnya/kelompokbahanperpustakaan/(:segment)', 'User\Barang\AsetTetapLainnya\AsetTetapLainnya::kelompokbahanperpustakaan/$1');

// Routes langsung ke BahanPerpustakaan controller (yang sebenarnya memproses data)
$routes->get('user/barang/asettetaplainnya/bahanperpustakaan/dashboard', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::dashboard');
$routes->get('user/barang/asettetaplainnya/bahanperpustakaan/kelompokbahanperpustakaan', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::kelompokBahanPerpustakaan');
$routes->get('user/barang/asettetaplainnya/bahanperpustakaan/kelompokbahanperpustakaan/(:segment)', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::kelompokDetail/$1');
$routes->post('user/barang/asettetaplainnya/bahanperpustakaan/tambah', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::tambah');
$routes->post('user/barang/asettetaplainnya/bahanperpustakaan/importFromApi', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::importFromApi');
$routes->post('user/barang/asettetaplainnya/bahanperpustakaan/resetData', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::resetData');
$routes->get('user/barang/asettetaplainnya/bahanperpustakaan/exportBahanPerpustakaanList/(:segment)', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::exportBahanPerpustakaanList/$1');

// 6.01 Bahan Perpustakaan - redirect ke kelompok
$routes->get('user/barang/asettetaplainnya/bahanperpustakaan', 'User\Barang\AsetTetapLainnya\AsetTetapLainnya::bahanperpustakaan');

// Debug route untuk test API (bisa dihapus setelah selesai debugging)
$routes->get('user/barang/asettetaplainnya/bahanperpustakaan/test-api', 'User\Barang\AsetTetapLainnya\BahanPerpustakaan::testApi');
// 6.02 Barang Bercorak - redirect ke kelompok
$routes->get('user/barang/asettetaplainnya/barangbercorak', 'User\Barang\AsetTetapLainnya\AsetTetapLainnya::barangbercorak');

// ========== ROUTES UNTUK HEWAN (TERPUSAT) ==========
$routes->get('user/barang/asettetaplainnya/hewan/kelompokhewan', 'User\Barang\AsetTetapLainnya\Hewan::kelompokHewan');
$routes->get('user/barang/asettetaplainnya/hewan/kelompokhewan/(:segment)', 'User\Barang\AsetTetapLainnya\Hewan::kelompokDetail/$1');
$routes->post('user/barang/asettetaplainnya/hewan/importFromApi', 'User\Barang\AsetTetapLainnya\Hewan::importFromApi');
$routes->post('user/barang/asettetaplainnya/hewan/resetData', 'User\Barang\AsetTetapLainnya\Hewan::resetData');
$routes->get('user/barang/asettetaplainnya/hewan/exportHewanList/(:segment)', 'User\Barang\AsetTetapLainnya\Hewan::exportHewanList/$1');

// 6.03 Hewan - redirect ke kelompok
$routes->get('user/barang/asettetaplainnya/hewan', 'User\Barang\AsetTetapLainnya\AsetTetapLainnya::hewan');

// ========== ROUTES UNTUK IKAN (TERPUSAT) ==========
$routes->get('user/barang/asettetaplainnya/ikan/kelompokikan', 'User\Barang\AsetTetapLainnya\Ikan::kelompokIkan');
$routes->get('user/barang/asettetaplainnya/ikan/kelompokikan/(:segment)', 'User\Barang\AsetTetapLainnya\Ikan::kelompokDetail/$1');
$routes->post('user/barang/asettetaplainnya/ikan/importFromApi', 'User\Barang\AsetTetapLainnya\Ikan::importFromApi');
$routes->post('user/barang/asettetaplainnya/ikan/resetData', 'User\Barang\AsetTetapLainnya\Ikan::resetData');
$routes->get('user/barang/asettetaplainnya/ikan/exportIkanList/(:segment)', 'User\Barang\AsetTetapLainnya\Ikan::exportIkanList/$1');

// 6.04 Ikan - redirect ke kelompok
$routes->get('user/barang/asettetaplainnya/ikan', 'User\Barang\AsetTetapLainnya\AsetTetapLainnya::ikan');

// ========== ROUTES UNTUK TANAMAN (TERPUSAT) ==========
// Routes langsung ke Tanaman controller
$routes->get('user/barang/asettetaplainnya/tanaman/dashboard', 'User\Barang\AsetTetapLainnya\Tanaman::dashboard');
$routes->get('user/barang/asettetaplainnya/tanaman/kelompoktanaman', 'User\Barang\AsetTetapLainnya\Tanaman::kelompokTanaman');
$routes->get('user/barang/asettetaplainnya/tanaman/kelompoktanaman/(:segment)', 'User\Barang\AsetTetapLainnya\Tanaman::kelompokDetail/$1');
$routes->post('user/barang/asettetaplainnya/tanaman/tambah', 'User\Barang\AsetTetapLainnya\Tanaman::tambah');
$routes->post('user/barang/asettetaplainnya/tanaman/importFromApi', 'User\Barang\AsetTetapLainnya\Tanaman::importFromApi');
$routes->post('user/barang/asettetaplainnya/tanaman/resetData', 'User\Barang\AsetTetapLainnya\Tanaman::resetData');
$routes->get('user/barang/asettetaplainnya/tanaman/exportTanamanList/(:segment)', 'User\Barang\AsetTetapLainnya\Tanaman::exportTanamanList/$1');

// 6.05 Tanaman - redirect ke kelompok  
$routes->get('user/barang/asettetaplainnya/tanaman', 'User\Barang\AsetTetapLainnya\AsetTetapLainnya::tanaman');


// ========== ROUTES UNTUK BARANG KOLEKSI NON BUDAYA (TERPUSAT) ==========
$routes->get('user/barang/asettetaplainnya/barangkoleksinonbudaya/kelompokbarangkoleksinonbudaya', 'User\Barang\AsetTetapLainnya\BarangKoleksiNonBudaya::kelompokBarangKoleksiNonBudaya');
$routes->get('user/barang/asettetaplainnya/barangkoleksinonbudaya/kelompokbarangkoleksinonbudaya/(:segment)', 'User\Barang\AsetTetapLainnya\BarangKoleksiNonBudaya::kelompokDetail/$1');
$routes->post('user/barang/asettetaplainnya/barangkoleksinonbudaya/importFromApi', 'User\Barang\AsetTetapLainnya\BarangKoleksiNonBudaya::importFromApi');
$routes->post('user/barang/asettetaplainnya/barangkoleksinonbudaya/resetData', 'User\Barang\AsetTetapLainnya\BarangKoleksiNonBudaya::resetData');
$routes->get('user/barang/asettetaplainnya/barangkoleksinonbudaya/exportBarangKoleksiNonBudayaList/(:segment)', 'User\Barang\AsetTetapLainnya\BarangKoleksiNonBudaya::exportBarangKoleksiNonBudayaList/$1');

// 6.06 Barang Koleksi Non Budaya - redirect ke kelompok
$routes->get('user/barang/asettetaplainnya/barangkoleksinonbudaya', 'User\Barang\AsetTetapLainnya\AsetTetapLainnya::barangkoleksinonbudaya');

// ========== ROUTES UNTUK ASET TETAP DALAM RENOVASI (TERPUSAT) ==========
$routes->get('user/barang/asettetaplainnya/asettetapdalamrenovasi/kelompokasettetapdalamrenovasi', 'User\Barang\AsetTetapLainnya\AsetTetapDalamRenovasi::kelompokAsetTetapDalamRenovasi');
$routes->get('user/barang/asettetaplainnya/asettetapdalamrenovasi/kelompokasettetapdalamrenovasi/(:segment)', 'User\Barang\AsetTetapLainnya\AsetTetapDalamRenovasi::kelompokDetail/$1');
$routes->post('user/barang/asettetaplainnya/asettetapdalamrenovasi/importFromApi', 'User\Barang\AsetTetapLainnya\AsetTetapDalamRenovasi::importFromApi');
$routes->post('user/barang/asettetaplainnya/asettetapdalamrenovasi/resetData', 'User\Barang\AsetTetapLainnya\AsetTetapDalamRenovasi::resetData');
$routes->get('user/barang/asettetaplainnya/asettetapdalamrenovasi/exportAsetTetapDalamRenovasiList/(:segment)', 'User\Barang\AsetTetapLainnya\AsetTetapDalamRenovasi::exportAsetTetapDalamRenovasiList/$1');

// 6.07 Aset Tetap Dalam Renovasi - redirect ke kelompok
$routes->get('user/barang/asettetaplainnya/asettetapdalamrenovasi', 'User\Barang\AsetTetapLainnya\AsetTetapLainnya::asettetapdalamrenovasi');

// Routes untuk Aset Tak Berwujud (mengikuti pola Alat Besar yang benar)
$routes->get('user/barang/asettakberwujud', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::kelompokAsetTakBerwujud');
$routes->get('user/barang/asettakberwujud/kelompokasettakberwujud', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::kelompokAsetTakBerwujud');
$routes->get('user/barang/asettakberwujud/kelompokasettakberwujud/(:segment)', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::kelompokDetail/$1');

// CRUD Operations - DIPERBAIKI
$routes->post('user/barang/asettakberwujud/tambah', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::tambah');
$routes->post('user/barang/asettakberwujud/importFromApi', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::importFromApi');
$routes->post('user/barang/asettakberwujud/resetData', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::resetData');
$routes->get('user/barang/asettakberwujud/exportAsetTakBerwujudList/(:segment)', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::exportAsetTakBerwujudList/$1');

// ========== ROUTES UNTUK ASET TAK BERWUJUD (TERPUSAT) ==========

// Routes melalui PeralatanDanMesin (untuk redirect) - jika diperlukan
$routes->get('user/barang/peralatandanmesin/kelompokasettakberwujud', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::kelompokasettakberwujud');
$routes->get('user/barang/peralatandanmesin/kelompokasettakberwujud/(:segment)', 'User\Barang\PeralatanDanMesin\PeralatanDanMesin::kelompokasettakberwujud/$1');

// Routes langsung ke AsetTakBerwujud controller (yang sebenarnya memproses data)
// 1. Dashboard Aset Tak Berwujud - Menampilkan data dari API
$routes->get('user/barang/asettakberwujud/dashboard', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::dashboard');

// 2. Kelompok Aset Tak Berwujud - Overview semua kategori (tanpa parameter)
$routes->get('user/barang/asettakberwujud/kelompokasettakberwujud', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::kelompokAsetTakBerwujud');

// 3. Kelompok Detail - Menampilkan data per kategori dengan parameter
$routes->get('user/barang/asettakberwujud/kelompokasettakberwujud/(:segment)', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::kelompokDetail/$1');

// 4. Form Tambah Aset Tak Berwujud - POST handler untuk form tambah manual
$routes->post('user/barang/asettakberwujud/tambah', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::tambah');

// 5. Import dari API - POST handler untuk import/sync data dari API
$routes->post('user/barang/asettakberwujud/importFromApi', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::importFromApi');

// 6. Reset Data - POST handler untuk menghapus semua data
$routes->post('user/barang/asettakberwujud/resetData', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::resetData');

// 7. Export Data - GET handler untuk export CSV per kategori
$routes->get('user/barang/asettakberwujud/exportAsetTakBerwujudList/(:segment)', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::exportAsetTakBerwujudList/$1');

// 8. Statistik - GET handler untuk menampilkan statistik database (jika diperlukan)
$routes->get('user/barang/asettakberwujud/stats', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::stats');

// ========== ROUTES ALTERNATIF (UNTUK BACKWARD COMPATIBILITY) ==========
// Jika masih ada link lama yang mengarah ke controller lama, redirect ke yang baru

// ROUTES UNTUK URL PENDEK
$routes->get('user/asettakberwujud', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::kelompokAsetTakBerwujud');
$routes->get('user/asettakberwujud/kelompokasettakberwujud', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::kelompokAsetTakBerwujud');
$routes->get('user/asettakberwujud/kelompokasettakberwujud/(:segment)', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::kelompokDetail/$1');

// TEST API ROUTE (opsional)
$routes->get('user/barang/asettakberwujud/test-api', 'User\Barang\AsetTakBerwujud\AsetTakBerwujud::testApi');
// ULTRA CLEAN SIMAN API ROUTES - NO ERRORS
// ==========================================
$routes->get('siman-test', 'SimanApi::testConnection');
$routes->get('siman-sync-all', 'SimanApi::syncAllData');
$routes->get('siman-sync/(:segment)', 'SimanApi::syncByCategory/$1');
$routes->get('siman-stats', 'SimanApi::getStatistics');
$routes->get('siman-schema', 'SimanApi::getDatabaseSchema');

// Column management
$routes->get('siman-check-columns', 'SimanApi::checkExistingColumns');
$routes->get('siman-create-columns', 'SimanApi::createColumnsForAllCategoriesSafe');
$routes->get('siman-skip-columns', 'SimanApi::skipColumnCreation');
$routes->get('siman-fix-columns', 'SimanApi::fixColumnSizes');

// Data extraction (SEMUA DATA - NO LIMIT)
$routes->get('siman-extract-all', 'SimanApi::extractAllDataSafe'); // Extract SEMUA 14K+ records
$routes->get('siman-extract-all/(:segment)', 'SimanApi::extractAllDataSafe/$1'); // Specific category, no limit
$routes->get('siman-extract-batch', 'SimanApi::extractAllDataBatch'); // Batch processing untuk data besar
$routes->get('siman-extract-batch/(:segment)', 'SimanApi::extractAllDataBatch/$1'); // Batch per category

// Data extraction dengan limit (untuk testing)
$routes->get('siman-extract', 'SimanApi::extractAllDataSafe'); // Default behavior (all data)
$routes->get('siman-extract/(:segment)', 'SimanApi::extractAllDataSafe/$1');
$routes->get('siman-extract/(:segment)/(:num)', 'SimanApi::extractAllDataSafe/$1/$2'); // With specific limit

// All-in-one
$routes->get('siman-auto-sync', 'SimanApi::autoSyncWithDynamicColumns');
$routes->get('siman-auto-sync/(:segment)', 'SimanApi::autoSyncWithDynamicColumns/$1');