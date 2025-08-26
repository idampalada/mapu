<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<div class="page-heading">
    <div class="row">
        <div class="col-12">
            <h3>Sistem Manajemen Aset Kementerian Pekerjaan Umum</h3>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="row mb-4">
        <!-- Statistik Kendaraan -->
        <div class="col-12 col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h4>Statistik Kendaraan</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stats-card bg-light p-3 rounded">
                                <h6>Total</h6>
                                <h3 id="totalKendaraan"><?= esc($total_kendaraan ?? 0) ?></h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card bg-success bg-opacity-10 p-3 rounded">
                                <h6 class="text-white">Tersedia</h6>
                                <h3 class="text-white" id="kendaraanTersedia"><?= esc($tersedia_kendaraan ?? 0) ?></h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card bg-primary bg-opacity-10 p-3 rounded">
                                <h6 class="text-white">Dipinjam</h6>
                                <h3 class="text-white" id="kendaraanDipinjam"><?= esc($dipinjam_kendaraan ?? 0) ?></h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card bg-warning bg-opacity-10 p-3 rounded">
                                <h6>Verifikasi</h6>
                                <h3 id="kendaraanVerifikasi"><?= esc($verifikasi_kendaraan ?? 0) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Ruangan -->
        <div class="col-12 col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h4>Statistik Ruangan</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stats-card bg-light p-3 rounded">
                                <h6>Total</h6>
                                <h3 id="totalRuangan"><?= esc($total_ruangan ?? 0) ?></h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card bg-success bg-opacity-10 p-3 rounded">
                                <h6 class="text-white">Tersedia</h6>
                                <h3 class="text-white" id="ruanganTersedia"><?= esc($tersedia_ruangan ?? 0) ?></h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card bg-primary bg-opacity-10 p-3 rounded">
                                <h6 class="text-white">Dibooking</h6>
                                <h3 class="text-white" id="ruanganDibooking"><?= esc($dibooking_ruangan ?? 0) ?></h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card bg-warning bg-opacity-10 p-3 rounded">
                                <h6>Verifikasi</h6>
                                <h3 id="ruanganVerifikasi"><?= esc($verifikasi_ruangan ?? 0) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Barang -->
        <div class="col-12 col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h4>Statistik Barang</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stats-card bg-light p-3 rounded">
                                <h6>Total</h6>
                                <h3 id="totalBarang"><?= esc($total_barang ?? 0) ?></h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card bg-success bg-opacity-10 p-3 rounded">
                                <h6 class="text-white">Tersedia</h6>
                                <h3 class="text-white" id="barangTersedia"><?= esc($tersedia_barang ?? 0) ?></h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card bg-primary bg-opacity-10 p-3 rounded">
                                <h6 class="text-white">Dipinjam</h6>
                                <h3 class="text-white" id="barangDipinjam"><?= esc($dipinjam_barang ?? 0) ?></h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card bg-warning bg-opacity-10 p-3 rounded">
                                <h6>Verifikasi</h6>
                                <h3 id="barangVerifikasi"><?= esc($verifikasi_barang ?? 0) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <div class="col-12 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h4>Tren Peminjaman Kendaraan</h4>
                </div>
                <div class="card-body">
                    <canvas id="peminjamanKendaraanChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h4>Tren Peminjaman Ruangan</h4>
                </div>
                <div class="card-body">
                    <canvas id="peminjamanRuanganChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="row">
        <?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
    <!-- Status Kendaraan -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Status Kendaraan</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">

                <!-- Dropdown untuk memilih jumlah data -->
                <form method="get" class="d-flex align-items-center mb-3">
                    <label for="per_page" class="me-2 mb-0">Tampilkan</label>
                    <select name="per_page" id="per_page" class="form-select w-auto me-2" onchange="this.form.submit()">
                        <option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
                        <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                    </select>
                    <span class="text-muted">data per halaman</span>
                </form>

                <!-- Tabel riwayat peminjaman -->
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>No Polisi</th>
                            <th>Kendaraan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($riwayatKendaraan)): ?>
                            <?php foreach ($riwayatKendaraan as $row): ?>
                                <tr>
                                    <td><?= esc($row->tanggal_pinjam) ?></td>
                                    <td><?= esc($row->tanggal_kembali) ?></td>
                                    <td><?= esc($row->no_polisi) ?></td>
                                    <td><?= esc($row->merk) ?></td>
                                    <td><?= ucfirst(esc($row->status)) ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada data peminjaman.</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    <?= $pager->links('default', 'default_full') ?>
                </div>

            </div>
        </div>
    </div>
</div>
<?php endif ?>

<?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
    <!-- Status Ruangan -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Status Ruangan</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">

                <!-- Dropdown untuk memilih jumlah data -->
                <form method="get" class="d-flex align-items-center mb-3">
                    <label for="per_page_ruangan" class="me-2 mb-0">Tampilkan</label>
                    <select name="per_page_ruangan" id="per_page_ruangan" class="form-select w-auto me-2" onchange="this.form.submit()">
                        <option value="5" <?= $perPageRuangan == 5 ? 'selected' : '' ?>>5</option>
                        <option value="10" <?= $perPageRuangan == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= $perPageRuangan == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $perPageRuangan == 50 ? 'selected' : '' ?>>50</option>
                    </select>
                    <span class="text-muted">data per halaman</span>
                </form>

                <!-- Tabel status ruangan -->
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Nama Ruangan</th>
                            <th>Lokasi</th>
                            <th>Tanggal</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($statusRuangan)): ?>
                            <?php foreach ($statusRuangan as $row): ?>
                                <tr>
                                    <td><?= esc($row->nama_ruangan) ?></td>
                                    <td><?= esc($row->lokasi) ?></td>
                                    <td><?= date('d M Y', strtotime($row->tanggal)) ?></td>
                                    <td><?= esc($row->waktu_mulai) ?></td>
                                    <td><?= esc($row->waktu_selesai) ?></td>
                                    <td><?= ucfirst(esc($row->status)) ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada data ruangan.</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                <?= $pager->links('ruangan', 'default_full') ?>

                </div>


            </div>
        </div>
    </div>
</div>
<?php endif ?>

<?= $this->endSection() ?>