<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>3.10 Komputer</title>
<link rel="stylesheet" href="<?= base_url('assets/css/peralatan-mesin.css') ?>">

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang/peralatandanmesin') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">3.10 KOMPUTER</h1>
    </div>

    <div class="grid">
        <?php
        $komputerList = [
            ['id' => '1', 'label' => 'Komputer Unit', 'icon' => 'bi-pc-display', 'url' => base_url('user/barang/peralatandanmesin/komputer/komputerunit')],
            ['id' => '2', 'label' => 'Peralatan Komputer', 'icon' => 'bi-keyboard', 'url' => base_url('user/barang/peralatandanmesin/komputer/peralatankomputer')],
        ];

        foreach ($komputerList as $komputer): ?>
            <a href="<?= $komputer['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($komputer['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($komputer['label']) ?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

<?= $this->endSection() ?>