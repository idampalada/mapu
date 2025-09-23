<?php

namespace App\Controllers\User\Barang\GedungDanBangunan;

use App\Controllers\BaseController;

class GedungDanBangunan extends BaseController
{
    // Method untuk halaman utama Gedung dan Bangunan
    public function index()
    {
        return view('user/barang/gedungdanbangunan/gedungdanbangunan');
    }

    // ================== 4.01 BANGUNAN GEDUNG ==================
    public function bangunangedung()
    {
        return redirect()->to('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung');
    }

    public function bangunangedungtempatkerja()
    {
        return redirect()->to('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung/BANGUNAN GEDUNG TEMPAT KERJA');
    }

    public function bangunangedungtempattinggal()
    {
        return redirect()->to('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung/BANGUNAN GEDUNG TEMPAT TINGGAL');
    }

    // ================== 4.02 MONUMEN ==================
    public function monumen()
{
    return redirect()->to('user/barang/gedungdanbangunan/monumen/kelompokmonumen');
}

public function candituguperingatan()
{
    return redirect()->to('user/barang/gedungdanbangunan/monumen/kelompokmonumen');
}

    // Method untuk redirect ke kelompok monumen spesifik
    public function candi()
    {
        return redirect()->to('user/barang/gedungdanbangunan/monumen/kelompokmonumen/CANDI');
    }

    public function tuguperingatan()
    {
        return redirect()->to('user/barang/gedungdanbangunan/monumen/kelompokmonumen/TUGU PERINGATAN');
    }

    public function prasasti()
    {
        return redirect()->to('user/barang/gedungdanbangunan/monumen/kelompokmonumen/PRASASTI');
    }

    public function monumentredirect()
    {
        return redirect()->to('user/barang/gedungdanbangunan/monumen/kelompokmonumen/MONUMEN');
    }

    // ================== 4.03 BANGUNAN MENARA ==================
    public function bangunanmenara()
    {
        return view('user/barang/gedungdanbangunan/bangunanmenara');
    }

    public function bangunanmenaraperambuan()
    {
        return redirect()->to('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung/BANGUNAN MENARA PERAMBUAN');
    }

    public function menarapengawas()
    {
        return redirect()->to('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung/MENARA PENGAWAS');
    }

    public function menara()
    {
        return redirect()->to('user/barang/gedungdanbangunan/bangunangedung/kelompokbangunangedung/MENARA');
    }

    // ================== 4.04 TUGU TITIK KONTROL/PASTI ==================
    public function tugutitikkontrol()
    {
        return redirect()->to('user/barang/gedungdanbangunan/tugutitikkontrol/kelompoktugutitikkontrol');
    }

    public function tugutandabatas()
    {
        return redirect()->to('user/barang/gedungdanbangunan/tugutitikkontrol/kelompoktugutitikkontrol');
    }

    // Method untuk redirect ke kelompok tugu titik kontrol spesifik
    public function tugubatas()
    {
        return redirect()->to('user/barang/gedungdanbangunan/tugutitikkontrol/kelompoktugutitikkontrol');
    }

    public function tandabatas()
    {
        return redirect()->to('user/barang/gedungdanbangunan/tugutitikkontrol/kelompoktugutitikkontrol');
    }

    public function tugutitikkontrolredirect()
    {
        return redirect()->to('user/barang/gedungdanbangunan/tugutitikkontrol/kelompoktugutitikkontrol');
    }

    // ================== CRUD OPERATIONS (LEGACY) ==================
    public function tambah()
    {
        // Implementation for adding new Gedung dan Bangunan
        // This will be handled by specific controllers like BangunanGedung
    }

    public function importFromApi()
    {
        // Implementation for importing data from API
        // This will be handled by specific controllers like BangunanGedung
    }

    public function resetData()
    {
        // Implementation for resetting data
        // This will be handled by specific controllers like BangunanGedung
    }

    public function exportGedungBangunanList($kelompok)
    {
        // Implementation for exporting data
        // This will be handled by specific controllers like BangunanGedung
    }
}