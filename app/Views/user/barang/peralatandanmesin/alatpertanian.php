<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>3.04 Alat Pertanian</title>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang/peralatandanmesin') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">3.04 ALAT PERTANIAN</h1>
    </div>

    <div class="grid">
        <?php
        $alatPertanianList = [
            ['id' => '1', 'label' => 'Alat Pengolahan', 'icon' => 'bi-flower2', 'url' => base_url('user/barang/peralatandanmesin/alatpertanian/alatpengolahan')],
        ];

        foreach ($alatPertanianList as $alat): ?>
            <a href="<?= $alat['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($alat['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($alat['label']) ?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

<link rel="stylesheet" href="<?= base_url('assets/css/peralatan-mesin.css') ?>">
<?= $this->endSection() ?>