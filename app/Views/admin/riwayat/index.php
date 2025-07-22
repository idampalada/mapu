<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <h3 class="text-center mb-5">Riwayat Peminjaman dan Pengembalian</h3>

    <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm border-primary">
                <div class="card-body">
                    <i class="bi bi-truck display-4 text-primary mb-3"></i>
                    <h5 class="card-title">Kendaraan</h5>
                    <a href="<?= base_url('admin/riwayat/kendaraan') ?>" class="btn btn-outline-primary mt-2">Lihat Riwayat</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm border-success">
                <div class="card-body">
                    <i class="bi bi-door-open display-4 text-success mb-3"></i>
                    <h5 class="card-title">Ruangan</h5>
                    <a href="<?= base_url('admin/riwayat/ruangan') ?>" class="btn btn-outline-success mt-2">Lihat Riwayat</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm border-warning">
                <div class="card-body">
                    <i class="bi bi-box-seam display-4 text-warning mb-3"></i>
                    <h5 class="card-title">Barang</h5>
                    <a href="<?= base_url('admin/riwayat/barang') ?>" class="btn btn-outline-warning mt-2">Lihat Riwayat</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
