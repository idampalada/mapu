<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<title>Riwayat Peminjaman dan Pengembalian</title>

<div class="content-container">
    <div class="page-heading">
        <div class="page-title">
            <div class="row mb-4">
                <div class="col-12">
                    <h3>Riwayat Peminjaman & Pengembalian</h3>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs d-flex justify-content-between mb-4 border-bottom-0" id="riwayatTab"
                    role="tablist">
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link active w-100 rounded-0 border-bottom-0" id="peminjaman-tab"
                            data-bs-toggle="tab" data-bs-target="#peminjaman" type="button" role="tab">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Peminjaman
                        </button>
                    </li>
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link w-100 rounded-0 border-bottom-0" id="pengembalian-tab"
                            data-bs-toggle="tab" data-bs-target="#pengembalian" type="button" role="tab">
                            <i class="bi bi-box-arrow-in-left me-2"></i>
                            Pengembalian
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="riwayatTabContent">
                    <div class="tab-pane fade show active" id="peminjaman" role="tabpanel">
                        <?php if (!empty($peminjaman)): ?>
                            <div class="table-responsive mt-4">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Kendaraan</th>
                                            <th>Tujuan</th>
                                            <th>Tanggal Pinjam</th>
                                            <th>Tanggal Kembali</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1;
                                        foreach ($peminjaman as $pinjam): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= date('d/m/Y', strtotime($pinjam['created_at'])) ?></td>
                                                <td><?= $pinjam['merk'] ?> (<?= $pinjam['no_polisi'] ?>)</td>
                                                <td><?= $pinjam['urusan_kedinasan'] ?></td>
                                                <td><?= date('d/m/Y', strtotime($pinjam['tanggal_pinjam'])) ?></td>
                                                <td><?= date('d/m/Y', strtotime($pinjam['tanggal_kembali'])) ?></td>
                                                <td>
                                                    <span class="badge <?=
                                                        $pinjam['status'] === 'pending' ? 'bg-warning' :
                                                        ($pinjam['status'] === 'disetujui' || $pinjam['status'] === 'selesai' ? 'bg-success' : 'bg-danger')
                                                        ?>">
                                                        <?= ucfirst($pinjam['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info text-white"
                                                        onclick="showDetail('peminjaman', <?= $pinjam['id'] ?>)">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="bi bi-info-circle me-2"></i>
                                Belum ada data peminjaman
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="pengembalian" role="tabpanel">
                        <?php if (!empty($pengembalian)): ?>
                            <div class="table-responsive mt-4">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Kendaraan</th>
                                            <th>Tanggal Kembali</th>
                                            <th>Dokumen</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1;
                                        foreach ($pengembalian as $kembali): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= date('d/m/Y', strtotime($kembali['created_at'])) ?></td>
                                                <td><?= $kembali['merk'] ?> (<?= $kembali['no_polisi'] ?>)</td>
                                                <td><?= $kembali['tanggal_kembali'] ?></td>
                                                <td>
                                                    <?php if (!empty($kembali['surat_pengembalian']) && file_exists(ROOTPATH . 'public/uploads/documents/' . $kembali['surat_pengembalian'])): ?>
                                                        <a href="<?= base_url('/uploads/documents/' . $kembali['surat_pengembalian']) ?>"
                                                            target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                                            <i class="bi bi-file-earmark-pdf"></i> Surat Pengembalian
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?=
                                                        $kembali['status'] === 'pending' ? 'bg-warning' :
                                                        ($kembali['status'] === 'disetujui' || $kembali['status'] === 'selesai' ? 'bg-success' : 'bg-danger')
                                                        ?>">
                                                        <?= ucfirst($kembali['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info text-white"
                                                        onclick="showDetail('pengembalian', <?= $kembali['id'] ?>)">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="bi bi-info-circle me-2"></i>
                                Belum ada data pengembalian
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Riwayat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detailContent"></div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>