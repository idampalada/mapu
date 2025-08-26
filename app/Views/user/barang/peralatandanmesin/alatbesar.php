<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>3.01 Alat Besar</title>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang/peralatandanmesin') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">3.01 ALAT BESAR</h1>
    </div>

    <div class="grid">
        <?php
        $alatBesarList = [
            ['id' => '1', 'label' => 'Alat Besar Darat', 'icon' => 'bi-truck', 'url' => base_url('user/barang/peralatandanmesin/alatbesar/alatbesardarat')],
            ['id' => '2', 'label' => 'Alat Besar Apung', 'icon' => 'bi-water', 'url' => base_url('user/barang/peralatandanmesin/alatbesar/alatbesarapung')],
            ['id' => '3', 'label' => 'Alat Bantu', 'icon' => 'bi-tools', 'url' => base_url('user/barang/peralatandanmesin/alatbesar/alatbantu')],
        ];

        foreach ($alatBesarList as $alat): ?>
            <a href="<?= $alat['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($alat['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($alat['label']) ?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

<link rel="stylesheet" href="<?= base_url('assets/css/peralatan-mesin.css') ?>">

<?= $this->endSection() ?>