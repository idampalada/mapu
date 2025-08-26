<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<div class="page-heading mb-4">
    <h3 class="text-center">Riwayat Peminjaman dan Pengembalian Barang</h3>
<div class="mt-3 text-start">
        <a href="javascript:history.back()" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <ul class="nav nav-tabs d-flex justify-content-between border-0" id="historyTabs" role="tablist">
            <li class="nav-item flex-fill" role="presentation">
                <button class="nav-link active w-100 d-flex align-items-center justify-content-center"
                        id="peminjaman-history-tab" data-bs-toggle="tab" data-bs-target="#peminjaman-history" role="tab">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Peminjaman
                </button>
            </li>
            <li class="nav-item flex-fill" role="presentation">
                <button class="nav-link w-100 d-flex align-items-center justify-content-center"
                        id="pengembalian-history-tab" data-bs-toggle="tab" data-bs-target="#pengembalian-history" role="tab">
                    <i class="bi bi-box-arrow-in-left me-2"></i> Pengembalian
                </button>
            </li>
        </ul>

        <?php
        $statusClass = [
            'diajukan' => 'bg-warning',
            'disetujui' => 'bg-success',
            'ditolak' => 'bg-danger',
            'selesai' => 'bg-success',
            'proses_pengembalian' => 'bg-primary'
        ];

        $statusLabel = [
            'diajukan' => 'Menunggu',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'selesai' => 'Selesai',
            'proses_pengembalian' => 'Proses Pengembalian'
        ];

        $statusIcon = [
            'diajukan' => 'bi-clock',
            'disetujui' => 'bi-check-circle',
            'ditolak' => 'bi-x-circle',
            'selesai' => 'bi-check-circle-fill',
            'proses_pengembalian' => 'bi-arrow-repeat'
        ];
        ?>

        <div class="tab-content mt-4" id="historyTabContent">

            <!-- TABEL PEMINJAMAN -->
            <div class="tab-pane fade show active" id="peminjaman-history">
                <?php if (empty($peminjaman_history)): ?>
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="bi bi-info-circle me-2"></i> Tidak ada riwayat peminjaman
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Peminjam</th>
                                    <th>Barang</th>
                                    <th>Status</th>
                                    <th>Keperluan</th>
                                    <th>Waktu</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($peminjaman_history as $row): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                        <td><?= esc($row['nama_peminjam']) ?></td>
                                        <td><?= esc($row['nama_barang']) ?><br><small class="text-muted"><?= esc($row['kategori']) ?> - <?= esc($row['lokasi']) ?></small></td>
                                        <td>
                                            <span class="badge text-white <?= $statusClass[$row['status']] ?? 'bg-secondary' ?>">
                                                <i class="bi <?= $statusIcon[$row['status']] ?? 'bi-question-circle' ?> me-1"></i>
                                                <?= $statusLabel[$row['status']] ?? 'Tidak diketahui' ?>
                                            </span>
                                        </td>
                                        <td><?= esc($row['keperluan']) ?></td>
                                        <td><?= $row['waktu_mulai'] ?> - <?= $row['waktu_selesai'] ?></td>
                                        <td><?= !empty($row['keterangan']) ? esc($row['keterangan']) : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TABEL PENGEMBALIAN -->
<div class="tab-pane fade" id="pengembalian-history">
    <?php if (empty($pengembalian_history)): ?>
        <div class="alert alert-info d-flex align-items-center">
            <i class="bi bi-info-circle me-2"></i> Tidak ada riwayat pengembalian
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal Kembali</th>
                        <th>Nama Peminjam</th>
                        <th>Barang</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pengembalian_history as $row): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_kembali'])) ?></td>
                            <td><?= esc($row['nama_peminjam']) ?></td>
                            <td><?= esc($row['nama_barang']) ?><br><small class="text-muted"><?= esc($row['kategori']) ?> - <?= esc($row['lokasi']) ?></small></td>
                            <td>
                                <span class="badge text-white <?= $statusClass[$row['status']] ?? 'bg-secondary' ?>">
                                    <i class="bi <?= $statusIcon[$row['status']] ?? 'bi-question-circle' ?> me-1"></i>
                                    <?= $statusLabel[$row['status']] ?? 'Tidak diketahui' ?>
                                </span>
                            </td>
                            <td><?= esc($row['waktu_mulai']) ?> - <?= esc($row['waktu_selesai']) ?></td>
                            <td><?= !empty($row['keterangan']) ? esc($row['keterangan']) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
