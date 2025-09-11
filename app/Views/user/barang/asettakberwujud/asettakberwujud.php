<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Aset Tak Berwujud
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Aset Tak Berwujud</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('user/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('user/barang') ?>">Barang</a></li>
        <li class="breadcrumb-item active">Aset Tak Berwujud</li>
    </ol>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Info Card -->
    <div class="row mb-4">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Informasi Aset Tak Berwujud
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        Halaman ini mengelola data Aset Tak Berwujud yang terdiri dari:
                    </p>
                    <ul class="mt-2">
                        <li><strong>Aset Tak Berwujud</strong> - Aset yang sudah selesai dan dapat digunakan</li>
                        <li><strong>Aset Tak Berwujud Dalam Penyelesaian</strong> - Aset yang masih dalam proses pengembangan</li>
                        <li><strong>Aset Kemitraan</strong> - Aset yang dimiliki bersama dengan pihak lain</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                Total Aset Tak Berwujud
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                <?= number_format(count(array_filter($asetTakBerwujudList ?? [], function($item) {
                                    return strtoupper($item['kelompok'] ?? '') === 'ASET TAK BERWUJUD';
                                }))) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?= base_url('user/barang/asettakberwujud/kelompokasettakberwujud/ASET TAK BERWUJUD') ?>">
                        View Details
                    </a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                Aset Dalam Penyelesaian
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                <?= number_format(count(array_filter($asetTakBerwujudList ?? [], function($item) {
                                    return strtoupper($item['kelompok'] ?? '') === 'ASET TAK BERWUJUD DALAM PENYELESAIAN';
                                }))) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?= base_url('user/barang/asettakberwujud/kelompokasettakberwujud/ASET TAK BERWUJUD DALAM PENYELESAIAN') ?>">
                        View Details
                    </a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                Aset Kemitraan
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                <?= number_format(count(array_filter($asetTakBerwujudList ?? [], function($item) {
                                    return strtoupper($item['kelompok'] ?? '') === 'ASET KEMITRAAN';
                                }))) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?= base_url('user/barang/asettakberwujud/kelompokasettakberwujud/ASET KEMITRAAN') ?>">
                        View Details
                    </a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-tools me-1"></i>
                    Aksi Cepat
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('user/barang/asettakberwujud/kelompokasettakberwujud') ?>" class="btn btn-primary w-100">
                                <i class="fas fa-list me-1"></i>
                                Lihat Semua Data
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('user/barang/asettakberwujud/stats') ?>" class="btn btn-info w-100">
                                <i class="fas fa-chart-bar me-1"></i>
                                Statistik
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-download me-1"></i>
                                Import dari API
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('user/barang/asettakberwujud/test-api') ?>" class="btn btn-secondary w-100" target="_blank">
                                <i class="fas fa-plug me-1"></i>
                                Test API
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Data Aset Tak Berwujud (Semua Kategori)
        </div>
        <div class="card-body">
            <?php if (!empty($asetTakBerwujudList)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatablesSimple">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Kelompok</th>
                            <th>Merk</th>
                            <th>Kondisi</th>
                            <th>Nilai Perolehan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach (array_slice($asetTakBerwujudList, 0, 100) as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($item['kode_barang'] ?? '') ?></td>
                            <td><?= esc($item['nama_barang'] ?? '') ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    strtoupper($item['kelompok'] ?? '') === 'ASET TAK BERWUJUD' ? 'primary' : 
                                    (strtoupper($item['kelompok'] ?? '') === 'ASET TAK BERWUJUD DALAM PENYELESAIAN' ? 'warning' : 'success') 
                                ?>">
                                    <?= esc($item['kelompok'] ?? '') ?>
                                </span>
                            </td>
                            <td><?= esc($item['merk'] ?? '') ?></td>
                            <td>
                                <?php
                                $kondisi = strtoupper($item['kondisi'] ?? '');
                                $badgeClass = $kondisi === 'BAIK' ? 'success' : ($kondisi === 'RUSAK RINGAN' ? 'warning' : 'danger');
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>">
                                    <?= esc($item['kondisi'] ?? '') ?>
                                </span>
                            </td>
                            <td>Rp <?= number_format($item['nilai_perolehan'] ?? 0, 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (count($asetTakBerwujudList) > 100): ?>
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-1"></i>
                Menampilkan 100 data pertama dari total <?= count($asetTakBerwujudList) ?> data. 
                <a href="<?= base_url('user/barang/asettakberwujud/kelompokasettakberwujud') ?>">Lihat semua data</a>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Belum ada data aset tak berwujud. Silakan import data dari API atau tambah data manual.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data dari API</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mengimpor data aset tak berwujud dari API SIMAN?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Catatan:</strong> Proses ini akan mengambil semua data aset tak berwujud dari API dan menyimpannya ke database lokal.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?= base_url('user/barang/asettakberwujud/importFromApi') ?>" method="post" style="display: inline;">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download me-1"></i>
                        Import Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- DataTables CSS & JS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#datatablesSimple').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
        },
        "pageLength": 25,
        "order": [[1, "asc"]]
    });
});
</script>

<?= $this->endSection() ?>