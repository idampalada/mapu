<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

    <div class="header-section py-4 px-3 mb-4 rounded-lg" style="background: linear-gradient(135deg, #2D3748 0%, #1E293B 100%);">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="text-white mb-0 display-6 fw-bold">Sistem Manajemen Aset</h2>
                <p class="text-light mb-0 opacity-75">Halaman Mainpage</p>
            </div>
            <div class="col-md-6">
                <nav aria-label="breadcrumb" class="d-flex justify-content-md-end">
                    <ol class="breadcrumb mb-0 bg-transparent py-2 px-3 rounded" style="background-color: rgba(255,255,255,0.1);">
                        <li class="breadcrumb-item"><a href="#" class="text-light">Beranda</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Mainpage</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
<!-- Statistik Cards Row -->
<div class="row g-4 mb-5">
    <!-- Kendaraan Stats -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-gradient-primary text-white border-bottom-0 py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-car-front-fill fs-4 me-2"></i>
                    <h5 class="mb-0 fw-semibold text-white">Statistik Kendaraan</h5>
                </div>
            </div>
            <div class="card-body" style="background-color: #f8f9fe;">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="bg-white rounded-3 p-3 h-100 shadow-sm border-start border-5 border-dark">
                            <span class="d-block text-muted small">Total</span>
                            <h3 class="mb-0 fw-bold text-dark" id="totalKendaraan"><?= esc($total_kendaraan ?? 0) ?></h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white rounded-3 p-3 h-100 shadow-sm border-start border-5 border-success">
                            <span class="d-block text-success small">Tersedia</span>
                            <h3 class="mb-0 fw-bold text-success" id="kendaraanTersedia"><?= esc($tersedia_kendaraan ?? 0) ?></h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white rounded-3 p-3 h-100 shadow-sm border-start border-5 border-info">
                            <span class="d-block text-info small">Dipinjam</span>
                            <h3 class="mb-0 fw-bold text-info" id="kendaraanDipinjam"><?= esc($dipinjam_kendaraan ?? 0) ?></h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white rounded-3 p-3 h-100 shadow-sm border-start border-5 border-warning">
                            <span class="d-block text-warning small">Verifikasi</span>
                            <h3 class="mb-0 fw-bold text-warning" id="kendaraanVerifikasi"><?= esc($verifikasi_kendaraan ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ruangan Stats -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-gradient-danger text-white border-bottom-0 py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-building fs-4 me-2"></i>
                    <h5 class="mb-0 fw-semibold">Statistik Ruangan</h5>
                </div>
            </div>
            <div class="card-body" style="background-color: #fef8f9;">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="bg-white rounded-3 p-3 h-100 shadow-sm border-start border-5 border-dark">
                            <span class="d-block text-muted small">Total</span>
                            <h3 class="mb-0 fw-bold text-dark" id="totalRuangan"><?= esc($total_ruangan ?? 0) ?></h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white rounded-3 p-3 h-100 shadow-sm border-start border-5 border-success">
                            <span class="d-block text-success small">Tersedia</span>
                            <h3 class="mb-0 fw-bold text-success" id="ruanganTersedia"><?= esc($tersedia_ruangan ?? 0) ?></h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white rounded-3 p-3 h-100 shadow-sm border-start border-5 border-danger">
                            <span class="d-block text-danger small">Dibooking</span>
                            <h3 class="mb-0 fw-bold text-danger" id="ruanganDibooking"><?= esc($dibooking_ruangan ?? 0) ?></h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white rounded-3 p-3 h-100 shadow-sm border-start border-5 border-warning">
                            <span class="d-block text-warning small">Verifikasi</span>
                            <h3 class="mb-0 fw-bold text-warning" id="ruanganVerifikasi"><?= esc($verifikasi_ruangan ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barang Stats -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-gradient-success text-white border-bottom-0 py-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-box-seam fs-4 me-2"></i>
                    <h5 class="mb-0 fw-semibold">Statistik Barang</h5>
                </div>
            </div>
            <div class="card-body" style="background-color: #f8fef9;">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="bg-white rounded-3 p-3 h-100 shadow-sm border-start border-5 border-dark">
                            <span class="d-block text-muted small">Total</span>
                            <h3 class="mb-0 fw-bold text-dark" id="totalBarang"><?= esc($total_barang ?? 0) ?></h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white rounded-3 p-3 h-100 shadow-sm border-start border-5 border-success">
                            <span class="d-block text-success small">Tersedia</span>
                            <h3 class="mb-0 fw-bold text-success" id="barangTersedia"><?= esc($tersedia_barang ?? 0) ?></h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white rounded-3 p-3 h-100 shadow-sm border-start border-5 border-purple" style="--bs-border-color: #6f42c1;">
                            <span class="d-block small" style="color: #6f42c1;">Dipinjam</span>
                            <h3 class="mb-0 fw-bold" style="color: #6f42c1;" id="barangDipinjam"><?= esc($dipinjam_barang ?? 0) ?></h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white rounded-3 p-3 h-100 shadow-sm border-start border-5 border-warning">
                            <span class="d-block text-warning small">Verifikasi</span>
                            <h3 class="mb-0 fw-bold text-warning" id="barangVerifikasi"><?= esc($verifikasi_barang ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(45deg, #4e73df, #2e59d9);
    }
    
    .bg-gradient-success {
        background: linear-gradient(45deg, #1cc88a, #13855c);
    }
    
    .bg-gradient-danger {
        background: linear-gradient(45deg, #e74a3b, #be2617);
    }
    
    .card {
        transition: transform 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
</style>

    <!-- Charts Row -->
    <div class="row g-4 mb-5">
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-semibold">Tren Peminjaman Kendaraan</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownKendaraan" data-bs-toggle="dropdown" aria-expanded="false">
                            Tahun 2025
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownKendaraan">
                            <li><a class="dropdown-item" href="#">2025</a></li>
                            <li><a class="dropdown-item" href="#">2024</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="peminjamanKendaraanChart" style="height: 300px !important;"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-semibold">Tren Peminjaman Ruangan</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownRuangan" data-bs-toggle="dropdown" aria-expanded="false">
                            Tahun 2025
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownRuangan">
                            <li><a class="dropdown-item" href="#">2025</a></li>
                            <li><a class="dropdown-item" href="#">2024</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="peminjamanRuanganChart" style="height: 300px !important;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
    <div class="row g-4">
        <!-- Status Kendaraan -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-semibold">Status Kendaraan</h5>
                    <form method="get" class="d-flex align-items-center">
                        <label for="per_page" class="form-label me-2 mb-0 small">Tampilkan</label>
                        <select name="per_page" id="per_page" class="form-select form-select-sm me-2" style="width: 70px" onchange="this.form.submit()">
                            <option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
                            <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
                            <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                        </select>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3">Tanggal Pinjam</th>
                                    <th>Tanggal Kembali</th>
                                    <th>No Polisi</th>
                                    <th>Kendaraan</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($riwayatKendaraan)): ?>
                                    <?php foreach ($riwayatKendaraan as $row): ?>
                                        <tr>
                                            <td class="px-3"><?= esc($row->tanggal_pinjam) ?></td>
                                            <td><?= esc($row->tanggal_kembali) ?></td>
                                            <td><?= esc($row->no_polisi) ?></td>
                                            <td><?= esc($row->merk) ?></td>
                                            <td class="text-center">
                                                <?php
                                                $statusClass = '';
                                                switch(strtolower($row->status)) {
                                                    case 'dipinjam':
                                                        $statusClass = 'badge bg-primary';
                                                        break;
                                                    case 'tersedia':
                                                        $statusClass = 'badge bg-success';
                                                        break;
                                                    case 'verifikasi':
                                                        $statusClass = 'badge bg-warning';
                                                        break;
                                                    default:
                                                        $statusClass = 'badge bg-secondary';
                                                }
                                                ?>
                                                <span class="<?= $statusClass ?>"><?= ucfirst(esc($row->status)) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Tidak ada data peminjaman kendaraan.</td>
                                    </tr>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-center">
                        <?= $pager->links('default', 'default_full') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Ruangan -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-semibold">Status Ruangan</h5>
                    <form method="get" class="d-flex align-items-center">
                        <label for="per_page_ruangan" class="form-label me-2 mb-0 small">Tampilkan</label>
                        <select name="per_page_ruangan" id="per_page_ruangan" class="form-select form-select-sm me-2" style="width: 70px" onchange="this.form.submit()">
                            <option value="5" <?= $perPageRuangan == 5 ? 'selected' : '' ?>>5</option>
                            <option value="10" <?= $perPageRuangan == 10 ? 'selected' : '' ?>>10</option>
                            <option value="25" <?= $perPageRuangan == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?= $perPageRuangan == 50 ? 'selected' : '' ?>>50</option>
                        </select>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3">Nama Ruangan</th>
                                    <th>Lokasi</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($statusRuangan)): ?>
                                    <?php foreach ($statusRuangan as $row): ?>
                                        <tr>
                                            <td class="px-3"><?= esc($row->nama_ruangan) ?></td>
                                            <td><?= esc($row->lokasi) ?></td>
                                            <td><?= date('d M Y', strtotime($row->tanggal)) ?></td>
                                            <td><?= esc($row->waktu_mulai) ?> - <?= esc($row->waktu_selesai) ?></td>
                                            <td class="text-center">
                                                <?php
                                                $statusClass = '';
                                                switch(strtolower($row->status)) {
                                                    case 'dibooking':
                                                        $statusClass = 'badge bg-primary';
                                                        break;
                                                    case 'tersedia':
                                                        $statusClass = 'badge bg-success';
                                                        break;
                                                    case 'verifikasi':
                                                        $statusClass = 'badge bg-warning';
                                                        break;
                                                    default:
                                                        $statusClass = 'badge bg-secondary';
                                                }
                                                ?>
                                                <span class="<?= $statusClass ?>"><?= ucfirst(esc($row->status)) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Tidak ada data peminjaman ruangan.</td>
                                    </tr>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-center">
                        <?= $pager->links('ruangan', 'default_full') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif ?>
</div>

<!-- Add this to your content section -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Chart configuration for Kendaraan
    const ctxKendaraan = document.getElementById('peminjamanKendaraanChart').getContext('2d');
    const kendaraanChart = new Chart(ctxKendaraan, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Peminjaman',
                data: [12, 19, 13, 15, 20, 25, 22, 19, 23, 25, 18, 15],
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Chart configuration for Ruangan
    const ctxRuangan = document.getElementById('peminjamanRuanganChart').getContext('2d');
    const ruanganChart = new Chart(ctxRuangan, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Peminjaman',
                data: [8, 15, 12, 18, 25, 22, 30, 25, 20, 15, 10, 12],
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                borderColor: 'rgba(25, 135, 84, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>

<!-- Custom pagination style -->
<style>
.pagination .page-item .page-link {
    color: #0d6efd;
    border: none;
    margin: 0 2px;
    border-radius: 4px;
}
.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    color: white;
}
</style>

<?= $this->endSection() ?>