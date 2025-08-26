<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\AsetModel;
use App\Models\PinjamModel;
use App\Models\KembaliModel;
use App\Models\RuanganModel;
use App\Models\PinjamRuanganModel;
use App\Models\PinjamBarangModel;

class Mainpage extends BaseController
{
    public function index()
    {
        $db    = \Config\Database::connect();
        $pager = \Config\Services::pager();
    
        // === PAGINATION KENDARAAN ===
        $perPageKendaraan = (int) ($this->request->getGet('per_page') ?? 10);
        $pageKendaraan    = (int) ($this->request->getGet('page') ?? 1);
        $offsetKendaraan  = ($pageKendaraan - 1) * $perPageKendaraan;

        $builderKendaraan = $db->table('pinjam')
            ->select('pinjam.tanggal_pinjam, pinjam.tanggal_kembali, pinjam.status, assets.no_polisi, assets.merk')
            ->join('assets', 'assets.id = pinjam.kendaraan_id', 'left')
            ->orderBy('pinjam.tanggal_pinjam', 'DESC');

        $totalKendaraan = $builderKendaraan->countAllResults(false);

        $riwayatKendaraan = $builderKendaraan
            ->limit($perPageKendaraan, $offsetKendaraan)
            ->get()
            ->getResult();

        // === PAGINATION RUANGAN ===
        $perPageRuangan = (int) ($this->request->getGet('per_page_ruangan') ?? 5);
        $pageRuangan    = (int) ($this->request->getGet('page_ruangan') ?? 1);
        $offsetRuangan  = ($pageRuangan - 1) * $perPageRuangan;

        $builderRuangan = $db->table('pinjam_ruangan')
            ->select('ruangan.nama_ruangan, ruangan.lokasi, pinjam_ruangan.tanggal, pinjam_ruangan.waktu_mulai, pinjam_ruangan.waktu_selesai, pinjam_ruangan.status')
            ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id', 'left')
            ->orderBy('pinjam_ruangan.tanggal', 'DESC');

        $totalRuangan = $builderRuangan->countAllResults(false);

        $statusRuangan = $builderRuangan
            ->limit($perPageRuangan, $offsetRuangan)
            ->get()
            ->getResult();

            $asetModel = new \App\Models\AsetModel();
$ruanganModel = new \App\Models\RuanganModel();
$barangModel = new \App\Models\BarangModel();
$pinjamModel = new \App\Models\PinjamModel();
$pinjamRuanganModel = new \App\Models\PinjamRuanganModel();
$pinjamBarangModel = new \App\Models\PinjamBarangModel();

$dataStatistik = [
    // Kendaraan
    'total_kendaraan' => $asetModel->where('deleted_at', null)->countAllResults(),
    'tersedia_kendaraan' => $asetModel->where(['status_pinjam' => 'Tersedia', 'deleted_at' => null])->countAllResults(),
    'dipinjam_kendaraan' => $asetModel->where(['status_pinjam' => 'Dipinjam', 'deleted_at' => null])->countAllResults(),
    'verifikasi_kendaraan' => $pinjamModel->where('status', 'pending')->countAllResults(),

    // Ruangan
    'total_ruangan' => $ruanganModel->where('deleted_at', null)->countAllResults(),
    'tersedia_ruangan' => $ruanganModel->where(['status' => 'Tersedia', 'deleted_at' => null])->countAllResults(),
    'dibooking_ruangan' => $ruanganModel->where(['status' => 'Dibooking', 'deleted_at' => null])->countAllResults(),
    'verifikasi_ruangan' => $pinjamRuanganModel->where('status', 'pending')->countAllResults(),

    // Barang
    'total_barang' => $barangModel->where('deleted_at', null)->countAllResults(),
    'tersedia_barang' => $barangModel->where(['status' => 'Tersedia', 'deleted_at' => null])->countAllResults(),
    'dipinjam_barang' => $barangModel->where(['status' => 'Dipinjam', 'deleted_at' => null])->countAllResults(),
    'verifikasi_barang' => $pinjamBarangModel->where('status', 'pending')->countAllResults(),
];

        // === RETURN VIEW ===
        return view('user/mainpage', array_merge($dataStatistik, [
            'riwayatKendaraan'   => $riwayatKendaraan,
            'perPage'            => $perPageKendaraan,
            'pager_links'        => $pager->makeLinks($pageKendaraan, $perPageKendaraan, $totalKendaraan, 'default_full'),
            'statusRuangan'      => $statusRuangan,
            'perPageRuangan'     => $perPageRuangan,
            'pager'              => $pager
        ]));
    }


    public function getStatistikKendaraanAPI()
{
    $db = \Config\Database::connect();
    $query = $db->query("SELECT TO_CHAR(tanggal_pinjam, 'YYYY-MM') AS label, COUNT(*) AS jumlah FROM pinjam GROUP BY 1 ORDER BY 1");
    return $this->response->setJSON($query->getResult());
}

public function getStatistikRuanganAPI()
{
    $db = \Config\Database::connect();
    $query = $db->query("SELECT TO_CHAR(tanggal, 'YYYY-MM') AS label, COUNT(*) AS jumlah FROM pinjam_ruangan WHERE status = 'selesai' GROUP BY 1 ORDER BY 1");
    return $this->response->setJSON($query->getResult());
}
}