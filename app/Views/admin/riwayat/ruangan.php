<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<div class="page-heading mb-4">
    <h3 class="text-center">Riwayat Peminjaman dan Pengembalian Ruangan</h3>
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
                        id="peminjaman-tab" data-bs-toggle="tab" data-bs-target="#peminjaman" role="tab">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Peminjaman
                </button>
            </li>
            <li class="nav-item flex-fill" role="presentation">
                <button class="nav-link w-100 d-flex align-items-center justify-content-center"
                        id="pengembalian-tab" data-bs-toggle="tab" data-bs-target="#pengembalian" role="tab">
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
            <!-- PEMINJAMAN -->
            <div class="tab-pane fade show active" id="peminjaman">
                <?php if (empty($peminjaman_history)): ?>
                    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i> Tidak ada data peminjaman.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Peminjam</th>
                                    <th>Ruangan</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($peminjaman_history as $row): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                        <td><?= esc($row['nama_penanggung_jawab']) ?><br><small class="text-muted"><?= esc($row['unit_organisasi']) ?></small></td>
<td><?= esc($row['nama_ruangan']) ?><br><small class="text-muted"><?= esc($row['lokasi']) ?></small></td>

                                        <td>
                                            <span class="badge text-white <?= $statusClass[$row['status']] ?? 'bg-secondary' ?>">
                                                <i class="bi <?= $statusIcon[$row['status']] ?? 'bi-question-circle' ?> me-1"></i>
                                                <?= $statusLabel[$row['status']] ?? 'Tidak diketahui' ?>
                                            </span>
                                        </td>
                                        <td><?= esc($row['waktu_mulai']) ?> - <?= esc($row['waktu_selesai']) ?></td>
                                        <td><?= !empty($row['keterangan']) ? esc($row['keterangan']) : '<span class="text-muted">-</span>' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- PENGEMBALIAN -->
            <div class="tab-pane fade" id="pengembalian">
                <?php if (empty($pengembalian_history)): ?>
                    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i> Tidak ada data pengembalian.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal Pengembalian</th>
                                    <th>Nama Peminjam</th>
                                    <th>Ruangan</th>
                                    <th>Status</th>
                                    <th>Dokumen</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pengembalian_history as $row): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                        <td><?= esc($row['nama_penanggung_jawab']) ?><br><small class="text-muted"><?= esc($row['unit_organisasi']) ?></small></td>
<td><?= esc($row['nama_ruangan']) ?><br><small class="text-muted"><?= esc($row['lokasi']) ?></small></td>
                                        <td>
                                            <span class="badge text-white <?= $statusClass[$row['status']] ?? 'bg-secondary' ?>">
                                                <i class="bi <?= $statusIcon[$row['status']] ?? 'bi-question-circle' ?> me-1"></i>
                                                <?= $statusLabel[$row['status']] ?? 'Tidak diketahui' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['surat_pengembalian'])): ?>
                                                <a href="<?= base_url('/uploads/documents/' . $row['surat_pengembalian']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="bi bi-file-earmark-pdf"></i> Dokumen
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= !empty($row['keterangan']) ? esc($row['keterangan']) : '<span class="text-muted">-</span>' ?></td>
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

<?= $this->endSection() ?>
