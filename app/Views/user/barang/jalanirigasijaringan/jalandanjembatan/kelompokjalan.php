<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Jalan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 1400px;
            margin: auto;
            padding: 20px;
        }
        h1 {
            color: #2c5282;
            margin-bottom: 30px;
            font-size: 32px;
            position: relative;
            padding-bottom: 15px;
            text-align: center;
        }
        h1:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background-color: #2c5282;
        }
        .table-premium-blue th {
            background-color: #2c5282 !important;
            color: white;
            font-weight: 600;
        }
        .table-bordered {
            border-color: #d4d4d4;
        }
        .table-bordered tbody tr:hover {
            background-color: #f2f2f2;
        }
        .table-bordered td {
            border-color: #e6e6e6;
        }
        .badge.bg-light {
            background-color: #e6e6e6 !important;
            color: #333333 !important;
            border: 1px solid #d4d4d4;
        }
        .badge.bg-success {
            background-color: #3c8765 !important;
        }
        .badge.bg-warning {
            background-color: #d19a26 !important;
            color: #ffffff !important;
        }
        .badge.bg-danger {
            background-color: #b54b4b !important;
        }
        .badge.bg-secondary {
            background-color: #6c757d !important;
        }
        .badge.bg-primary {
            background-color: #2c5282 !important;
        }
    </style>
</head>
<body>
    <div class="container">
    <h1>Data Jalan</h1>

    <!-- Pesan Flash -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tombol Import -->
    <div class="mb-3 text-end">
        <form action="<?= base_url('user/barang/jalanirigasijaringan/jalan/importFromApi') ?>" method="post" class="d-inline">
            <button type="submit" class="btn btn-success" onclick="return confirm('Import/sync data dari API Jalan?')">
                <i class="bi bi-cloud-download"></i> Import/Sync API
            </button>
        </form>
        
        <form action="<?= base_url('user/barang/jalanirigasijaringan/jalan/resetData') ?>" method="post" class="d-inline">
            <button type="submit" class="btn btn-danger" onclick="return confirm('PERINGATAN: Menghapus SEMUA data jalan! Yakin?')">
                <i class="bi bi-trash"></i> Reset Data
            </button>
        </form>
    </div>

    <!-- Info bantuan -->
    <div class="alert alert-info mb-3">
        <h6><i class="bi bi-info-circle"></i> Petunjuk Import:</h6>
        <ul class="mb-0">
            <li><strong>Import/Sync API:</strong> Mengambil data jalan dari API</li>
            <li><strong>Reset Data:</strong> Menghapus semua data dari database</li>
        </ul>
    </div>

    <!-- Pencarian -->
    <form method="GET" class="d-flex mb-4">
        <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama, kode, merk, konstruksi" value="<?= esc($searchTerm ?? '') ?>">
        <button type="submit" class="btn btn-primary ms-2">Cari</button>
    </form>

    <!-- Tombol Urutkan Data dan Ekspor -->
    <div class="d-flex justify-content-between mb-3">
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown">
                <i class="fas fa-sort"></i> Urutkan Data
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item <?= (isset($sort) && $sort == 'kode_barang' && isset($order) && $order == 'asc') ? 'active' : '' ?>" href="<?= base_url('user/barang/jalanirigasijaringan/jalan?sort=kode_barang&order=asc') ?>">
                    <i class="fas fa-sort-alpha-down"></i> Kode Barang (A-Z)
                </a></li>
                <li><a class="dropdown-item <?= (isset($sort) && $sort == 'kode_barang' && isset($order) && $order == 'desc') ? 'active' : '' ?>" href="<?= base_url('user/barang/jalanirigasijaringan/jalan?sort=kode_barang&order=desc') ?>">
                    <i class="fas fa-sort-alpha-up-alt"></i> Kode Barang (Z-A)
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item <?= (isset($sort) && $sort == 'nama_barang' && isset($order) && $order == 'asc') ? 'active' : '' ?>" href="<?= base_url('user/barang/jalanirigasijaringan/jalan?sort=nama_barang&order=asc') ?>">
                    <i class="fas fa-sort-alpha-down"></i> Nama Barang (A-Z)
                </a></li>
                <li><a class="dropdown-item <?= (isset($sort) && $sort == 'nama_barang' && isset($order) && $order == 'desc') ? 'active' : '' ?>" href="<?= base_url('user/barang/jalanirigasijaringan/jalan?sort=nama_barang&order=desc') ?>">
                    <i class="fas fa-sort-alpha-up-alt"></i> Nama Barang (Z-A)
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item <?= (isset($sort) && $sort == 'panjang' && isset($order) && $order == 'desc') ? 'active' : '' ?>" href="<?= base_url('user/barang/jalanirigasijaringan/jalan?sort=panjang&order=desc') ?>">
                    <i class="fas fa-sort-numeric-up-alt"></i> Panjang (Terpanjang)
                </a></li>
                <li><a class="dropdown-item <?= (isset($sort) && $sort == 'nilai_perolehan' && isset($order) && $order == 'desc') ? 'active' : '' ?>" href="<?= base_url('user/barang/jalanirigasijaringan/jalan?sort=nilai_perolehan&order=desc') ?>">
                    <i class="fas fa-sort-numeric-up-alt"></i> Nilai Perolehan (Tertinggi)
                </a></li>
            </ul>
        </div>

        <!-- Tombol Ekspor Data -->
        <div>
            <a href="<?= base_url('user/barang/jalanirigasijaringan/jalan/exportJalanList') ?>" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Ekspor Data Jalan
            </a>
        </div>
    </div>

    <div class="mb-3 text-end">
        <button class="btn btn-primary" id="toggleFormBtn">
            <i class="bi bi-plus-lg"></i> Tambah Data Jalan
        </button>
    </div>

    <!-- Form Tambah Jalan -->
    <div class="card mb-4" id="formTambahJalan" style="display: none;">
        <div class="card-header bg-primary text-white">
            <strong>Form Tambah Data Jalan</strong>
        </div>
        <div class="card-body">
            <form action="<?= base_url('user/barang/jalanirigasijaringan/jalan/tambah') ?>" method="post">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kode_barang" class="form-label">Kode Barang</label>
                        <input type="text" name="kode_barang" class="form-control" required value="<?= old('kode_barang') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required value="<?= old('nama_barang') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nup" class="form-label">NUP</label>
                        <input type="text" name="nup" class="form-control" value="<?= old('nup') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="merk" class="form-label">Merk/Kontraktor</label>
                        <input type="text" name="merk" class="form-control" value="<?= old('merk') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sub_kelompok" class="form-label">Sub Kelompok</label>
                        <input type="text" name="sub_kelompok" class="form-control" value="<?= old('sub_kelompok') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kondisi" class="form-label">Kondisi</label>
                        <select name="kondisi" class="form-select">
                            <option value="">-- Pilih Kondisi --</option>
                            <option value="BAIK" <?= old('kondisi') === 'BAIK' ? 'selected' : '' ?>>Baik</option>
                            <option value="RUSAK RINGAN" <?= old('kondisi') === 'RUSAK RINGAN' ? 'selected' : '' ?>>Rusak Ringan</option>
                            <option value="RUSAK BERAT" <?= old('kondisi') === 'RUSAK BERAT' ? 'selected' : '' ?>>Rusak Berat</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="konstruksi" class="form-label">Konstruksi</label>
                        <input type="text" name="konstruksi" class="form-control" value="<?= old('konstruksi') ?>" placeholder="Contoh: Aspal, Beton, dll">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kuantitas" class="form-label">Kuantitas</label>
                        <input type="number" name="kuantitas" class="form-control" value="<?= old('kuantitas') ?: 1 ?>" min="1">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="panjang" class="form-label">Panjang (m)</label>
                        <input type="number" name="panjang" step="0.01" class="form-control" value="<?= old('panjang') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="lebar" class="form-label">Lebar (m)</label>
                        <input type="number" name="lebar" step="0.01" class="form-control" value="<?= old('lebar') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="luas" class="form-label">Luas (m²)</label>
                        <input type="number" name="luas" step="0.01" class="form-control" value="<?= old('luas') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nilai_perolehan" class="form-label">Nilai Perolehan</label>
                        <input type="number" name="nilai_perolehan" step="0.01" class="form-control" value="<?= old('nilai_perolehan') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nilai_buku" class="form-label">Nilai Buku</label>
                        <input type="number" name="nilai_buku" step="0.01" class="form-control" value="<?= old('nilai_buku') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_perolehan" class="form-label">Tanggal Perolehan</label>
                        <input type="date" name="tanggal_perolehan" class="form-control" value="<?= old('tanggal_perolehan') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama_satker" class="form-label">Nama Satker</label>
                        <input type="text" name="nama_satker" class="form-control" value="<?= old('nama_satker') ?>">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="status_penggunaan" class="form-label">Status Penggunaan</label>
                        <textarea name="status_penggunaan" class="form-control" rows="2"><?= old('status_penggunaan') ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success mt-3">
                    <i class="bi bi-check-circle"></i> Simpan Data Jalan
                </button>
            </form>
        </div>
    </div>

    <?php if (!empty($jalanList)): ?>
    <div class="table-responsive mt-5">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-premium-blue">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Kode Barang</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Merk/Kontraktor</th>
                    <th class="text-center">NUP</th>
                    <th class="text-center">Sub Kelompok</th>
                    <th class="text-center">Konstruksi</th>
                    <th class="text-center">Kondisi</th>
                    <th class="text-center">Panjang (m)</th>
                    <th class="text-center">Lebar (m)</th>
                    <th class="text-center">Luas (m²)</th>
                    <th class="text-center">Nilai Perolehan</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($jalanList as $item): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><span class="badge bg-light text-dark"><?= esc($item['kode_barang'] ?? '-') ?></span></td>
                        <td class="fw-medium"><?= esc($item['nama_barang'] ?? '-') ?></td>
                        <td><?= esc($item['merk'] ?? '-') ?></td>
                        <td><?= esc($item['nup'] ?? '-') ?></td>
                        <td><?= esc($item['sub_kelompok'] ?? '-') ?></td>
                        <td>
                            <?php if (!empty($item['konstruksi'])): ?>
                                <span class="badge bg-primary"><?= esc($item['konstruksi']) ?></span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php 
                                $kondisiClass = 'secondary';
                                if (!empty($item['kondisi'])) {
                                    $kondisi = strtolower($item['kondisi']);
                                    if (strpos($kondisi, 'baik') !== false) {
                                        $kondisiClass = 'success';
                                    } elseif (strpos($kondisi, 'rusak ringan') !== false) {
                                        $kondisiClass = 'warning';
                                    } elseif (strpos($kondisi, 'rusak berat') !== false || strpos($kondisi, 'rusak') !== false) {
                                        $kondisiClass = 'danger';
                                    }
                                }
                            ?>
                            <span class="badge bg-<?= $kondisiClass ?>"><?= esc($item['kondisi'] ?? '-') ?></span>
                        </td>
                        <td class="text-end">
                            <?php if (!empty($item['panjang']) && $item['panjang'] > 0): ?>
                                <?= number_format(floatval($item['panjang']), 2, ',', '.') ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if (!empty($item['lebar']) && $item['lebar'] > 0): ?>
                                <?= number_format(floatval($item['lebar']), 2, ',', '.') ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if (!empty($item['luas']) && $item['luas'] > 0): ?>
                                <?= number_format(floatval($item['luas']), 2, ',', '.') ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="fw-medium">
                            <?php if (!empty($item['nilai_perolehan']) && $item['nilai_perolehan'] > 0): ?>
                                Rp <?= number_format(floatval($item['nilai_perolehan']), 0, ',', '.') ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php 
                                $statusClass = 'secondary';
                                if (!empty($item['status_penggunaan'])) {
                                    $status = strtolower($item['status_penggunaan']);
                                    if (strpos($status, 'digunakan') !== false || strpos($status, 'aktif') !== false) {
                                        $statusClass = 'success';
                                    } elseif (strpos($status, 'proses') !== false || strpos($status, 'perbaikan') !== false) {
                                        $statusClass = 'warning';
                                    } elseif (strpos($status, 'tidak') !== false || strpos($status, 'rusak') !== false) {
                                        $statusClass = 'danger';
                                    }
                                }
                            ?>
                            <span class="badge bg-<?= $statusClass ?>"><?= esc($item['status_penggunaan'] ?? '-') ?></span>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            <p class="mb-0">Menampilkan <?= count($jalanList) ?> dari <?= isset($totalItems) ? esc($totalItems) : count($jalanList) ?> data</p>
        </div>
        <div>
            <?php if (isset($pager) && isset($totalPages) && $totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if (isset($currentPage) && $currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('user/barang/jalanirigasijaringan/jalan') ?>?page=1<?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>">&laquo;&laquo;</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('user/barang/jalanirigasijaringan/jalan') ?>?page=<?= $currentPage - 1 ?><?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>">&laquo;</a>
                            </li>
                        <?php endif; ?>

                        <?php
                        $start = isset($currentPage) && isset($totalPages) ? max(1, $currentPage - 2) : 1;
                        $end = isset($currentPage) && isset($totalPages) ? min($totalPages, $currentPage + 2) : 1;
                        
                        for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= isset($currentPage) && $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= base_url('user/barang/jalanirigasijaringan/jalan') ?>?page=<?= $i ?><?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if (isset($currentPage) && isset($totalPages) && $currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('user/barang/jalanirigasijaringan/jalan') ?>?page=<?= $currentPage + 1 ?><?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>">&raquo;</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('user/barang/jalanirigasijaringan/jalan') ?>?page=<?= $totalPages ?><?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>">&raquo;&raquo;</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>
        <div class="alert alert-info text-center">
            <h5>Tidak ada data jalan.</h5>
            <p>Silakan gunakan tombol "Import/Sync API" untuk mengimpor data dari API Jalan ke database.</p>
        </div>
    <?php endif; ?>

    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('toggleFormBtn')?.addEventListener('click', function () {
        const form = document.getElementById('formTambahJalan');
        if (form) {
            form.style.display = (form.style.display === 'none') ? 'block' : 'none';
            this.innerHTML = form.style.display === 'block'
                ? '<i class="bi bi-dash-circle"></i> Sembunyikan Form'
                : '<i class="bi bi-plus-lg"></i> Tambah Data Jalan';
        }
    });
    </script>

</body>
</html>

<?= $this->endSection() ?>