<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>3.06 Alat Studio, Komunikasi dan Pemancar</title>
<link rel="stylesheet" href="<?= base_url('assets/css/peralatan-mesin.css') ?>">

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang/peralatandanmesin') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">3.06 ALAT STUDIO, KOMUNIKASI DAN PEMANCAR</h1>
    </div>

    <div class="grid">
        <?php
        $alatStudioKomunikasiList = [
            ['id' => '1', 'label' => 'Alat Studio', 'icon' => 'bi-camera-video', 'url' => base_url('user/barang/peralatandanmesin/alatstudiokomunikasi/alatstudio')],
            ['id' => '2', 'label' => 'Alat Komunikasi', 'icon' => 'bi-telephone', 'url' => base_url('user/barang/peralatandanmesin/alatstudiokomunikasi/alatkomunikasi')],
            ['id' => '3', 'label' => 'Peralatan Pemancar', 'icon' => 'bi-broadcast-pin', 'url' => base_url('user/barang/peralatandanmesin/alatstudiokomunikasi/peralatanpemancar')],
            ['id' => '4', 'label' => 'Peralatan Komunikasi Navigasi', 'icon' => 'bi-geo-alt', 'url' => base_url('user/barang/peralatandanmesin/alatstudiokomunikasi/peralatankomunikasiNavigasi')],
        ];

        foreach ($alatStudioKomunikasiList as $alat): ?>
            <a href="<?= $alat['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($alat['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($alat['label']) ?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

<?= $this->endSection() ?>