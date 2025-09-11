<?php

namespace App\Controllers\User\Barang\AsetTetapLainnya;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
class AsetTetapLainnya extends BaseController
{
    // Method untuk halaman utama Aset Tetap Lainnya
    public function index()
    {
        return view('user/barang/asettetaplainnya/asettetaplainnya');
    }

    // ================== 6.01 BAHAN PERPUSTAKAAN ==================
    public function bahanperpustakaan()
    {
        return redirect()->to('user/barang/asettetaplainnya/bahanperpustakaan/kelompokbahanperpustakaan');
    }

    // ================== 6.02 BARANG BERCORAK KESENIAN/KEBUDAYAAN/OLAHRAGA ==================
    public function barangbercorak()
    {
        return redirect()->to('user/barang/asettetaplainnya/barangbercorak/kelompokbarangbercorak');
    }

    // ================== 6.03 HEWAN ==================
    public function hewan()
    {
        return redirect()->to('user/barang/asettetaplainnya/hewan/kelompokhewan');
    }

    // ================== 6.04 IKAN ==================
    public function ikan()
    {
        return redirect()->to('user/barang/asettetaplainnya/ikan/kelompokikan');
    }

    // ================== 6.05 TANAMAN ==================
    public function tanaman()
    {
        return redirect()->to('user/barang/asettetaplainnya/tanaman/kelompoktanaman');
    }

    // ================== 6.06 BARANG KOLEKSI NON BUDAYA ==================
    public function barangkoleksinonbudaya()
    {
        return redirect()->to('user/barang/asettetaplainnya/barangkoleksinonbudaya/kelompokbarangkoleksinonbudaya');
    }

    // ================== 6.07 ASET TETAP DALAM RENOVASI ==================
    public function asettetapdalamrenovasi()
    {
        return redirect()->to('user/barang/asettetaplainnya/asettetapdalamrenovasi/kelompokasettetapdalamrenovasi');
    }
}