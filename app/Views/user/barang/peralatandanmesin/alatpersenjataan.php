<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>3.09 Alat Persenjataan</title>
<link rel="stylesheet" href="<?= base_url('assets/css/peralatan-mesin.css') ?>">

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang/peralatandanmesin') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">3.09 ALAT PERSENJATAAN</h1>
    </div>

    <div class="grid">
        <?php
        $alatPersenjataanList = [
            ['id' => '1', 'label' => 'Senjata Api', 'icon' => 'bi-crosshair', 'url' => base_url('user/barang/peralatandanmesin/alatpersenjataan/senjataapi')],
            ['id' => '2', 'label' => 'Persenjataan Non Senjata Api', 'icon' => 'bi-shield-exclamation', 'url' => base_url('user/barang/peralatandanmesin/alatpersenjataan/persenjataannonsenjataapi')],
            ['id' => '3', 'label' => 'Senjata Sinar', 'icon' => 'bi-lightning-charge', 'url' => base_url('user/barang/peralatandanmesin/alatpersenjataan/senjatasinar')],
            ['id' => '4', 'label' => 'Alat Khusus Kepolisian', 'icon' => 'bi-person-badge', 'url' => base_url('user/barang/peralatandanmesin/alatpersenjataan/alatkhususkepolisian')],
        ];

        foreach ($alatPersenjataanList as $alat): ?>
            <a href="<?= $alat['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($alat['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($alat['label']) ?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

<?= $this->endSection() ?>