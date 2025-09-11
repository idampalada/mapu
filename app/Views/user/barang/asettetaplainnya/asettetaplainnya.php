<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>Aset Tetap Lainnya</title>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">Aset Tetap Lainnya</h1>
    </div>

    <div class="grid">
        <?php
        $asetTetapLainnyaList = [
            ['id' => '601', 'label' => '6.01 BAHAN PERPUSTAKAAN', 'icon' => 'bi-book', 'url' => base_url('user/barang/asettetaplainnya/bahanperpustakaan')],
            ['id' => '602', 'label' => '6.02 BARANG BERCORAK KESENIAN/KEBUDAYAAN/OLAHRAGA', 'icon' => 'bi-palette', 'url' => base_url('user/barang/asettetaplainnya/barangbercorak')],
            ['id' => '603', 'label' => '6.03 HEWAN', 'icon' => 'bi-heart', 'url' => base_url('user/barang/asettetaplainnya/hewan')],
            ['id' => '604', 'label' => '6.04 IKAN', 'icon' => 'bi-water', 'url' => base_url('user/barang/asettetaplainnya/ikan')],
            ['id' => '605', 'label' => '6.05 TANAMAN', 'icon' => 'bi-flower1', 'url' => base_url('user/barang/asettetaplainnya/tanaman')],
            ['id' => '606', 'label' => '6.06 BARANG KOLEKSI NON BUDAYA', 'icon' => 'bi-collection', 'url' => base_url('user/barang/asettetaplainnya/barangkoleksinonbudaya')],
            ['id' => '607', 'label' => '6.07 ASET TETAP DALAM RENOVASI', 'icon' => 'bi-tools', 'url' => base_url('user/barang/asettetaplainnya/asettetapdalamrenovasi')],
        ];

        foreach ($asetTetapLainnyaList as $aset): ?>
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