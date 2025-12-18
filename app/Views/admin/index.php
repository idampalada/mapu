<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<title>Dashboard</title>

<div class="content-container">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
        <div>
            <h3 class="mb-1">Selamat Datang, <a><?= user()->username; ?></a></h3>
            <p class="text-muted">Dashboard Manajemen Aset</p>
        </div>
        <div>
            <span class="badge bg-primary"><?= date('d F Y') ?></span>
        </div>
    </div>

    <?php if(in_groups('admin') || in_groups('admin_gedungutama')): ?>
        <div class="row mb-4">
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div class="stats-card">
                    <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-inboxes"></i>
                    </div>
                    <h4 class="fs-5 fw-bold"><?= $total_kendaraan ?></h4>
                    <p class="text-muted mb-0">Total Kendaraan</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div class="stats-card">
                    <div class="stats-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h4 class="fs-5 fw-bold"><?= $kendaraan_tersedia ?></h4>
                    <p class="text-muted mb-0">Kendaraan Tersedia</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div class="stats-card">
                    <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <h4 class="fs-5 fw-bold"><?= count($peminjaman_pending) + count($pengembalian_pending) ?></h4>
                    <p class="text-muted mb-0">Menunggu Verifikasi</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div class="stats-card">
                    <div class="stats-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h4 class="fs-5 fw-bold"><?= $peminjaman_aktif ?></h4>
                    <p class="text-muted mb-0">Peminjaman Aktif</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (in_groups('admin_gedungutama') || 
                in_groups('admin_pusdatin') || 
                in_groups('admin_binamarga') || 
                in_groups('admin_ciptakarya') || 
                in_groups('admin_sda') || 
                in_groups('admin_gedungg') ||
                in_groups('admin_heritage') ||
                in_groups('admin') ||
                in_groups('admin_auditorium')): ?>
        <div class="row mb-4">
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div class="stats-card">
                    <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-building"></i>
                    </div>
                    <h4 class="fs-5 fw-bold"><?= $total_ruangan ?></h4>
                    <p class="text-muted mb-0">Total Ruangan</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div class="stats-card">
                    <div class="stats-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h4 class="fs-5 fw-bold"><?= $ruangan_tersedia ?></h4>
                    <p class="text-muted mb-0">Ruangan Tersedia</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div class="stats-card">
                    <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <h4 class="fs-5 fw-bold"><?= count($ruangan_pending) + count($ruangan_kembali_pending) ?></h4>
                    <p class="text-muted mb-0">Menunggu Verifikasi</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div class="stats-card">
                    <div class="stats-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h4 class="fs-5 fw-bold"><?= $ruangan_digunakan ?></h4>
                    <p class="text-muted mb-0">Ruangan Digunakan</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (in_groups('admin_gedungutama') || 
                in_groups('admin_pusdatin') || 
                in_groups('admin_binamarga') || 
                in_groups('admin_ciptakarya') || 
                in_groups('admin_sda') || 
                in_groups('admin_gedungg') ||
                in_groups('admin_heritage') ||
                in_groups('admin') ||
                in_groups('admin_auditorium')): ?>
    <div class="row mb-4">
        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-box"></i>
                </div>
                <h4 class="fs-5 fw-bold"><?= $total_barang ?></h4>
                <p class="text-muted mb-0">Total Barang</p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h4 class="fs-5 fw-bold"><?= $barang_tersedia ?></h4>
                <p class="text-muted mb-0">Barang Tersedia</p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="stats-card">
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
                <h4 class="fs-5 fw-bold"><?= count($barang_pending) + count($pengembalian_barang_pending) ?></h4>
                <p class="text-muted mb-0">Menunggu Verifikasi</p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="stats-card">
                <div class="stats-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-box-seam"></i>
                </div>
                <h4 class="fs-5 fw-bold"><?= $barang_digunakan ?></h4>
                <p class="text-muted mb-0">Barang Digunakan</p>
            </div>
        </div>
    </div>
<?php endif; ?>


    <div class="quick-actions mb-4">
        <div class="action-card" data-action="tambah">
            <i class="bi bi-plus-circle fs-4 text-primary mb-2"></i>
            <h6>Tambah</h6>
        </div>
        <div class="action-card" data-action="pemeliharaan">
            <i class="bi bi-tools fs-4 text-warning mb-2"></i>
            <h6>Buat Jadwal Pemeliharaan</h6>
        </div>
        <div class="action-card" data-action="verifikasi">
            <i class="bi bi-gear fs-4 text-info mb-2"></i>
            <h6>Verifikasi</h6>
        </div>
        <div class="action-card" data-action="laporan">
            <i class="bi bi-file-earmark-text fs-4 text-success mb-2"></i>
            <h6>Buat Laporan</h6>
        </div>
    </div>

    <div class="row">
    <!-- Grafik Bulanan -->
    <div class="col-12 col-lg-12 mb-4">
        <div class="chart-card">
            <canvas id="peminjamanChart"></canvas>
        </div>
    </div>

    <div class="card p-3 chart-card">
  <div class="row mb-3">
    <div class="col-md-4">
      <label for="modeChartSelector">Tampilkan Berdasarkan</label>
      <select id="modeChartSelector" class="form-control">
        <option value="bulanan">Bulanan</option>
        <option value="mingguan">Mingguan</option>
        <option value="harian">Harian</option>
      </select>
    </div>

    <div class="col-md-4" id="grupFilterBulan">
      <label for="filterBulan">Pilih Bulan</label>
      <input type="month" id="filterBulan" class="form-control" />
    </div>

    <div class="col-md-4" id="grupFilterMinggu">
      <label for="filterMinggu">Pilih Minggu</label>
      <select id="filterMinggu" class="form-control">
        <option value="1">Minggu 1</option>
        <option value="2">Minggu 2</option>
        <option value="3">Minggu 3</option>
        <option value="4">Minggu 4</option>
        <option value="5">Minggu 5</option>
      </select>
    </div>

    <div class="col-md-4" id="grupFilterTanggal">
      <label for="filterTanggal">Pilih Tanggal</label>
      <input type="date" id="filterTanggal" class="form-control" />
    </div>
  </div>

  <canvas id="chartPeminjamanUnified" style="height: 250px; max-height: 250px; width: 100%;"></canvas>
</div>

<!-- Panggil JS -->
<script src="<?= base_url('js/dashboard_chart.js') ?>"></script>


<!-- Grafik Pengembalian Bulanan-->
<div class="col-12 col-lg-12 mb-4">
  <div class="chart-card">
    <canvas id="pengembalianChart"></canvas>
  </div>
</div>

<div class="card p-3 chart-card mb-4">
  <div class="row mb-3">
    <div class="col-md-4">
      <label for="modeChartSelectorPengembalian">Tampilkan Berdasarkan</label>
      <select id="modeChartSelectorPengembalian" class="form-control">
        <option value="bulanan">Bulanan</option>
        <option value="mingguan">Mingguan</option>
        <option value="harian">Harian</option>
      </select>
    </div>

    <div class="col-md-4" id="grupFilterBulanPengembalian" style="display: none;">
      <label for="filterBulanPengembalian">Pilih Bulan</label>
      <input type="month" id="filterBulanPengembalian" class="form-control" />
    </div>

    <div class="col-md-4" id="grupFilterMingguPengembalian" style="display: none;">
      <label for="filterMingguPengembalian">Pilih Minggu</label>
      <select id="filterMingguPengembalian" class="form-control">
        <option value="1">Minggu 1</option>
        <option value="2">Minggu 2</option>
        <option value="3">Minggu 3</option>
        <option value="4">Minggu 4</option>
        <option value="5">Minggu 5</option>
      </select>
    </div>

    <div class="col-md-4" id="grupFilterTanggalPengembalian" style="display: none;">
      <label for="filterTanggalPengembalian">Pilih Tanggal</label>
      <input type="date" id="filterTanggalPengembalian" class="form-control" />
    </div>
  </div>

  <canvas id="chartPengembalianUnified" style="height: 250px; max-height: 250px; width: 100%;"></canvas>
</div>
<!-- Grafik Peminjaman Barang --><!-- Grafik Default Peminjaman Barang (tampilan garis bulanan otomatis) -->
<div class="col-12 col-lg-12 mb-4">
  <div class="chart-card">
    <canvas id="chartPeminjamanBarang"></canvas>
  </div>
</div>

<!-- Grafik Dinamis dengan Filter (Bulanan/Mingguan/Harian) -->
<div class="card p-3 chart-card mb-4">
  <div class="row mb-3">
    <div class="col-md-4">
      <label for="modeChartSelectorBarang">Tampilkan Berdasarkan</label>
      <select id="modeChartSelectorBarang" class="form-control">
        <option value="bulanan">Bulanan</option>
        <option value="mingguan">Mingguan</option>
        <option value="harian">Harian</option>
      </select>
    </div>

    <div class="col-md-4" id="grupFilterBulanBarang" style="display: none;">
      <label for="filterBulanBarang">Pilih Bulan</label>
      <input type="month" id="filterBulanBarang" class="form-control" />
    </div>

    <div class="col-md-4" id="grupFilterMingguBarang" style="display: none;">
      <label for="filterMingguBarang">Pilih Minggu</label>
      <select id="filterMingguBarang" class="form-control">
        <option value="1">Minggu 1</option>
        <option value="2">Minggu 2</option>
        <option value="3">Minggu 3</option>
        <option value="4">Minggu 4</option>
        <option value="5">Minggu 5</option>
      </select>
    </div>

    <div class="col-md-4" id="grupFilterTanggalBarang" style="display: none;">
      <label for="filterTanggalBarang">Pilih Tanggal</label>
      <input type="date" id="filterTanggalBarang" class="form-control" />
    </div>
  </div>

  <!-- Grafik yang dikontrol dropdown -->
  <canvas id="chartPeminjamanBarangUnified" style="height: 250px; max-height: 250px; width: 100%;"></canvas>
</div>




<div class="modal fade" id="modalPilihVerifikasi" tabindex="-1" aria-labelledby="modalPilihVerifikasiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPilihVerifikasiLabel">Pilih Verifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card h-100 pilih-card" onclick="showVerifikasiKendaraan()">
                            <div class="card-body text-center">
                                <i class="bi bi-tools fs-1 text-primary mb-2"></i>
                                <h6 class="card-title">Verifikasi Kendaraan</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card h-100 pilih-card" onclick="showVerifikasiRuangan()">
                            <div class="card-body text-center">
                                <i class="bi bi-building fs-1 text-success mb-2"></i>
                                <h6 class="card-title">Verifikasi Ruangan</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card h-100 pilih-card" onclick="showVerifikasiBarang()">
                            <div class="card-body text-center">
                                <i class="bi bi-box fs-1 text-success mb-2"></i>
                                <h6 class="card-title">Verifikasi Barang</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (in_groups('admin') || in_groups('admin_gedungutama')): ?><div class="modal fade" id="modalVerifikasiBarang" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="modalVerifikasiBarangLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalVerifikasiBarangLabel">Verifikasi Peminjaman & Pengembalian Barang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" id="verificationBarangTabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="peminjaman-barang-tab" data-bs-toggle="tab" href="#peminjaman-barang" role="tab">
              Peminjaman Barang Pending
              <?php if (!empty($barang_pending)): ?>
                <span class="badge bg-danger"><?= count($barang_pending) ?></span>
              <?php endif; ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="pengembalian-barang-tab" data-bs-toggle="tab" href="#pengembalian-barang" role="tab">
              Pengembalian Barang Pending
              <?php if (!empty($pengembalian_barang_pending)): ?>
                <span class="badge bg-danger"><?= count($pengembalian_barang_pending) ?></span>
              <?php endif; ?>
            </a>
          </li>
        </ul>

        <div class="tab-content mt-3">
          <!-- Tab Peminjaman Barang -->
          <div class="tab-pane fade show active" id="peminjaman-barang">
            <?php if (empty($barang_pending)): ?>
              <div class="alert alert-info">
                Tidak ada peminjaman barang yang menunggu verifikasi
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Tanggal</th>
                      <th>Nama Peminjam</th>
                      <th>Barang</th>
                      <th>Keperluan</th>
                      <th>Jadwal</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($barang_pending as $pinjam): ?>
                      <tr>
                        <td><?= date('d/m/Y', strtotime($pinjam['created_at'])) ?></td>
                        <td><?= esc($pinjam['nama_peminjam']) ?></td>
                        <td><?= esc($pinjam['nama_barang']) ?></td>
                        <td><?= esc($pinjam['keperluan']) ?></td>
                        <td>
                          <?= date('d/m/Y', strtotime($pinjam['tanggal'])) ?><br>
                          <?= esc($pinjam['waktu_mulai']) ?> - <?= esc($pinjam['waktu_selesai']) ?>
                        </td>
                        <td><span class="badge bg-warning">Pending</span></td>
                        <td>
                          <button class="btn btn-sm btn-success"
                            onclick="verifikasiPeminjamanBarang(<?= $pinjam['id'] ?>, 'disetujui')">
                            Setujui
                          </button>
                          <button class="btn btn-sm btn-danger"
        data-tipe="barang" 
        data-id="<?= $pinjam['id'] ?>"
        onclick="showTolakModal('barang', <?= $pinjam['id'] ?>)">
    Tolak
</button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>

         <!-- Tab Pengembalian Barang -->
<div class="tab-pane fade" id="pengembalian-barang">
  <?php if (empty($pengembalian_barang_pending)): ?>
    <div class="alert alert-info mt-3">
      Tidak ada pengembalian barang yang pending saat ini.
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Nama Peminjam</th>
            <th>Barang</th>
            <th>Lokasi</th>
            <th>Status</th>
            <th>Dokumen</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pengembalian_barang_pending as $item): ?>
            <tr>
              <td><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
              <td><?= esc($item['nama_peminjam']) ?></td>
              <td><?= esc($item['nama_barang']) ?></td>
              <td><?= esc($item['lokasi']) ?></td>
              <td><span class="badge bg-warning">Pending</span></td>
              <td><?= $item['tanggal'] ?? '-' ?></td>
              <td><?= $item['updated_at'] ?? '-' ?></td>
              <td><?= !empty($item['tanggal_kembali']) ? date('d/m/Y H:i:s', strtotime($item['tanggal_kembali'])) : '-' ?></td>
              <td>
              <button class="btn btn-sm btn-success"
                onclick="verifikasiPengembalianBarang(<?= $item['id'] ?>, 'disetujui')">
                Setujui
                </button>
                <button class="btn btn-sm btn-danger"
  onclick="verifikasiPengembalianBarang(<?= $item['id'] ?>, 'ditolak')">
  Tolak
<!-- </button> jika mau menggunakan pdf saat menolak
                <button class="btn btn-sm btn-danger"
                  onclick="showTolakModal('pengembalian_barang', <?= $item['id'] ?>)">
                  Tolak
                          </button> -->
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<?php if (in_groups('admin_gedungutama') || 
                in_groups('admin_pusdatin') || 
                in_groups('admin_binamarga') || 
                in_groups('admin_ciptakarya') || 
                in_groups('admin_sda') || 
                in_groups('admin_gedungg') ||
                in_groups('admin_heritage') ||
                in_groups('admin') ||
                in_groups('admin_auditorium')): ?>
    <div class="modal fade" id="modalVerifikasiRuangan" tabindex="-1" data-bs-backdrop="static"
        aria-labelledby="modalVerifikasiRuanganLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVerifikasiRuanganLabel">Verifikasi Peminjaman & Pengembalian Ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="verificationRuanganTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="peminjaman-ruangan-tab" data-bs-toggle="tab" href="#peminjaman-ruangan" role="tab">
                                Peminjaman Ruangan Pending
                                <?php if (!empty($ruangan_pending)): ?>
                                    <span class="badge bg-danger"><?= count($ruangan_pending) ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pengembalian-ruangan-tab" data-bs-toggle="tab" href="#pengembalian-ruangan" role="tab">
                                Pengembalian Ruangan Pending
                                <?php if (!empty($ruangan_kembali_pending)): ?>
                                    <span class="badge bg-danger"><?= count($ruangan_kembali_pending) ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="peminjaman-ruangan">
                            <?php if (empty($ruangan_pending)): ?>
                                <div class="alert alert-info">
                                    Tidak ada peminjaman ruangan yang menunggu verifikasi
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Penanggung Jawab</th>
                                                <th>Ruangan</th>
                                                <th>Keperluan</th>
                                                <th>Jadwal</th>
                                                <th>Status</th>
                                                <th>Dokumen</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ruangan_pending as $pinjam): ?>
                                                <tr>
                                                    <td><?= date('d/m/Y', strtotime($pinjam['created_at'])) ?></td>
                                                    <td><?= $pinjam['nama_penanggung_jawab'] ?></td>
                                                    <td><?= $pinjam['nama_ruangan'] ?></td>
                                                    <td><?= $pinjam['keperluan'] ?></td>
                                                    <td>
                                                        <?= date('d/m/Y', strtotime($pinjam['tanggal'])) ?><br>
                                                        <?= $pinjam['waktu_mulai'] ?> - <?= $pinjam['waktu_selesai'] ?>
                                                    </td>
                                                    <td><span class="badge bg-warning">Pending</span></td>
                                                    <td>
                                                        <?php if (!empty($pinjam['surat_permohonan'])): ?>
                                                            <a href="<?= base_url('uploads/documents/' . $pinjam['surat_permohonan']) ?>" 
                                                            target="_blank" 
                                                            class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-file-pdf"></i> Surat Permohonan
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-success" 
                                                                onclick="verifikasiPeminjamanRuangan(<?= $pinjam['id'] ?>, 'disetujui')">
                                                            Setujui
                                                        </button>

    <!-- Ubah Jam (NEW) -->
    <button class="btn btn-sm btn-warning me-1" 
            onclick="bukaModalUbahJam(<?= $pinjam['id'] ?>)"
            title="Ubah jam dan setujui">
        <i class="bi bi-clock-history"></i> Ubah Jam
    </button>

                                                        <button class="btn btn-sm btn-danger"
        data-tipe="ruangan" 
        data-id="<?= $pinjam['id'] ?>"
        onclick="showTolakModal('ruangan', <?= $pinjam['id'] ?>)">
    Tolak
</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="tab-pane fade" id="pengembalian-ruangan">
                            <?php if (empty($ruangan_kembali_pending)): ?>
                                <div class="alert alert-info">
                                    Tidak ada pengembalian ruangan yang menunggu verifikasi
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Penanggung Jawab</th>
                                                    <th>Kendaraan</th>
                                                    <th>Status</th>
                                                    <th>Dokumen</th>
                                                    <th>Tanggal Pinjam</th>
                                                    <th>Tanggal Kembali</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($pengembalian_pending as $kembali): ?>
                                                    <tr>
                                                        <td><?= date('d/m/Y', strtotime($kembali['created_at'])) ?></td>
                                                        <td><?= $kembali['nama_penanggung_jawab'] ?></td>
                                                        <td><?= $kembali['merk'] ?></td>
                                                        <td>
                                                            <span class="badge bg-warning">Pending</span>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($kembali['surat_pengembalian'])): ?>
                                                                <a href="<?= base_url('/uploads/documents/' . $kembali['surat_pengembalian']) ?>"
                                                                    target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                                                    <i class="bi bi-file-earmark-pdf"></i> Surat Jalan
                                                                </a>
                                                            <?php endif; ?>

                                                            <?php if (!empty($kembali['berita_acara_pengembalian'])): ?>
                                                                <a href="<?= base_url('/uploads/documents/' . $kembali['berita_acara_pengembalian']) ?>"
                                                                    target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                                                    <i class="bi bi-file-earmark-pdf"></i> Berita Acara
                                                                </a>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= $kembali['tanggal_pinjam'] ?></td>
                                                        <td><?= $kembali['tanggal_kembali'] ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-success" 
                                                                    onclick="verifikasiPeminjamanRuangan(<?= $pinjam['id'] ?>, 'disetujui')">
                                                                Setujui
                                                            </button>
                                                            <button class="btn btn-sm btn-danger"
        data-tipe="pengembalian" 
        data-id="<?= $kembali['id'] ?>"
        onclick="showTolakModal('pengembalian', <?= $kembali['id'] ?>)">
    Tolak
</button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
    <div class="modal fade" id="modalVerifikasi" tabindex="-1" data-bs-backdrop="static"
        aria-labelledby="modalVerifikasiLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVerifikasiLabel">Verifikasi Peminjaman & Pengembalian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="tab-content" id="verificationTabContent">
                        <ul class="nav nav-tabs" id="verificationTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="peminjaman-tab" data-bs-toggle="tab" href="#peminjaman"
                                    role="tab">
                                    Peminjaman Pending
                                    <?php if (!empty($peminjaman_pending)): ?>
                                        <span class="badge bg-danger"><?= count($peminjaman_pending) ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pengembalian-tab" data-bs-toggle="tab" href="#pengembalian"
                                    role="tab">
                                    Pengembalian Pending
                                    <?php if (!empty($pengembalian_pending)): ?>
                                        <span class="badge bg-danger"><?= count($pengembalian_pending) ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content" id="verificationTabContent">
                            <div class="tab-pane fade show active" id="peminjaman">
                                <?php if (empty($peminjaman_pending)): ?>
                                    <div class="alert alert-info mt-3">
                                        Tidak ada peminjaman yang menunggu verifikasi
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Penanggung Jawab</th>
                                                    <th>Kendaraan</th>
                                                    <th>Urusan Kedinasan</th>
                                                    <th>Status</th>
                                                    <th>Surat Permohonan</th>
                                                    <th>Tanggal Pinjam</th>
                                                    <th>Tanggal Kembali</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($peminjaman_pending as $pinjam): ?>
                                                    <tr>
                                                        <td><?= date('d/m/Y', strtotime($pinjam['created_at'])) ?></td>
                                                        <td><?= $pinjam['nama_penanggung_jawab'] ?></td>
                                                        <td><?= $pinjam['merk'] ?></td>
                                                        <td><?= $pinjam['urusan_kedinasan'] ?></td>
                                                        <td>
                                                            <span class="badge bg-warning">Pending</span>
                                                        </td>
                                                        <td>
                                                        <?php if (!empty($pinjam['surat_permohonan'])): ?>
                                                            <?php
                                                            // Deteksi tipe file berdasarkan prefix yang tersimpan di database
                                                            $isFinal = strpos($pinjam['surat_permohonan'], 'surat_permohonan_') === 0;
                                                            $buttonText = $isFinal ? 'Surat Permohonan' : 'Draft Surat';
                                                            $buttonClass = $isFinal ? 'btn-primary' : 'btn-outline-primary';
                                                            
                                                            // PENTING: Gunakan nama file LANGSUNG dari database
                                                            $filePath = base_url('/uploads/documents/' . $pinjam['surat_permohonan']);
                                                            
                                                            // Debug log
                                                            log_message('debug', 'Link PDF: ' . $filePath . ' | Nama file asli: ' . $pinjam['surat_permohonan']);
                                                            ?>
                                                            
                                                            <a href="<?= $filePath ?>" target="_blank" class="btn btn-sm <?= $buttonClass ?> mb-1">
                                                                <i class="bi bi-file-earmark-pdf"></i> <?= $buttonText ?>
                                                            </a>
                                                        <?php endif; ?>
                                                        </td>
                                                        <td><?= $pinjam['tanggal_pinjam'] ?></td>
                                                        <td><?= $pinjam['tanggal_kembali'] ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-success"
                                                                onclick="showSetujuModal(<?= $pinjam['id'] ?>)">
                                                                Setujui
                                                            </button>
                                                            <button class="btn btn-sm btn-danger"
                                                                data-tipe="kendaraan" 
                                                                data-id="<?= $pinjam['id'] ?>"
                                                                onclick="showTolakModal('kendaraan', <?= $pinjam['id'] ?>)">
                                                                Tolak
                                                            </button>
                                                            <!-- Tambah tombol edit surat -->
                                                            <button class="btn btn-sm btn-info"
                                                                onclick="showEditSuratModal(<?= $pinjam['id'] ?>)">
                                                                Penomoran Surat
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="tab-pane fade" id="pengembalian">
                                <?php if (empty($pengembalian_pending)): ?>
                                    <div class="alert alert-info mt-3">
                                        Tidak ada pengembalian yang menunggu verifikasi
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Penanggung Jawab</th>
                                                    <th>Kendaraan</th>
                                                    <th>Status</th>
                                                    <th>Rating User</th>
                                                    <th>Dokumen</th>
                                                    <th>Tanggal Pinjam</th>
                                                    <th>Tanggal Kembali</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($pengembalian_pending as $kembali): ?>
                                                    <tr>
                                                        <td><?= date('d/m/Y', strtotime($kembali['created_at'])) ?></td>
                                                        <td><?= $kembali['nama_penanggung_jawab'] ?></td>
                                                        <td><?= $kembali['merk'] ?></td>
                                                        <td>
                                                            <span class="badge bg-warning">Pending</span>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($kembali['rating_pengguna'])): ?>
                                                                <div class="d-flex align-items-center">
                                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                        <?php if ($i <= $kembali['rating_pengguna']): ?>
                                                                            <i class="bi bi-star-fill text-warning"></i>
                                                                        <?php else: ?>
                                                                            <i class="bi bi-star text-muted"></i>
                                                                        <?php endif; ?>
                                                                    <?php endfor; ?>
                                                                    <span class="ms-1"><?= $kembali['rating_pengguna'] ?>/5</span>
                                                                </div>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($kembali['berita_acara_pengembalian'])): ?>
                                                                <a href="<?= base_url('/uploads/documents/' . $kembali['berita_acara_pengembalian']) ?>"
                                                                    target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                                                    <i class="bi bi-file-earmark-pdf"></i> Berita Acara
                                                                </a>
                                                            <?php endif; ?>

                                                            <?php if (!empty($kembali['foto_pengembalian'])): ?>
                                                                <a href="<?= base_url('/uploads/images/' . $kembali['foto_pengembalian']) ?>"
                                                                    target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                                                    <i class="bi bi-image"></i> Foto Kendaraan
                                                                </a>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= $kembali['tanggal_pinjam'] ?></td>
                                                        <td>
                                                            <?= !empty($kembali['tanggal_kembali']) ? date('d/m/Y', strtotime($kembali['tanggal_kembali'])) : '-' ?>
                                                            <div class="small text-muted">
                                                                <?= date('H:i:s', strtotime($kembali['created_at'])) ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-success"
                                                                onclick="showRatingModal(<?= $kembali['id'] ?>)">
                                                                Setujui
                                                            </button>
                                                            <button class="btn btn-sm btn-danger"
                                                                data-tipe="pengembalian" 
                                                                data-id="<?= $kembali['id'] ?>"
                                                                onclick="showTolakModal('pengembalian', <?= $kembali['id'] ?>)">
                                                                Tolak
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rating Admin -->
<!-- REPLACE the existing modalRatingAdmin in admin/index.php with this enhanced version -->
<!-- Modal Rating Admin dengan TTE Integration -->
<div class="modal fade" id="modalRatingAdmin" tabindex="-1" aria-labelledby="modalRatingAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRatingAdminLabel">
                    <i class="bi bi-star text-warning"></i> 
                    Rating Pengembalian Kendaraan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRatingAdmin">
                    <input type="hidden" id="kembali_id_rating" name="kembali_id">
                    <input type="hidden" name="status" value="disetujui">
                    
                    <div class="row">
                        <!-- KOLOM KIRI: Rating & Catatan -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Rating Kondisi Kendaraan <span class="text-danger">*</span></label>
                                <div class="rating-container">
                                    <div class="star-rating d-flex align-items-center">
                                        <div class="rating-stars">
                                            <input type="radio" id="admin_star5" name="rating_admin" value="5" required />
                                            <label for="admin_star5" title="Sangat Baik"><i class="bi bi-star-fill"></i></label>
                                            
                                            <input type="radio" id="admin_star4" name="rating_admin" value="4" />
                                            <label for="admin_star4" title="Baik"><i class="bi bi-star-fill"></i></label>
                                            
                                            <input type="radio" id="admin_star3" name="rating_admin" value="3" />
                                            <label for="admin_star3" title="Cukup"><i class="bi bi-star-fill"></i></label>
                                            
                                            <input type="radio" id="admin_star2" name="rating_admin" value="2" />
                                            <label for="admin_star2" title="Kurang"><i class="bi bi-star-fill"></i></label>
                                            
                                            <input type="radio" id="admin_star1" name="rating_admin" value="1" />
                                            <label for="admin_star1" title="Sangat Kurang"><i class="bi bi-star-fill"></i></label>
                                        </div>
                                        <span class="ms-3 rating-text-admin">0/5</span>
                                    </div>
                                    <div class="form-text">
                                        <small><i class="bi bi-info-circle"></i> Berikan rating kondisi kendaraan saat pengembalian (1-5)</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="keterangan" class="form-label">Catatan (opsional)</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                    placeholder="Catatan tambahan tentang kondisi kendaraan"></textarea>
                            </div>
                        </div>

                        <!-- KOLOM KANAN: TTE Options -->
                        <div class="col-md-6">
                            <div class="card border-success h-100">
                                <div class="card-header bg-success bg-opacity-10">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 text-success">
                                            <i class="bi bi-shield-check"></i> Tanda Tangan Elektronik
                                        </h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enableTTEPengembalian" name="enable_tte_pengembalian">
                                            <label class="form-check-label fw-bold text-success" for="enableTTEPengembalian">
                                                Aktif
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" id="tteOptionsPengembalian" style="display: none;">
                                    <!-- NIK -->
                                    <div class="mb-3">
                                        <label for="tte_nik_pengembalian" class="form-label">NIK Penandatangan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" id="tte_nik_pengembalian" name="tte_nik_pengembalian" 
                                               placeholder="Masukkan NIK 16 digit" maxlength="16">
                                        <div class="form-text">NIK harus terdaftar di sistem BSrE</div>
                                    </div>
                                    
                                    <!-- Passphrase -->
                                    <div class="mb-3">
                                        <label for="tte_passphrase_pengembalian" class="form-label">Passphrase <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control form-control-sm" id="tte_passphrase_pengembalian" name="tte_passphrase_pengembalian" 
                                                   placeholder="Masukkan passphrase TTE">
                                            <button class="btn btn-outline-secondary btn-sm" type="button" id="togglePassphrasePengembalian">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- QR Link -->
                                    <div class="mb-3">
                                        <label for="tte_qr_link_pengembalian" class="form-label">Link QR Code</label>
                                        <input type="url" class="form-control form-control-sm" id="tte_qr_link_pengembalian" name="tte_qr_link_pengembalian" 
                                               value="https://s.pu.go.id">
                                    </div>

                                    <!-- Load Credentials Button -->
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-sm btn-outline-warning" id="loadCredentialsPengembalian">
                                            <i class="bi bi-arrow-repeat"></i> Load Credential Terakhir
                                        </button>
                                    </div>

                                    <!-- SECTION POSISI SIGNATURE PENGEMBALIAN -->
                                    <div class="card border-info mb-3">
                                        <div class="card-header bg-info bg-opacity-10 py-2">
                                            <h6 class="mb-0 text-info">
                                                <i class="bi bi-crosshair"></i> Posisi Tanda Tangan
                                            </h6>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="form-group mb-3">
                                                <label for="tte_position_pengembalian" class="form-label">Posisi Tanda Tangan</label>
                                                <select class="form-select form-select-sm" name="tte_position_pengembalian" id="tte_position_pengembalian">
                                                    <option value="visible_custom">Halaman - Posisi Kustom</option>
                                                    <option value="invisible">Invisible (Tidak Terlihat)</option>
                                                </select>
                                            </div>

                                            <!-- Page Selection -->
                                            <div class="form-group mb-3" id="pageSelectionPengembalian">
                                                <label for="tte_page_pengembalian" class="form-label">Pilih Halaman</label>
                                                <select class="form-select form-select-sm" name="tte_page_pengembalian" id="tte_page_pengembalian">
                                                    <option value="1">Page 1</option>
                                                    <option value="2" selected>Page 2</option>
                                                    <option value="3">Page 3</option>
                                                </select>
                                            </div>

                                            <div id="customPositionPengembalian" style="display:block;">
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label">Posisi X</label>
                                                        <input type="number" class="form-control form-control-sm" 
                                                               id="tte_x_pengembalian" name="tte_x_pengembalian" 
                                                               value="650" min="0" max="2000">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label">Posisi Y</label>
                                                        <input type="number" class="form-control form-control-sm" 
                                                               id="tte_y_pengembalian" name="tte_y_pengembalian" 
                                                               value="340" min="0" max="2000">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label">Lebar</label>
                                                        <input type="number" class="form-control form-control-sm" 
                                                               id="tte_width_pengembalian" name="tte_width_pengembalian" 
                                                               value="200" min="0" max="2000">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label">Tinggi</label>
                                                        <input type="number" class="form-control form-control-sm" 
                                                               id="tte_height_pengembalian" name="tte_height_pengembalian" 
                                                               value="290" min="0" max="2000">
                                                    </div>
                                                </div>
                                                
                                                <div class="alert alert-info py-2">
                                                    <small>
                                                        <i class="bi bi-info-circle"></i> <strong>Koordinat Berita Acara Pengembalian:</strong><br>
                                                        • <strong>Page 2:</strong> X: 650, Y: 340 Lebar 200 Tinggi 290 (atas tengah) <span class="badge bg-success">Recommended Keterlambatan</span><br>
                                                        • Sesuaikan koordinat sesuai kebutuhan dokumen
                                                    </small>
                                                </div>
                                            </div>

                                            <!-- Hidden fields for advanced options -->
                                            <input type="hidden" id="tte_reason_pengembalian" name="tte_reason_pengembalian" value="Berita Acara Pengembalian telah disetujui dan ditandatangani secara elektronik">
                                            <input type="hidden" id="tte_location_pengembalian" name="tte_location_pengembalian" value="Jakarta">
                                        </div>
                                    </div>

                                    <div class="alert alert-info py-2">
                                        <small>
                                            <i class="bi bi-info-circle"></i>
                                            <strong>Info TTE:</strong> Proses tanda tangan elektronik membutuhkan waktu 30-60 detik.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSubmitRating">
                    <i class="bi bi-check-circle"></i> Konfirmasi Persetujuan
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal Edit Surat dengan TTE Credential Input -->
<div class="modal fade" id="modalEditSurat" tabindex="-1" aria-labelledby="modalEditSuratLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditSuratLabel">Edit Surat Permohonan & Tanda Tangan Elektronik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditSurat" action="<?= base_url('AsetKendaraan/updateSuratWithTTE'); ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" id="pinjam_id_surat" name="pinjam_id">

                    <div class="row">
                        <!-- KOLOM KIRI: Data Surat -->
                        <div class="col-md-7">
                            <h6 class="mb-3 text-primary"><i class="bi bi-file-earmark-text"></i> Data Surat Permohonan</h6>
                            
                            <div class="form-group mb-3">
                                <label for="nomor_surat" class="form-label">Nomor Surat<span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="tanggal_surat" class="form-label">Tanggal Surat</label>
                                        <input type="date" class="form-control" id="tanggal_surat" name="tanggal_surat" value="<?= date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="tempat_surat" class="form-label">Tempat Penandatanganan</label>
                                        <input type="text" class="form-control" id="tempat_surat" name="tempat_surat" value="Jakarta" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="nama_kepala_satuan_kerja" class="form-label">Kepala Satuan Kerja Selaku Kuasa Pengguna Barang</label>
                                <input type="text" class="form-control" id="nama_kepala_satuan_kerja" name="nama_kepala_satuan_kerja" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="nip_kepala_satuan_kerja" class="form-label">NIP Kepala Satuan Kerja</label>
                                <input type="text" class="form-control" id="nip_kepala_satuan_kerja" name="nip_kepala_satuan_kerja" required>
                            </div>
                        </div>

                        <!-- KOLOM KANAN: TTE Options dengan Credential Input -->
                        <div class="col-md-5">
                            <h6 class="mb-3 text-success"><i class="bi bi-shield-check"></i> Tanda Tangan Elektronik</h6>
                            
                            <div class="card border-success shadow-sm">
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="enableTTE" name="enable_tte" checked>
                                        <label class="form-check-label fw-bold text-success" for="enableTTE">
                                            Aktifkan Tanda Tangan Elektronik
                                        </label>
                                    </div>

                                    <div id="tteOptions">
                                        <!-- SECTION CREDENTIAL TTE -->
                                        <div class="card border-warning mb-3">
                                            <div class="card-header bg-warning bg-opacity-10 py-2">
                                                <h6 class="mb-0 text-warning">
                                                    <i class="bi bi-key"></i> Credential Penandatangan
                                                </h6>
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="mb-3">
                                                    <label for="tte_nik_permohonan" class="form-label">NIK Penandatangan <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm" id="tte_nik_permohonan" name="tte_nik" 
                                                           placeholder="Masukkan NIK 16 digit" maxlength="16" required>
                                                    <div class="form-text">NIK harus sudah terdaftar di sistem BSrE</div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="tte_passphrase_permohonan" class="form-label">Passphrase <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control form-control-sm" id="tte_passphrase_permohonan" name="tte_passphrase" 
                                                               placeholder="Masukkan passphrase TTE" required>
                                                        <button class="btn btn-outline-secondary btn-sm" type="button" id="togglePassphrasePermohonan">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    </div>
                                                    <div class="form-text">Passphrase untuk akses sertifikat elektronik</div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="tte_qr_link_permohonan" class="form-label">Link QR Code <span class="text-danger">*</span></label>
                                                    <input type="url" class="form-control form-control-sm" id="tte_qr_link_permohonan" name="tte_qr_link" 
                                                           placeholder="https://example.com" value="https://s.pu.go.id" required>
                                                    <div class="form-text">Link yang akan ditampilkan pada QR code</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SECTION POSISI SIGNATURE SURAT PERMOHONAN -->
                                        <div class="card border-info mb-3">
                                            <div class="card-header bg-info bg-opacity-10 py-2">
                                                <h6 class="mb-0 text-info">
                                                    <i class="bi bi-crosshair"></i> Posisi Tanda Tangan - Surat Permohonan
                                                </h6>
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="form-group mb-3">
                                                    <label for="tte_position_permohonan" class="form-label">Posisi Tanda Tangan</label>
                                                    <select class="form-select form-select-sm" name="tte_position" id="tte_position_permohonan">
                                                        <option value="visible_custom">Halaman 1 - Posisi Kustom</option>
                                                        <option value="invisible">Invisible (Tidak Terlihat)</option>
                                                    </select>
                                                </div>

                                                <div id="customPositionPermohonan" style="display:block;">
                                                    <div class="row mb-3">
                                                        <div class="col-6">
                                                            <label class="form-label">Posisi X</label>
                                                            <input type="number" class="form-control form-control-sm" 
                                                                   name="tte_x_permohonan" id="tte_x_permohonan" 
                                                                   value="650" min="0" max="2000">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label">Posisi Y</label>
                                                            <input type="number" class="form-control form-control-sm" 
                                                                   name="tte_y_permohonan" id="tte_y_permohonan" 
                                                                   value="190" min="0" max="2000">
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-6">
                                                            <label class="form-label">Lebar</label>
                                                            <input type="number" class="form-control form-control-sm" 
                                                                   name="tte_width_permohonan" id="tte_width_permohonan" 
                                                                   value="200" min="0" max="2000">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label">Tinggi</label>
                                                            <input type="number" class="form-control form-control-sm" 
                                                                   name="tte_height_permohonan" id="tte_height_permohonan" 
                                                                   value="160" min="0" max="2000">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="alert alert-info py-2">
                                                        <small>
                                                            <i class="bi bi-info-circle"></i>
                                                            <strong>Koordinat Surat Permohonan:</strong><br>
                                                            • X: 650, Y: 190 (posisi kanan-atas)<br>
                                                            • Lebar: 200, Tinggi: 160<br>
                                                            • Sesuaikan jika diperlukan
                                                        </small>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label for="tte_reason_permohonan" class="form-label">Alasan Penandatanganan</label>
                                                    <input type="text" class="form-control form-control-sm" id="tte_reason_permohonan" name="tte_reason" 
                                                           value="Dokumen telah disetujui dan ditandatangani">
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label for="tte_location_permohonan" class="form-label">Lokasi Penandatanganan</label>
                                                    <input type="text" class="form-control form-control-sm" id="tte_location_permohonan" name="tte_location" 
                                                           value="Jakarta">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-warning py-2">
                                            <small>
                                                <i class="bi bi-exclamation-triangle"></i>
                                                <strong>Perhatian Surat Permohonan:</strong> 
                                                <ul class="mb-0 mt-1">
                                                    <li>Proses TTE membutuhkan waktu 30-60 detik</li>
                                                    <li>Pastikan NIK sudah terdaftar di BSrE dengan status AKTIF</li>
                                                    <li>Koordinat: X=650, Y=190 (kanan-atas)</li>
                                                    <li>Credential akan digunakan sekali saja, tidak disimpan</li>
                                                </ul>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                    <button type="submit" class="btn btn-success" id="btnSubmitSurat">
                        <i class="bi bi-check-circle"></i> 
                        <span id="btnSubmitText">Simpan Surat & TTE</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="modalPilihTambah" tabindex="-1" aria-labelledby="modalPilihTambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPilihTambahLabel">Pilih</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card h-100 pilih-card" onclick="showTambahKendaraan()">
                            <div class="card-body text-center">
                                <i class="bi bi-tools fs-1 text-primary mb-2"></i>
                                <h6 class="card-title">Tambah Kendaraan</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card h-100 pilih-card" onclick="showTambahRuangan()">
                            <div class="card-body text-center">
                                <i class="bi bi-building fs-1 text-success mb-2"></i>
                                <h6 class="card-title">Tambah Ruangan</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
    <div class="card h-100 pilih-card" onclick="showTambahBarang()">
        <div class="card-body text-center">
            <i class="bi bi-box-seam fs-1 text-info mb-2"></i>
            <h6 class="card-title">Tambah Barang</h6>
        </div>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (in_groups('admin_gedungutama') || 
                in_groups('admin_pusdatin') || 
                in_groups('admin_binamarga') || 
                in_groups('admin_ciptakarya') || 
                in_groups('admin_sda') || 
                in_groups('admin_gedungg') ||
                in_groups('admin_heritage') ||
                in_groups('admin') ||
                in_groups('admin_auditorium')): ?>
     <div class="modal fade" id="modalTambahRuangan" tabindex="-1" aria-labelledby="modalTambahRuanganLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahRuanganLabel">Tambah Ruangan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formTambahRuangan" action="<?= base_url('/Ruangan/tambah'); ?>" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nama_ruangan">Nama Ruangan</label>
                                    <input type="text" class="form-control" id="nama_ruangan" name="nama_ruangan" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="lokasi">Pilih Lokasi Gedung</label>
                                    <select class="form-control" id="lokasi" name="lokasi" required>
                                        <option value="" class="text-muted" disabled selected> Pilih</option>
                                        <option value="Gedung Utama">Gedung Utama</option>
                                        <option value="Pusat Data dan Teknologi Informasi">Pusat Data dan Teknologi Informasi</option>
                                        <option value="Bina Marga">Bina Marga</option>
                                        <option value="Cipta Karya">Cipta Karya</option>
                                        <option value="Sumber Daya Air">Sumber Daya Air</option>
                                        <option value="Gedung G">Gedung G</option>
                                        <option value="Heritage">Heritage</option>
                                        <option value="Auditorium">Auditorium</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="kapasitas">Kapasitas</label>
                                    <input type="number" class="form-control" id="kapasitas" name="kapasitas" required>
                                </div>
                            </div>
                            <!-- Tambahkan field Luas Ruangan -->
<div class="form-group mb-3">
    <label for="luas_ruangan">Luas Ruangan</label>
    <div class="input-group">
        <input type="number" class="form-control" id="luas_ruangan" name="luas_ruangan" placeholder="Masukkan luas ruangan">
        <span class="input-group-text">m²</span>
    </div>
    <small class="text-muted">Masukkan angka luas ruangan dalam meter persegi</small>
</div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="fasilitas">Fasilitas</label>
                                    <div class="row px-3">
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]" value="TV" id="fasilitas_tv">
                                                <label class="form-check-label" for="fasilitas_tv">TV</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Projector" id="fasilitas_projector">
                                                <label class="form-check-label" for="fasilitas_projector">Projector</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Papan Tulis" id="fasilitas_papantulis">
                                                <label class="form-check-label" for="fasilitas_papantulis">Papan Tulis</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Sound System" id="fasilitas_sound">
                                                <label class="form-check-label" for="fasilitas_sound">Sound System</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]" value="AC" id="fasilitas_ac">
                                                <label class="form-check-label" for="fasilitas_ac">AC</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Wifi" id="fasilitas_wifi">
                                                <label class="form-check-label" for="fasilitas_wifi">Wifi</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Hidden input untuk memastikan fasilitas selalu terkirim, meski kosong -->
                                    <input type="hidden" name="fasilitas_submitted" value="1">
                                </div>
                                
                                <!-- Field Keterangan yang akan digabung dengan fasilitas -->
                                <div class="form-group mb-3">
                                    <label for="keterangan_fasilitas">Keterangan Tambahan Fasilitas</label>
                                    <textarea class="form-control" 
                                              id="keterangan_fasilitas" 
                                              name="keterangan" 
                                              rows="4" 
                                              placeholder="Tambahkan keterangan detail tentang fasilitas ruangan, kondisi, atau informasi penting lainnya..."></textarea>
                                    <small class="text-muted">Keterangan ini akan digabung dengan fasilitas yang dipilih di atas</small>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="foto_ruangan">Foto Ruangan</label>
                                    <input type="file" class="form-control" id="foto_ruangan" name="foto_ruangan[]" multiple accept="image/*">
                                    <small class="text-muted">Format: JPG, PNG (Max 2MB per foto, maksimal 5 foto)</small>
                                    <div id="previewRuanganContainer" class="mt-3">
                                        <div class="row" id="previewRuanganRow"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal Setuju Peminjaman -->
<!-- Modal Setuju Peminjaman -->
<!-- Modal Setuju Peminjaman -->
<!-- Modal Setuju Peminjaman -->
<div class="modal fade" id="modalSetuju" tabindex="-1" aria-labelledby="modalSetujuLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSetujuLabel">Verifikasi Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Nav tabs untuk dual tab -->
            <ul class="nav nav-tabs" id="verifikasiTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="surat-penanggung-tab" data-bs-toggle="tab" 
                            data-bs-target="#suratPenanggungTab" type="button" role="tab" 
                            aria-controls="suratPenanggungTab" aria-selected="true">
                        Buat Surat Penanggung Jawab KDF
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="surat-jalan-tab" data-bs-toggle="tab" 
                            data-bs-target="#suratJalanTab" type="button" role="tab" 
                            aria-controls="suratJalanTab" aria-selected="false">
                        Buat Surat Jalan
                    </button>
                </li>
            </ul>
            
            <div class="tab-content">
                <!-- Tab 1: Buat Surat Penanggung Jawab KDF -->
                <div class="tab-pane fade show active" id="suratPenanggungTab" role="tabpanel" 
             aria-labelledby="surat-penanggung-tab">
    <div class="modal-body">
        <input type="hidden" id="pinjamId" name="pinjam_id">
        
        <div class="row">
            <!-- KOLOM KIRI: Data KDF -->
            <div class="col-md-7">
                <!-- Checkbox untuk memilih surat -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="buatSuratPenanggungJawab" name="buatSuratPenanggungJawab" checked>
                    <label class="form-check-label" for="buatSuratPenanggungJawab">
                        <strong>Buat Surat Penanggung Jawab KDF</strong>
                    </label>
                </div>
                
                <!-- Data peminjaman -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="nama_penanggung_jawab" class="form-label">Nama Penanggung Jawab</label>
                            <input type="text" class="form-control" id="nama_penanggung_jawab" name="nama_penanggung_jawab" readonly>
                        </div>
                        <div class="form-group mb-2">
                            <label for="nip_nrp" class="form-label">NIP/NRP</label>
                            <input type="text" class="form-control" id="nip_nrp" name="nip_nrp" readonly>
                        </div>
                        <div class="form-group mb-2">
                            <label for="pangkat_golongan" class="form-label">Pangkat/Golongan</label>
                            <input type="text" class="form-control" id="pangkat_golongan" name="pangkat_golongan" readonly>
                        </div>
                        <div class="form-group mb-2">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="no_polisi" class="form-label">Nomor Polisi</label>
                            <input type="text" class="form-control" id="no_polisi" name="no_polisi" readonly>
                        </div>
                        <div class="form-group mb-2">
                            <label for="kode_barang" class="form-label">Kode Barang</label>
                            <input type="text" class="form-control" id="kode_barang" name="kode_barang" readonly>
                        </div>
                    </div>
                </div>
                
                <!-- Penomoran Surat -->
                <h5 class="mt-3 mb-2">Penomoran Surat Penanggung Jawab KDF</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label for="nomor_surat_kdf" class="form-label">Nomor Surat <span class="text-danger"> *</span></label>
                            <input type="text" class="form-control surat-penanggung-field" id="nomor_surat_kdf" name="nomor_surat">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label for="tanggal_surat_kdf" class="form-label">Tanggal Surat</label>
                            <input type="date" class="form-control surat-penanggung-field" id="tanggal_surat_kdf" name="tanggal_surat" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label for="tempat_surat_kdf" class="form-label">Tempat Surat</label>
                            <input type="text" class="form-control surat-penanggung-field" id="tempat_surat_kdf" name="tempat_surat" value="Jakarta">
                        </div>
                    </div>
                </div>
                
                <!-- Bagian untuk kedua penandatangan -->
                <h5 class="mt-3 mb-2">Data Penandatangan</h5>

                <!-- Penandatangan 1: Penanggung Jawab KDF -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="nama_penanggung_jawab_kendaraan" class="form-label">Nama Penanggung Jawab Kendaraan</label>
                            <input type="text" class="form-control surat-penanggung-field" id="nama_penanggung_jawab_kendaraan" name="nama_penanggung_jawab_kendaraan">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="nip_penanggung_jawab_kendaraan" class="form-label">NIP Penanggung Jawab Kendaraan</label>
                            <input type="text" class="form-control surat-penanggung-field" id="nip_penanggung_jawab_kendaraan" name="nip_penanggung_jawab_kendaraan">
                        </div>
                    </div>
                </div>

                <!-- Penandatangan 2: Kepala Satuan Kerja -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="nama_kepala_satuan_kerja" class="form-label">Nama Kepala Satuan Kerja</label>
                            <input type="text" class="form-control surat-penanggung-field" id="nama_kepala_satuan_kerja" name="nama_kepala_satuan_kerja" value="Suherman">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="nip_kepala_satuan_kerja" class="form-label">NIP Kepala Satuan Kerja</label>
                            <input type="text" class="form-control surat-penanggung-field" id="nip_kepala_satuan_kerja" name="nip_kepala_satuan_kerja" value="13579">
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: TTE Options untuk KDF -->
            <div class="col-md-5">
                <div class="card border-success h-100">
                    <div class="card-header bg-success bg-opacity-10">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-success">
                                <i class="bi bi-shield-check"></i> Tanda Tangan Elektronik KDF
                            </h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enableTTEKdf" name="enable_tte" checked>
                                <label class="form-check-label fw-bold text-success" for="enableTTEKdf">
                                    Aktif
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="tteOptionsKdf">
                            <!-- SECTION CREDENTIAL TTE KDF -->
                            <div class="card border-warning mb-3">
                                <div class="card-header bg-warning bg-opacity-10 py-2">
                                    <h6 class="mb-0 text-warning">
                                        <i class="bi bi-key"></i> Credential Penandatangan
                                    </h6>
                                </div>
                                <div class="card-body py-2">
                                    <div class="mb-3">
                                        <label for="tte_nik_kdf" class="form-label">NIK Penandatangan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" id="tte_nik_kdf" name="tte_nik" 
                                               placeholder="Masukkan NIK 16 digit" maxlength="16" required>
                                        <div class="form-text">NIK harus sudah terdaftar di sistem BSrE</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="tte_passphrase_kdf" class="form-label">Passphrase <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control form-control-sm" id="tte_passphrase_kdf" name="tte_passphrase" 
                                                   placeholder="Masukkan passphrase TTE" required>
                                            <button class="btn btn-outline-secondary btn-sm" type="button" id="togglePassphraseKdf">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="form-text">Passphrase untuk akses sertifikat elektronik</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="tte_qr_link_kdf" class="form-label">Link QR Code <span class="text-danger">*</span></label>
                                        <input type="url" class="form-control form-control-sm" id="tte_qr_link_kdf" name="tte_qr_link" 
                                               placeholder="https://example.com" value="https://s.pu.go.id" required>
                                        <div class="form-text">Link yang akan ditampilkan pada QR code</div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION POSISI SIGNATURE KDF -->
                            <div class="card border-info mb-3">
                                <div class="card-header bg-info bg-opacity-10 py-2">
                                    <h6 class="mb-0 text-info">
                                        <i class="bi bi-crosshair"></i> Posisi Tanda Tangan - KDF
                                    </h6>
                                </div>
                                <div class="card-body py-2">
                                    <div class="form-group mb-3">
                                        <label for="tte_position_kdf" class="form-label">Posisi Tanda Tangan</label>
                                        <select class="form-select form-select-sm" name="tte_position" id="tte_position_kdf">
                                            <option value="visible_custom">Halaman 1 - Posisi Kustom</option>
                                            <option value="invisible">Invisible (Tidak Terlihat)</option>
                                        </select>
                                    </div>

                                    <div id="customPositionKdf" style="display:block;">
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <label class="form-label">Posisi X</label>
                                                <input type="number" class="form-control form-control-sm" 
                                                       name="tte_x_kdf" id="tte_x_kdf" 
                                                       value="650" min="0" max="2000">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Posisi Y</label>
                                                <input type="number" class="form-control form-control-sm" 
                                                       name="tte_y_kdf" id="tte_y_kdf" 
                                                       value="220" min="0" max="2000">
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <label class="form-label">Lebar</label>
                                                <input type="number" class="form-control form-control-sm" 
                                                       name="tte_width_kdf" id="tte_width_kdf" 
                                                       value="200" min="0" max="2000">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Tinggi</label>
                                                <input type="number" class="form-control form-control-sm" 
                                                       name="tte_height_kdf" id="tte_height_kdf" 
                                                       value="170" min="0" max="2000">
                                            </div>
                                        </div>

                                        <div class="alert alert-info py-2">
                                            <small>
                                                <i class="bi bi-info-circle"></i>
                                                <strong>Koordinat Surat KDF:</strong><br>
                                                • X: 650, Y: 250 (posisi kanan-tengah)<br>
                                                • Lebar: 200, Tinggi: 200<br>
                                                • Sesuaikan jika diperlukan
                                            </small>
                                        </div>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label for="tte_reason_kdf" class="form-label">Alasan Penandatanganan</label>
                                        <input type="text" class="form-control form-control-sm" id="tte_reason_kdf" name="tte_reason" 
                                               value="Surat Penanggung Jawab KDF telah disetujui dan ditandatangani">
                                    </div>

                                    <div class="form-group mb-2">
                                        <label for="tte_location_kdf" class="form-label">Lokasi Penandatanganan</label>
                                        <input type="text" class="form-control form-control-sm" id="tte_location_kdf" name="tte_location" 
                                               value="Jakarta">
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning py-2">
                                <small>
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>Perhatian Surat KDF:</strong> 
                                    <ul class="mb-0 mt-1">
                                        <li>Proses TTE untuk KDF membutuhkan waktu 30-60 detik</li>
                                        <li>Surat KDF harus ditandatangani oleh pihak yang berwenang</li>
                                        <li>Koordinat: X=650, Y=250 (kanan-tengah)</li>
                                        <li>Credential akan digunakan sekali saja, tidak disimpan</li>
                                    </ul>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                
                <!-- Tab 2: Buat Surat Jalan -->
                <div class="tab-pane fade" id="suratJalanTab" role="tabpanel" aria-labelledby="surat-jalan-tab">
                    <div class="modal-body">
                        <input type="hidden" id="pinjamId2" name="pinjam_id">
                        
                        <div class="row">
                            <!-- KOLOM KIRI: Data Surat Jalan -->
                            <div class="col-md-7">
                                <!-- Checkbox untuk memilih surat -->
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="buatSuratJalan" name="buatSuratJalan" checked>
                                    <label class="form-check-label" for="buatSuratJalan">
                                        <strong>Buat Surat Jalan</strong>
                                    </label>
                                </div>
                                
                                <!-- Data peminjaman -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="nama_penanggung_jawab2" class="form-label">Nama Penanggung Jawab</label>
                                            <input type="text" class="form-control" id="nama_penanggung_jawab2" name="nama_penanggung_jawab2" readonly>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="nip_nrp2" class="form-label">NIP/NRP</label>
                                            <input type="text" class="form-control" id="nip_nrp2" name="nip_nrp2" readonly>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="pangkat_golongan2" class="form-label">Pangkat/Golongan</label>
                                            <input type="text" class="form-control" id="pangkat_golongan2" name="pangkat_golongan2" readonly>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="jabatan2" class="form-label">Jabatan</label>
                                            <input type="text" class="form-control" id="jabatan2" name="jabatan2" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="no_polisi2" class="form-label">Nomor Polisi</label>
                                            <input type="text" class="form-control" id="no_polisi2" name="no_polisi2" readonly>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="kode_barang2" class="form-label">Kode Barang</label>
                                            <input type="text" class="form-control" id="kode_barang2" name="kode_barang2" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Jadwal Peminjaman Surat Jalan -->
                                <h5 class="mt-3 mb-2">Jadwal Peminjaman Surat Jalan</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger"> *</span></label>
                                            <input type="date" class="form-control surat-jalan-field" id="tanggal_mulai" name="tanggal_mulai" required>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                            <input type="time" class="form-control surat-jalan-field" id="jam_mulai" name="jam_mulai" value="08:00">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger"> *</span></label>
                                            <input type="date" class="form-control surat-jalan-field" id="tanggal_selesai" name="tanggal_selesai" required>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="jam_selesai" class="form-label">Jam Selesai</label>
                                            <input type="time" class="form-control surat-jalan-field" id="jam_selesai" name="jam_selesai" value="17:00">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="urusan_kedinasan" class="form-label">Urusan Kedinasan <span class="text-danger"> *</span></label>
                                    <textarea class="form-control surat-jalan-field" id="urusan_kedinasan" name="urusan_kedinasan" rows="3" placeholder="Masukkan urusan kedinasan..." required></textarea>
                                </div>
                                
                                <!-- Data Pemegang Surat Penanggung Jawab -->
                                <h5 class="mt-3 mb-2">Data Pemegang Surat Penanggung Jawab</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="nama_pemegang_surat" class="form-label">Nama Pemegang Surat</label>
                                            <input type="text" class="form-control" id="nama_pemegang_surat" name="nama_pemegang_surat" value="Pak Udin">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="nip_pemegang_surat" class="form-label">NIP Pemegang Surat</label>
                                            <input type="text" class="form-control" id="nip_pemegang_surat" name="nip_pemegang_surat" value="12345678">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- KOLOM KANAN: TTE Options untuk Surat Jalan -->
                            <div class="col-md-5">
                                <div class="card border-success h-100">
                                    <div class="card-header bg-success bg-opacity-10">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-success">
                                                <i class="bi bi-shield-check"></i> Tanda Tangan Elektronik Surat Jalan
                                            </h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enableTTESuratJalan" name="enable_tte_surat_jalan" checked>
                                                <label class="form-check-label fw-bold text-success" for="enableTTESuratJalan">
                                                    Aktif
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="tteOptionsSuratJalan">
                                            <!-- SECTION CREDENTIAL TTE SURAT JALAN -->
                                            <div class="card border-warning mb-3">
                                                <div class="card-header bg-warning bg-opacity-10 py-2">
                                                    <h6 class="mb-0 text-warning">
                                                        <i class="bi bi-key"></i> Credential Penandatangan
                                                    </h6>
                                                </div>
                                                <div class="card-body py-2">
                                                    <div class="mb-3">
                                                        <label for="tte_nik_surat_jalan" class="form-label">NIK Penandatangan <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control form-control-sm" id="tte_nik_surat_jalan" name="tte_nik_surat_jalan" 
                                                               placeholder="Masukkan NIK 16 digit" maxlength="16" required>
                                                        <div class="form-text">NIK harus sudah terdaftar di sistem BSrE</div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="tte_passphrase_surat_jalan" class="form-label">Passphrase <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control form-control-sm" id="tte_passphrase_surat_jalan" name="tte_passphrase_surat_jalan" 
                                                                   placeholder="Masukkan passphrase TTE" required>
                                                            <button class="btn btn-outline-secondary btn-sm" type="button" id="togglePassphraseSuratJalan">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                        </div>
                                                        <div class="form-text">Passphrase untuk akses sertifikat elektronik</div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="tte_qr_link_surat_jalan" class="form-label">Link QR Code <span class="text-danger">*</span></label>
                                                        <input type="url" class="form-control form-control-sm" id="tte_qr_link_surat_jalan" name="tte_qr_link_surat_jalan" 
                                                               placeholder="https://example.com" value="https://s.pu.go.id" required>
                                                        <div class="form-text">Link yang akan ditampilkan pada QR code</div>
                                                    </div>

                                                    <!-- Load Credentials Button -->
                                                    <div class="mb-3">
                                                        <button type="button" class="btn btn-outline-warning btn-sm" id="loadCredentialsSuratJalan">
                                                            <i class="bi bi-arrow-repeat"></i> Gunakan Credential Terakhir
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- SECTION POSISI SIGNATURE SURAT JALAN -->
                                            <div class="card border-info mb-3">
                                                <div class="card-header bg-info bg-opacity-10 py-2">
                                                    <h6 class="mb-0 text-info">
                                                        <i class="bi bi-crosshair"></i> Posisi Tanda Tangan
                                                    </h6>
                                                </div>
                                                <div class="card-body py-2">
                                                    <div class="form-group mb-3">
                                                        <label for="tte_position_surat_jalan" class="form-label">Posisi Tanda Tangan</label>
                                                        <select class="form-select form-select-sm" name="tte_position_surat_jalan" id="tte_position_surat_jalan">
                                                            <option value="visible_custom">Halaman 1 - Posisi Kustom</option>
                                                            <option value="invisible">Invisible (Tidak Terlihat)</option>
                                                        </select>
                                                    </div>

                                                    <div id="customPositionSuratJalan" style="display:block;">
                                                        <div class="row mb-2">
                                                            <div class="col-6">
                                                                <label class="form-label">Posisi X</label>
                                                                <input type="number" class="form-control form-control-sm" id="tte_x_surat_jalan"
                                                                       name="tte_x_surat_jalan" value="650" min="0" max="2000">
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label">Posisi Y</label>
                                                                <input type="number" class="form-control form-control-sm" id="tte_y_surat_jalan"
                                                                       name="tte_y_surat_jalan" value="270" min="0" max="2000">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-6">
                                                                <label class="form-label">Lebar</label>
                                                                <input type="number" class="form-control form-control-sm" id="tte_width_surat_jalan"
                                                                       name="tte_width_surat_jalan" value="200" min="0" max="2000">
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label">Tinggi</label>
                                                                <input type="number" class="form-control form-control-sm" id="tte_height_surat_jalan"
                                                                       name="tte_height_surat_jalan" value="220" min="0" max="2000">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-2">
                                                        <label for="tte_reason_surat_jalan" class="form-label">Alasan Penandatanganan</label>
                                                        <input type="text" class="form-control form-control-sm" id="tte_reason_surat_jalan" name="tte_reason_surat_jalan" 
                                                               value="Surat Jalan telah disetujui dan ditandatangani secara elektronik">
                                                    </div>

                                                    <div class="form-group mb-2">
                                                        <label for="tte_location_surat_jalan" class="form-label">Lokasi Penandatanganan</label>
                                                        <input type="text" class="form-control form-control-sm" id="tte_location_surat_jalan" name="tte_location_surat_jalan" 
                                                               value="Jakarta">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="alert alert-warning py-2">
                                                <small>
                                                    <i class="bi bi-exclamation-triangle"></i>
                                                    <strong>Perhatian Surat Jalan:</strong> 
                                                    <ul class="mb-0 mt-1">
                                                        <li>Proses TTE untuk Surat Jalan membutuhkan waktu 30-60 detik</li>
                                                        <li>Surat Jalan harus ditandatangani oleh pihak yang berwenang</li>
                                                        <li>Credential akan digunakan sekali saja, tidak disimpan</li>
                                                    </ul>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
            <!-- Footer dengan tombol submit utama -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" id="btnSubmitAll" class="btn btn-success" onclick="submitKDFWithTTE()">
                    <span id="btnSubmitText">Setujui dan Buat Dokumen</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced styling for the modal */
.modal-xl {
    max-width: 90% !important;
}

/* Card styling untuk TTE */
.card {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card.border-warning,
.card.border-info,
.card.border-success {
    border-width: 2px !important;
}

/* NIK input styling */
#tte_nik_kdf {
    font-family: 'Courier New', monospace;
    letter-spacing: 1px;
    color: #6f42c1;
    font-weight: 600;
}

/* Form control validation styling */
.form-control.is-valid {
    border-color: #198754;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

/* Switch styling */
.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

/* Alert styling */
.alert-warning {
    border-left: 4px solid #ffc107;
}
</style>

<!-- Modal Sukses -->
<div class="modal fade" id="modalSuccess" tabindex="-1" aria-labelledby="modalSuccessLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h3 class="mb-3">Berhasil!</h3>
                <p class="mb-4">Verifikasi peminjaman berhasil</p>
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal" onclick="location.reload()">OK</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalPreviewGambar" tabindex="-1" aria-labelledby="modalPreviewGambarLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPreviewGambarLabel">Preview Gambar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" alt="Preview" style="max-width: 100%; max-height: 500px;">
            </div>
        </div>
    </div>
</div>
</div>

<?php if (in_groups('admin')): ?>
    <div class="modal fade" id="modalTambahAset" tabindex="-1" aria-labelledby="modalTambahAsetLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahAsetLabel">Tambah Aset Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formTambahAset" action="<?= base_url('/AsetKendaraan/tambah'); ?>" method="post" class="assets" enctype="multipart/form-data">
                <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="kategori_id">Kategori</label>
                                <select class="form-control" id="kategori_id" name="kategori_id" required>
                                    <option value="" class="text-muted" disabled selected>Pilih Kategori Aset</option>
                                    <option class="fw-bold text-dark" value="KDJ">Kendaraan Dinamis Jalan (KDJ)</option>
                                    <option class="text-muted" disabled>Sedan, Hatchback, dan SUV</option>
                                    <option class="fw-bold text-dark" value="KDO">Kendaraan Dinamis Off-road (KDO)</option>
                                    <option class="text-muted" disabled>Bus, Truk, dan Kendaraan Box</option>
                                    <option class="fw-bold text-dark" value="KDF">Kendaraan Dinamis Fasilitas (KDF)</option>
                                    <option class="text-muted" disabled>Ambulance, Mobil Derek, dan Mobil Crane</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="kode_barang">Kode Barang</label>
                                <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="merk">Merk</label>
                                <input type="text" class="form-control" id="merk" name="merk" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="warna">Warna</label>
                                <input type="text" class="form-control" id="warna" name="warna">
                            </div>
                            <div class="form-group mb-3">
                                <label for="tahun_pembuatan">Tahun Pembuatan</label>
                                <input type="text" class="form-control" id="tahun_pembuatan" name="tahun_pembuatan">
                            </div>
                            <div class="form-group mb-3">
                                <label for="kapasitas">Kapasitas</label>
                                <input type="number" class="form-control" id="kapasitas" name="kapasitas">
                            </div>
                            <div class="form-group mb-3">
                                <label for="no_stnk">Nomor STNK</label>
                                <input type="text" class="form-control" id="no_stnk" name="no_stnk" placeholder="Masukkan nomor STNK">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nomor_polisi">Nomor Polisi</label>
                                <input type="text" class="form-control" id="no_polisi" name="no_polisi">
                            </div>
                            <div class="form-group mb-3">
                                <label for="nup">NUP</label>
                                <input type="text" class="form-control" id="nup" name="nup">
                            </div>
                            <div class="form-group mb-3">
                                <label for="nomor_mesin">Nomor Mesin</label>
                                <input type="text" class="form-control" id="nomor_mesin" name="nomor_mesin">
                            </div>
                            <div class="form-group mb-3">
                                <label for="no_rangka">No Rangka</label>
                                <input type="text" class="form-control" id="no_rangka" name="no_rangka">
                            </div>
                            <div class="form-group mb-3">
                                <label for="kondisi">Kondisi</label>
                                <select class="form-control" id="kondisi" name="kondisi">
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="no_bpkb">Nomor BPKB</label>
                                <input type="text" class="form-control" id="no_bpkb" name="no_bpkb" placeholder="Masukkan nomor BPKB">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION UPLOAD FOTO -->
                    <hr class="my-4">
                    <h5 class="mb-3">📸 Upload Foto Kendaraan</h5>
                    
                    <div class="form-group mb-3">
                        <label for="gambar_mobil">Gambar Mobil <span class="text-danger"> *</span></label>
                        <input type="file" id="gambar_mobil" name="gambar_mobil[]" class="form-control" multiple accept="image/jpeg,image/png" required>
                        <small class="text-muted">Format: JPG, PNG (Max 5MB per foto, maksimal 5 foto)</small>
                        
                        <!-- CONTAINER PREVIEW - INI YANG PENTING -->
                        <div id="imagePreviewContainer" class="mt-4">
                            <div class="row" id="previewRow">
                                <!-- Preview foto akan muncul di sini -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <div class="modal fade" id="modalLaporan" tabindex="-1" aria-labelledby="modalLaporanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLaporanLabel">Buat Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formLaporan" action="<?= base_url('/admin/laporan/tambah'); ?>" method="post"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-200">
                                <div class="mb-3">
                                    <label for="kendaraan_laporan" class="form-label">Kendaraan</label>
                                    <select class="form-select" id="kendaraan_laporan" name="kendaraan_id" required>
                                        <option value="">Pilih Kendaraan</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="jenis_laporan" class="form-label">Jenis Laporan</label>
                                    <select class="form-select" id="jenis_laporan" name="jenis_laporan" required>
                                        <option value="" class="text-muted" disabled selected>Pilih Jenis Laporan</option>
                                        <option value="Laporan Insiden">Laporan Insiden</option>
                                        <option value="Laporan Kerusakan">Laporan Kerusakan</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_kejadian" class="form-label">Tanggal Kejadian</label>
                                    <input type="date" class="form-control" id="tanggal_kejadian" name="tanggal_kejadian"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="lokasi_kejadian" class="form-label">Lokasi Kejadian</label>
                                    <input type="text" class="form-control" id="lokasi_kejadian" name="lokasi_kejadian"
                                        placeholder="" required>
                                </div>
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="4" placeholder=""
                                        required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="tindak_lanjut" class="form-label">Tindak Lanjut</label>
                                    <textarea class="form-control" id="tindak_lanjut" name="tindak_lanjut" rows="3"
                                        placeholder=""></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="bukti_foto" class="form-label">Bukti Foto</label>
                                    <input type="file" class="form-control" id="bukti_foto" name="bukti_foto"
                                        accept="image/*">
                                    <small class="text-muted">Format: JPG, PNG, maksimal 2MB</small>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-labelledby="modalTambahJadwalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahJadwalLabel">Tambah Jadwal Pemeliharaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formTambahJadwal" action="<?= base_url('/PemeliharaanRutin/tambahJadwal'); ?>" method="post"
                    name="pemeliharaan_rutin">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kendaraan" class="form-label">Kendaraan</label>
                            <select class="form-select" id="kendaraan" name="kendaraan_id" required>
                                <option value="">Pilih Kendaraan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_pemeliharaan" class="form-label">Jenis Pemeliharaan</label>
                            <select class="form-select" id="jenis_pemeliharaan" name="jenis_pemeliharaan" required>
                                <option value="">Pilih Jenis</option>
                                <option value="Service Rutin">Service Rutin</option>
                                <option value="Ganti Oli">Ganti Oli</option>
                                <option value="Tune Up">Tune Up</option>
                                <!-- <option value="Ganti Spareparts">Ganti Spareparts</option> -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_terjadwal" class="form-label">Tanggal Terjadwal</label>
                            <input type="date" class="form-control" id="tanggal_terjadwal" name="tanggal_terjadwal"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="bengkel" class="form-label">Bengkel</label>
                            <input type="text" class="form-control" id="bengkel" name="bengkel">
                        </div>
                        <div class="mb-3">
                            <label for="biaya" class="form-label">Estimasi Biaya</label>
                            <input type="number" class="form-control" id="biaya" name="biaya" min="0" step="1000">
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="modal fade" id="fileUnsafeModal" tabindex="-1" aria-labelledby="fileUnsafeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="fileUnsafeModalLabel">Peringatan Keamanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-shield-x text-danger" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center" id="fileUnsafeMessage">File yang Anda upload terdeteksi tidak aman</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tolak - PERBAIKAN -->
<div class="modal fade" id="modalTolak" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTolakLabel">Alasan Penolakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formTolak">
                    <input type="hidden" id="tolakId" name="id">
                    <input type="hidden" id="tolakTipe" name="tipe">
                    <input type="hidden" id="jenisVerifikasi" value="peminjaman">
                    
                    <div class="mb-3">
                        <label for="alasanPenolakan" class="form-label">Alasan Penolakan<span class="text-danger"> *</span></label>
                        <textarea class="form-control" id="alasanPenolakan" name="alasan" rows="3" required
                                  placeholder="Masukkan alasan penolakan yang jelas dan informatif..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dokumen_tambahan" class="form-label">Dokumen Tambahan <span class="text-danger"> *</span></label>
                        <input type="file" class="form-control" id="dokumen_tambahan" name="dokumen_tambahan" 
                               accept="application/pdf,image/jpeg,image/jpg,image/png" required>
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> 
                            Upload dokumen pendukung. Max 2MB, format PDF, JPG, atau PNG.
                        </div>
                    </div>
                    
                    <!-- Debug Info (bisa dihapus di production) -->
                    <div class="alert alert-info" id="debugInfo" style="display: none;">
                        <small>
                            <strong>Debug Info:</strong><br>
                            ID: <span id="debugId">-</span><br>
                            Tipe: <span id="debugTipe">-</span><br>
                            Jenis: <span id="debugJenis">-</span>
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="submitPenolakan()">
                    <i class="bi bi-x-circle"></i> Kirim Penolakan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imageDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Gambar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="detailImage" src="" alt="Detail Preview" style="max-width: 100%; max-height: 80vh;">
            </div>
        </div>
    </div>
</div>
<?php if (in_groups('admin') || in_groups('admin_gedungutama')): ?>
<div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-labelledby="modalTambahBarangLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahBarangLabel">Tambah Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formTambahBarang" action="<?= base_url('/User/Barang/tambah') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_barang" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                            </div>
                            <div class="mb-3">
    <label for="kode_barang" class="form-label">Kode Barang</label>
    <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
</div>
                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <select class="form-select" id="kategori" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Komputer Jaringan">Komputer Jaringan</option>
                                    <option value="Personal Komputer">Personal Komputer</option>
                                    <option value="Komputer Unit">Komputer Unit</option>
                                    <option value="Peralatan Mainframe">Peralatan Mainframe</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="lokasi" class="form-label">Lokasi</label>
                                <input type="text" class="form-control" id="lokasi" name="lokasi">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kondisi" class="form-label">Kondisi</label>
                                <select class="form-select" id="kondisi" name="kondisi" required>
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="gambar" class="form-label">Gambar Barang</label>
                                <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG. Max 2MB</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <?php endif; ?>



    <script>
    // Konstanta dan konfigurasi
    const BASE_URL = '<?= base_url() ?>';
    const USER_ID = <?= user()->id ?>;
    const ROUTES = {
        getKendaraan: BASE_URL + '/admin/pemeliharaan-rutin/get-kendaraan',
        getPemeliharaan: BASE_URL + '/admin/pemeliharaan-rutin/get-pemeliharaan',
        tambahJadwal: BASE_URL + '/admin/pemeliharaan-rutin/tambah-jadwal',
        deleteJadwal: BASE_URL + '/admin/pemeliharaan-rutin/delete',
        updateJadwal: BASE_URL + '/admin/pemeliharaan-rutin/update',
        exportExcel: BASE_URL + '/admin/pemeliharaan-rutin/export-excel',
        exportPDF: BASE_URL + '/admin/pemeliharaan-rutin/export-pdf',
        tambahLaporan: BASE_URL + '/admin/laporan/tambah',
        getLaporan: BASE_URL + '/admin/laporan/get-laporan',
        updateLaporan: BASE_URL + '/admin/laporan/update',
        deleteLaporan: BASE_URL + '/admin/laporan/delete'
    };

    // Helper functions untuk modal
    function showTambahBarang() {
        const modal = new bootstrap.Modal(document.getElementById('modalTambahBarang'));
        modal.show();
    }

    function showVerifikasiBarang() {
        const modal = new bootstrap.Modal(document.getElementById('modalVerifikasiBarang'));
        modal.show();
    }

    function showVerifikasiRuangan() {
        const modal = new bootstrap.Modal(document.getElementById('modalVerifikasiRuangan'));
        modal.show();
    }

    function showVerifikasiKendaraan() {
        const modal = new bootstrap.Modal(document.getElementById('modalVerifikasi'));
        modal.show();
    }

    // PENTING: showTolakModal sudah didefinisikan di dashboard.js
    // JANGAN TULIS LAGI DI SINI untuk menghindari konflik
    
    // Function untuk verifikasi peminjaman barang
    function verifikasiPeminjamanBarang(id, status) {
        if (status === "ditolak") {
            showTolakModal("barang", id);
            return;
        }

        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menyetujui peminjaman barang ini?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, Setujui",
            cancelButtonText: "Batal",
            confirmButtonColor: "#198754",
            cancelButtonColor: "#dc3545",
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Memproses...",
                    text: "Mohon tunggu sebentar",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                const formData = new FormData();
                formData.append("pinjam_id", id);
                formData.append("status", "disetujui");

                fetch("/admin/User/Barang/verifikasiPeminjaman", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                    },
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: data.message || "Peminjaman barang telah disetujui.",
                            showConfirmButton: false,
                            timer: 1500,
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        throw new Error(data.error || "Terjadi kesalahan saat verifikasi");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Gagal!",
                        text: error.message,
                        confirmButtonText: "Tutup",
                    });
                });
            }
        });
    }

    // Function untuk verifikasi peminjaman ruangan
    function verifikasiPeminjamanRuangan(id, status) {
        if (status === "ditolak") {
            showTolakModal("ruangan", id);
            return;
        }

        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menyetujui peminjaman ini?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, Setuju",
            cancelButtonText: "Batal",
            confirmButtonColor: "#198754",
            cancelButtonColor: "#dc3545",
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Memproses...",
                    text: "Mohon tunggu sebentar",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                const formData = new FormData();
                formData.append("pinjam_id", id);
                formData.append("status", "disetujui");

                fetch("/admin/User/Ruangan/verifikasiPeminjaman", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                    },
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500,
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        throw new Error(data.error || "Terjadi kesalahan saat verifikasi");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Gagal!",
                        text: error.message,
                        confirmButtonText: "Tutup",
                    });
                });
            }
        });
    }

    // Debug mode untuk development
    <?php if (ENVIRONMENT === 'development'): ?>
    window.DEBUG_MODE = true;
    console.log('Dashboard loaded with debug mode');
    <?php endif; ?>
</script>

<!-- Baru setelah itu load JS -->
<script src="/assets/js/dashboard.js"></script>
<style>
    /* CSS untuk rating stars admin */
.rating-stars {
    direction: rtl;
    display: inline-block;
}

.rating-stars input[type="radio"] {
    display: none;
}

.rating-stars label {
    color: #bbb;
    font-size: 1.5rem;
    padding: 0;
    cursor: pointer;
    margin: 0 2px;
}

.rating-stars label:hover,
.rating-stars label:hover ~ label,
.rating-stars input[type="radio"]:checked ~ label {
    color: #ffb700;
}

.rating-text-admin {
    font-size: 1rem;
    align-self: center;
}

.rating-container {
    margin-bottom: 10px;
}
</style>


<?= $this->endSection() ?>