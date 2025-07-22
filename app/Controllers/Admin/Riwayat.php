<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\PinjamModel;
use App\Models\KembaliModel;
use App\Models\AsetModel;
use App\Models\PinjamBarangModel;
use App\Models\PinjamRuanganModel;


class Riwayat extends Controller
{
    protected $pinjamModel;
    protected $kembaliModel;
    protected $asetModel;
    protected $pinjamBarangModel;
    protected $pinjamRuanganModel;

    public function __construct()
    {
        $this->pinjamModel = new PinjamModel();
        $this->kembaliModel = new KembaliModel();
        $this->asetModel = new AsetModel();
        $this->pinjamBarangModel = new PinjamBarangModel();
        $this->pinjamRuanganModel = new PinjamRuanganModel();
    }

    public function index()
    {

        return view('admin/riwayat/index');
    }

    public function kendaraan()
    {
    $peminjaman_history = $this->pinjamModel->select('
    pinjam.*, 
    assets.merk, 
    assets.no_polisi
')
->join('assets', 'assets.id = pinjam.kendaraan_id')
->where([
    'pinjam.status !=' => 'pending'
])
->orderBy('pinjam.created_at', 'DESC')
->find();

$pengembalian_history = $this->kembaliModel->select('
    kembali.*, 
    assets.merk, 
    assets.no_polisi
')
->join('assets', 'assets.id = kembali.kendaraan_id')
->where([
    'kembali.status !=' => 'pending'
])
->orderBy('kembali.created_at', 'DESC')
->find();

foreach ($peminjaman_history as &$pinjam) {
if (!empty($pinjam['tanggal_pinjam'])) {
    $pinjam['tanggal_pinjam'] = date('Y-m-d', strtotime($pinjam['tanggal_pinjam']));
}
if (!empty($pinjam['tanggal_kembali'])) {
    $pinjam['tanggal_kembali'] = date('Y-m-d', strtotime($pinjam['tanggal_kembali']));
}

switch ($pinjam['status']) {
    case 'disetujui':
        $pinjam['status_label'] = 'Disetujui';
        $pinjam['status_class'] = 'success';
        break;
    case 'ditolak':
        $pinjam['status_label'] = 'Ditolak';
        $pinjam['status_class'] = 'danger';
        break;
    default:
        $pinjam['status_label'] = 'Pending';
        $pinjam['status_class'] = 'warning';
}
}

foreach ($pengembalian_history as &$kembali) {
if (!empty($kembali['tanggal_pinjam'])) {
    $kembali['tanggal_pinjam'] = date('Y-m-d', strtotime($kembali['tanggal_pinjam']));
}
if (!empty($kembali['tanggal_kembali'])) {
    $kembali['tanggal_kembali'] = date('Y-m-d', strtotime($kembali['tanggal_kembali']));
}

switch ($kembali['status']) {
    case 'disetujui':
        $kembali['status_label'] = 'Disetujui';
        $kembali['status_class'] = 'success';
        break;
    case 'ditolak':
        $kembali['status_label'] = 'Ditolak';
        $kembali['status_class'] = 'danger';
        break;
    default:
        $kembali['status_label'] = 'Pending';
        $kembali['status_class'] = 'warning';
}
}

$data = [
'title' => 'Riwayat Peminjaman & Pengembalian',
'peminjaman_history' => $peminjaman_history,
'pengembalian_history' => $pengembalian_history
];
return view('admin/riwayat/kendaraan', $data);
    }

    public function detail($type, $id)
    {
        if ($type === 'peminjaman') {
            $detail = $this->pinjamModel->select('
                    pinjam.*, 
                    assets.merk, 
                    assets.no_polisi
                ')
                ->join('assets', 'assets.id = pinjam.kendaraan_id')
                ->find($id);
        } else {
            $detail = $this->kembaliModel->select('
                    kembali.*, 
                    assets.merk, 
                    assets.no_polisi
                ')
                ->join('assets', 'assets.id = kembali.kendaraan_id')
                ->find($id);
        }

        if (!$detail) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $detail
        ]);
    }
    public function barang()
    {
        $model = new \App\Models\PinjamBarangModel();

        $peminjaman_history = $model->select('pinjam_barang.*, barang.nama_barang, barang.kategori, barang.lokasi')
            ->join('barang', 'barang.id = pinjam_barang.barang_id')
            ->where('pinjam_barang.deleted_at', null)
            ->orderBy('pinjam_barang.created_at', 'DESC')
            ->findAll();
    
        $pengembalian_history = $model->select('pinjam_barang.*, barang.nama_barang, barang.kategori, barang.lokasi')
            ->join('barang', 'barang.id = pinjam_barang.barang_id')
            ->whereIn('pinjam_barang.status', ['proses_pengembalian', 'selesai'])
            ->where('pinjam_barang.deleted_at', null)
            ->orderBy('pinjam_barang.updated_at', 'DESC')
            ->findAll();
    
        $statusMap = [
            'diajukan'             => ['label' => 'Menunggu', 'class' => 'bg-warning', 'icon' => 'bi-clock'],
            'disetujui'            => ['label' => 'Disetujui', 'class' => 'bg-success', 'icon' => 'bi-check-circle'],
            'ditolak'              => ['label' => 'Ditolak', 'class' => 'bg-danger', 'icon' => 'bi-x-circle'],
            'proses_pengembalian' => ['label' => 'Proses Pengembalian', 'class' => 'bg-primary', 'icon' => 'bi-arrow-repeat'],
            'selesai'              => ['label' => 'Selesai', 'class' => 'bg-success', 'icon' => 'bi-check-circle-fill'],
        ];
    
        foreach ($peminjaman_history as &$row) {
            $status = $row['status'];
            $row['status_label'] = $statusMap[$status]['label'] ?? 'Tidak diketahui';
            $row['status_class'] = $statusMap[$status]['class'] ?? 'bg-secondary';
            $row['status_icon']  = $statusMap[$status]['icon'] ?? 'bi-question-circle';
        }
    
        foreach ($pengembalian_history as &$row) {
            $status = $row['status'];
            $row['status_label'] = $statusMap[$status]['label'] ?? 'Tidak diketahui';
            $row['status_class'] = $statusMap[$status]['class'] ?? 'bg-secondary';
            $row['status_icon']  = $statusMap[$status]['icon'] ?? 'bi-question-circle';
        }
    
        return view('admin/riwayat/barang', [
            'peminjaman_history'   => $peminjaman_history,
            'pengembalian_history' => $pengembalian_history
        ]);
    }
    public function ruangan()
{
    $db = \Config\Database::connect();

    $peminjaman_history = $db->table('pinjam_ruangan pr')
        ->select('pr.*, r.nama_ruangan, r.lokasi')
        ->join('ruangan r', 'r.id = pr.ruangan_id')
        ->orderBy('pr.created_at', 'DESC')
        ->get()
        ->getResultArray();

    $pengembalian_history = array_filter($peminjaman_history, function ($item) {
        return in_array($item['status'], ['proses_pengembalian', 'selesai']);
    });

    $statusMap = [
        'diajukan'             => ['label' => 'Menunggu', 'class' => 'bg-warning', 'icon' => 'bi-clock'],
        'disetujui'            => ['label' => 'Disetujui', 'class' => 'bg-success', 'icon' => 'bi-check-circle'],
        'ditolak'              => ['label' => 'Ditolak', 'class' => 'bg-danger', 'icon' => 'bi-x-circle'],
        'proses_pengembalian' => ['label' => 'Proses Pengembalian', 'class' => 'bg-primary', 'icon' => 'bi-arrow-repeat'],
        'selesai'              => ['label' => 'Selesai', 'class' => 'bg-secondary', 'icon' => 'bi-check-circle-fill'],
    ];

    foreach ($peminjaman_history as &$row) {
        $row['status_label'] = $statusMap[$row['status']]['label'] ?? 'Tidak diketahui';
        $row['status_class'] = $statusMap[$row['status']]['class'] ?? 'bg-secondary';
        $row['status_icon']  = $statusMap[$row['status']]['icon'] ?? 'bi-question-circle';
    }

    foreach ($pengembalian_history as &$row) {
        $row['status_label'] = $statusMap[$row['status']]['label'] ?? 'Tidak diketahui';
        $row['status_class'] = $statusMap[$row['status']]['class'] ?? 'bg-secondary';
        $row['status_icon']  = $statusMap[$row['status']]['icon'] ?? 'bi-question-circle';
    }

    return view('admin/riwayat/ruangan', [
        'peminjaman_history' => $peminjaman_history,
        'pengembalian_history' => $pengembalian_history
    ]);
}

}
