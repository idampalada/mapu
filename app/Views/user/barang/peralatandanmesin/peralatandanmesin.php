<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>Peralatan dan Mesin</title>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('user/barang') ?>" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1 class="fw-bold mb-0">Peralatan dan Mesin</h1>
    </div>

    <div class="grid">
        <?php
        $peralatanMesinList = [
            ['id' => '301', 'label' => '3.01 ALAT BESAR', 'icon' => 'bi-truck', 'url' => base_url('user/barang/peralatandanmesin/alatbesar')],
            ['id' => '302', 'label' => '3.02 ALAT ANGKUTAN', 'icon' => 'bi-bus-front', 'url' => base_url('user/barang/peralatandanmesin/alatangkutan')],
            ['id' => '303', 'label' => '3.03 ALAT BENGKEL DAN ALAT UKUR', 'icon' => 'bi-wrench', 'url' => base_url('user/barang/peralatandanmesin/alatbengkelukur')],
            ['id' => '304', 'label' => '3.04 ALAT PERTANIAN', 'icon' => 'bi-droplet', 'url' => base_url('user/barang/peralatandanmesin/alatpertanian')],
            ['id' => '305', 'label' => '3.05 ALAT KANTOR & RUMAH TANGGA', 'icon' => 'bi-house', 'url' => base_url('user/barang/peralatandanmesin/alatkantorrt')],
            ['id' => '306', 'label' => '3.06 ALAT STUDIO, KOMUNIKASI DAN PEMANCAR', 'icon' => 'bi-broadcast', 'url' => base_url('user/barang/peralatandanmesin/alatstudiokomunikasi')],
            ['id' => '307', 'label' => '3.07 ALAT KEDOKTERAN DAN KESEHATAN', 'icon' => 'bi-heart-pulse', 'url' => base_url('user/barang/peralatandanmesin/alatkedokterankesehatan')],
            ['id' => '308', 'label' => '3.08 ALAT LABORATORIUM', 'icon' => 'bi-clipboard2-pulse', 'url' => base_url('user/barang/peralatandanmesin/alatlaboratorium')],
            ['id' => '309', 'label' => '3.09 ALAT PERSENJATAAN', 'icon' => 'bi-shield-check', 'url' => base_url('user/barang/peralatandanmesin/alatpersenjataan')],
            ['id' => '310', 'label' => '3.10 KOMPUTER', 'icon' => 'bi-laptop', 'url' => base_url('user/barang/peralatandanmesin/komputer')],
            ['id' => '311', 'label' => '3.11 ALAT EKSPLORASI', 'icon' => 'bi-search', 'url' => base_url('user/barang/peralatandanmesin/alateksplorasi')],
            ['id' => '312', 'label' => '3.12 ALAT PENGEBORAN', 'icon' => 'bi-cone-striped', 'url' => base_url('user/barang/peralatandanmesin/alatpengeboran')],
            ['id' => '313', 'label' => '3.13 ALAT PRODUKSI, PENGOLAHAN DAN PEMURNIAN', 'icon' => 'bi-gear-wide-connected', 'url' => base_url('user/barang/peralatandanmesin/alatproduksipengolahan')],
            ['id' => '314', 'label' => '3.14 ALAT BANTU EKSPLORASI', 'icon' => 'bi-compass', 'url' => base_url('user/barang/peralatandanmesin/alatbantueksplorasi')],
            ['id' => '315', 'label' => '3.15 ALAT KESELAMATAN KERJA', 'icon' => 'bi-shield-plus', 'url' => base_url('user/barang/peralatandanmesin/alatkeselamatankerja')],
            ['id' => '316', 'label' => '3.16 ALAT PERAGA', 'icon' => 'bi-easel', 'url' => base_url('user/barang/peralatandanmesin/alatperaga')],
            ['id' => '317', 'label' => '3.17 PERALATAN PROFESI/PRODUKSI', 'icon' => 'bi-tools', 'url' => base_url('user/barang/peralatandanmesin/peralatanprofesiproduksi')],
            ['id' => '318', 'label' => '3.18 RAMBU-RAMBU', 'icon' => 'bi-sign-stop', 'url' => base_url('user/barang/peralatandanmesin/ramburambu')],
            ['id' => '319', 'label' => '3.19 PERALATAN OLAHRAGA', 'icon' => 'bi-trophy', 'url' => base_url('user/barang/peralatandanmesin/peralatanolahraga')],
        ];

        foreach ($peralatanMesinList as $peralatan): ?>
            <a href="<?= $peralatan['url'] ?? '#' ?>" class="item text-decoration-none">
                <div class="icon"><i class="bi <?= esc($peralatan['icon']) ?>"></i></div>
                <div class="item-title"><?= esc($peralatan['label']) ?></div>
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