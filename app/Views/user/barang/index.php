<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<title>Barang</title>

<div class="content-container py-4">
    <div class="page-heading text-center mb-5">
        <h3 class="fw-bold text-primary">Pilih Jenis Barang Komputer Unit</h3>
    </div>

    <div class="row justify-content-center gx-4 gy-4">
        <?php foreach ($kategoriBarang as $kategori): ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('user/barang/kategori/' . strtolower(str_replace(' ', '', $kategori['kode']))) ?>" class="text-decoration-none">
                    <div class="card kategori-card h-100 border-0">
                        <div class="kategori-wrapper position-relative">
                            <div class="kategori-label">
                                <?= esc($kategori['nama']) ?>
                            </div>
                            <img src="<?= base_url('uploads/barang/' . $kategori['gambar']) ?>"
                                 class="barang-image"
                                 alt="<?= esc($kategori['nama']) ?>">
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .content-container {
        background-color: #f0f4f8;
        min-height: calc(100vh - 70px);
        padding: 0 2rem;
    }
    
    .page-heading h3 {
        color: #2c3e50;
        font-size: 1.8rem;
        position: relative;
        display: inline-block;
    }
    
    .page-heading h3:after {
        content: '';
        position: absolute;
        width: 60px;
        height: 3px;
        background-color: #0057b8;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
    }

    .kategori-card {
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .kategori-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .kategori-wrapper {
        height: 200px;
        background-color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }

    .barang-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: all 0.3s ease;
    }
    
    .kategori-card:hover .barang-image {
        transform: scale(1.05);
    }

    .kategori-label {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        background-color:rgb(6, 14, 177);
        color: white;
        padding: 8px 12px;
        font-size: 0.9rem;
        font-weight: 600;
        z-index: 10;
        text-align: center;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    
    @media (max-width: 768px) {
        .content-container {
            padding: 0 1rem;
        }
        
        .col-sm-6 {
            padding: 0 10px;
        }
        
        .kategori-wrapper {
            height: 180px;
        }
    }
</style>

<?= $this->endSection() ?>