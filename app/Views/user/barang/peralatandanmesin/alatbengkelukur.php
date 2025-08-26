<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>3.03 Alat Bengkel dan Alat Ukur</title>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang/peralatandanmesin') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">3.03 ALAT BENGKEL DAN ALAT UKUR</h1>
    </div>

    <div class="grid">
        <?php
        $alatBengkelUkurList = [
            ['id' => '1', 'label' => 'Alat Bengkel Bermesin', 'icon' => 'bi-gear-fill', 'url' => base_url('user/barang/peralatandanmesin/alatbengkelukur/bengkelbermesin')],
            ['id' => '2', 'label' => 'Alat Bengkel Tak Bermesin', 'icon' => 'bi-hammer', 'url' => base_url('user/barang/peralatandanmesin/alatbengkelukur/bengkeltakbermesin')],
            ['id' => '3', 'label' => 'Alat Ukur', 'icon' => 'bi-rulers', 'url' => base_url('user/barang/peralatandanmesin/alatbengkelukur/alatukur')],
        ];

        foreach ($alatBengkelUkurList as $alat): ?>
            <a href="<?= $alat['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($alat['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($alat['label']) ?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

<link rel="stylesheet" href="<?= base_url('assets/css/peralatan-mesin.css') ?>">
<?= $this->endSection() ?>