<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>3.02 Alat Angkutan</title>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang/peralatandanmesin') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">3.02 ALAT ANGKUTAN</h1>
    </div>

    <div class="grid">
        <?php
        $alatAngkutanList = [
            ['id' => '1', 'label' => 'Alat Angkutan Darat Bermotor', 'icon' => 'bi-truck', 'url' => base_url('user/barang/peralatandanmesin/alatangkutan/daratbermotor')],
            ['id' => '2', 'label' => 'Alat Angkutan Darat Tak Bermotor', 'icon' => 'bi-bicycle', 'url' => base_url('user/barang/peralatandanmesin/alatangkutan/darattakbermotor')],
            ['id' => '3', 'label' => 'Alat Angkutan Apung Bermotor', 'icon' => 'bi-ship', 'url' => base_url('user/barang/peralatandanmesin/alatangkutan/apungbermotor')],
            ['id' => '4', 'label' => 'Alat Angkutan Apung Tak Bermotor', 'icon' => 'bi-sailboat', 'url' => base_url('user/barang/peralatandanmesin/alatangkutan/apungtakbermotor')],
            ['id' => '5', 'label' => 'Alat Angkutan Bermotor Udara', 'icon' => 'bi-airplane', 'url' => base_url('user/barang/peralatandanmesin/alatangkutan/bermotorudara')],
        ];

        foreach ($alatAngkutanList as $alat): ?>
            <a href="<?= $alat['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($alat['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($alat['label']) ?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

<link rel="stylesheet" href="<?= base_url('assets/css/peralatan-mesin.css') ?>">

<?= $this->endSection() ?>