<?php

namespace App\Controllers\User\Barang\AsetTetapLainnya;

use App\Controllers\BaseController;
use App\Models\HewanModel;

class Hewan extends BaseController
{
    protected $hewanModel;

    public function __construct()
    {
        $this->hewanModel = new HewanModel();
    }

    public function kelompokHewan()
    {
        $searchTerm = $this->request->getGet('search');
        $activeKelompok = $this->request->getGet('kelompok', FILTER_SANITIZE_STRING);

        $allHewanList = $this->hewanModel->findAll();
        
        $validKelompok = ['HEWAN PIARAAN', 'TERNAK'];

        $hewanList = array_filter($allHewanList, function($item) use ($validKelompok) {
            return in_array(strtoupper($item['kelompok'] ?? ''), $validKelompok);
        });

        if (!empty($searchTerm)) {
            $hewanList = array_filter($hewanList, function($item) use ($searchTerm) {
                return stripos($item['nama_barang'] ?? '', $searchTerm) !== false ||
                       stripos($item['kode_barang'] ?? '', $searchTerm) !== false;
            });
        }

        if (!empty($activeKelompok)) {
            $hewanList = array_filter($hewanList, function($item) use ($activeKelompok) {
                return strtoupper($item['kelompok'] ?? '') === strtoupper($activeKelompok);
            });
        }

        $groupedData = [];
        foreach ($allHewanList as $item) {
            $kelompok = $item['kelompok'] ?? 'UNKNOWN';
            if (in_array(strtoupper($kelompok), $validKelompok)) {
                if (!isset($groupedData[$kelompok])) {
                    $groupedData[$kelompok] = [];
                }
                $groupedData[$kelompok][] = $item;
            }
        }

        $data = [
            'hewanList' => array_values($hewanList),
            'groupedData' => $groupedData,
            'totalCount' => count($hewanList),
            'searchTerm' => $searchTerm,
            'activeKelompok' => $activeKelompok
        ];

        return view('user/barang/asettetaplainnya/hewan/kelompokhewan', $data);
    }

    public function kelompokDetail($kelompok)
    {
        $kelompok = urldecode($kelompok);
        $searchTerm = $this->request->getGet('search');
        return redirect()->to('user/barang/asettetaplainnya/hewan/kelompokhewan?kelompok=' . urlencode($kelompok) . ($searchTerm ? '&search=' . urlencode($searchTerm) : ''));
    }

    // Method import, export, reset, tambah sama seperti BahanPerpustakaan dan Ikan
    // (copy dari controller Ikan dan sesuaikan)
}