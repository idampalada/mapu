<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>3.15 Alat Keselamatan Kerja</title>
<link rel="stylesheet" href="<?= base_url('assets/css/peralatan-mesin.css') ?>">

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang/peralatandanmesin') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">3.15 ALAT KESELAMATAN KERJA</h1>
    </div>

    <div class="grid">
        <?php
        $alatKeselamatanKerjaList = [
            ['id' => '1', 'label' => 'Alat Deteksi', 'icon' => 'bi-radar', 'url' => base_url('user/barang/peralatandanmesin/alatkeselamatankerja/alatdeteksi')],
            ['id' => '2', 'label' => 'Alat Pelindung', 'icon' => 'bi-shield-check', 'url' => base_url('user/barang/peralatandanmesin/alatkeselamatankerja/alatpelindung')],
            ['id' => '3', 'label' => 'Alat SAR', 'icon' => 'bi-life-preserver', 'url' => base_url('user/barang/peralatandanmesin/alatkeselamatankerja/alatsar')],
            ['id' => '4', 'label' => 'Alat Kerja Penerbangan', 'icon' => 'bi-airplane-engines', 'url' => base_url('user/barang/peralatandanmesin/alatkeselamatankerja/alatkerjaPenerbangan')],
        ];

        foreach ($alatKeselamatanKerjaList as $alat): ?>
            <a href="<?= $alat['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($alat['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($alat['label']) ?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

<?= $this->endSection() ?>