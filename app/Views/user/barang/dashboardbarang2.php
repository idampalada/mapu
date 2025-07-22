<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold mb-0">Kategori Aset</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kategori Aset</li>
            </ol>
        </nav>
    </div>

    <div class="asset-category-grid">
        <?php
        $asetList = [
            [
                'id' => 'tanah', 
                'label' => 'Tanah', 
                'icon' => 'bi-globe-americas', 
                'url' => base_url('user/barang/kelompoktanah'),
                'color' => '#4361ee',
                'bg' => 'linear-gradient(135deg, #e9ecfb 0%, #f3f4fd 100%)'
            ],
            [
                'id' => 'peralatandanmesin', 
                'label' => 'Peralatan dan Mesin', 
                'icon' => 'bi-tools', 
                'url' => base_url('user/barang/peralatandanmesin'),
                'color' => '#3a0ca3',
                'bg' => 'linear-gradient(135deg, #e7e2f8 0%, #f1edfb 100%)'
            ],
            [
                'id' => 'gedungdanbangunan', 
                'label' => 'Gedung dan Bangunan', 
                'icon' => 'bi-building', 
                'url' => base_url('user/barang/gedungdanbangunan'),
                'color' => '#f72585',
                'bg' => 'linear-gradient(135deg, #fde2f0 0%, #fee7f3 100%)'
            ],
            [
                'id' => 'jalanirigasijaringan', 
                'label' => 'Jalan, Irigasi dan Jaringan', 
                'icon' => 'bi-signpost-split-fill', 
                'url' => base_url('user/barang/jalanirigasijaringan'),
                'color' => '#4cc9f0',
                'bg' => 'linear-gradient(135deg, #e0f5fd 0%, #ebf9fe 100%)'
            ],
            [
                'id' => 'asettetaplainnya', 
                'label' => 'Aset Tetap Lainnya', 
                'icon' => 'bi-box-seam', 
                'url' => base_url('user/barang/asettetaplainnya'),
                'color' => '#4d908e',
                'bg' => 'linear-gradient(135deg, #e1efee 0%, #edf5f5 100%)'
            ],
            [
                'id' => 'konstruksidp', 
                'label' => 'Konstruksi Dalam Pengerjaan', 
                'icon' => 'bi-hammer', 
                'url' => base_url('user/barang/konstruksidp'),
                'color' => '#fb8500',
                'bg' => 'linear-gradient(135deg, #ffebcc 0%, #fff2e0 100%)'
            ],
            [
                'id' => 'asettakberwujud', 
                'label' => 'Aset Tak Berwujud', 
                'icon' => 'bi-cloud', 
                'url' => base_url('user/barang/asettakberwujud'),
                'color' => '#2b2d42',
                'bg' => 'linear-gradient(135deg, #d6d7de 0%, #e7e7eb 100%)'
            ],
        ];

        foreach ($asetList as $aset): ?>
            <a href="<?= $aset['url'] ?? '#' ?>" class="asset-card" style="background: <?= $aset['bg'] ?>;">
                <div class="asset-icon" style="color: <?= $aset['color'] ?>;">
                    <i class="bi <?= esc($aset['icon']) ?>"></i>
                </div>
                <h3 class="asset-title" style="color: <?= $aset['color'] ?>;"><?= esc($aset['label']) ?></h3>
                <div class="overlay" style="background: <?= $aset['color'] ?>;"></div>
            </a>
        <?php endforeach ?>
    </div>
</div>

<style>
    .asset-category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 30px;
    }
    
    .asset-card {
        position: relative;
        border-radius: 16px;
        padding: 32px 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        overflow: hidden;
        text-decoration: none;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        height: 220px;
        justify-content: center;
        z-index: 1;
    }
    
    .asset-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .asset-card:hover .overlay {
        opacity: 0.03;
    }
    
    .asset-icon {
        font-size: 56px;
        margin-bottom: 16px;
        transition: transform 0.3s ease;
    }
    
    .asset-card:hover .asset-icon {
        transform: scale(1.1);
    }
    
    .asset-title {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        transition: color 0.3s ease;
        z-index: 2;
    }
    
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 0;
    }
    
    @media (max-width: 768px) {
        .asset-category-grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
        }
        
        .asset-card {
            padding: 24px 16px;
            height: 180px;
        }
        
        .asset-icon {
            font-size: 42px;
        }
        
        .asset-title {
            font-size: 16px;
        }
    }
</style>

<?= $this->endSection() ?>