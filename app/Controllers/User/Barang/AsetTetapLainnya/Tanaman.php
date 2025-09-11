<?php

namespace App\Controllers\User\Barang\AsetTetapLainnya;

use App\Controllers\BaseController;
use App\Models\TanamanModel;

class Tanaman extends BaseController
{
    protected $tanamanModel;

    public function __construct()
    {
        $this->tanamanModel = new TanamanModel();
    }

    public function kelompokTanaman()
    {
        $searchTerm = $this->request->getGet('search');
        $activeKelompok = $this->request->getGet('kelompok', FILTER_SANITIZE_STRING);

        $allTanamanList = $this->tanamanModel->findAll();
        
        $validKelompok = ['TANAMAN'];

        $tanamanList = array_filter($allTanamanList, function($item) use ($validKelompok) {
            return in_array(strtoupper($item['kelompok'] ?? ''), $validKelompok);
        });

        // Filter logic sama seperti controller lainnya...

        $data = [
            'tanamanList' => array_values($tanamanList),
            'groupedData' => [],
            'totalCount' => count($tanamanList),
            'searchTerm' => $searchTerm,
            'activeKelompok' => $activeKelompok
        ];

        return view('user/barang/asettetaplainnya/tanaman/kelompoktanaman', $data);
    }

    public function kelompokDetail($kelompok)
    {
        $kelompok = urldecode($kelompok);
        $searchTerm = $this->request->getGet('search');
        return redirect()->to('user/barang/asettetaplainnya/tanaman/kelompoktanaman?kelompok=' . urlencode($kelompok) . ($searchTerm ? '&search=' . urlencode($searchTerm) : ''));
    }

    // Method import, export, reset, tambah sama seperti yang lain
}