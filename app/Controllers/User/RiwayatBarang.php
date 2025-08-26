<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\PinjamBarangModel;

class RiwayatBarang extends BaseController
{
    public function index()
    {
        $userId = user_id();
        $pinjamModel = new PinjamBarangModel();
        
        $riwayat = $pinjamModel->getPeminjamanHistory($userId);

        return view('user/riwayat_barang/index', [
            'riwayat' => $riwayat
        ]);
    }

    public function detail($id)
    {
        $pinjamModel = new PinjamBarangModel();
        $data = $pinjamModel->select('pinjam_barang.*, barang.nama_barang, barang.kategori, barang.lokasi')
                             ->join('barang', 'barang.id = pinjam_barang.barang_id')
                             ->where('pinjam_barang.id', $id)
                             ->first();

        if (!$data) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        return view('user/riwayat_barang/detail', [
            'data' => $data
        ]);
    }
}
