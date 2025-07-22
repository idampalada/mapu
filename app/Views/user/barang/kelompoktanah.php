<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Tanah</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }
        .item {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            padding: 25px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 150px;
            text-decoration: none;
            border: 1px solid #e0e0e0;
        }
        .item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            background-color: #f8f8f8;
        }
        .item.active {
            background-color: #2c5282;
            color: white;
            border-color: #1e3a5f;
        }
        .item.active .icon i,
        .item.active .item-title {
            color: white;
        }
        .icon i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #2c5282;
        }
        .item-title {
            font-weight: 600;
            color: #2c5282;
            font-size: 18px;
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
    </style>
</head>
<body>
    <div class="container">
    <h1>Kategori Tanah</h1>

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

<!-- Tombol Import yang sudah disederhanakan -->
<div class="mb-3 text-end">
    <!-- Tombol Import/Sync dari API -->
    <form action="<?= base_url('user/tanah/importFromApi') ?>" method="post" class="d-inline">
        <button type="submit" class="btn btn-success" onclick="return confirm('Import/sync data dari API? (Data existing akan di-update, data baru akan ditambahkan)')">
            <i class="bi bi-cloud-download"></i> Import/Sync API
        </button>
    </form>
    
    <!-- Tombol Reset Data -->
    <form action="<?= base_url('user/tanah/resetData') ?>" method="post" class="d-inline">
        <button type="submit" class="btn btn-danger" onclick="return confirm('PERINGATAN: Ini akan menghapus SEMUA data tanah! Yakin ingin melanjutkan?')">
            <i class="bi bi-trash"></i> Reset Data
        </button>
    </form>
</div>

<!-- Info bantuan untuk user -->
<div class="alert alert-info mb-3">
    <h6><i class="bi bi-info-circle"></i> Petunjuk Import:</h6>
    <ul class="mb-0">
        <li><strong>Import/Sync API:</strong> Mengambil data dari API, update data yang sudah ada, tambah data baru</li>
        <li><strong>Reset Data:</strong> Menghapus semua data dari database (gunakan dengan hati-hati!)</li>
    </ul>
</div>
    <!-- Pencarian -->
    <form method="GET" class="d-flex mb-4">
        <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama, kode, alamat" value="<?= esc($searchTerm ?? '') ?>">
        <button type="submit" class="btn btn-primary ms-2">Cari</button>
    </form>

    <?php
        $active = strtoupper($activeKelompok ?? '');
    ?>

    <!-- Kategori Tanah -->
    <div class="grid mb-4">
        <?php
            $buttons = [
                'TANAH PERSIL' => 'bi-houses',
                'TANAH NON PERSIL' => 'bi-map',
                'LAPANGAN' => 'bi-layout-wtf',
            ];
            foreach ($buttons as $label => $icon):
                $isActive = $active === $label;
        ?>
        <a href="<?= base_url('user/tanah/kelompoktanah/' . urlencode($label)) ?>"
           class="item <?= $isActive ? 'active' : '' ?>">
            <div class="icon"><i class="bi <?= $icon ?>"></i></div>
            <div class="item-title"><?= $label ?></div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Tombol Urutkan Data dan Ekspor -->
    <div class="d-flex justify-content-between mb-3">
        <!-- Dropdown untuk Urutkan Data -->
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-sort"></i> Urutkan Data
            </button>
            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                <li><a class="dropdown-item <?= ($sort == 'kode_barang' && $order == 'asc') ? 'active' : '' ?>" href="<?= base_url('user/tanah/kelompoktanah/' . urlencode($active) . '?sort=kode_barang&order=asc') ?>">
                    <i class="fas fa-sort-alpha-down"></i> Kode Barang (A-Z)
                </a></li>
                <li><a class="dropdown-item <?= ($sort == 'kode_barang' && $order == 'desc') ? 'active' : '' ?>" href="<?= base_url('user/tanah/kelompoktanah/' . urlencode($active) . '?sort=kode_barang&order=desc') ?>">
                    <i class="fas fa-sort-alpha-up-alt"></i> Kode Barang (Z-A)
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item <?= ($sort == 'nama_barang' && $order == 'asc') ? 'active' : '' ?>" href="<?= base_url('user/tanah/kelompoktanah/' . urlencode($active) . '?sort=nama_barang&order=asc') ?>">
                    <i class="fas fa-sort-alpha-down"></i> Nama Barang (A-Z)
                </a></li>
                <li><a class="dropdown-item <?= ($sort == 'nama_barang' && $order == 'desc') ? 'active' : '' ?>" href="<?= base_url('user/tanah/kelompoktanah/' . urlencode($active) . '?sort=nama_barang&order=desc') ?>">
                    <i class="fas fa-sort-alpha-up-alt"></i> Nama Barang (Z-A)
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item <?= ($sort == 'luas_tanah_seluruhnya' && $order == 'asc') ? 'active' : '' ?>" href="<?= base_url('user/tanah/kelompoktanah/' . urlencode($active) . '?sort=luas_tanah_seluruhnya&order=asc') ?>">
                    <i class="fas fa-sort-numeric-down"></i> Luas Tanah (Terkecil)
                </a></li>
                <li><a class="dropdown-item <?= ($sort == 'luas_tanah_seluruhnya' && $order == 'desc') ? 'active' : '' ?>" href="<?= base_url('user/tanah/kelompoktanah/' . urlencode($active) . '?sort=luas_tanah_seluruhnya&order=desc') ?>">
                    <i class="fas fa-sort-numeric-up-alt"></i> Luas Tanah (Terbesar)
                </a></li>
            </ul>
        </div>

        <!-- Tombol Ekspor Data (muncul sesuai kategori yang dipilih) -->
        <div>
            <?php if ($active === 'TANAH PERSIL'): ?>
                <a href="<?= base_url('user/tanah/exportTanahList/persil') ?>" class="btn btn-success">Ekspor Data Persil</a>
            <?php elseif ($active === 'TANAH NON PERSIL'): ?>
                <a href="<?= base_url('user/tanah/exportTanahList/nonpersil') ?>" class="btn btn-success">Ekspor Data Non Persil</a>
            <?php elseif ($active === 'LAPANGAN'): ?>
                <a href="<?= base_url('user/tanah/exportTanahList/lapangan') ?>" class="btn btn-success">Ekspor Data Lapangan</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($activeKelompok)): ?>
    <div class="mb-3 text-end">
        <button class="btn btn-primary" id="toggleFormBtn">
            <i class="bi bi-plus-lg"></i> Tambah Aset
        </button>
    </div>

    <!-- Form Tambah Tanah -->
    <div class="card mb-4" id="formTambahTanah" style="display: none;">
        <div class="card-header bg-primary text-white">
            <strong>Form Tambah Tanah</strong>
        </div>
        <div class="card-body">
            <form action="<?= base_url('user/tanah/tambah') ?>" method="post">
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
                        <label for="alamat" class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" value="<?= old('alamat') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kelompok" class="form-label">Kelompok</label>
                        <select name="kelompok" class="form-select" required>
                            <option value="">-- Pilih Kelompok --</option>
                            <option value="TANAH PERSIL" <?= ($activeKelompok === 'TANAH PERSIL' || old('kelompok') === 'TANAH PERSIL') ? 'selected' : '' ?>>Tanah Persil</option>
                            <option value="TANAH NON PERSIL" <?= ($activeKelompok === 'TANAH NON PERSIL' || old('kelompok') === 'TANAH NON PERSIL') ? 'selected' : '' ?>>Tanah Non Persil</option>
                            <option value="LAPANGAN" <?= ($activeKelompok === 'LAPANGAN' || old('kelompok') === 'LAPANGAN') ? 'selected' : '' ?>>Lapangan</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="luas_tanah_seluruhnya" class="form-label">Luas Tanah (m<sup>2</sup>)</label>
                        <input type="number" name="luas_tanah_seluruhnya" step="0.01" class="form-control" required value="<?= old('luas_tanah_seluruhnya') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status_penggunaan" class="form-label">Status Penggunaan</label>
                        <input type="text" name="status_penggunaan" class="form-control" value="<?= old('status_penggunaan') ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-success mt-3">
                    <i class="bi bi-check-circle"></i> Simpan Tanah
                </button>
            </form>
        </div>
    </div>

    <?php endif; ?>

    <?php if (!empty($tanahList)): ?>
    <div class="table-responsive mt-5">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-premium-blue">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Kode Barang</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Alamat</th>
                    <th class="text-center">Kelompok</th>
                    <th class="text-center">Luas (m2)</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($tanahList as $item): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><span class="badge bg-light text-dark"><?= esc($item['kode_barang'] ?? '-') ?></span></td>
                        <td class="fw-medium"><?= esc($item['nama_barang'] ?? '-') ?></td>
                        <td>
                            <?php
                                $alamatParts = [];
                                if (!empty($item['alamat'])) $alamatParts[] = $item['alamat'];
                                if (!empty($item['rt_rw'])) $alamatParts[] = 'RT/RW ' . $item['rt_rw'];
                                if (!empty($item['kelurahan_desa'])) $alamatParts[] = 'Kelurahan ' . $item['kelurahan_desa'];
                                if (!empty($item['kecamatan'])) $alamatParts[] = 'Kecamatan ' . $item['kecamatan'];
                                if (!empty($item['uraian_provinsi'])) $alamatParts[] = 'Provinsi ' . $item['uraian_provinsi'];
                                if (!empty($item['kode_pos'])) $alamatParts[] = 'Kode Pos ' . $item['kode_pos'];
                                echo esc(implode(', ', $alamatParts) ?: '-');
                            ?>
                        </td>
                        <td><?= esc($item['kelompok'] ?? '-') ?></td>
                        <td class="fw-medium"><?= number_format(floatval($item['luas_tanah_seluruhnya'] ?? 0), 2, ',', '.') ?></td>
                        <td class="text-center">
                            <?php 
                                $statusClass = 'secondary';
                                if (!empty($item['status_penggunaan'])) {
                                    $status = strtolower($item['status_penggunaan']);
                                    if (strpos($status, 'digunakan') !== false) {
                                        $statusClass = 'success';
                                    } elseif (strpos($status, 'proses') !== false) {
                                        $statusClass = 'warning';
                                    } elseif (strpos($status, 'tidak') !== false || strpos($status, 'sengketa') !== false) {
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
            <!-- Menampilkan jumlah data yang ditampilkan dan total data -->
            <p class="mb-0">Menampilkan <?= count($tanahList) ?> dari <?= esc($totalItems) ?> data</p>
        </div>
        <div>
            <?php if ($pager && $totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('user/tanah/kelompoktanah/' . urlencode($activeKelompok)) ?>?page=1<?= !empty($searchTerm) ? '&search=' . $searchTerm : '' ?><?= !empty($sort) ? '&sort=' . $sort . '&order=' . $order : '' ?>" aria-label="First">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('user/tanah/kelompoktanah/' . urlencode($activeKelompok)) ?>?page=<?= $currentPage - 1 ?><?= !empty($searchTerm) ? '&search=' . $searchTerm : '' ?><?= !empty($sort) ? '&sort=' . $sort . '&order=' . $order : '' ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php
                        $start = max(1, $currentPage - 2);
                        $end = min($totalPages, $currentPage + 2);
                        
                        for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= base_url('user/tanah/kelompoktanah/' . urlencode($activeKelompok)) ?>?page=<?= $i ?><?= !empty($searchTerm) ? '&search=' . $searchTerm : '' ?><?= !empty($sort) ? '&sort=' . $sort . '&order=' . $order : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('user/tanah/kelompoktanah/' . urlencode($activeKelompok)) ?>?page=<?= $currentPage + 1 ?><?= !empty($searchTerm) ? '&search=' . $searchTerm : '' ?><?= !empty($sort) ? '&sort=' . $sort . '&order=' . $order : '' ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('user/tanah/kelompoktanah/' . urlencode($activeKelompok)) ?>?page=<?= $totalPages ?><?= !empty($searchTerm) ? '&search=' . $searchTerm : '' ?><?= !empty($sort) ? '&sort=' . $sort . '&order=' . $order : '' ?>" aria-label="Last">
                                    <span aria-hidden="true">&raquo;&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>
        <div class="alert alert-info text-center">
            <h5>Tidak ada data untuk kelompok ini.</h5>
            <p>Silakan gunakan tombol "Import Data dari API" untuk mengimpor data dari API ke database.</p>
        </div>
    <?php endif; ?>

    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('toggleFormBtn').addEventListener('click', function () {
        const form = document.getElementById('formTambahTanah');
        form.style.display = (form.style.display === 'none') ? 'block' : 'none';
        this.innerHTML = form.style.display === 'block'
            ? '<i class="bi bi-dash-circle"></i> Sembunyikan Form'
            : '<i class="bi bi-plus-lg"></i> Tambah Aset';
    });
    </script>

</body>
</html>

<?= $this->endSection() ?>