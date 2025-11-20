<?= $this->extend('admin/layouts/app') ?>
<?php
use App\Models\PinjamRuanganModel;
?>

<?= $this->section('content') ?>

<div class="content-container">
    <div class="page-heading">
        <div class="page-title">
            <div class="row mb-4">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Gedung <?= $lokasi ?></h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url('user/ruangan') ?>">Ruangan</a></li>
                            <li class="breadcrumb-item active"><?= $lokasi ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Sistem Peminjaman Ruangan - <?= $lokasi ?></h5>
            <p class="mb-0 text-muted">Pilih jenis peminjaman yang diinginkan</p>
        </div>
        <div class="card-body">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" id="peminjamanTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="booking-tab" data-bs-toggle="tab" data-bs-target="#booking" 
                            type="button" role="tab" aria-controls="booking" aria-selected="true">
                        <i class="bi bi-calendar-plus me-2"></i>Booking Ruangan
                        <span class="badge bg-success ms-2">Langsung</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="confirm-tab" data-bs-toggle="tab" data-bs-target="#confirm" 
                            type="button" role="tab" aria-controls="confirm" aria-selected="false">
                        <i class="bi bi-check-circle me-2"></i>Confirm Ruangan
                        <span class="badge bg-warning ms-2">Perlu Approval</span>
                    </button>
                </li>
                
                <!-- Tab Pengaturan Ruangan - Hanya untuk Admin -->
                <?php if (in_groups('admin_gedungutama') || 
                    in_groups('admin_pusdatin') || 
                    in_groups('admin_binamarga') || 
                    in_groups('admin_ciptakarya') || 
                    in_groups('admin_sda') || 
                    in_groups('admin_gedungg') ||
                    in_groups('admin_heritage') ||
                    in_groups('admin') ||
                    in_groups('admin_auditorium')): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pengaturan-tab" data-bs-toggle="tab" data-bs-target="#pengaturan" 
                                type="button" role="tab" aria-controls="pengaturan" aria-selected="false">
                            <i class="bi bi-gear me-2"></i>Pengaturan Ruangan
                            <span class="badge bg-info ms-2">Admin Only</span>
                        </button>
                    </li>
                <?php endif; ?>
            </ul>
            
            <!-- Tabs Content -->
            <div class="tab-content mt-3" id="peminjamanTabsContent">
                <!-- Tab Booking -->
                <div class="tab-pane fade show active" id="booking" role="tabpanel" aria-labelledby="booking-tab">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Booking Ruangan:</strong> User dapat melakukan booking ruangan secara bebas tanpa perlu persetujuan admin. 
                        Booking akan langsung aktif setelah disubmit.
                    </div>
                    
                    <!-- Filter untuk Booking -->
                    <div class="filter-section mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Filter Ruangan - Booking</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Cari Nama Ruangan</label>
                                        <input type="text" class="form-control" id="filterNamaBooking" placeholder="Cari ruangan...">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Filter Kapasitas</label>
                                        <select class="form-select" id="filterKapasitasBooking">
                                            <option value="">Semua Kapasitas</option>
                                            <option value="1-10">1-10 Orang</option>
                                            <option value="11-30">11-30 Orang</option>
                                            <option value="31-50">31-50 Orang</option>
                                            <option value="50+">>50 Orang</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Filter Status</label>
                                        <select class="form-select" id="filterStatusBooking">
                                            <option value="">Semua Status</option>
                                            <option value="Tersedia">Tersedia</option>
                                            <option value="Dibooking">Sedang Dipinjam</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Filter Fasilitas</label>
                                        <select class="form-select" id="filterFasilitasBooking">
                                            <option value="">Semua Fasilitas</option>
                                            <option value="Projector">Projektor</option>
                                            <option value="Sound System">Sound System</option>
                                            <option value="AC">AC</option>
                                            <option value="Wifi">WiFi</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary" onclick="resetFilterBooking()">
                                            <i class="bi bi-arrow-clockwise"></i> Reset Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Daftar Ruangan untuk Booking -->
                    <div class="card-grid" id="ruanganGridBooking">
                        <?php if (!empty($ruangans)): ?>
                            <?php foreach ($ruangans as $ruangan): ?>
                                <div class="card h-300 room-card booking-room-card" 
                                    data-nama="<?= htmlspecialchars(strtolower($ruangan['nama_ruangan'])) ?>"
                                    data-kapasitas="<?= $ruangan['kapasitas'] ?>"
                                    data-status="<?= htmlspecialchars($ruangan['status']) ?>"
                                    data-fasilitas="<?= htmlspecialchars(strtolower($ruangan['fasilitas'])) ?>">

                                    <div class="position-relative" style="height: 13rem;">
                                        <?php 
                                        $fotos = json_decode($ruangan['foto_ruangan'], true) ?? [];
                                        if (!empty($fotos)):
                                            $mainFoto = $fotos[0];
                                        ?>
                                            <img src="<?= base_url('uploads/ruangan/' . $mainFoto) ?>" 
                                                class="w-100 h-100 object-fit-cover image-preview-trigger"
                                                style="cursor: pointer; border-top-left-radius: .7rem; border-top-right-radius: .7rem;"
                                                data-ruangan='<?= htmlspecialchars(json_encode($ruangan)) ?>'
                                                data-fotos='<?= htmlspecialchars(json_encode($fotos)) ?>'
                                                alt="<?= $ruangan['nama_ruangan'] ?>">
                                        <?php else: ?>
                                            <img src="<?= base_url('assets/images/no-image.jpg') ?>" 
                                                class="w-100 h-100 object-fit-cover"
                                                style="border-top-left-radius: .7rem; border-top-right-radius: .7rem;"
                                                alt="No Image">
                                        <?php endif; ?>
                                        
                                        <div class="position-absolute top-0 end-0 p-2">
                                            <span class="badge <?= $ruangan['status'] === 'Tersedia' ? 'bg-success' : 
                                                ($ruangan['status'] === 'Dibooking' ? 'bg-warning' : 'bg-info') ?>">
                                                <?= $ruangan['status'] ?>
                                            </span>
                                        </div>
                                        
                                        <!-- Badge Booking Type -->
                                        <div class="position-absolute top-0 start-0 p-2">
                                            <span class="badge bg-success booking-type-badge">
                                                <i class="bi bi-lightning-fill"></i> BOOKING
                                            </span>
                                        </div>
                                    </div>

                                    <?php 
                                    $isRuanganActive = ($ruangan['is_active'] === true || $ruangan['is_active'] === 't' || $ruangan['is_active'] === '1' || $ruangan['is_active'] === 1);
                                    $cleanRuanganName = htmlspecialchars($ruangan['nama_ruangan'], ENT_QUOTES);
                                    $cleanFasilitas = htmlspecialchars($ruangan['fasilitas'] ?? '', ENT_QUOTES);
                                    ?>

                                    <div class="card-body">
                                        <h5 class="card-title fw-bold"><?= $ruangan['nama_ruangan'] ?></h5>
                                        <div class="mb-3">
                                            <p class="mb-1">
                                                <small class="text-muted">Kapasitas:</small>
                                                <?= $ruangan['kapasitas'] ?> orang
                                            </p>
                                            <?php if (!empty($ruangan['luas_ruangan'])): ?>
                                                <p class="mb-1">
                                                    <small class="text-muted">Luas Ruangan:</small>
                                                    <?= $ruangan['luas_ruangan'] ?> m²
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($ruangan['fasilitas'])): ?>
                                                <p class="mb-1">
                                                    <small class="text-muted">Fasilitas:</small>
                                                    <?= htmlspecialchars($ruangan['fasilitas']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="card-footer bg-white border-0">
                                        <div class="d-grid">
                                            <?php if ($isRuanganActive): ?>
                                                <button class="btn btn-success btn-sm rounded-pill shadow-sm hover-effect d-flex align-items-center justify-content-center gap-2 btn-booking-ruangan"
                                                        style="height: 2.2rem; background: linear-gradient(135deg, #28a745, #20c997); border: none;" 
                                                        data-ruangan-id="<?= $ruangan['id'] ?>"
                                                        data-ruangan-nama="<?= $cleanRuanganName ?>"
                                                        data-ruangan-kapasitas="<?= $ruangan['kapasitas'] ?>"
                                                        data-ruangan-fasilitas="<?= $cleanFasilitas ?>"
                                                        data-booking-type="booking">
                                                    <i class="bi bi-lightning-fill"></i>
                                                    <span>Booking Sekarang</span>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm rounded-pill d-flex align-items-center justify-content-center gap-2"
                                                        style="height: 2.2rem; cursor: not-allowed;" disabled>
                                                    <i class="bi bi-tools"></i>
                                                    <span>Maintenance</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    Tidak ada ruangan yang tersedia untuk booking di lokasi ini.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Tab Confirm -->
                <div class="tab-pane fade" id="confirm" role="tabpanel" aria-labelledby="confirm-tab">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Confirm Ruangan:</strong> Peminjaman ruangan melalui proses ini memerlukan persetujuan admin. 
                        Status akan menjadi "Pending" sampai admin melakukan verifikasi.
                    </div>
                    
                    <!-- Filter untuk Confirm -->
                    <div class="filter-section mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Filter Ruangan - Confirm</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Cari Nama Ruangan</label>
                                        <input type="text" class="form-control" id="filterNamaConfirm" placeholder="Cari ruangan...">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Filter Kapasitas</label>
                                        <select class="form-select" id="filterKapasitasConfirm">
                                            <option value="">Semua Kapasitas</option>
                                            <option value="1-10">1-10 Orang</option>
                                            <option value="11-30">11-30 Orang</option>
                                            <option value="31-50">31-50 Orang</option>
                                            <option value="50+">>50 Orang</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Filter Status</label>
                                        <select class="form-select" id="filterStatusConfirm">
                                            <option value="">Semua Status</option>
                                            <option value="Tersedia">Tersedia</option>
                                            <option value="Dibooking">Sedang Dipinjam</option>
                                            <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Filter Fasilitas</label>
                                        <select class="form-select" id="filterFasilitasConfirm">
                                            <option value="">Semua Fasilitas</option>
                                            <option value="Projector">Projektor</option>
                                            <option value="Sound System">Sound System</option>
                                            <option value="AC">AC</option>
                                            <option value="Wifi">WiFi</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary" onclick="resetFilterConfirm()">
                                            <i class="bi bi-arrow-clockwise"></i> Reset Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Daftar Ruangan untuk Confirm - DUPLIKASI DARI BOOKING DENGAN BUTTON BERBEDA -->
                    <div class="card-grid" id="ruanganGridConfirm">
                        <?php if (!empty($ruangans)): ?>
                            <?php foreach ($ruangans as $ruangan): ?>
                                <div class="card h-300 room-card confirm-room-card" 
                                    data-nama="<?= htmlspecialchars(strtolower($ruangan['nama_ruangan'])) ?>"
                                    data-kapasitas="<?= $ruangan['kapasitas'] ?>"
                                    data-status="<?= htmlspecialchars($ruangan['status']) ?>"
                                    data-fasilitas="<?= htmlspecialchars(strtolower($ruangan['fasilitas'])) ?>">

                                    <div class="position-relative" style="height: 13rem;">
                                        <?php 
                                        $fotos = json_decode($ruangan['foto_ruangan'], true) ?? [];
                                        if (!empty($fotos)):
                                            $mainFoto = $fotos[0];
                                        ?>
                                            <img src="<?= base_url('uploads/ruangan/' . $mainFoto) ?>" 
                                                class="w-100 h-100 object-fit-cover image-preview-trigger"
                                                style="cursor: pointer; border-top-left-radius: .7rem; border-top-right-radius: .7rem;"
                                                data-ruangan='<?= htmlspecialchars(json_encode($ruangan)) ?>'
                                                data-fotos='<?= htmlspecialchars(json_encode($fotos)) ?>'
                                                alt="<?= $ruangan['nama_ruangan'] ?>">
                                        <?php else: ?>
                                            <img src="<?= base_url('assets/images/no-image.jpg') ?>" 
                                                class="w-100 h-100 object-fit-cover"
                                                style="border-top-left-radius: .7rem; border-top-right-radius: .7rem;"
                                                alt="No Image">
                                        <?php endif; ?>
                                        
                                        <div class="position-absolute top-0 end-0 p-2">
                                            <span class="badge <?= $ruangan['status'] === 'Tersedia' ? 'bg-success' : 
                                                ($ruangan['status'] === 'Dibooking' ? 'bg-warning' : 'bg-info') ?>">
                                                <?= $ruangan['status'] ?>
                                            </span>
                                        </div>
                                        
                                        <!-- Badge Confirm Type -->
                                        <div class="position-absolute top-0 start-0 p-2">
                                            <span class="badge bg-warning confirm-type-badge text-dark">
                                                <i class="bi bi-check-circle"></i> CONFIRM
                                            </span>
                                        </div>
                                    </div>

                                    <?php 
                                    $isRuanganActive = ($ruangan['is_active'] === true || $ruangan['is_active'] === 't' || $ruangan['is_active'] === '1' || $ruangan['is_active'] === 1);
                                    $cleanRuanganName = htmlspecialchars($ruangan['nama_ruangan'], ENT_QUOTES);
                                    $cleanFasilitas = htmlspecialchars($ruangan['fasilitas'] ?? '', ENT_QUOTES);
                                    ?>

                                    <div class="card-body">
                                        <h5 class="card-title fw-bold"><?= $ruangan['nama_ruangan'] ?></h5>
                                        <div class="mb-3">
                                            <p class="mb-1">
                                                <small class="text-muted">Kapasitas:</small>
                                                <?= $ruangan['kapasitas'] ?> orang
                                            </p>
                                            <?php if (!empty($ruangan['luas_ruangan'])): ?>
                                                <p class="mb-1">
                                                    <small class="text-muted">Luas Ruangan:</small>
                                                    <?= $ruangan['luas_ruangan'] ?> m²
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($ruangan['fasilitas'])): ?>
                                                <p class="mb-1">
                                                    <small class="text-muted">Fasilitas:</small>
                                                    <?= htmlspecialchars($ruangan['fasilitas']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="card-footer bg-white border-0">
                                        <div class="d-grid">
                                            <?php if ($isRuanganActive): ?>
                                                <button class="btn btn-warning btn-sm rounded-pill shadow-sm hover-effect d-flex align-items-center justify-content-center gap-2 btn-pinjam-ruangan"
                                                        style="height: 2.2rem; background: linear-gradient(135deg, #ffc107, #fd7e14); border: none; color: #000;" 
                                                        data-ruangan-id="<?= $ruangan['id'] ?>"
                                                        data-ruangan-nama="<?= $cleanRuanganName ?>"
                                                        data-ruangan-kapasitas="<?= $ruangan['kapasitas'] ?>"
                                                        data-ruangan-fasilitas="<?= $cleanFasilitas ?>"
                                                        data-booking-type="confirm">
                                                    <i class="bi bi-check-circle"></i>
                                                    <span>Request Confirm</span>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm rounded-pill d-flex align-items-center justify-content-center gap-2"
                                                        style="height: 2.2rem; cursor: not-allowed;" disabled>
                                                    <i class="bi bi-tools"></i>
                                                    <span>Maintenance</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-warning text-center">
                                    Tidak ada ruangan yang tersedia untuk confirm di lokasi ini.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Tab Pengaturan Ruangan - STRUKTUR ASLI LENGKAP -->
                <?php if (in_groups('admin_gedungutama') || 
                    in_groups('admin_pusdatin') || 
                    in_groups('admin_binamarga') || 
                    in_groups('admin_ciptakarya') || 
                    in_groups('admin_sda') || 
                    in_groups('admin_gedungg') ||
                    in_groups('admin_heritage') ||
                    in_groups('admin') ||
                    in_groups('admin_auditorium')): ?>
                    <div class="tab-pane fade" id="pengaturan" role="tabpanel" aria-labelledby="pengaturan-tab">
                        <div class="alert alert-info">
                            <i class="bi bi-shield-check me-2"></i>
                            <strong>Pengaturan Ruangan:</strong> Section khusus untuk admin untuk mengelola data ruangan. 
                            Anda dapat mengedit atau menghapus ruangan dari section ini.
                        </div>
                        
                        <!-- Filter untuk Pengaturan -->
                        <div class="filter-section mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Filter Ruangan - Pengaturan Admin</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Cari Nama Ruangan</label>
                                            <input type="text" class="form-control" id="filterNamaPengaturan" placeholder="Cari ruangan...">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Filter Kapasitas</label>
                                            <select class="form-select" id="filterKapasitasPengaturan">
                                                <option value="">Semua Kapasitas</option>
                                                <option value="1-10">1-10 Orang</option>
                                                <option value="11-30">11-30 Orang</option>
                                                <option value="31-50">31-50 Orang</option>
                                                <option value="50+">>50 Orang</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Filter Status</label>
                                            <select class="form-select" id="filterStatusPengaturan">
                                                <option value="">Semua Status</option>
                                                <option value="active">Aktif</option>
                                                <option value="maintenance">Maintenance</option>
                                                <option value="Tersedia">Tersedia</option>
                                                <option value="Dibooking">Sedang Dipinjam</option>
                                                <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Filter Fasilitas</label>
                                            <select class="form-select" id="filterFasilitasPengaturan">
                                                <option value="">Semua Fasilitas</option>
                                                <option value="Projector">Projektor</option>
                                                <option value="Sound System">Sound System</option>
                                                <option value="AC">AC</option>
                                                <option value="Wifi">WiFi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button class="btn btn-primary" onclick="resetFilterPengaturan()">
                                                <i class="bi bi-arrow-clockwise"></i> Reset Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Daftar Ruangan untuk Pengaturan - MENGGUNAKAN STRUKTUR ASLI LENGKAP -->
                        <div class="card-grid" id="ruanganGridPengaturan">
                            <?php if (!empty($ruangans)): ?>
                                <?php foreach ($ruangans as $ruangan): ?>
                                    <div class="card h-300 room-card admin-room-card" 
                                        data-nama="<?= htmlspecialchars(strtolower($ruangan['nama_ruangan'])) ?>"
                                        data-kapasitas="<?= $ruangan['kapasitas'] ?>"
                                        data-status="<?= htmlspecialchars($ruangan['status']) ?>"
                                        data-fasilitas="<?= htmlspecialchars(strtolower($ruangan['fasilitas'])) ?>">

                                        <div class="position-relative" style="height: 13rem;">
                                            <?php 
                                            $fotos = json_decode($ruangan['foto_ruangan'], true) ?? [];
                                            if (!empty($fotos)):
                                                $mainFoto = $fotos[0];
                                            ?>
                                                <img src="<?= base_url('uploads/ruangan/' . $mainFoto) ?>" 
                                                    class="w-100 h-100 object-fit-cover image-preview-trigger"
                                                    style="cursor: pointer; border-top-left-radius: .7rem; border-top-right-radius: .7rem;"
                                                    data-ruangan='<?= htmlspecialchars(json_encode($ruangan)) ?>'
                                                    data-fotos='<?= htmlspecialchars(json_encode($fotos)) ?>'
                                                    alt="<?= $ruangan['nama_ruangan'] ?>">
                                            <?php else: ?>
                                                <img src="<?= base_url('assets/images/no-image.jpg') ?>" 
                                                    class="w-100 h-100 object-fit-cover"
                                                    style="border-top-left-radius: .7rem; border-top-right-radius: .7rem;"
                                                    alt="No Image">
                                            <?php endif; ?>
                                            
                                            <div class="position-absolute top-0 end-0 p-2">
                                                <span class="badge <?= $ruangan['status'] === 'Tersedia' ? 'bg-success' : 
                                                    ($ruangan['status'] === 'Dibooking' ? 'bg-warning' : 'bg-info') ?>">
                                                    <?= $ruangan['status'] ?>
                                                </span>
                                            </div>
                                        </div>

                                        <?php 
                                        $isAvailable = $ruangan['status'] === 'Tersedia' && !isset($ruangan['peminjam_id']);
                                        $isPending = $ruangan['status'] === 'Menunggu Verifikasi';
                                        $isCurrentUserBorrowing = isset($ruangan['peminjam_id']) && $ruangan['peminjam_id'] == user_id();
                                        
                                        // Sanitasi data untuk JavaScript - PERSIS SEPERTI ASLI
                                        $cleanRuanganName = htmlspecialchars($ruangan['nama_ruangan'], ENT_QUOTES);
                                        $cleanFasilitas = htmlspecialchars($ruangan['fasilitas'] ?? '', ENT_QUOTES);
                                        $cleanFasilitas = str_replace(["\r\n", "\n", "\r"], ' ', $cleanFasilitas);
                                        $cleanFasilitas = str_replace(["'", '"'], ["\\'", '\\"'], $cleanFasilitas);
                                        
                                        // Cek status aktif untuk PostgreSQL (support 't', 'f', true, false) - PERSIS SEPERTI ASLI
                                        $isRuanganActive = ($ruangan['is_active'] === true || $ruangan['is_active'] === 't' || $ruangan['is_active'] === '1' || $ruangan['is_active'] === 1);
                                        ?>

                                        <?php if ($isRuanganActive): ?>
                                            <!-- KONTEN BODY PERSIS SEPERTI ASLI DENGAN SEMUA LOGIKA WAKTU -->
                                            <div class="card-body">
                                                <h5 class="card-title fw-bold"><?= $ruangan['nama_ruangan'] ?></h5>
                                                <div class="mb-3">
                                                    <p class="mb-1">
                                                        <small class="text-muted">Kapasitas:</small>
                                                        <?= $ruangan['kapasitas'] ?> orang
                                                    </p>
                                                    <?php if (!empty($ruangan['luas_ruangan'])): ?>
                                                        <p class="mb-1">
                                                            <small class="text-muted">Luas Ruangan:</small>
                                                            <?= $ruangan['luas_ruangan'] ?> m²
                                                        </p>
                                                    <?php endif; ?>
                                                    <?php if (!empty($ruangan['fasilitas'])): ?>
                                                        <p class="mb-1">
                                                            <small class="text-muted">Fasilitas & Keterangan:</small>
                                                            <?= htmlspecialchars($ruangan['fasilitas']) ?>
                                                        </p>
                                                    <?php endif; ?>
                                                    
                                                    <!-- SEMUA LOGIKA WAKTU ASLI ANDA -->
                                                    <?php if (!empty($ruangan['jam_mulai']) && !empty($ruangan['jam_selesai'])): ?>
                                                        <p class="mb-1">
                                                            <small class="text-muted">Dipinjam:</small>
                                                            <?= substr($ruangan['jam_mulai'], 0, 5) ?> - <?= substr($ruangan['jam_selesai'], 0, 5) ?> WIB
                                                        </p>
                                                        
                                                        <?php 
                                                        // Cek semua peminjaman hari ini untuk ruangan ini
                                                        $pinjamModel = new PinjamRuanganModel();
                                                        $bookings = $pinjamModel->where('ruangan_id', $ruangan['id'])
                                                            ->where('tanggal', date('Y-m-d'))
                                                            ->where('status', 'disetujui')
                                                            ->where('deleted_at', null)
                                                            ->orderBy('waktu_mulai', 'ASC')
                                                            ->findAll();

                                                        // Jam operasional
                                                        $startTime = '07:30';
                                                        $endTime = '16:30';
                                                        
                                                        // Jam saat ini
                                                        $currentTime = date('H:i');
                                                        
                                                        // Gunakan waktu saat ini jika sudah melewati waktu mulai operasional
                                                        if (strtotime($currentTime) > strtotime($startTime)) {
                                                            $startTime = $currentTime;
                                                        }

                                                        // Array untuk menyimpan semua slot waktu yang tidak tersedia (termasuk buffer 30 menit)
                                                        $unavailableSlots = [];

                                                        // Loop semua booking untuk mengidentifikasi slot yang tidak tersedia
                                                        foreach ($bookings as $booking) {
                                                            $bookingStart = substr($booking['waktu_mulai'], 0, 5);
                                                            $bookingEnd = substr($booking['waktu_selesai'], 0, 5);
                                                            
                                                            // Tambahkan buffer 30 menit sebelum dan setelah peminjaman
                                                            $unavailableStart = date('H:i', strtotime('-30 minutes', strtotime($bookingStart)));
                                                            $unavailableEnd = date('H:i', strtotime('+30 minutes', strtotime($bookingEnd)));
                                                            
                                                            $unavailableSlots[] = [
                                                                'start' => $unavailableStart,
                                                                'end' => $unavailableEnd
                                                            ];
                                                        }

                                                        // Gabungkan slot yang tumpang tindih
                                                        $mergedUnavailableSlots = [];
                                                        if (!empty($unavailableSlots)) {
                                                            // Sort berdasarkan waktu mulai
                                                            usort($unavailableSlots, function($a, $b) {
                                                                return strcmp($a['start'], $b['start']);
                                                            });
                                                            
                                                            $mergedUnavailableSlots[] = $unavailableSlots[0];
                                                            for ($i = 1; $i < count($unavailableSlots); $i++) {
                                                                $lastSlot = &$mergedUnavailableSlots[count($mergedUnavailableSlots) - 1];
                                                                
                                                                // Jika slot berikutnya tumpang tindih dengan slot terakhir, gabungkan
                                                                if (strtotime($unavailableSlots[$i]['start']) <= strtotime($lastSlot['end'])) {
                                                                    if (strtotime($unavailableSlots[$i]['end']) > strtotime($lastSlot['end'])) {
                                                                        $lastSlot['end'] = $unavailableSlots[$i]['end'];
                                                                    }
                                                                } else {
                                                                    // Tidak tumpang tindih, tambahkan slot baru
                                                                    $mergedUnavailableSlots[] = $unavailableSlots[$i];
                                                                }
                                                            }
                                                        }

                                                        // Tentukan slot yang tersedia
                                                        $availableSlots = [];
                                                        $currentStart = $startTime;

                                                        // Jika tidak ada slot yang tidak tersedia, semua jam tersedia
                                                        if (empty($mergedUnavailableSlots)) {
                                                            $availableSlots[] = ['start' => $startTime, 'end' => $endTime];
                                                        } else {
                                                            // Cek apakah ada slot tersedia sebelum slot pertama yang tidak tersedia
                                                            foreach ($mergedUnavailableSlots as $slot) {
                                                                if (strtotime($currentStart) < strtotime($slot['start'])) {
                                                                    $availableSlots[] = ['start' => $currentStart, 'end' => $slot['start']];
                                                                }
                                                                $currentStart = $slot['end'];
                                                            }
                                                            
                                                            // Cek apakah ada slot tersedia setelah slot terakhir yang tidak tersedia
                                                            if (strtotime($currentStart) < strtotime($endTime)) {
                                                                $availableSlots[] = ['start' => $currentStart, 'end' => $endTime];
                                                            }
                                                        }

                                                        // Tampilkan slot tersedia
                                                        if (!empty($availableSlots)) {
                                                            foreach ($availableSlots as $slot) {
                                                                // Tampilkan hanya jika durasi valid (waktu mulai lebih kecil dari waktu selesai)
                                                                // dan waktu mulai lebih besar atau sama dengan waktu saat ini
                                                                if (strtotime($slot['start']) < strtotime($slot['end']) && 
                                                                    strtotime($slot['end']) > strtotime($currentTime)) {
                                                                    
                                                                    // Jika waktu mulai sudah lewat, gunakan waktu saat ini
                                                                    $displayStart = (strtotime($slot['start']) < strtotime($currentTime)) 
                                                                        ? $currentTime 
                                                                        : $slot['start'];
                                                                        
                                                                    echo '<div class="mb-1">';
                                                                    echo '<span class="badge bg-success available-time">Tersedia ' . $displayStart . ' - ' . $slot['end'] . '</span>';
                                                                    echo '</div>';
                                                                }
                                                            }
                                                        } else {
                                                            echo '<div class="mb-1 text-muted"><small>Tidak tersedia hari ini</small></div>';
                                                        }
                                                        ?>
                                                    <?php else: ?>
                                                        <p class="mb-1 text-success fw-bold">Tersedia Hari Ini</p>
                                                        <?php
                                                        // Jam saat ini
                                                        $currentTime = date('H:i');
                                                        $startTime = '07:30';
                                                        $endTime = '16:30';
                                                        
                                                        // Jika waktu saat ini sudah melewati waktu mulai operasional, gunakan waktu saat ini
                                                        $displayStart = (strtotime($currentTime) > strtotime($startTime)) ? $currentTime : $startTime;
                                                        ?>
                                                        
                                                        <div class="mb-1">
                                                            <span class="badge bg-success available-time">Tersedia <?= $displayStart ?> - <?= $endTime ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- TOMBOL ADMIN PERSIS SEPERTI ASLI -->
                                            <div class="card-footer bg-white border-0">
                                                <div class="d-grid">
                                                    <button class="btn btn-primary btn-sm rounded-pill shadow-sm hover-effect d-flex align-items-center justify-content-center gap-2"
                                                            style="background-color: #133E87; color: white; border: none; height: 2.2rem;" 
                                                            data-ruangan-id="<?= $ruangan['id'] ?>"
                                                            data-ruangan-nama="<?= $cleanRuanganName ?>"
                                                            data-ruangan-kapasitas="<?= $ruangan['kapasitas'] ?>"
                                                            data-ruangan-fasilitas="<?= $cleanFasilitas ?>"
                                                            onclick="bukaPinjamModal('<?= $ruangan['id'] ?>', '<?= $cleanRuanganName ?>', '<?= $ruangan['kapasitas'] ?>', '<?= $cleanFasilitas ?>')">
                                                        <i class="bi bi-calendar-plus"></i>
                                                        <span>Pinjam</span>
                                                    </button>
                                                    
                                                    <!-- Admin buttons - PERSIS SEPERTI ASLI -->
                                                    <div class="d-flex flex-column gap-2 mt-2">
                                                        <button type="button" class="btn btn-warning btn-sm rounded-pill shadow-sm hover-effect d-flex align-items-center justify-content-center gap-2"
                                                            style="background-color: #608BC1; color: white; border: none;"
                                                            onclick="openEditRuangan('<?= $ruangan['id'] ?>')">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </button>
                                                        <button type="button" class="btn btn-sm rounded-pill shadow-sm hover-effect d-flex align-items-center justify-content-center gap-2" 
                                                            style="background-color: #AE445A; color: white; border: none;"
                                                            onclick="deleteRuangan('<?= $ruangan['id'] ?>')">
                                                            <i class="bi bi-trash"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <!-- MAINTENANCE STATE PERSIS SEPERTI ASLI -->
                                            <div class="card-body">
                                                <h5 class="card-title fw-bold"><?= $ruangan['nama_ruangan'] ?></h5>
                                                <div class="mb-3">
                                                    <p class="mb-1">
                                                        <small class="text-muted">Kapasitas:</small>
                                                        <?= $ruangan['kapasitas'] ?> orang
                                                    </p>
                                                    <?php if (!empty($ruangan['luas_ruangan'])): ?>
                                                        <p class="mb-1">
                                                            <small class="text-muted">Luas Ruangan:</small>
                                                            <?= $ruangan['luas_ruangan'] ?> m²
                                                        </p>
                                                    <?php endif; ?>
                                                    <?php if (!empty($ruangan['fasilitas'])): ?>
                                                        <p class="mb-1">
                                                            <small class="text-muted">Fasilitas & Keterangan:</small>
                                                            <?= htmlspecialchars($ruangan['fasilitas']) ?>
                                                        </p>
                                                    <?php endif; ?>
                                                    <p class="mb-1 text-warning fw-bold">Maintenance</p>
                                                </div>
                                            </div>
                                            
                                            <div class="card-footer bg-white border-0">
                                                <div class="d-grid">
                                                    <!-- TOMBOL MAINTENANCE - LOCKED PERSIS SEPERTI ASLI -->
                                                    <button class="btn btn-secondary btn-sm rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2"
                                                            style="height: 2.2rem; cursor: not-allowed;" disabled>
                                                        <i class="bi bi-tools"></i>
                                                        <span>Maintenance</span>
                                                    </button>
                                                
                                                    <small class="text-center mt-2 text-warning">
                                                        <i class="bi bi-exclamation-triangle"></i>
                                                        Ruangan sedang maintenance, tidak dapat dipinjam
                                                    </small>
                                                    
                                                    <!-- TOMBOL ADMIN TETAP ADA UNTUK MAINTENANCE -->
                                                    <div class="d-flex flex-column gap-2 mt-2">
                                                        <button type="button" class="btn btn-warning btn-sm rounded-pill shadow-sm hover-effect d-flex align-items-center justify-content-center gap-2"
                                                            style="background-color: #608BC1; color: white; border: none;"
                                                            onclick="openEditRuangan('<?= $ruangan['id'] ?>')">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </button>
                                                        <button type="button" class="btn btn-sm rounded-pill shadow-sm hover-effect d-flex align-items-center justify-content-center gap-2" 
                                                            style="background-color: #AE445A; color: white; border: none;"
                                                            onclick="deleteRuangan('<?= $ruangan['id'] ?>')">
                                                            <i class="bi bi-trash"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-secondary text-center">
                                        Tidak ada ruangan yang tersedia untuk dikelola di lokasi ini.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Container Notifikasi Booking -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">🔔 Pemberitahuan Booking Ruangan</h5>
        </div>
        <div class="card-body">
            <div id="bookingNotice">
                <div class="text-muted">Memuat data booking ruangan...</div>
            </div>
        </div>
    </div>

    <!-- Kalender Booking Ruangan -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📅 Kalender Booking Ruangan - <?= $lokasi ?></h5>
            <button class="btn btn-primary" id="toggleCalendar" style="background-color: #133E87; border: none;">
                <i class="bi bi-calendar3" id="calendarIcon"></i>
                <span id="calendarButtonText">Tampilkan Kalender</span>
            </button>
        </div>
        <div class="card-body" id="calendarContainer" style="display: none;">
            <div class="calendar-container">
                <div class="calendar-header">
                    <div class="calendar-nav">
                        <button class="nav-btn" id="prevMonth">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <h4 id="currentMonthYear" class="mb-0"></h4>
                        <button class="nav-btn" id="nextMonth">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Jadwal Booking Ruangan</h6>
                        <div class="d-flex gap-2">
                            <span class="legend-item">
                                <span class="legend-color bg-success"></span>
                                Disetujui
                            </span>
                            <span class="legend-item">
                                <span class="legend-color bg-warning"></span>
                                Pending
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="calendar-body p-0">
                    <div class="calendar-grid" id="calendarGrid">
                        <!-- Calendar akan di-generate oleh JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SEMUA MODAL ASLI -->

<!-- Modal Detail Ruangan -->
<div class="modal fade" id="modalDetailRuangan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="carouselRuangan" class="carousel slide mb-3" data-bs-ride="carousel">
                    <div class="carousel-inner">
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselRuangan" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselRuangan" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
                <div class="ruangan-info">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pinjam Ruangan -->
<div class="modal fade" id="modalPinjamRuangan" tabindex="-1" aria-labelledby="modalPinjamRuanganLabel" aria-hidden="true">
</div>

<!-- Modal Edit Ruangan -->
<div class="modal fade" id="modalEditRuangan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditRuangan" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Ruangan</label>
                                <input type="text" class="form-control" name="nama_ruangan" id="edit_nama_ruangan" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Lokasi</label>
                                <select class="form-select" name="lokasi" id="edit_lokasi" required>
                                    <option value="">Pilih Lokasi</option>
                                    <option value="Gedung Utama">Gedung Utama</option>
                                    <option value="Pusat Data dan Teknologi Informasi">Pusat Data dan Teknologi Informasi</option>
                                    <option value="Bina Marga">Bina Marga</option>
                                    <option value="Cipta Karya">Cipta Karya</option>
                                    <option value="Sumber Daya Air">Sumber Daya Air</option>
                                    <option value="Gedung G">Gedung G</option>
                                    <option value="Heritage">Heritage</option>
                                    <option value="Auditorium">Auditorium</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kapasitas</label>
                                <input type="number" class="form-control" name="kapasitas" id="edit_kapasitas" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Luas Ruangan</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" name="luas_ruangan" id="edit_luas_ruangan" placeholder="Masukkan luas ruangan">
                                    <span class="input-group-text">m²</span>
                                </div>
                                <small class="text-muted">Masukkan angka luas ruangan dalam meter persegi</small>
                            </div>

                            <!-- STATUS AKTIF -->
                            <div class="mb-3">
                                <label class="form-label">Status Ruangan</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                                    <label class="form-check-label" for="edit_is_active">
                                        <span id="status_label">Aktif (Dapat dipinjam)</span>
                                    </label>
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i>
                                    Nonaktifkan jika ruangan sedang maintenance atau tidak dapat dipinjam
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <!-- Fasilitas -->
                            <div class="mb-3">
                                <label class="form-label">Fasilitas</label>
                                <div id="edit_fasilitas_container">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Proyektor" id="edit_fasilitas_proyektor">
                                                <label class="form-check-label" for="edit_fasilitas_proyektor">Proyektor</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Whiteboard" id="edit_fasilitas_whiteboard">
                                                <label class="form-check-label" for="edit_fasilitas_whiteboard">Whiteboard</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Microphone" id="edit_fasilitas_mic">
                                                <label class="form-check-label" for="edit_fasilitas_mic">Microphone</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Sound System" id="edit_fasilitas_sound">
                                                <label class="form-check-label" for="edit_fasilitas_sound">Sound System</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]" value="AC" id="edit_fasilitas_ac">
                                                <label class="form-check-label" for="edit_fasilitas_ac">AC</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="fasilitas[]" value="Wifi" id="edit_fasilitas_wifi">
                                                <label class="form-check-label" for="edit_fasilitas_wifi">Wifi</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="fasilitas_submitted" value="1">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Keterangan Tambahan Fasilitas</label>
                                <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3" 
                                          placeholder="Tambahkan keterangan detail fasilitas..."></textarea>
                                <small class="text-muted">Keterangan akan digabung dengan fasilitas yang dipilih</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Foto Ruangan (Opsional)</label>
                                <input type="file" class="form-control" name="foto_ruangan[]" accept="image/*" multiple>
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Booking -->
<div class="modal fade" id="modalDetailBooking" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #133E87, #1e5bb8); color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-event me-2"></i>
                    Detail Booking Ruangan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBookingContent">
                <!-- Content akan diisi oleh JavaScript -->
            </div>
        </div>
    </div>
</div>

<style>
    /* Tab Styling */
    .nav-tabs .nav-link {
        color: #666;
        font-weight: 500;
        border-bottom: 2px solid transparent;
    }
    
    .nav-tabs .nav-link.active {
        background-color: #f8f9fa;
        border-color: #dee2e6 #dee2e6 #f8f9fa;
        color: #133E87;
        font-weight: 600;
        border-bottom: 2px solid #133E87;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        isolation: isolate;
    }
    
    /* Badge Styling */
    .booking-type-badge {
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .confirm-type-badge {
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    /* Button Styling */
    .btn-booking-ruangan {
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .btn-booking-ruangan:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }
    
    .btn-pinjam-ruangan {
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .btn-pinjam-ruangan:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    }
    
    /* Card Styling */
    .booking-room-card {
        border-left: 4px solid #28a745;
    }
    
    .confirm-room-card {
        border-left: 4px solid #ffc107;
    }
    
    .admin-room-card {
        border-left: 4px solid #0d6efd;
    }
    
    .available-time {
        font-size: 0.8rem;
        padding: 5px 10px;
        border-radius: 4px;
    }
    
    .custom-alert-booking {
        background-color: var(--booking-bg, #f8e48c);
        border-color: var(--booking-border, #f3d96a);
        color: var(--booking-text, #4b3b00);
    }
    
    /* Responsive Grid */
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .card-grid {
            grid-template-columns: 1fr;
        }
        
        .nav-tabs .nav-link {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }
        
        .booking-type-badge,
        .confirm-type-badge {
            font-size: 0.6rem;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event listener untuk tombol booking langsung
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-booking-ruangan')) {
            const button = e.target.closest('.btn-booking-ruangan');
            const ruanganId = button.getAttribute('data-ruangan-id');
            const ruanganNama = button.getAttribute('data-ruangan-nama');
            const ruanganKapasitas = button.getAttribute('data-ruangan-kapasitas');
            const ruanganFasilitas = button.getAttribute('data-ruangan-fasilitas');
            
            bukaBookingModal(ruanganId, ruanganNama, ruanganKapasitas, ruanganFasilitas);
        }
    });
    
    // Event listener untuk tombol pinjam (confirm)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-pinjam-ruangan')) {
            const button = e.target.closest('.btn-pinjam-ruangan');
            const ruanganId = button.getAttribute('data-ruangan-id');
            const ruanganNama = button.getAttribute('data-ruangan-nama');
            const ruanganKapasitas = button.getAttribute('data-ruangan-kapasitas');
            const ruanganFasilitas = button.getAttribute('data-ruangan-fasilitas');
            
            bukaPinjamModal(ruanganId, ruanganNama, ruanganKapasitas, ruanganFasilitas);
        }
    });
    
    // Load booking notices
    loadBookingNotices();
    
    // Initialize filter functions
    initializeFilters();
});

// Fungsi untuk booking langsung
function bukaBookingModal(ruanganId, ruanganNama, ruanganKapasitas, ruanganFasilitas) {
    console.log('Booking langsung untuk ruangan:', ruanganNama);
    alert('Booking langsung untuk ruangan: ' + ruanganNama + ' (function khusus booking)');
}

// Fungsi untuk pinjam dengan confirm (existing)
function bukaPinjamModal(ruanganId, ruanganNama, ruanganKapasitas, ruanganFasilitas) {
    console.log('Request confirm untuk ruangan:', ruanganNama);
    alert('Request confirm untuk ruangan: ' + ruanganNama + ' (function asli pinjam)');
}

function initializeFilters() {
    console.log('Filters initialized');
}

function resetFilterBooking() {
    document.getElementById('filterNamaBooking').value = '';
    document.getElementById('filterKapasitasBooking').value = '';
    document.getElementById('filterStatusBooking').value = '';
    document.getElementById('filterFasilitasBooking').value = '';
}

function resetFilterConfirm() {
    document.getElementById('filterNamaConfirm').value = '';
    document.getElementById('filterKapasitasConfirm').value = '';
    document.getElementById('filterStatusConfirm').value = '';
    document.getElementById('filterFasilitasConfirm').value = '';
}

function resetFilterPengaturan() {
    document.getElementById('filterNamaPengaturan').value = '';
    document.getElementById('filterKapasitasPengaturan').value = '';
    document.getElementById('filterStatusPengaturan').value = '';
    document.getElementById('filterFasilitasPengaturan').value = '';
}

// Functions untuk admin (edit, hapus) - PERSIS SEPERTI ASLI
function openEditRuangan(ruanganId) {
    console.log('Edit ruangan ID:', ruanganId);
    alert('Edit ruangan ID: ' + ruanganId + ' (implementasi akan ditambahkan)');
}

function deleteRuangan(ruanganId) {
    if (confirm('Apakah Anda yakin ingin menghapus ruangan ini?')) {
        console.log('Delete ruangan ID:', ruanganId);
        alert('Hapus ruangan ID: ' + ruanganId + ' (implementasi akan ditambahkan)');
    }
}

function loadBookingNotices() {
    fetch("<?= base_url('User/Ruangan/getBookingPublik') ?>")
        .then(res => res.json())
        .then(data => {
            const noticeContainer = document.getElementById("bookingNotice");
            const bookings = data.data;

            if (!bookings || bookings.length === 0) {
                noticeContainer.innerHTML = `
                    <div class="alert alert-info text-center">
                        Belum ada booking ruangan aktif saat ini.
                    </div>`;
                return;
            }

            const html = bookings.map(item => {
                const hari = getIndonesianDayName(item.tanggal);
                const mulai = formatDateTime(item.tanggal, item.waktu_mulai);
                const selesai = formatDateTime(item.tanggal, item.waktu_selesai);
                
                return `
                    <div class="alert custom-alert-booking shadow-sm mb-2">
                        <i class="bi bi-calendar-event me-2 fs-4"></i>
                        <div>
                            <strong>${item.nama_ruangan}</strong> telah dibooking<br>
                            Hari: <strong>${hari}</strong> <br>
                            Tanggal & Waktu: <strong>${mulai}</strong> s.d. <strong>${selesai}</strong><br>
                            Keperluan: <em>${item.keperluan}</em>
                        </div>
                    </div>
                `;
            }).join("");

            noticeContainer.innerHTML = html;
        })
        .catch(error => {
            console.error(error);
            document.getElementById("bookingNotice").innerHTML = `
                <div class="alert alert-danger">
                    Gagal memuat data booking ruangan. Silakan refresh halaman.
                </div>`;
        });
}

function getIndonesianDayName(dateStr) {
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const date = new Date(dateStr);
    return days[date.getDay()];
}

function formatDateTime(dateStr, timeStr) {
    const dateTime = new Date(`${dateStr}T${timeStr}`);
    return dateTime.toLocaleString("id-ID", {
        day: "numeric", month: "long", year: "numeric", hour: "2-digit", minute: "2-digit"
    });
}

// Set base URL untuk JavaScript
const baseUrl = '<?= base_url() ?>';
</script>

<!-- Load JavaScript files -->
<script src="<?= base_url('assets/js/pinjam-ruangan.js') ?>"></script>

<?= $this->endSection() ?>