<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<title>Daftar Aset</title>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <h5 class="fw-bold mb-0">Daftar Aset</h5>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="asetTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Merk</th>
                            <th>No. Polisi</th>
                            <th>Kode Barang</th>
                            <th>No. BPKB</th>
                            <th>No. STNK</th>
                            <th>Status</th>
                            <th>Kondisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($aset as $item): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $item['merk'] ?></td>
                                <td><?= $item['no_polisi'] ?></td>
                                <td><?= $item['kode_barang'] ?></td>
                                <td><?= $item['no_bpkb'] ?></td>
                                <td><?= $item['no_stnk'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $item['status_pinjam'] === 'Tersedia' ? 'success' :
                                        ($item['status_pinjam'] === 'Dipinjam' ? 'warning' :
                                            ($item['status_pinjam'] === 'Dalam Verifikasi' ? 'info' : 'primary')) ?>">
                                        <?= $item['status_pinjam'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $item['kondisi'] === 'Baik' ? 'success' :
                                        ($item['kondisi'] === 'Rusak Ringan' ? 'warning' : 'danger') ?>">
                                        <?= $item['kondisi'] ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info text-white"
                                        onclick="showDetailAset(<?= $item['id'] ?>)">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetailAset" tabindex="-1" aria-labelledby="modalDetailAsetLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailAsetLabel">Detail Aset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detailAsetContent">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>