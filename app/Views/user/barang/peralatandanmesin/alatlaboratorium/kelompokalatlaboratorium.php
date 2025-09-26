<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Alat Laboratorium</title>
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
        .badge.bg-primary {
            background-color: #2c5282 !important;
        }
        .badge.bg-info {
            background-color: #17a2b8 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Kategori Alat Laboratorium</h1>

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
            <form action="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/importFromApi') ?>" method="post" class="d-inline">
                <button type="submit" class="btn btn-success" onclick="return confirm('Import/sync data dari API Alat Laboratorium? (Data existing akan di-update, data baru akan ditambahkan)')">
                    <i class="bi bi-cloud-download"></i> Import/Sync API
                </button>
            </form>
            
            <!-- Tombol Reset Data -->
            <form action="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/resetData') ?>" method="post" class="d-inline">
                <button type="submit" class="btn btn-danger" onclick="return confirm('PERINGATAN: Ini akan menghapus SEMUA data alat laboratorium! Yakin ingin melanjutkan?')">
                    <i class="bi bi-trash"></i> Reset Data
                </button>
            </form>
        </div>

        <!-- Info bantuan untuk user -->
        <div class="alert alert-info mb-3">
            <h6><i class="bi bi-info-circle"></i> Petunjuk Import:</h6>
            <ul class="mb-0">
                <li><strong>Import/Sync API:</strong> Mengambil data dari API Peralatan dan Mesin Non-TIK kategori 3.08 (Alat Laboratorium)</li>
                <li><strong>Reset Data:</strong> Menghapus semua data dari database (gunakan dengan hati-hati!)</li>
            </ul>
        </div>

        <!-- Pencarian -->
        <form method="GET" class="d-flex mb-4">
            <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama, kode, merk, spesifikasi" value="<?= esc($searchTerm ?? '') ?>">
            <button type="submit" class="btn btn-primary ms-2">Cari</button>
        </form>

        <?php
            $active = strtoupper($activeKelompok ?? '');
        ?>

        <!-- Kategori Alat Laboratorium (8 Kategori) -->
        <div class="grid mb-4">
            <?php
                $buttons = [
                    'UNIT ALAT LABORATORIUM' => 'bi-flask',
                    'UNIT ALAT LABORATORIUM KIMIA PELAJAR' => 'bi-eyedropper',
                    'ALAT LABORATORIUM FISIKA NUKLIR/ELEKTRONIKA' => 'bi-radioactive',
                    'ALAT PROTEKSI RADIASI/PROTEKSI LINGKUNGAN' => 'bi-shield-fill-check',
                    'RADIATION APPLICATION NON DESTRUCTIVE TESTING LABORATORY' => 'bi-lightning',
                    'ALAT LABORATORIUM LINGKUNGAN HIDUP' => 'bi-tree',
                    'PERALATAN LABORATORIUM HYDRODINAMICA' => 'bi-water',
                    'ALAT LABORATORIUM STANDARISASI KALIBRASI & INSTRUMENTASI' => 'bi-speedometer2'
                ];
                
                foreach ($buttons as $label => $icon):
                    $isActive = $active === $label;
                    // Handle URL mapping untuk karakter khusus
                    $urlLabel = ($label === 'RADIATION APPLICATION NON DESTRUCTIVE TESTING LABORATORY') 
                        ? str_replace(' ', '%20', 'RADIATION APPLICATION & NON DESTRUCTIVE TESTING LABORATORY')
                        : urlencode($label);
            ?>
            <a href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/' . $urlLabel) ?>" class="item <?= $isActive ? 'active' : '' ?>">
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
                    <li><a class="dropdown-item <?= (isset($sort) && $sort == 'kode_barang' && isset($order) && $order == 'asc') ? 'active' : '' ?>" href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/' . urlencode($active) . '?sort=kode_barang&order=asc') ?>">
                        <i class="fas fa-sort-alpha-down"></i> Kode Barang (A-Z)
                    </a></li>
                    <li><a class="dropdown-item <?= (isset($sort) && $sort == 'kode_barang' && isset($order) && $order == 'desc') ? 'active' : '' ?>" href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/' . urlencode($active) . '?sort=kode_barang&order=desc') ?>">
                        <i class="fas fa-sort-alpha-up-alt"></i> Kode Barang (Z-A)
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item <?= (isset($sort) && $sort == 'nama_barang' && isset($order) && $order == 'asc') ? 'active' : '' ?>" href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/' . urlencode($active) . '?sort=nama_barang&order=asc') ?>">
                        <i class="fas fa-sort-alpha-down"></i> Nama Barang (A-Z)
                    </a></li>
                    <li><a class="dropdown-item <?= (isset($sort) && $sort == 'nama_barang' && isset($order) && $order == 'desc') ? 'active' : '' ?>" href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/' . urlencode($active) . '?sort=nama_barang&order=desc') ?>">
                        <i class="fas fa-sort-alpha-up-alt"></i> Nama Barang (Z-A)
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item <?= (isset($sort) && $sort == 'nilai_perolehan' && isset($order) && $order == 'asc') ? 'active' : '' ?>" href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/' . urlencode($active) . '?sort=nilai_perolehan&order=asc') ?>">
                        <i class="fas fa-sort-numeric-down"></i> Nilai Perolehan (Terendah)
                    </a></li>
                    <li><a class="dropdown-item <?= (isset($sort) && $sort == 'nilai_perolehan' && isset($order) && $order == 'desc') ? 'active' : '' ?>" href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/' . urlencode($active) . '?sort=nilai_perolehan&order=desc') ?>">
                        <i class="fas fa-sort-numeric-up-alt"></i> Nilai Perolehan (Tertinggi)
                    </a></li>
                </ul>
            </div>

            <!-- Tombol Ekspor Data (muncul sesuai kategori yang dipilih) -->
            <div>
                <?php if ($active === 'UNIT ALAT LABORATORIUM'): ?>
                    <a href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/exportAlatLaboratoriumList/unitalatlaboratorium') ?>" class="btn btn-success">Ekspor Data Unit Alat Lab</a>
                <?php elseif ($active === 'UNIT ALAT LABORATORIUM KIMIA PELAJAR'): ?>
                    <a href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/exportAlatLaboratoriumList/unitalatlabkimiapelajar') ?>" class="btn btn-success">Ekspor Data Lab Kimia Pelajar</a>
                <?php elseif ($active === 'ALAT LABORATORIUM FISIKA NUKLIR/ELEKTRONIKA'): ?>
                    <a href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/exportAlatLaboratoriumList/alatlabfisikanuklir') ?>" class="btn btn-success">Ekspor Data Lab Fisika Nuklir</a>
                <?php elseif ($active === 'ALAT PROTEKSI RADIASI/PROTEKSI LINGKUNGAN'): ?>
                    <a href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/exportAlatLaboratoriumList/alatproteksiradiasi') ?>" class="btn btn-success">Ekspor Data Proteksi Radiasi</a>
                <?php elseif ($active === 'RADIATION APPLICATION NON DESTRUCTIVE TESTING LABORATORY'): ?>
                    <a href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/exportAlatLaboratoriumList/radiationapplication') ?>" class="btn btn-success">Ekspor Data Radiation App</a>
                <?php elseif ($active === 'ALAT LABORATORIUM LINGKUNGAN HIDUP'): ?>
                    <a href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/exportAlatLaboratoriumList/alatlablingkunganhidup') ?>" class="btn btn-success">Ekspor Data Lab Lingkungan</a>
                <?php elseif ($active === 'PERALATAN LABORATORIUM HYDRODINAMICA'): ?>
                    <a href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/exportAlatLaboratoriumList/peralatanlabhydrodinamica') ?>" class="btn btn-success">Ekspor Data Lab Hydro</a>
                <?php elseif ($active === 'ALAT LABORATORIUM STANDARISASI KALIBRASI & INSTRUMENTASI'): ?>
                    <a href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/exportAlatLaboratoriumList/alatlabstandarisasikalibrasi') ?>" class="btn btn-success">Ekspor Data Lab Kalibrasi</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($activeKelompok)): ?>
        <div class="mb-3 text-end">
            <button class="btn btn-primary" id="toggleFormBtn">
                <i class="bi bi-plus-lg"></i> Tambah Aset
            </button>
        </div>

        <!-- Form Tambah Alat Laboratorium -->
        <div class="card mb-4" id="formTambahAlatLaboratorium" style="display: none;">
            <div class="card-header bg-primary text-white">
                <strong>Form Tambah <?= esc($activeKelompok) ?></strong>
            </div>
            <div class="card-body">
                <form action="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/tambah') ?>" method="post">
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
                            <label for="merk" class="form-label">Merk</label>
                            <input type="text" name="merk" class="form-control" value="<?= old('merk') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="kelompok" class="form-label">Kelompok</label>
                            <select name="kelompok" class="form-select" required>
                                <option value="">-- Pilih Kelompok --</option>
                                <option value="UNIT ALAT LABORATORIUM" <?= ($activeKelompok === 'UNIT ALAT LABORATORIUM' || old('kelompok') === 'UNIT ALAT LABORATORIUM') ? 'selected' : '' ?>>Unit Alat Laboratorium</option>
                                <option value="UNIT ALAT LABORATORIUM KIMIA PELAJAR" <?= ($activeKelompok === 'UNIT ALAT LABORATORIUM KIMIA PELAJAR' || old('kelompok') === 'UNIT ALAT LABORATORIUM KIMIA PELAJAR') ? 'selected' : '' ?>>Unit Alat Laboratorium Kimia Pelajar</option>
                                <option value="ALAT LABORATORIUM FISIKA NUKLIR/ELEKTRONIKA" <?= ($activeKelompok === 'ALAT LABORATORIUM FISIKA NUKLIR/ELEKTRONIKA' || old('kelompok') === 'ALAT LABORATORIUM FISIKA NUKLIR/ELEKTRONIKA') ? 'selected' : '' ?>>Alat Laboratorium Fisika Nuklir/Elektronika</option>
                                <option value="ALAT PROTEKSI RADIASI/PROTEKSI LINGKUNGAN" <?= ($activeKelompok === 'ALAT PROTEKSI RADIASI/PROTEKSI LINGKUNGAN' || old('kelompok') === 'ALAT PROTEKSI RADIASI/PROTEKSI LINGKUNGAN') ? 'selected' : '' ?>>Alat Proteksi Radiasi/Proteksi Lingkungan</option>
                                <option value="RADIATION APPLICATION & NON DESTRUCTIVE TESTING LABORATORY" <?= ($activeKelompok === 'RADIATION APPLICATION NON DESTRUCTIVE TESTING LABORATORY' || old('kelompok') === 'RADIATION APPLICATION & NON DESTRUCTIVE TESTING LABORATORY') ? 'selected' : '' ?>>Radiation Application & NDT Laboratory</option>
                                <option value="ALAT LABORATORIUM LINGKUNGAN HIDUP" <?= ($activeKelompok === 'ALAT LABORATORIUM LINGKUNGAN HIDUP' || old('kelompok') === 'ALAT LABORATORIUM LINGKUNGAN HIDUP') ? 'selected' : '' ?>>Alat Laboratorium Lingkungan Hidup</option>
                                <option value="PERALATAN LABORATORIUM HYDRODINAMICA" <?= ($activeKelompok === 'PERALATAN LABORATORIUM HYDRODINAMICA' || old('kelompok') === 'PERALATAN LABORATORIUM HYDRODINAMICA') ? 'selected' : '' ?>>Peralatan Laboratorium Hydrodinamica</option>
                                <option value="ALAT LABORATORIUM STANDARISASI KALIBRASI & INSTRUMENTASI" <?= ($activeKelompok === 'ALAT LABORATORIUM STANDARISASI KALIBRASI & INSTRUMENTASI' || old('kelompok') === 'ALAT LABORATORIUM STANDARISASI KALIBRASI & INSTRUMENTASI') ? 'selected' : '' ?>>Alat Laboratorium Standarisasi Kalibrasi & Instrumentasi</option>
                            </select>
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
                            <label for="kuantitas" class="form-label">Kuantitas</label>
                            <input type="number" name="kuantitas" class="form-control" value="<?= old('kuantitas') ?: 1 ?>" min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="processor" class="form-label">Processor</label>
                            <input type="text" name="processor" class="form-control" value="<?= old('processor') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="memori" class="form-label">Memori</label>
                            <input type="text" name="memori" class="form-control" value="<?= old('memori') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hardisk" class="form-label">Hardisk</label>
                            <input type="text" name="hardisk" class="form-control" value="<?= old('hardisk') ?>">
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
                            <label for="spek_lain" class="form-label">Spesifikasi Lain</label>
                            <textarea name="spek_lain" class="form-control" rows="2" placeholder="Deskripsi spesifikasi tambahan alat laboratorium"><?= old('spek_lain') ?></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="status_penggunaan" class="form-label">Status Penggunaan</label>
                            <textarea name="status_penggunaan" class="form-control" rows="2" placeholder="Digunakan untuk keperluan laboratorium penelitian"><?= old('status_penggunaan') ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success mt-3">
                        <i class="bi bi-check-circle"></i> Simpan <?= esc($activeKelompok) ?>
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($alatLaboratoriumList)): ?>
        <div class="table-responsive mt-5">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-premium-blue">
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Kode Barang</th>
                        <th class="text-center">Nama Barang</th>
                        <th class="text-center">Merk</th>
                        <th class="text-center">NUP</th>
                        <th class="text-center">Kelompok</th>
                        <th class="text-center">Sub Kelompok</th>
                        <th class="text-center">Kondisi</th>
                        <th class="text-center">Spesifikasi</th>
                        <th class="text-center">Nilai Perolehan</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($alatLaboratoriumList as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><span class="badge bg-light text-dark"><?= esc($item['kode_barang'] ?? '-') ?></span></td>
                            <td class="fw-medium"><?= esc($item['nama_barang'] ?? '-') ?></td>
                            <td><?= esc($item['merk'] ?? '-') ?></td>
                            <td><?= esc($item['nup'] ?? '-') ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    (stripos($item['kelompok'] ?? '', 'unit alat laboratorium') !== false) ? 'primary' : 
                                    ((stripos($item['kelompok'] ?? '', 'kimia') !== false) ? 'info' : 
                                    ((stripos($item['kelompok'] ?? '', 'fisika') !== false || stripos($item['kelompok'] ?? '', 'nuklir') !== false) ? 'warning' :
                                    ((stripos($item['kelompok'] ?? '', 'proteksi') !== false || stripos($item['kelompok'] ?? '', 'radiasi') !== false) ? 'danger' :
                                    ((stripos($item['kelompok'] ?? '', 'radiation') !== false) ? 'secondary' :
                                    ((stripos($item['kelompok'] ?? '', 'lingkungan') !== false) ? 'success' :
                                    ((stripos($item['kelompok'] ?? '', 'hydro') !== false) ? 'dark' : 'primary'))))))
                                ?>">
                                    <?= esc($item['kategori_detail'] ?? $item['kelompok'] ?? '-') ?>
                                </span>
                            </td>
                            <td><?= esc($item['sub_kelompok'] ?? '-') ?></td>
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
                            <td class="text-center">
                                <?php 
                                    $spek = [];
                                    if (!empty($item['processor'])) $spek[] = "CPU: " . $item['processor'];
                                    if (!empty($item['memori'])) $spek[] = "RAM: " . $item['memori'];
                                    if (!empty($item['hardisk'])) $spek[] = "Storage: " . $item['hardisk'];
                                    if (!empty($item['monitor'])) $spek[] = "Monitor: " . $item['monitor'];
                                    if (!empty($item['spek_lain'])) $spek[] = $item['spek_lain'];
                                ?>
                                <small><?= !empty($spek) ? implode('<br>', $spek) : '-' ?></small>
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
                <!-- Menampilkan jumlah data yang ditampilkan dan total data -->
                <p class="mb-0">Menampilkan <?= count($alatLaboratoriumList) ?> dari <?= isset($totalItems) ? esc($totalItems) : count($alatLaboratoriumList) ?> data</p>
            </div>
            <div>
                <?php if (isset($pager) && isset($totalPages) && $totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php if (isset($currentPage) && $currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/' . urlencode($activeKelompok)) ?>?page=1<?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>" aria-label="First">
                                        <span aria-hidden="true">&laquo;&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/' . urlencode($activeKelompok)) ?>?page=<?= $currentPage - 1 ?><?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php
                            $start = isset($currentPage) && isset($totalPages) ? max(1, $currentPage - 2) : 1;
                            $end = isset($currentPage) && isset($totalPages) ? min($totalPages, $currentPage + 2) : 1;
                            
                            for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?= isset($currentPage) && $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/' . urlencode($activeKelompok)) ?>?page=<?= $i ?><?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if (isset($currentPage) && isset($totalPages) && $currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/' . urlencode($activeKelompok)) ?>?page=<?= $currentPage + 1 ?><?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="<?= base_url('user/barang/peralatandanmesin/alatlaboratorium/kelompokalatlaboratorium/' . urlencode($activeKelompok)) ?>?page=<?= $totalPages ?><?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>" aria-label="Last">
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
                <p>Silakan gunakan tombol "Import/Sync API" untuk mengimpor data dari API Peralatan dan Mesin Non-TIK kategori 3.08 ke database.</p>
            </div>
        <?php endif; ?>

    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('toggleFormBtn').addEventListener('click', function () {
        const form = document.getElementById('formTambahAlatLaboratorium');
        form.style.display = (form.style.display === 'none') ? 'block' : 'none';
        this.innerHTML = form.style.display === 'block'
            ? '<i class="bi bi-dash-circle"></i> Sembunyikan Form'
            : '<i class="bi bi-plus-lg"></i> Tambah Aset';
    });
    </script>

</body>
</html>

<?= $this->endSection() ?>