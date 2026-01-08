<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Komputer</title>
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
            position: sticky;
            top: 0;
            z-index: 10;
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
        /* Style untuk QR Code Images */
        .qr-code-container {
            display: inline-block;
            position: relative;
        }
        .qr-code-container img {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 4px;
        }
        .qr-code-container img:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .qr-code-placeholder {
            background: linear-gradient(45deg, #f0f0f0 25%, transparent 25%), 
                        linear-gradient(-45deg, #f0f0f0 25%, transparent 25%), 
                        linear-gradient(45deg, transparent 75%, #f0f0f0 75%), 
                        linear-gradient(-45deg, transparent 75%, #f0f0f0 75%);
            background-size: 10px 10px;
            background-position: 0 0, 0 5px, 5px -5px, -5px 0px;
        }
        .qr-text-display {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .qr-text-display:hover {
            background: #e9ecef;
        }

        /* ========== COMPACT TABLE STYLES ========== */
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-top: 20px;
        }

        .table-header-controls {
            background: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .column-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            align-items: center;
        }

        .column-toggle-btn {
            padding: 3px 8px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 12px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s;
            color: #6c757d;
            white-space: nowrap;
        }

        .column-toggle-btn.active {
            background: #2c5282;
            color: white;
            border-color: #2c5282;
        }

        .column-toggle-btn:hover {
            background: #e9ecef;
        }

        .column-toggle-btn.active:hover {
            background: #1e3a5f;
        }

        .compact-table-responsive {
            max-height: 70vh;
            overflow: auto;
            border: 1px solid #dee2e6;
        }

        .compact-table {
            margin-bottom: 0;
            font-size: 11px;
            width: auto;
            min-width: 100%;
        }

        .compact-table th {
            background-color: #2c5282 !important;
            color: white !important;
            font-weight: 600;
            padding: 8px 4px;
            text-align: center;
            vertical-align: middle;
            border: none;
            position: sticky;
            top: 0;
            z-index: 5;
            white-space: nowrap;
            font-size: 10px;
            max-width: none !important;
        }

        .compact-table td {
            padding: 8px 4px;
            border-bottom: 1px solid #dee2e6;
            border-right: 1px solid #f1f3f5;
            vertical-align: top;
            font-size: 11px;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 60px;
            line-height: 1.3;
        }

        .compact-table td:last-child {
            border-right: none;
        }

        .compact-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* FIXED COLUMN WIDTHS - ADJUSTED */
        .compact-table th:nth-child(1),  /* No */
        .compact-table td:nth-child(1) {
            width: 35px;
            max-width: 35px;
        }

        .compact-table th:nth-child(2),  /* Kode */
        .compact-table td:nth-child(2) {
            width: 80px;
            max-width: 80px;
        }

        .compact-table th:nth-child(3),  /* Bidang */
        .compact-table td:nth-child(3) {
            width: 45px;
            max-width: 45px;
        }

        .compact-table th:nth-child(4),  /* Nama Barang */
        .compact-table td:nth-child(4) {
            width: 120px;
            max-width: 120px;
            text-align: left;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.2;
        }

        .compact-table th:nth-child(5),  /* Merk */
        .compact-table td:nth-child(5) {
            width: 60px;
            max-width: 60px;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.2;
        }

        .compact-table th:nth-child(6),  /* NUP */
        .compact-table td:nth-child(6) {
            width: 45px;
            max-width: 45px;
        }

        .compact-table th:nth-child(7),  /* Kelompok */
        .compact-table td:nth-child(7) {
            width: 80px;
            max-width: 80px;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.2;
        }

        .compact-table th:nth-child(8),  /* Processor */
        .compact-table td:nth-child(8) {
            width: 70px;
            max-width: 70px;
        }

        .compact-table th:nth-child(9),  /* Memori */
        .compact-table td:nth-child(9) {
            width: 50px;
            max-width: 50px;
        }

        .compact-table th:nth-child(10), /* Hardisk */
        .compact-table td:nth-child(10) {
            width: 60px;
            max-width: 60px;
        }

        .compact-table th:nth-child(11), /* Tanggal */
        .compact-table td:nth-child(11) {
            width: 70px;
            max-width: 70px;
        }

        .compact-table th:nth-child(12), /* Nilai */
        .compact-table td:nth-child(12) {
            width: 100px;
            max-width: 100px;
        }

        .compact-table th:nth-child(13), /* User Sebelumnya */
        .compact-table td:nth-child(13) {
            width: 80px;
            max-width: 80px;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.2;
        }

        .compact-table th:nth-child(14), /* User Sekarang */
        .compact-table td:nth-child(14) {
            width: 80px;
            max-width: 80px;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.2;
        }

        .compact-table th:nth-child(15), /* Kondisi */
        .compact-table td:nth-child(15) {
            width: 60px;
            max-width: 60px;
        }

        .compact-table th:nth-child(16), /* Status Pakai */
        .compact-table td:nth-child(16) {
            width: 60px;
            max-width: 60px;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.2;
        }

        .compact-table th:nth-child(17), /* Status Barang */
        .compact-table td:nth-child(17) {
            width: 70px;
            max-width: 70px;
        }

        .compact-table th:nth-child(18), /* Keterangan */
        .compact-table td:nth-child(18) {
            width: 90px;
            max-width: 90px;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.2;
        }

        .compact-table th:nth-child(19), /* QR Code */
        .compact-table td:nth-child(19) {
            width: 50px;
            max-width: 50px;
        }

        .compact-table th:nth-child(20), /* Aksi */
        .compact-table td:nth-child(20) {
            width: 80px;
            max-width: 80px;
        }

        /* Column visibility controls */
        .col-processor,
        .col-memory, 
        .col-hardisk,
        .col-monitor,
        .col-user-prev,
        .col-status-usage,
        .col-keterangan,
        .col-spek-lain {
            display: table-cell;
        }

        .col-processor.hidden,
        .col-memory.hidden,
        .col-hardisk.hidden, 
        .col-monitor.hidden,
        .col-user-prev.hidden,
        .col-status-usage.hidden,
        .col-keterangan.hidden,
        .col-spek-lain.hidden {
            display: none;
        }

        /* Action buttons improvements */
        .action-buttons {
            display: flex;
            gap: 1px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            padding: 2px 4px;
            font-size: 10px;
            min-width: 24px;
            border-radius: 3px;
        }

        /* QR Code improvements */
        .qr-cell {
            text-align: center;
            padding: 2px;
        }

        .qr-image {
            width: 35px;
            height: 35px;
            cursor: pointer;
            border: 1px solid #ddd;
            border-radius: 3px;
            transition: transform 0.2s;
        }

        .qr-image:hover {
            transform: scale(1.2);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .qr-placeholder {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed #ddd;
            border-radius: 3px;
            color: #ccc;
            font-size: 16px;
        }

        /* Badge improvements */
        .compact-badge {
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 8px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        /* Tooltip for truncated content */
        .truncated {
            cursor: help;
            position: relative;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .table-header-controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .column-controls {
                justify-content: center;
            }
            
            .compact-table-responsive {
                max-height: 60vh;
            }
            
            .compact-table {
                font-size: 10px;
            }
            
            .compact-table th,
            .compact-table td {
                padding: 4px 2px;
            }
        }

        /* Money formatting */
        .money-value {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #28a745;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="container">
    <h1>Kategori Komputer</h1>

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

    <!-- Tombol Import yang diperbarui dengan tambahan Import Excel -->
    <div class="mb-3 text-end">
        <!-- Tombol Import Excel baru -->
        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#importExcelModal">
            <i class="bi bi-file-excel"></i> Import Excel
        </button>
        
        <!-- Tombol Import/Sync dari API (tetap seperti sebelumnya) -->
        <form action="<?= base_url('user/barang/peralatandanmesin/komputer/importFromApi') ?>" method="post" class="d-inline">
            <button type="submit" class="btn btn-success" onclick="return confirm('Import/sync data dari API PM-TIK? (Data existing akan di-update, data baru akan ditambahkan)')">
                <i class="bi bi-cloud-download"></i> Import/Sync API
            </button>
        </form>
        
        <!-- Tombol Reset Data (tetap seperti sebelumnya) -->
        <form action="<?= base_url('user/barang/peralatandanmesin/komputer/resetData') ?>" method="post" class="d-inline">
            <button type="submit" class="btn btn-danger" onclick="return confirm('PERINGATAN: Ini akan menghapus SEMUA data komputer! Yakin ingin melanjutkan?')">
                <i class="bi bi-trash"></i> Reset Data
            </button>
        </form>
    </div>

    <!-- Info bantuan untuk user - diperbarui dengan QR Code -->
    <div class="alert alert-info mb-3">
        <h6><i class="bi bi-info-circle"></i> Petunjuk Import:</h6>
        <ul class="mb-0">
            <li><strong>Import Excel:</strong> Mengimpor data dari file Excel (format: .xlsx). <strong>Kolom M akan dibaca sebagai QR CODE dan ditampilkan sebagai barcode</strong></li>
            <li><strong>Import/Sync API:</strong> Mengambil data dari API PM-TIK, update data yang sudah ada, tambah data baru</li>
            <li><strong>Reset Data:</strong> Menghapus semua data dari database (gunakan dengan hati-hati!)</li>
            <li><strong>QR Code:</strong> Klik pada barcode untuk melihat detail dan copy text</li>
        </ul>
    </div>

    <!-- Modal Import Excel baru -->
    <div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="importExcelModalLabel">Import Data dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('user/barang/peralatandanmesin/komputer/importFromExcel') ?>" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="excelFile" class="form-label">Pilih File Excel (.xlsx)</label>
                            <input type="file" class="form-control" id="excelFile" name="excelFile" accept=".xlsx" required>
                            <div class="form-text text-muted">
                                File harus berformat .xlsx dan memiliki sheet: Master Aset, MTI, BDI, TU, dll.<br>
                                <strong>Kolom M di sheet "Master Aset" akan dibaca sebagai QR CODE dan ditampilkan sebagai barcode yang bisa di-scan</strong>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pencarian -->
    <form method="GET" class="d-flex mb-4">
        <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama, kode, merk, processor, memori, QR Code" value="<?= esc($searchTerm ?? '') ?>">
        <button type="submit" class="btn btn-primary ms-2">Cari</button>
    </form>

    <?php
        $active = strtoupper($activeKelompok ?? '');
    ?>

    <!-- Kategori Komputer -->
    <div class="grid mb-4">
        <?php
            $buttons = [
                'KOMPUTER UNIT' => 'bi-pc-display',
                'PERALATAN KOMPUTER' => 'bi-keyboard',
            ];
            foreach ($buttons as $label => $icon):
                $isActive = $active === $label;
        ?>
        <a href="<?= base_url('user/barang/peralatandanmesin/komputer/kelompokkomputer/' . urlencode($label)) ?>"
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
                <li><a class="dropdown-item <?= (isset($sort) && $sort == 'kode_barang' && isset($order) && $order == 'asc') ? 'active' : '' ?>" href="<?= base_url('user/barang/peralatandanmesin/komputer/kelompokkomputer/' . urlencode($active) . '?sort=kode_barang&order=asc') ?>">
                    <i class="fas fa-sort-alpha-down"></i> Kode Barang (A-Z)
                </a></li>
                <li><a class="dropdown-item <?= (isset($sort) && $sort == 'kode_barang' && isset($order) && $order == 'desc') ? 'active' : '' ?>" href="<?= base_url('user/barang/peralatandanmesin/komputer/kelompokkomputer/' . urlencode($active) . '?sort=kode_barang&order=desc') ?>">
                    <i class="fas fa-sort-alpha-up-alt"></i> Kode Barang (Z-A)
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item <?= (isset($sort) && $sort == 'nama_barang' && isset($order) && $order == 'asc') ? 'active' : '' ?>" href="<?= base_url('user/barang/peralatandanmesin/komputer/kelompokkomputer/' . urlencode($active) . '?sort=nama_barang&order=asc') ?>">
                    <i class="fas fa-sort-alpha-down"></i> Nama Barang (A-Z)
                </a></li>
                <li><a class="dropdown-item <?= (isset($sort) && $sort == 'nama_barang' && isset($order) && $order == 'desc') ? 'active' : '' ?>" href="<?= base_url('user/barang/peralatandanmesin/komputer/kelompokkomputer/' . urlencode($active) . '?sort=nama_barang&order=desc') ?>">
                    <i class="fas fa-sort-alpha-up-alt"></i> Nama Barang (Z-A)
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item <?= (isset($sort) && $sort == 'nilai_perolehan' && isset($order) && $order == 'asc') ? 'active' : '' ?>" href="<?= base_url('user/barang/peralatandanmesin/komputer/kelompokkomputer/' . urlencode($active) . '?sort=nilai_perolehan&order=asc') ?>">
                    <i class="fas fa-sort-numeric-down"></i> Nilai Perolehan (Terendah)
                </a></li>
                <li><a class="dropdown-item <?= (isset($sort) && $sort == 'nilai_perolehan' && isset($order) && $order == 'desc') ? 'active' : '' ?>" href="<?= base_url('user/barang/peralatandanmesin/komputer/kelompokkomputer/' . urlencode($active) . '?sort=nilai_perolehan&order=desc') ?>">
                    <i class="fas fa-sort-numeric-up-alt"></i> Nilai Perolehan (Tertinggi)
                </a></li>
            </ul>
        </div>

        <!-- Tombol Ekspor Data (muncul sesuai kategori yang dipilih) -->
        <div>
            <?php if ($active === 'KOMPUTER UNIT'): ?>
                <a href="<?= base_url('user/barang/peralatandanmesin/komputer/exportKomputerList/komputer-unit') ?>" class="btn btn-success">Ekspor Data Komputer Unit</a>
            <?php elseif ($active === 'PERALATAN KOMPUTER'): ?>
                <a href="<?= base_url('user/barang/peralatandanmesin/komputer/exportKomputerList/peralatan-komputer') ?>" class="btn btn-success">Ekspor Data Peralatan Komputer</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($activeKelompok)): ?>
    <div class="mb-3 text-end">
        <button class="btn btn-primary" id="toggleFormBtn">
            <i class="bi bi-plus-lg"></i> Tambah Aset
        </button>
    </div>

    <!-- Form Tambah Komputer - diperbarui dengan field baru -->
    <div class="card mb-4" id="formTambahKomputer" style="display: none;">
        <div class="card-header bg-primary text-white">
            <strong>Form Tambah <?= esc($activeKelompok) ?></strong>
        </div>
        <div class="card-body">
            <form action="<?= base_url('user/barang/peralatandanmesin/komputer/tambah') ?>" method="post">
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
                        <label for="bidang" class="form-label">Bidang</label>
                        <select name="bidang" class="form-select">
                            <option value="">-- Pilih Bidang --</option>
                            <option value="MTI" <?= old('bidang') === 'MTI' ? 'selected' : '' ?>>MTI</option>
                            <option value="BDI" <?= old('bidang') === 'BDI' ? 'selected' : '' ?>>BDI</option>
                            <option value="TU" <?= old('bidang') === 'TU' ? 'selected' : '' ?>>TU</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kelompok" class="form-label">Kelompok</label>
                        <select name="kelompok" class="form-select" required>
                            <option value="">-- Pilih Kelompok --</option>
                            <option value="KOMPUTER UNIT" <?= ($activeKelompok === 'KOMPUTER UNIT' || old('kelompok') === 'KOMPUTER UNIT') ? 'selected' : '' ?>>Komputer Unit</option>
                            <option value="PERALATAN KOMPUTER" <?= ($activeKelompok === 'PERALATAN KOMPUTER' || old('kelompok') === 'PERALATAN KOMPUTER') ? 'selected' : '' ?>>Peralatan Komputer</option>
                        </select>
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
                        <label for="processor" class="form-label">Processor</label>
                        <input type="text" name="processor" class="form-control" placeholder="Intel Core i5, AMD Ryzen, dll" value="<?= old('processor') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="memori" class="form-label">Memori (RAM)</label>
                        <input type="text" name="memori" class="form-control" placeholder="8GB, 16GB, dll" value="<?= old('memori') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="hardisk" class="form-label">Hardisk/Storage</label>
                        <input type="text" name="hardisk" class="form-control" placeholder="500GB HDD, 1TB SSD, dll" value="<?= old('hardisk') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="monitor" class="form-label">Monitor</label>
                        <input type="text" name="monitor" class="form-control" placeholder="LED 21 inch, LCD 19 inch, dll" value="<?= old('monitor') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kuantitas" class="form-label">Kuantitas</label>
                        <input type="number" name="kuantitas" class="form-control" value="<?= old('kuantitas') ?: 1 ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status_penggunaan" class="form-label">Status Penggunaan</label>
                        <input type="text" name="status_penggunaan" class="form-control" value="<?= old('status_penggunaan') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nilai_perolehan" class="form-label">Nilai Perolehan</label>
                        <input type="number" name="nilai_perolehan" step="0.01" class="form-control" value="<?= old('nilai_perolehan') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_perolehan" class="form-label">Tanggal Perolehan</label>
                        <input type="date" name="tanggal_perolehan" class="form-control" value="<?= old('tanggal_perolehan') ?>">
                    </div>
                    <!-- Field Baru -->
                    <div class="col-md-6 mb-3">
                        <label for="pengguna_sebelumnya" class="form-label">Pengguna Sebelumnya</label>
                        <input type="text" name="pengguna_sebelumnya" class="form-control" value="<?= old('pengguna_sebelumnya') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pengguna_sekarang" class="form-label">Pengguna Sekarang</label>
                        <input type="text" name="pengguna_sekarang" class="form-control" value="<?= old('pengguna_sekarang') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status_barang" class="form-label">Status Barang</label>
                        <input type="text" name="status_barang" class="form-control" value="<?= old('status_barang') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="1"><?= old('keterangan') ?></textarea>
                    </div>
                    <!-- TAMBAHAN BARU: QR Code field -->
                    <div class="col-md-6 mb-3">
                        <label for="qr_code" class="form-label">QR Code</label>
                        <input type="text" name="qr_code" class="form-control" placeholder="Masukkan QR Code jika ada" value="<?= old('qr_code') ?>">
                        <small class="form-text text-muted">QR Code akan ditampilkan sebagai barcode yang bisa di-scan</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="spek_lain" class="form-label">Spesifikasi Lain</label>
                        <textarea name="spek_lain" class="form-control" rows="1" placeholder="Spesifikasi tambahan lainnya"><?= old('spek_lain') ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success mt-3">
                    <i class="bi bi-check-circle"></i> Simpan <?= esc($activeKelompok) ?>
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($komputerList)): ?>
    
    <!-- COMPACT TABLE WITH COLUMN CONTROLS -->
    <div class="table-container">
        <!-- Table Header Controls -->
        <div class="table-header-controls">
            <div>
                <h6 class="mb-0"><i class="bi bi-table me-2"></i>Data Tabel</h6>
                <small class="text-muted">Klik tombol untuk hide/show kolom</small>
            </div>
            <div class="column-controls">
                <span class="text-muted me-2" style="font-size: 11px;">Tampilkan:</span>
                <button class="column-toggle-btn active" data-column="processor">Processor</button>
                <button class="column-toggle-btn active" data-column="memory">Memory</button>
                <button class="column-toggle-btn active" data-column="hardisk">Hardisk</button>
                <button class="column-toggle-btn active" data-column="user-prev">User Lama</button>
                <button class="column-toggle-btn active" data-column="status-usage">Status Pakai</button>
                <button class="column-toggle-btn active" data-column="keterangan">Keterangan</button>
            </div>
        </div>

        <!-- Compact Table -->
        <div class="compact-table-responsive">
            <table class="compact-table table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th> 
                        <th>Bidang</th>
                        <th>Nama Barang</th>
                        <th>Merk</th>
                        <th>NUP</th>
                        <th>Kelompok</th>
                        <th class="col-processor">Processor</th>
                        <th class="col-memory">Memory</th>
                        <th class="col-hardisk">Hardisk</th>
                        <th>Tanggal</th>
                        <th>Nilai</th>
                        <th class="col-user-prev">User Lama</th>
                        <th>User Sekarang</th>
                        <th>Kondisi</th>
                        <th class="col-status-usage">Status Pakai</th>
                        <th>Status Barang</th>
                        <th class="col-keterangan">Keterangan</th>
                        <th>QR</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($komputerList as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($item['kode_barang'] ?? '-') ?></td> 
                            <td><?= esc(!empty($item['bidang']) ? $item['bidang'] : '') ?></td>
                            <td title="<?= esc($item['nama_barang'] ?? '') ?>">
                                <strong><?= esc($item['nama_barang'] ?? '-') ?></strong>
                            </td>
                            <td title="<?= esc($item['merk'] ?? '') ?>">
                                <?= esc($item['merk'] ?? '-') ?>
                            </td>
                            <td><?= esc($item['nup'] ?? '-') ?></td>
                            <td style="white-space: normal; word-wrap: break-word; line-height: 1.2;">
                                <?= esc($item['kelompok'] ?? '-') ?>
                            </td>
                            <td class="col-processor truncated" title="<?= esc($item['processor'] ?? '') ?>">
                                <?= esc(strlen($item['processor'] ?? '') > 10 ? substr($item['processor'], 0, 7) . '...' : ($item['processor'] ?? '-')) ?>
                            </td>
                            <td class="col-memory"><?= esc($item['memori'] ?? '-') ?></td>
                            <td class="col-hardisk truncated" title="<?= esc($item['hardisk'] ?? '') ?>">
                                <?= esc(strlen($item['hardisk'] ?? '') > 8 ? substr($item['hardisk'], 0, 5) . '...' : ($item['hardisk'] ?? '-')) ?>
                            </td>
                            <td>
                                <?php if (!empty($item['tanggal_perolehan'])): ?>
                                    <?= date('d/m/y', strtotime($item['tanggal_perolehan'])) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($item['nilai_perolehan']) && $item['nilai_perolehan'] > 0): ?>
                                    Rp <?= number_format(floatval($item['nilai_perolehan']), 0, ',', '.') ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="col-user-prev" title="<?= esc($item['pengguna_sebelumnya'] ?? '') ?>">
                                <?= esc($item['pengguna_sebelumnya'] ?? '-') ?>
                            </td>
                            <td title="<?= esc($item['pengguna_sekarang'] ?? '') ?>">
                                <?= esc($item['pengguna_sekarang'] ?? '-') ?>
                            </td>
                            <td>
                                <?= !empty($item['kondisi']) ? esc($item['kondisi']) : '' ?>
                            </td>
                            <td class="col-status-usage" title="<?= esc($item['status_penggunaan'] ?? '') ?>">
                                <?= !empty($item['status_penggunaan']) ? esc($item['status_penggunaan']) : '' ?>
                            </td>
                            <td title="<?= esc($item['status_barang'] ?? '') ?>">
                                <?= esc($item['status_barang'] ?? '-') ?>
                            </td>
                            <td class="col-keterangan" title="<?= esc($item['keterangan'] ?? '') ?>">
                                <?= !empty($item['keterangan']) ? esc($item['keterangan']) : '' ?>
                            </td>
                            
                            <!-- QR Code Column -->
                            <td class="qr-cell">
                                <?php if (!empty($item['qr_code']) && trim($item['qr_code']) !== ''): ?>
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=70x70&data=<?= urlencode($item['qr_code']) ?>" 
                                         class="qr-image" 
                                         alt="QR"
                                         onclick="showQRPreview('<?= esc(addslashes($item['qr_code'])) ?>')"
                                         title="Klik untuk preview: <?= esc($item['qr_code']) ?>">
                                <?php else: ?>
                                    <div class="qr-placeholder">
                                        <i class="bi bi-dash"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            
                            <!-- Action Column -->
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= base_url('user/barang/peralatandanmesin/komputer/detail/' . $item['id']) ?>" 
                                       class="btn btn-info btn-sm" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= base_url('user/barang/peralatandanmesin/komputer/edit/' . $item['id']) ?>" 
                                       class="btn btn-primary btn-sm" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?= base_url('user/barang/peralatandanmesin/komputer/hapus/' . $item['id']) ?>" 
                                       class="btn btn-danger btn-sm" title="Hapus"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            <p class="mb-0">Menampilkan <?= count($komputerList) ?> dari <?= isset($totalItems) ? esc($totalItems) : count($komputerList) ?> data</p>
        </div>
        <div>
            <?php if (isset($pager) && isset($totalPages) && $totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if (isset($currentPage) && $currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('user/barang/peralatandanmesin/komputer/kelompokkomputer/' . urlencode($activeKelompok)) ?>?page=1<?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>" aria-label="First">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('user/barang/peralatandanmesin/komputer/kelompokkomputer/' . urlencode($activeKelompok)) ?>?page=<?= $currentPage - 1 ?><?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php
                        $start = isset($currentPage) && isset($totalPages) ? max(1, $currentPage - 2) : 1;
                        $end = isset($currentPage) && isset($totalPages) ? min($totalPages, $currentPage + 2) : 1;
                        
                        for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= isset($currentPage) && $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= base_url('user/barang/peralatandanmesin/komputer/kelompokkomputer/' . urlencode($activeKelompok)) ?>?page=<?= $i ?><?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if (isset($currentPage) && isset($totalPages) && $currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('user/barang/peralatandanmesin/komputer/kelompokkomputer/' . urlencode($activeKelompok)) ?>?page=<?= $currentPage + 1 ?><?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('user/barang/peralatandanmesin/komputer/kelompokkomputer/' . urlencode($activeKelompok)) ?>?page=<?= $totalPages ?><?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?><?= !empty($sort) && !empty($order) ? '&sort=' . urlencode($sort) . '&order=' . urlencode($order) : '' ?>" aria-label="Last">
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
            <p>Silakan gunakan tombol "Import/Sync API" atau "Import Excel" untuk mengimpor data ke database.</p>
        </div>
    <?php endif; ?>

    </div>

    <!-- QR Preview Modal -->
    <div class="modal fade" id="qrPreviewModal" tabindex="-1" aria-labelledby="qrPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrPreviewModalLabel">QR Code Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="qrPreviewImage" class="mb-3" onclick="copyQRText()" style="cursor: pointer;">
                        <!-- QR Code image will be inserted here -->
                    </div>
                    <small class="text-muted">Click QR Code to copy</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Global variable to store current QR text for copying
    let currentQRText = '';

    // Form toggle functionality - EXISTING
    document.getElementById('toggleFormBtn')?.addEventListener('click', function () {
        const form = document.getElementById('formTambahKomputer');
        form.style.display = (form.style.display === 'none') ? 'block' : 'none';
        this.innerHTML = form.style.display === 'block'
            ? '<i class="bi bi-dash-circle"></i> Sembunyikan Form'
            : '<i class="bi bi-plus-lg"></i> Tambah Aset';
    });

    // QR Preview Modal function
    function showQRPreview(qrText) {
        currentQRText = qrText;
        
        // Create large QR code image
        const qrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrText)}`;
        
        // Update modal content
        document.getElementById('qrPreviewImage').innerHTML = `
            <img src="${qrImageUrl}" alt="QR Code" class="img-fluid" 
                 style="max-width: 200px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer;" 
                 title="Click to copy QR Code text">
        `;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('qrPreviewModal'));
        modal.show();
    }

    // Copy QR text function
    function copyQRText() {
        if (currentQRText) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(currentQRText).then(() => {
                    showToast('QR Code berhasil disalin: ' + currentQRText, 'success');
                }).catch(() => {
                    fallbackCopyTextToClipboard(currentQRText);
                });
            } else {
                fallbackCopyTextToClipboard(currentQRText);
            }
        }
    }

    // Legacy copy to clipboard function - keep for compatibility
    function copyToClipboard(text, element) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('QR Code berhasil disalin: ' + text, 'success');
                // Visual feedback
                if (element) {
                    element.style.background = '#d4edda';
                    setTimeout(() => {
                        element.style.background = '#f8f9fa';
                    }, 1000);
                }
            }).catch(() => {
                fallbackCopyTextToClipboard(text);
            });
        } else {
            fallbackCopyTextToClipboard(text);
        }
    }

    function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showToast('QR Code berhasil disalin!', 'success');
        } catch (err) {
            showToast('Gagal menyalin QR Code', 'error');
        }
        
        document.body.removeChild(textArea);
    }

    // Toast notification function - EXISTING
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show`;
        toast.style.minWidth = '300px';
        toast.innerHTML = `
            <strong>${type === 'success' ? 'Berhasil!' : type === 'error' ? 'Error!' : 'Info!'}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.getElementById('toast-container').appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 3000);
    }

    // Column toggle functionality
    document.querySelectorAll('.column-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const column = this.dataset.column;
            const isActive = this.classList.contains('active');
            
            // Toggle button state
            if (isActive) {
                this.classList.remove('active');
            } else {
                this.classList.add('active');
            }
            
            // Toggle column visibility
            document.querySelectorAll(`.col-${column}`).forEach(cell => {
                if (isActive) {
                    cell.classList.add('hidden');
                } else {
                    cell.classList.remove('hidden');
                }
            });
        });
    });
    </script>

</body>
</html>

<?= $this->endSection() ?>