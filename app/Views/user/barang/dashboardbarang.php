<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>Dashboard Barang</title>

<div class="container py-4">
    <h1 class="text-center fw-bold mb-4">Kategori Aset</h1>

    <div class="grid">
        <?php
        $asetList = [
            ['id' => 'tanah', 'label' => 'Tanah', 'icon' => 'bi-globe', 'url' => base_url('user/tanah/kelompoktanah')],
            ['id' => 'peralatandanmesin', 'label' => 'Peralatan dan Mesin', 'icon' => 'bi-tools', 'url' => base_url('user/barang/peralatandanmesin')],
            ['id' => 'gedungdanbangunan', 'label' => 'Gedung dan Bangunan', 'icon' => 'bi-building', 'url' => base_url('user/barang/gedungdanbangunan')],
            ['id' => 'jalanirigasijaringan', 'label' => 'Jalan, Irigasi dan Jaringan', 'icon' => 'bi-signpost-split', 'url' => base_url('user/barang/jalanirigasijaringan')],
            ['id' => 'asettetaplainnya', 'label' => 'Aset Tetap Lainnya', 'icon' => 'bi-box', 'url' => base_url('user/barang/asettetaplainnya')],
            ['id' => 'konstruksidp', 'label' => 'Konstruksi Dalam Pengerjaan', 'icon' => 'bi-hammer', 'url' => base_url('user/barang/konstruksidp')],
            ['id' => 'asettakberwujud', 'label' => 'Aset Tak Berwujud', 'icon' => 'bi-cloud', 'url' => base_url('user/barang/asettakberwujud')],
        ];

        foreach ($asetList as $aset): ?>
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
