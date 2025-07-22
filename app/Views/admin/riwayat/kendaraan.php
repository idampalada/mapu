<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<div class="page-heading mb-4">
    <h3 class="text-center">Riwayat Peminjaman dan Pengembalian Kendaraan</h3>
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
                    <i class="bi bi-box-arrow-in-right me-2"></i> Riwayat Peminjaman
                </button>
            </li>
            <li class="nav-item flex-fill" role="presentation">
                <button class="nav-link w-100 d-flex align-items-center justify-content-center"
                        id="pengembalian-history-tab" data-bs-toggle="tab" data-bs-target="#pengembalian-history" role="tab">
                    <i class="bi bi-box-arrow-in-left me-2"></i> Riwayat Pengembalian
                </button>
            </li>
        </ul>
        <?php
$statusClass = [
    'pending' => 'bg-warning',
    'disetujui' => 'bg-success',
    'ditolak' => 'bg-danger',
    'selesai' => 'bg-success'
];

$statusLabel = [
    'pending' => 'Menunggu',
    'disetujui' => 'Disetujui',
    'ditolak' => 'Ditolak',
    'selesai' => 'Selesai'
];

$statusIcon = [
    'pending' => 'bi-clock',
    'disetujui' => 'bi-check-circle',
    'ditolak' => 'bi-x-circle',
    'selesai' => 'bi-check-circle-fill'
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
                                    <th>Tanggal Pengajuan</th>
                                    <th>Penanggung Jawab</th>
                                    <th>Kendaraan</th>
                                    <th>Status</th>
                                    <th>Dokumen</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Urusan Kedinasan</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($peminjaman_history as $pinjam): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($pinjam['created_at'])) ?></td>
                                        <td><?= esc($pinjam['nama_penanggung_jawab']) ?><br><small class="text-muted"><?= esc($pinjam['nip_nrp']) ?></small></td>
                                        <td><?= esc($pinjam['merk']) ?><br><small class="text-muted"><?= esc($pinjam['no_polisi']) ?></small></td>
                                        <td>
                                            <span class="badge <?= $statusClass[$pinjam['status']] ?? 'bg-secondary' ?>">
                                                <i class="bi <?= $statusIcon[$pinjam['status']] ?? 'bi-question-circle' ?> me-1"></i>
                                                <?= $statusLabel[$pinjam['status']] ?? 'Tidak diketahui' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group-vertical w-100">
                                                <?php if (!empty($pinjam['surat_permohonan'])): ?>
                                                    <a href="<?= base_url('/uploads/documents/' . $pinjam['surat_permohonan']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                                        <i class="bi bi-file-earmark-pdf"></i> Surat Permohonan
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($pinjam['surat_jalan_admin']) && $pinjam['status'] === 'disetujui'): ?>
                                                    <a href="<?= base_url('/uploads/documents/' . $pinjam['surat_jalan_admin']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-file-earmark-pdf"></i> Surat Jalan
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($pinjam['tanggal_pinjam'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($pinjam['tanggal_kembali'])) ?></td>
                                        <td><?= esc($pinjam['urusan_kedinasan']) ?></td>
                                        <td><?= !empty($pinjam['keterangan']) ? esc($pinjam['keterangan']) : '<span class="text-muted">-</span>' ?></td>
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
                                    <th>Tanggal Pengajuan</th>
                                    <th>Penanggung Jawab</th>
                                    <th>Kendaraan</th>
                                    <th>Status</th>
                                    <th>Dokumen</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pengembalian_history as $kembali): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($kembali['created_at'])) ?></td>
                                        <td><?= esc($kembali['nama_penanggung_jawab']) ?><br><small class="text-muted"><?= esc($kembali['nip_nrp']) ?></small></td>
                                        <td><?= esc($kembali['merk']) ?><br><small class="text-muted"><?= esc($kembali['no_polisi']) ?></small></td>
                                        <td>
                                            <span class="badge <?= $statusClass[$kembali['status']] ?? 'bg-secondary' ?>">
                                                <i class="bi <?= $statusIcon[$kembali['status']] ?? 'bi-question-circle' ?> me-1"></i>
                                                <?= $statusLabel[$kembali['status']] ?? 'Tidak diketahui' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group-vertical w-100">
                                                <?php if (!empty($kembali['surat_pengembalian'])): ?>
                                                    <a href="<?= base_url('/uploads/documents/' . $kembali['surat_pengembalian']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                                        <i class="bi bi-file-earmark-pdf"></i> Surat Pengembalian
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($kembali['berita_acara_pengembalian'])): ?>
                                                    <a href="<?= base_url('/uploads/documents/' . $kembali['berita_acara_pengembalian']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-file-earmark-pdf"></i> Berita Acara
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($kembali['tanggal_pinjam'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($kembali['tanggal_kembali'])) ?></td>
                                        <td><?= !empty($kembali['keterangan']) ? esc($kembali['keterangan']) : '<span class="text-muted">-</span>' ?></td>
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
