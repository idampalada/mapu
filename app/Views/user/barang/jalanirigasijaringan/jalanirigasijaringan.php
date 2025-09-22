<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>Jalan, Irigasi dan Jaringan</title>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">Jalan, Irigasi dan Jaringan</h1>
    </div>

    <div class="grid">
        <?php
        $jalanIrigasiJaringanList = [
            ['id' => '501', 'label' => '5.01 JALAN DAN JEMBATAN', 'icon' => 'bi-signpost-split', 'url' => base_url('user/barang/jalanirigasijaringan/jalandanjembatan')],
            ['id' => '502', 'label' => '5.02 BANGUNAN AIR', 'icon' => 'bi-water', 'url' => base_url('user/barang/jalanirigasijaringan/bangunanair')],
            ['id' => '503', 'label' => '5.03 INSTALASI', 'icon' => 'bi-gear', 'url' => base_url('user/barang/jalanirigasijaringan/instalasi')],
            ['id' => '504', 'label' => '5.04 JARINGAN', 'icon' => 'bi-diagram-3', 'url' => base_url('user/barang/jalanirigasijaringan/jaringan')],
        ];

        foreach ($jalanIrigasiJaringanList as $aset): ?>
            <a href="<?= $aset['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($aset['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($aset['label']) ?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

<!-- Styling sama dengan asettetaplainnya.php -->
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 1200px;
        margin: auto;
        padding: 20px;
    }
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }
    .item {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 180px;
    }
    .item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        background-color: #f0f7ff;
    }
    .icon i {
        font-size: 40px;
        margin-bottom: 15px;
        color: #2c5282;
    }
    .item-title {
        font-weight: 600;
        color: #2d3748;
        font-size: 14px;
        line-height: 1.3;
    }
</style>

<?= $this->endSection() ?>