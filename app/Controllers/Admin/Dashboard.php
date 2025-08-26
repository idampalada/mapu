<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\PinjamModel;
use App\Models\KembaliModel;
use App\Models\AsetModel;
use App\Models\PemeliharaanRutinModel;
use App\Models\RuanganModel;
use App\Models\PinjamRuanganModel;
use App\Models\PinjamBarangModel;
use CodeIgniter\I18n\Time;

class Dashboard extends Controller
{
    protected $pinjamModel;
    protected $kembaliModel;
    protected $asetModel;
    protected $pemeliharaanModel;
    protected $ruanganModel;
    protected $pinjamRuanganModel;
    protected $pinjamBarangModel;
    protected $barangModel;

    public function __construct()
    {
        $this->pinjamModel = new PinjamModel();
        $this->kembaliModel = new KembaliModel();
        $this->asetModel = new AsetModel();
        $this->pemeliharaanModel = new PemeliharaanRutinModel();
        $this->ruanganModel = new RuanganModel();
        $this->pinjamRuanganModel = new PinjamRuanganModel();
        $this->pinjamBarangModel = new PinjamBarangModel();
        $this->barangModel = new \App\Models\BarangModel();

    }

    private function getStatistikRuangan()
    {
        $year = date('Y');
        $statistics = [];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = "{$year}-{$month}-01";
            $endDate = date('Y-m-t', strtotime($startDate));

            $count = $this->pinjamRuanganModel->where('status', 'disetujui')
                ->where('created_at >=', $startDate)
                ->where('created_at <=', $endDate)
                ->countAllResults();

            $statistics[] = $count;
        }

        return $statistics;
    }

    public function index()
    {
        $userModel = new \Myth\Auth\Models\UserModel();
        $user = $userModel->find(user_id());
         if ($user) {
        session()->set('user_role', $user->role);
    }

    // Ambil role terbaru dari session yang telah diperbarui
    $userRole = session()->get('user_role');
        $peminjaman_pending = $this->pinjamModel->select('pinjam.*, assets.merk')
            ->join('assets', 'assets.id = pinjam.kendaraan_id')
            ->where('pinjam.status', PinjamModel::STATUS_PENDING)
            ->where('pinjam.deleted_at', null)
            ->findAll();

        $pengembalian_pending = $this->kembaliModel->select('kembali.*, assets.merk')
            ->join('assets', 'assets.id = kembali.kendaraan_id')
            ->where('kembali.status', KembaliModel::STATUS_PENDING)
            ->where('kembali.deleted_at', null)
            ->findAll();

        if (in_groups('admin') || in_groups('admin_gedungutama')) {
        $total_kendaraan = $this->asetModel->where('deleted_at', null)->countAllResults();
        $kendaraan_tersedia = $this->asetModel->where('status_pinjam', 'Tersedia')
            ->where('deleted_at', null)
            ->countAllResults();
        $peminjaman_aktif = $this->asetModel->where('status_pinjam', 'Dipinjam')
            ->where('deleted_at', null)
            ->countAllResults();
        } else {
            // Jika bukan admin atau admin_gedungutama, set nilai kosong untuk kendaraan
            $total_kendaraan = 0;
            $kendaraan_tersedia = 0;
            $peminjaman_aktif = 0;
        }

        $ruangan_pending = $this->pinjamRuanganModel
            ->select('pinjam_ruangan.*, ruangan.nama_ruangan')
            ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id')
            ->where('pinjam_ruangan.status', 'pending')
            ->findAll();

        $ruangan_kembali_pending = $this->pinjamRuanganModel
            ->select('pinjam_ruangan.*, ruangan.nama_ruangan')
            ->join('ruangan', 'ruangan.id = pinjam_ruangan.ruangan_id')
            ->where('pinjam_ruangan.status', 'menunggu_verifikasi_pengembalian')
            ->findAll();
            $ruanganStats = $this->ruanganModel->getRuanganStats();

            $barang_pending = $this->pinjamBarangModel
            ->select('pinjam_barang.*, users.username AS nama_penanggung_jawab, barang.nama_barang')
            ->join('users', 'users.id = pinjam_barang.user_id')
            ->join('barang', 'barang.id = pinjam_barang.barang_id')
            ->where('pinjam_barang.status', 'diajukan')
            ->orderBy('pinjam_barang.created_at', 'DESC')
            ->findAll();

            $pengembalian_barang_pending = $this->pinjamBarangModel
            ->select('pinjam_barang.*, barang.nama_barang, users.username AS nama_peminjam, barang.lokasi')
            ->join('barang', 'barang.id = pinjam_barang.barang_id')
            ->join('users', 'users.id = pinjam_barang.user_id')
            ->where('pinjam_barang.status', 'proses_pengembalian')
            ->where('pinjam_barang.deleted_at', null)
            ->orderBy('pinjam_barang.updated_at', 'DESC')
            ->findAll();

            // Statistik barang
$total_barang = $this->barangModel->where('deleted_at', null)->countAllResults();

$barang_tersedia = $this->barangModel
    ->where('status', 'Tersedia') 
    ->where('deleted_at', null)
    ->countAllResults();

$barang_digunakan = $this->barangModel
    ->where('status', 'Dipinjam')
    ->where('deleted_at', null)
    ->countAllResults();
        

        $data = [
            'peminjaman_pending' => $peminjaman_pending,
            'pengembalian_pending' => $pengembalian_pending,
            'total_kendaraan' => $total_kendaraan,
            'kendaraan_tersedia' => $kendaraan_tersedia,
            'peminjaman_aktif' => $peminjaman_aktif,
            'status_kendaraan' => [
                'tersedia' => $kendaraan_tersedia,
                'dipinjam' => $peminjaman_aktif,
                'maintenance' => $this->asetModel->where('kondisi !=', 'Baik')
                    ->where('deleted_at', null)
                    ->countAllResults()
            ],
            'total_ruangan' => $ruanganStats['total'],
            'ruangan_tersedia' => $ruanganStats['tersedia'],
            'ruangan_digunakan' => $ruanganStats['digunakan'],
            'ruangan_pending' => $ruangan_pending,
            'ruangan_kembali_pending' => $ruangan_kembali_pending,
            'barang_pending' => $barang_pending,
            'pengembalian_barang_pending' => $pengembalian_barang_pending,
            'statistik_bulanan' => $this->getStatistikBulanan(),
            'kendaraan_maintenance' => $this->getKendaraanMaintenance(),
            'peminjaman_terbaru' => $this->getPeminjamanTerbaru(),
            'quick_info' => $this->getQuickInfo(),
            'barang_digunakan' => $barang_digunakan,
            'barang_tersedia' => $barang_tersedia,
            'total_barang' => $total_barang

        ];

        return view('admin/index', $data);
    }

    private function getStatistikBulanan()
    {
        $year = date('Y');
        $statistics = [];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = "{$year}-{$month}-01";
            $endDate = date('Y-m-t', strtotime($startDate));

            $count = $this->pinjamModel->where('status', 'disetujui')
                ->where('created_at >=', $startDate)
                ->where('created_at <=', $endDate)
                ->countAllResults();

            $statistics[] = $count;
        }

        return $statistics;
    }

    private function getKendaraanMaintenance()
    {
        return $this->asetModel->select('assets.*, pemeliharaan_rutin.tanggal_terjadwal, pemeliharaan_rutin.jenis_pemeliharaan')
            ->join('pemeliharaan_rutin', 'pemeliharaan_rutin.kendaraan_id = assets.id', 'left')
            ->where('pemeliharaan_rutin.status', PemeliharaanRutinModel::STATUS_PENDING)
            ->where('assets.deleted_at', null)
            ->where('pemeliharaan_rutin.deleted_at', null)
            ->orWhere('assets.kondisi', 'Rusak Ringan')
            ->orWhere('assets.kondisi', 'Rusak Berat')
            ->findAll();
    }

    private function getPeminjamanTerbaru()
    {
        return $this->pinjamModel->select('pinjam.*, assets.merk, assets.no_polisi')
            ->join('assets', 'assets.id = pinjam.kendaraan_id')
            ->where('pinjam.deleted_at', null)
            ->orderBy('pinjam.created_at', 'DESC')
            ->limit(5)
            ->findAll();
    }

    private function getQuickInfo()
    {
        $now = Time::now();

        $peminjaman_hari_ini = $this->pinjamModel
            ->where('DATE(created_at)', $now->toDateString())
            ->countAllResults();

        $kendaraan_overdue = $this->pinjamModel
            ->where('tanggal_kembali <', $now->toDateString())
            ->where('status', 'disetujui')
            ->where('deleted_at', null)
            ->countAllResults();

        $maintenance_mendatang = $this->pemeliharaanModel
            ->where('status', PemeliharaanRutinModel::STATUS_PENDING)
            ->where('tanggal_terjadwal >', $now->toDateString())
            ->where('deleted_at', null)
            ->countAllResults();

        return [
            'peminjaman_hari_ini' => $peminjaman_hari_ini,
            'kendaraan_overdue' => $kendaraan_overdue,
            'maintenance_mendatang' => $maintenance_mendatang
        ];
    }

    public function getStatistikAPI()
    {
        $statistik = $this->getStatistikBulanan();
        return $this->response->setJSON(['data' => $statistik]);
    }

    public function getStatusKendaraanAPI()
    {
        $status = $this->asetModel->select('status_pinjam, COUNT(*) as total')
            ->where('deleted_at', null)
            ->groupBy('status_pinjam')
            ->findAll();

        return $this->response->setJSON(['data' => $status]);
    }

    public function getMaintenanceKendaraanAPI()
    {
        $kendaraan = $this->getKendaraanMaintenance();
        return $this->response->setJSON(['data' => $kendaraan]);
    }

    public function getPeminjamanTerbaruAPI()
    {
        $peminjaman = $this->getPeminjamanTerbaru();
        return $this->response->setJSON(['data' => $peminjaman]);
    }
    public function getPengembalianRuanganAPI()
{
    $model = new \App\Models\KembaliModel(); // ganti jika modelnya beda
    $data = $model->findAll();
    return $this->response->setJSON($data);
}
// public function chartPeminjaman() data dummy
// {
//     $db = \Config\Database::connect();
//     $query = $db->query("
//         SELECT TO_CHAR(tanggal_pinjam, 'YYYY-MM') AS bulan, COUNT(*) AS jumlah
//         FROM pinjam
//         GROUP BY 1
//         ORDER BY 1
//     ");
//     return $this->response->setJSON($query->getResult());
// }
public function chartPeminjaman()
{
    $db = \Config\Database::connect();
    $query = $db->query("
        SELECT TO_CHAR(tanggal_pinjam, 'YYYY-MM') AS bulan, COUNT(*) AS jumlah
        FROM pinjam
        GROUP BY 1
        ORDER BY 1
    ");
    return $this->response->setJSON($query->getResult());
}

public function chartPengembalian()
{
    $db = \Config\Database::connect();
    $query = $db->query("
        SELECT TO_CHAR(tanggal_kembali, 'YYYY-MM') AS bulan, COUNT(*) AS jumlah
        FROM kembali
        GROUP BY 1
        ORDER BY 1
    ");
    return $this->response->setJSON($query->getResult());
}
// public function chartPeminjamanBulanan() data dummy
// {
//     $db = \Config\Database::connect();
//     $data = [
//         ['label' => 'Januari', 'jumlah' => 12],
//         ['label' => 'Februari', 'jumlah' => 19],
//         ['label' => 'Maret', 'jumlah' => 23],
//         ['label' => 'April', 'jumlah' => 30],
//         ['label' => 'Mei', 'jumlah' => 15],
//         ['label' => 'Juni', 'jumlah' => 17],
//         ['label' => 'Juli', 'jumlah' => 25],
//         ['label' => 'Agustus', 'jumlah' => 22],
//         ['label' => 'September', 'jumlah' => 18],
//         ['label' => 'Oktober', 'jumlah' => 26],
//         ['label' => 'November', 'jumlah' => 29],
//         ['label' => 'Desember', 'jumlah' => 31],
//     ];
//     return $this->response->setJSON($data);
// }
public function chartPeminjamanBulanan()
{
    $builder = $this->pinjamModel->builder();
    $result = $builder->select("TO_CHAR(tanggal_pinjam, 'YYYY-MM') as label, COUNT(*) as jumlah")
                     ->groupBy("TO_CHAR(tanggal_pinjam, 'YYYY-MM')")
                     ->orderBy("TO_CHAR(tanggal_pinjam, 'YYYY-MM')")
                     ->get()
                     ->getResult();
    
    return $this->response->setJSON($result);
}

// public function chartPeminjamanMingguan() Data Dummy
// {
//     $bulan = $this->request->getGet('bulan');
//     $minggu = (int) $this->request->getGet('minggu');

//     if (!$bulan || $minggu < 1 || $minggu > 5) {
//         return $this->response->setJSON([]);
//     }

//     $db = \Config\Database::connect();
//     $data = [
//         ['label' => '2025-04-01', 'jumlah' => 3],
//         ['label' => '2025-04-02', 'jumlah' => 5],
//         ['label' => '2025-04-03', 'jumlah' => 2],
//         ['label' => '2025-04-04', 'jumlah' => 7],
//         ['label' => '2025-04-05', 'jumlah' => 4],
//         ['label' => '2025-04-06', 'jumlah' => 6],
//         ['label' => '2025-04-07', 'jumlah' => 1],
//     ];
//     return $this->response->setJSON($data);
// }
public function chartPeminjamanMingguan()
{
    $bulan = $this->request->getGet('bulan');
    $minggu = (int) $this->request->getGet('minggu');

    if (!$bulan || $minggu < 1 || $minggu > 5) {
        return $this->response->setJSON([]);
    }

    $db = \Config\Database::connect();
    $query = $db->query(
        "SELECT tanggal_pinjam::date AS label, COUNT(*) AS jumlah
         FROM pinjam
         WHERE TO_CHAR(tanggal_pinjam, 'YYYY-MM') = ?
           AND EXTRACT(WEEK FROM tanggal_pinjam) - EXTRACT(WEEK FROM DATE_TRUNC('month', tanggal_pinjam)) + 1 = ?
         GROUP BY tanggal_pinjam
         ORDER BY tanggal_pinjam",
        [$bulan, $minggu]
    );

    return $this->response->setJSON($query->getResult());
}


public function chartPeminjamanHarian()
{
    $tanggal = $this->request->getGet('tanggal');
    if (!$tanggal) {
        return $this->response->setJSON([]);
    }

    $db = \Config\Database::connect();
    $query = $db->query(
        "SELECT u.username AS label, COUNT(*) AS jumlah
         FROM pinjam p
         JOIN users u ON u.id = p.user_id
         WHERE p.tanggal_pinjam::date = ?
         GROUP BY u.username
         ORDER BY jumlah DESC",
        [$tanggal]
    );

    return $this->response->setJSON($query->getResult());
}
public function chartPengembalianBulanan()
{
    $db = \Config\Database::connect();
    $query = $db->query("
        SELECT TO_CHAR(tanggal_kembali, 'YYYY-MM') AS label, COUNT(*) AS jumlah
        FROM kembali
        GROUP BY label
        ORDER BY label
    ");
    return $this->response->setJSON($query->getResult());
}

public function chartPengembalianMingguan()
{
    $bulan = $this->request->getGet('bulan');
    $minggu = (int) $this->request->getGet('minggu');

    if (!$bulan || $minggu < 1 || $minggu > 5) {
        return $this->response->setJSON([]);
    }

    $db = \Config\Database::connect();
    $query = $db->query(
        "SELECT TO_CHAR(tanggal_kembali, 'YYYY-MM-DD') AS label, COUNT(*) AS jumlah
         FROM kembali
         WHERE TO_CHAR(tanggal_kembali, 'YYYY-MM') = ?
           AND EXTRACT(WEEK FROM tanggal_kembali) - EXTRACT(WEEK FROM DATE_TRUNC('month', tanggal_kembali)) + 1 = ?
         GROUP BY label
         ORDER BY label",
        [$bulan, $minggu]
    );

    return $this->response->setJSON($query->getResult());
}

public function chartPengembalianHarian()
{
    $tanggal = $this->request->getGet('tanggal');
    if (!$tanggal) {
        return $this->response->setJSON([]);
    }

    $db = \Config\Database::connect();
    $query = $db->query(
        "SELECT u.username AS label, COUNT(*) AS jumlah
         FROM kembali k
         JOIN users u ON u.id = k.user_id
         WHERE DATE(k.tanggal_kembali) = ?
         GROUP BY label
         ORDER BY jumlah DESC",
        [$tanggal]
    );

    return $this->response->setJSON($query->getResult());
}
public function getPengembalianBarangPending()
{
    $model = new \App\Models\PinjamBarangModel();
    $data = $model->where('status', 'dipinjam')
                  ->where('deleted_at', null)
                  ->join('barang', 'barang.id = pinjam_barang.barang_id')
                  ->select('pinjam_barang.*, barang.nama_barang, barang.lokasi')
                  ->orderBy('tanggal', 'DESC')
                  ->findAll();
    return $this->response->setJSON($data);
}

public function chartPeminjamanBarangBulanan()
{
    $db = \Config\Database::connect();
    $query = $db->query("
        SELECT TO_CHAR(tanggal, 'YYYY-MM') AS label, COUNT(*) AS jumlah
        FROM pinjam_barang
        GROUP BY label
        ORDER BY label
    ");
    return $this->response->setJSON($query->getResult());
}
public function chartPeminjamanBarangMingguan()
{
    $bulan = $this->request->getGet('bulan');
    $minggu = (int) $this->request->getGet('minggu');

    if (!$bulan || $minggu < 1 || $minggu > 5) {
        return $this->response->setJSON([]);
    }

    $db = \Config\Database::connect();
    $query = $db->query(
        "SELECT tanggal::date AS label, COUNT(*) AS jumlah
         FROM pinjam_barang
         WHERE TO_CHAR(tanggal, 'YYYY-MM') = ?
           AND EXTRACT(WEEK FROM tanggal) - EXTRACT(WEEK FROM DATE_TRUNC('month', tanggal)) + 1 = ?
         GROUP BY tanggal
         ORDER BY tanggal",
        [$bulan, $minggu]
    );

    return $this->response->setJSON($query->getResult());
}
public function chartPeminjamanBarangHarian()
{
    $tanggal = $this->request->getGet('tanggal');
    if (!$tanggal) {
        return $this->response->setJSON([]);
    }

    $db = \Config\Database::connect();
    $query = $db->query(
        "SELECT u.username AS label, COUNT(*) AS jumlah
         FROM pinjam_barang pb
         JOIN users u ON u.id = pb.user_id
         WHERE DATE(pb.tanggal) = ?
         GROUP BY u.username
         ORDER BY jumlah DESC",
        [$tanggal]
    );

    return $this->response->setJSON($query->getResult());
}


}