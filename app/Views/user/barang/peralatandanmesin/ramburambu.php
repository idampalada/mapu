<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>3.18 Rambu-Rambu</title>
<link rel="stylesheet" href="<?= base_url('assets/css/peralatan-mesin.css') ?>">

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang/peralatandanmesin') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">3.18 RAMBU-RAMBU</h1>
    </div>

    <div class="grid">
        <?php
        $rambuRambuList = [
            ['id' => '1', 'label' => 'Rambu-Rambu Lalu Lintas Darat', 'icon' => 'bi-sign-stop-fill', 'url' => base_url('user/barang/peralatandanmesin/ramburambu/rambulalulintas_darat')],
            ['id' => '2', 'label' => 'Rambu-Rambu Lalu Lintas Udara', 'icon' => 'bi-airplane-fill', 'url' => base_url('user/barang/peralatandanmesin/ramburambu/rambulalulintas_udara')],
        ];

        foreach ($rambuRambuList as $rambu): ?>
            <a href="<?= $rambu['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($rambu['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($rambu['label']) ?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

<?= $this->endSection() ?>