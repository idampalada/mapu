<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>Kategori Ikan</title>

<div class="container">
    <h1>Kategori Ikan</h1>

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

    <!-- Tombol Import/Export -->
    <div class="mb-3 text-end">
        <form action="<?= base_url('user/barang/asettetaplainnya/ikan/importFromApi') ?>" method="post" class="d-inline">
            <button type="submit" class="btn btn-success" onclick="return confirm('Import/sync data dari API?')">
                <i class="bi bi-cloud-download"></i> Import/Sync API
            </button>
        </form>
        
        <form action="<?= base_url('user/barang/asettetaplainnya/ikan/resetData') ?>" method="post" class="d-inline">
            <button type="submit" class="btn btn-danger" onclick="return confirm('PERINGATAN: Ini akan menghapus SEMUA data ikan! Yakin?')">
                <i class="bi bi-trash"></i> Reset Data
            </button>
        </form>
    </div>

    <!-- Pencarian -->
    <form method="GET" class="d-flex mb-4">
        <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama, kode, merk" value="<?= esc($searchTerm ?? '') ?>">
        <button type="submit" class="btn btn-primary ms-2">Cari</button>
    </form>

    <!-- Tab navigasi untuk kelompok -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link <?= empty($activeKelompok) ? 'active' : '' ?>" 
               href="<?= base_url('user/barang/asettetaplainnya/ikan/kelompokikan') ?>">
                Semua (<?= $totalCount ?>)
            </a>
        </li>
        <?php foreach ($groupedData as $kelompok => $items): ?>
            <li class="nav-item">
                <a class="nav-link <?= ($activeKelompok === $kelompok) ? 'active' : '' ?>" 
                   href="<?= base_url('user/barang/asettetaplainnya/ikan/kelompokikan?kelompok=' . urlencode($kelompok)) ?>">
                    <?= esc($kelompok) ?> (<?= count($items) ?>)
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Export buttons -->
    <div class="mb-3">
        <div class="btn-group" role="group">
            <a href="<?= base_url('user/barang/asettetaplainnya/ikan/exportIkanList/semua') ?>" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-excel"></i> Export Semua
            </a>
            <a href="<?= base_url('user/barang/asettetaplainnya/ikan/exportIkanList/bersirip') ?>" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-excel"></i> Export Ikan Bersirip
            </a>
            <a href="<?= base_url('user/barang/asettetaplainnya/ikan/exportIkanList/crustea') ?>" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-excel"></i> Export Crustea
            </a>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kelompok</th>
                    <th>Sub Kelompok</th>
                    <th>Merk</th>
                    <th>Kondisi</th>
                    <th>Kuantitas</th>
                    <th>Status Penggunaan</th>
                    <th>Nilai Perolehan</th>
                    <th>Nilai Buku</th>
                    <th>Tanggal Perolehan</th>
                    <th>Satker</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ikanList)): ?>
                    <?php $no = 1; foreach ($ikanList as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($item['kode_barang'] ?? '-') ?></td>
                            <td><?= esc($item['nama_barang'] ?? '-') ?></td>
                            <td>
                                <span class="badge bg-info"><?= esc($item['kelompok'] ?? '-') ?></span>
                            </td>
                            <td><?= esc($item['sub_kelompok'] ?? '-') ?></td>
                            <td><?= esc($item['merk'] ?? '-') ?></td>
                            <td>
                                <?php 
                                $kondisi = strtolower($item['kondisi'] ?? '');
                                $badgeClass = 'bg-secondary';
                                if (strpos($kondisi, 'baik') !== false) $badgeClass = 'bg-success';
                                elseif (strpos($kondisi, 'rusak') !== false) $badgeClass = 'bg-danger';
                                elseif (strpos($kondisi, 'sedang') !== false) $badgeClass = 'bg-warning';
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= esc($item['kondisi'] ?? '-') ?></span>
                            </td>
                            <td><?= esc($item['kuantitas'] ?? '0') ?></td>
                            <td><?= esc($item['status_penggunaan'] ?? '-') ?></td>
                            <td>Rp <?= number_format(floatval($item['nilai_perolehan'] ?? 0), 0, ',', '.') ?></td>
                            <td>Rp <?= number_format(floatval($item['nilai_buku'] ?? 0), 0, ',', '.') ?></td>
                            <td><?= esc($item['tanggal_perolehan'] ?? '-') ?></td>
                            <td><?= esc($item['nama_satker'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="13" class="text-center">Tidak ada data yang ditemukan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Form Tambah Data Manual -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Tambah Data Manual</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('user/barang/asettetaplainnya/ikan/tambah') ?>" method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kode_barang" class="form-label">Kode Barang *</label>
                            <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang *</label>
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                        </div>
                        <div class="mb-3">
                            <label for="kelompok" class="form-label">Kelompok *</label>
                            <select class="form-select" id="kelompok" name="kelompok" required>
                                <option value="">Pilih Kelompok</option>
                                <option value="IKAN BERSIRIP">Ikan Bersirip</option>
                                <option value="CRUSTEA">Crustea</option>
                                <option value="MOLLUSCA">Mollusca</option>
                                <option value="COELENTERATA">Coelenterata</option>
                                <option value="ECHINODERMATA">Echinodermata</option>
                                <option value="AMPHIBIA">Amphibia</option>
                                <option value="REPTILIA">Reptilia</option>
                                <option value="MAMMALIA">Mammalia</option>
                                <option value="ALGAE">Algae</option>
                                <option value="BIOTA PERAIRAN LAINNYA">Biota Perairan Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kuantitas" class="form-label">Kuantitas</label>
                            <input type="number" class="form-control" id="kuantitas" name="kuantitas" value="1" min="1">
                        </div>
                        <div class="mb-3">
                            <label for="kondisi" class="form-label">Kondisi</label>
                            <select class="form-select" id="kondisi" name="kondisi">
                                <option value="">Pilih Kondisi</option>
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nilai_perolehan" class="form-label">Nilai Perolehan</label>
                            <input type="number" class="form-control" id="nilai_perolehan" name="nilai_perolehan" step="0.01">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>