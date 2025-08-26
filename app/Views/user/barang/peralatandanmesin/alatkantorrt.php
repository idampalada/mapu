<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>3.05 Alat Kantor & Rumah Tangga</title>
<link rel="stylesheet" href="<?= base_url('assets/css/peralatan-mesin.css') ?>">

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang/peralatandanmesin') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">3.05 ALAT KANTOR & RUMAH TANGGA</h1>
    </div>

    <div class="grid">
        <?php
        $alatKantorRTList = [
            ['id' => '1', 'label' => 'Alat Kantor', 'icon' => 'bi-briefcase', 'url' => base_url('user/barang/peralatandanmesin/alatkantorrt/alatkantor')],
            ['id' => '2', 'label' => 'Alat Rumah Tangga', 'icon' => 'bi-house-door', 'url' => base_url('user/barang/peralatandanmesin/alatkantorrt/alatrumahTangga')],
        ];

        foreach ($alatKantorRTList as $alat): ?>
            <a href="<?= $alat['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($alat['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($alat['label']) ?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

<?= $this->endSection() ?>