<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>3.08 Alat Laboratorium</title>
<link rel="stylesheet" href="<?= base_url('assets/css/peralatan-mesin.css') ?>">

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang/peralatandanmesin') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">3.08 ALAT LABORATORIUM</h1>
    </div>

    <div class="grid">
        <?php
        $alatLaboratoriumList = [
            ['id' => '1', 'label' => 'Unit Alat Laboratorium', 'icon' => 'bi-flask', 'url' => base_url('user/barang/peralatandanmesin/alatlaboratorium/unitalatlaboratorium')],
            ['id' => '2', 'label' => 'Unit Alat Laboratorium Kimia Pelajar', 'icon' => 'bi-eyedropper', 'url' => base_url('user/barang/peralatandanmesin/alatlaboratorium/unitalatlabkimiapelajar')],
            ['id' => '3', 'label' => 'Alat Laboratorium Fisika Nuklir/Elektronika', 'icon' => 'bi-radioactive', 'url' => base_url('user/barang/peralatandanmesin/alatlaboratorium/alatlabfisikanuklir')],
            ['id' => '4', 'label' => 'Alat Proteksi Radiasi/Proteksi Lingkungan', 'icon' => 'bi-shield-fill-check', 'url' => base_url('user/barang/peralatandanmesin/alatlaboratorium/alatproteksiRadiasi')],
            ['id' => '5', 'label' => 'Radiation Application & Non Destructive Testing Laboratory', 'icon' => 'bi-lightning', 'url' => base_url('user/barang/peralatandanmesin/alatlaboratorium/radiationApplication')],
            ['id' => '6', 'label' => 'Alat Laboratorium Lingkungan Hidup', 'icon' => 'bi-tree', 'url' => base_url('user/barang/peralatandanmesin/alatlaboratorium/alatlablingkunganhidup')],
            ['id' => '7', 'label' => 'Peralatan Laboratorium Hydrodinamica', 'icon' => 'bi-water', 'url' => base_url('user/barang/peralatandanmesin/alatlaboratorium/peralatanlabhydrodinamica')],
            ['id' => '8', 'label' => 'Alat Laboratorium Standarisasi Kalibrasi & Instrumentasi', 'icon' => 'bi-speedometer2', 'url' => base_url('user/barang/peralatandanmesin/alatlaboratorium/alatlabstandarisasikalibrasi')],
        ];

        foreach ($alatLaboratoriumList as $alat): ?>
            <a href="<?= $alat['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($alat['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($alat['label']) ?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

<?= $this->endSection() ?>