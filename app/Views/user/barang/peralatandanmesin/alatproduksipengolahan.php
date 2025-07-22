<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>3.13 Alat Produksi, Pengolahan dan Pemurnian</title>
<link rel="stylesheet" href="<?= base_url('assets/css/peralatan-mesin.css') ?>">

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang/peralatandanmesin') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">3.13 ALAT PRODUKSI, PENGOLAHAN DAN PEMURNIAN</h1>
    </div>

    <div class="grid">
        <?php
        $alatProduksiPengolahanList = [
            ['id' => '1', 'label' => 'Sumur', 'icon' => 'bi-droplet-fill', 'url' => base_url('user/barang/peralatandanmesin/alatproduksipengolahan/sumur')],
            ['id' => '2', 'label' => 'Produksi', 'icon' => 'bi-factory', 'url' => base_url('user/barang/peralatandanmesin/alatproduksipengolahan/produksi')],
            ['id' => '3', 'label' => 'Pengolahan dan Pemurnian', 'icon' => 'bi-funnel', 'url' => base_url('user/barang/peralatandanmesin/alatproduksipengolahan/pengolahanpemurnian')],
        ];

        foreach ($alatProduksiPengolahanList as $alat): ?>
            <a href="<?= $alat['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($alat['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($alat['label']) ?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

<?= $this->endSection() ?>