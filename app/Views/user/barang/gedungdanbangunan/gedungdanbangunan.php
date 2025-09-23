<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>Gedung dan Bangunan</title>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">Gedung dan Bangunan</h1>
    </div>

    <div class="grid">
        <?php
        $gedungBangunanList = [
            ['id' => '401', 'label' => '4.01 BANGUNAN GEDUNG', 'icon' => 'bi-building', 'url' => base_url('user/barang/gedungdanbangunan/bangunangedung')],
            ['id' => '402', 'label' => '4.02 MONUMEN', 'icon' => 'bi-building-exclamation', 'url' => base_url('user/barang/gedungdanbangunan/monumen')],
            ['id' => '403', 'label' => '4.03 BANGUNAN MENARA', 'icon' => 'bi-broadcast-pin', 'url' => base_url('user/barang/gedungdanbangunan/bangunanmenara')],
            ['id' => '404', 'label' => '4.04 TUGU TITIK KONTROL/PASTI', 'icon' => 'bi-geo-alt', 'url' => base_url('user/barang/gedungdanbangunan/tugutitikkontrol')],
        ];

        foreach ($gedungBangunanList as $aset): ?>
            <a href="<?= $aset['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($aset['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($aset['label']) ?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

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
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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
        font-size: 16px;
    }
</style>

<?= $this->endSection() ?>