<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<title>Ruangan</title>

<div class="content-container">
    <div class="page-heading">
        <div class="page-title">
            <div class="row mb-4">
                <div class="col-12">
                    <h3>Pilih Gedung</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-2"> <!-- Ubah spacing dengan g-2 -->
    <?php foreach($unitOrganisasi as $unit): ?>
        <div class="col-md-3 p-2"> <!-- Tambahkan p-2 untuk padding yang lebih kecil -->
            <a href="<?= base_url('user/ruangan/' . strtolower(str_replace(' ', '', $unit['kode']))) ?>" class="text-decoration-none">
                <div class="card ruangan-unit-card p-0 border-0"> <!-- Tambahkan p-0 dan border-0 -->
                    <div class="ruangan-card-content position-relative" style="height: 180px; padding: 0; overflow: hidden;">
                        <img src="<?= base_url('uploads/unit-images/' . $unit['gambar']) ?>" 
                            class="card-img ruangan-unit-background" 
                            style="width: 100%; height: 100%; object-fit: cover;"
                            alt="<?= $unit['nama'] ?>">
                        <div class="ruangan-overlay"></div>
                        <div class="ruangan-card-body position-relative d-flex align-items-center justify-content-center">
                            <h5 class="ruangan-card-title text-white position-relative"><?= $unit['nama'] ?></h5>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>
</div>

<?= $this->endSection() ?>