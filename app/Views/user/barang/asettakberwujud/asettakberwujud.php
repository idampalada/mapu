<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

 ASET TAK BERWUJUD DALAM PENYELESAIAN', 'icon' => 'bi-hourglass-split', 'url' => base_url('user/barang/asettakberwujud/kelompokasettakberwujud/ASET TAK BERWUJUD DALAM PENYELESAIAN')],
            ['id' => '103', 'label' => '1.03 ASET KEMITRAAN', 'icon' => 'bi-people-fill', 'url' => base_url('user/barang/asettakberwujud/kelompokasettakberwujud/ASET KEMITRAAN')],
        ];

        foreach ($asetTakBerwujudList as $aset): ?>
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