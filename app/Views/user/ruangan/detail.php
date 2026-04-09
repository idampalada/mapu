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
                    <h3>Sistem Peminjaman Ruangan</h3>
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
            <p class="mb-0 text-muted">Pilih jenis ruangan yang diinginkan</p>
        </div>
        <div class="card-body">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" id="peminjamanTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="booking-tab" data-bs-toggle="tab" data-bs-target="#booking" 
                            type="button" role="tab" aria-controls="booking" aria-selected="true">
                        <i class="bi bi-calendar-plus me-2"></i>Booking Ruangan
                    </button>
                </li>
                                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="daftar-booking-tab" data-bs-toggle="tab" data-bs-target="#daftar-booking" 
                            type="button" role="tab" aria-controls="daftar-booking" aria-selected="false">
                        <i class="bi bi-list-check me-2"></i>Daftar Booking Saya
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
                                                 BOOKING
                                            </span>
                                        </div>
                                    </div>

                                    <?php 
                                    // PERBAIKAN: Logika is_active yang fleksibel
                                    $isRuanganActive = true; // Default active jika field tidak ada
                                    
                                    if (isset($ruangan['is_active'])) {
                                        // Cek berbagai format is_active dari database
                                        $isActiveValue = $ruangan['is_active'];
                                        
                                        // False conditions (maintenance)
                                        if ($isActiveValue === false || 
                                            $isActiveValue === 'f' || 
                                            $isActiveValue === '0' || 
                                            $isActiveValue === 0 ||
                                            $isActiveValue === 'false' ||
                                            $isActiveValue === null) {
                                            $isRuanganActive = false;
                                        }
                                    }
                                    
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

                                            <div class="mt-2 ketersediaan-ruangan"
     data-ruangan="<?= htmlspecialchars($ruangan['nama_ruangan']) ?>">
     
    <small class="text-muted"> Ketersediaan Hari Ini:</small>

    <div class="ketersediaan-content">
        <small class="text-muted">Memuat...</small>
    </div>

</div>
                                        </div>
                                    </div>

                                    <div class="card-footer bg-white border-0">
                                        <div class="d-grid gap-2">
                                            <?php if ($isRuanganActive): ?>
                                                <!-- Button Booking Sekarang -->
                                            <button class="btn btn-primary btn-sm rounded-pill shadow-sm hover-effect 
                                                        d-flex align-items-center justify-content-center btn-booking-ruangan"
                                                    style="height: 2.2rem; background-color: #0056B3; border: none;"
                                                    data-ruangan-id="<?= $ruangan['id'] ?>"
                                                    data-ruangan-nama="<?= $cleanRuanganName ?>"
                                                    data-ruangan-kapasitas="<?= $ruangan['kapasitas'] ?>"
                                                    data-ruangan-fasilitas="<?= $cleanFasilitas ?>"
                                                    data-booking-type="booking">
                                                Booking Sekarang
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
                
                <!-- Tab Daftar Booking Saya -->
                <div class="tab-pane fade" id="daftar-booking" role="tabpanel" aria-labelledby="daftar-booking-tab">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Daftar Booking Saya:</strong> Kelola semua booking ruangan yang telah Anda buat. 
                        Anda dapat melihat status booking dan melakukan upload surat permohonan untuk approval admin.
                    </div>
                    
                    <!-- Filter untuk Daftar Booking -->
                    <div class="filter-section mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Filter Booking Saya</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Cari Nama Ruangan</label>
<select class="form-select" id="filterNamaBookingSaya">
    <option value="">Semua Ruangan</option>
    <?php 
    $uniqueRuangan = [];
    foreach ($ruangans as $r) {
        if (!in_array($r['nama_ruangan'], $uniqueRuangan)) {
            $uniqueRuangan[] = $r['nama_ruangan'];
            echo '<option value="'.htmlspecialchars($r['nama_ruangan']).'">'
                . htmlspecialchars($r['nama_ruangan']) .
                '</option>';
        }
    }
    ?>
</select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Filter Status</label>
                                        <select class="form-select" id="filterStatusBookingSaya">
                                            <option value="">Semua Status</option>
                                            <option value="aktif">Pending</option>
                                            <option value="pending">Menunggu Approval</option>
                                            <option value="disetujui">Disetujui</option>
                                            <option value="ditolak">Ditolak</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Filter Tanggal</label>
                                        <input type="date" class="form-control" id="filterTanggalBookingSaya">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Aksi</label>
                                        <div class="d-grid">
                                            <button class="btn btn-primary btn-sm" onclick="resetFilterBookingSaya()">
                                                <i class="bi bi-arrow-clockwise"></i> Reset Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Daftar Booking User -->
                    <div class="row" id="daftarBookingSayaContainer">
                        <!-- Data akan dimuat via AJAX -->
                        <div class="col-12">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div class="mt-3">Memuat data booking Anda...</div>
                            </div>
                        </div>
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
                                        
                                        // PERBAIKAN: Logika is_active yang sama dengan tab lain
                                        $isRuanganActive = true;
                                        
                                        if (isset($ruangan['is_active'])) {
                                            $isActiveValue = $ruangan['is_active'];
                                            
                                            if ($isActiveValue === false || 
                                                $isActiveValue === 'f' || 
                                                $isActiveValue === '0' || 
                                                $isActiveValue === 0 ||
                                                $isActiveValue === 'false' ||
                                                $isActiveValue === null) {
                                                $isRuanganActive = false;
                                            }
                                        }
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

                                            <!-- TOMBOL ADMIN UPDATED WITH UBAH JAM -->
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
                                                    
                                                    <!-- Admin buttons - UPDATED WITH UBAH JAM -->
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
                                                        <!-- NEW: Ubah Jam Button -->
                                                        <button type="button" class="btn btn-sm rounded-pill shadow-sm hover-effect d-flex align-items-center justify-content-center gap-2"
                                                            style="background-color: #28a745; color: white; border: none;"
                                                            onclick="bukaModalUbahJamAdmin('<?= $ruangan['id'] ?>', '<?= htmlspecialchars($ruangan['nama_ruangan'], ENT_QUOTES) ?>')">
                                                            <i class="bi bi-clock-history"></i> Ubah Jam
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
                                                    
                                                    <!-- TOMBOL ADMIN TETAP ADA UNTUK MAINTENANCE + UBAH JAM -->
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
                                                        <!-- NEW: Ubah Jam Button untuk Maintenance -->
                                                        <button type="button" class="btn btn-sm rounded-pill shadow-sm hover-effect d-flex align-items-center justify-content-center gap-2"
                                                            style="background-color: #28a745; color: white; border: none;"
                                                            onclick="bukaModalUbahJamAdmin('<?= $ruangan['id'] ?>', '<?= htmlspecialchars($ruangan['nama_ruangan'], ENT_QUOTES) ?>')">
                                                            <i class="bi bi-clock-history"></i> Ubah Jam
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

<div class="modal fade" id="modalUbahJam" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">
                    <i class="bi bi-clock-history"></i> 
                    Ubah Jam Pinjaman Ruangan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
                <!-- Info Peminjaman -->
                <div class="alert alert-info mb-4">
                    <h6><i class="bi bi-info-circle"></i> Detail Peminjaman</h6>
                    <div id="info_peminjaman_ubah">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Pemohon:</strong> <span id="nama_pemohon">-</span><br>
                                <strong>Ruangan:</strong> <span id="nama_ruangan">-</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Tanggal:</strong> <span id="tanggal_pinjam">-</span><br>
                                <strong>Waktu Saat Ini:</strong> <span id="waktu_original">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PERBAIKAN: Time Picker Grid untuk Ubah Jam -->
                <div class="time-picker-ubah-jam-container">
                    <h6 class="text-center mb-3">
                        <i class="bi bi-clock"></i> Pilih Waktu Baru (Interval 30 Menit)
                    </h6>
                    
                    <!-- Legend -->
                    <div class="legend mb-3">
                        <div class="legend-item">
                            <div class="legend-color available"></div>
                            <span>Tersedia</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color booked"></div>
                            <span>Sudah Dibooking</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color selected-start"></div>
                            <span>Waktu Mulai</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color selected-end"></div>
                            <span>Waktu Selesai</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color original-time"></div>
                            <span>Waktu Asli</span>
                        </div>
                    </div>
                    
                    <!-- Time Grid -->
                    <div class="ubah-jam-time-grid" id="ubah_jam_time_ruler">
                        <!-- Time slots akan di-generate oleh JavaScript -->
                    </div>
                    
                    <!-- Display waktu terpilih -->
                    <div class="time-selection-display mt-3">
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label">Waktu Mulai Baru:</label>
                                <div class="selected-time" id="ubah_jam_display_waktu_mulai">Belum dipilih</div>
                            </div>
                            <div class="col-4">
                                <label class="form-label">Waktu Selesai Baru:</label>
                                <div class="selected-time" id="ubah_jam_display_waktu_selesai">Belum dipilih</div>
                            </div>
                            <div class="col-4">
                                <label class="form-label">Durasi:</label>
                                <div class="selected-time">
                                    <span id="durasi_baru" class="badge bg-info">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i>
                            Klik untuk memilih waktu mulai, lalu klik lagi untuk waktu selesai
                        </small>
                    </div>
                </div>

                <!-- Form Ubah Jam -->
                <form id="formUbahJam" class="mt-4" onsubmit="submitUbahJam(event)">
                    <input type="hidden" id="ubah_pinjam_id" name="pinjam_id">
                    
                    <!-- Hidden inputs untuk waktu (akan diisi oleh JavaScript) -->
                    <input type="hidden" id="waktu_mulai_baru" name="waktu_mulai">
                    <input type="hidden" id="waktu_selesai_baru" name="waktu_selesai">
                    
                    <!-- Warning Konflik -->
                    <div id="warning_konflik" class="alert alert-warning" style="display: none;">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span id="pesan_konflik"></span>
                    </div>
                    
                    <!-- Alasan Perubahan -->
                    <div class="mb-3">
                        <label for="alasan_ubah_jam" class="form-label">
                            <i class="bi bi-chat-text"></i> 
                            Alasan Perubahan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="alasan_ubah_jam" name="alasan" 
                                  rows="3" required placeholder="Jelaskan alasan perubahan waktu..."></textarea>
                    </div>
                    
                    <!-- Preview -->
                    <div id="preview_ubah" class="alert alert-success" style="display: none;">
                        <h6><i class="bi bi-eye"></i> Preview Perubahan</h6>
                        <div id="preview_content_ubah"></div>
                    </div>
                </form>

                <!-- List Peminjaman Aktif -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-list"></i> Peminjaman Aktif untuk Ruangan Ini</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Penanggung Jawab</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Keperluan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelPeminjamanAktif">
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <i class="bi bi-hourglass-split"></i> Loading...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        <i class="bi bi-x-circle"></i> Batal
    </button>
                    <button
        type="submit"
        form="formUbahJam"
        class="btn btn-warning disabled"
        id="btnUbahSetujui"
        disabled>
        <i class="bi bi-check-circle"></i> Ubah & Setujui
    </button>
</div>
        </div>
    </div>
</div>

<style>
/* PERBAIKAN: CSS untuk Time Picker Ubah Jam */
.time-picker-ubah-jam-container {
    border: 2px solid #ffc107;
    border-radius: 12px;
    padding: 20px;
    background: linear-gradient(135deg, #fff9c4 0%, #fffbdf 100%);
    margin: 20px 0;
}

.ubah-jam-time-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    gap: 8px;
    padding: 20px;
    background: white;
    border-radius: 8px;
    border: 2px solid #dee2e6;
    margin: 15px 0;
    max-height: 400px;
    overflow-y: auto;
}

.ubah-jam-time-slot {
    padding: 10px 8px;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    background: white;
    color: #495057;
    font-weight: 500;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s ease;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.ubah-jam-time-slot:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-color: #ffc107;
}

.ubah-jam-time-slot.available {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-color: #28a745;
    color: #155724;
}

.ubah-jam-time-slot.available:hover {
    background: linear-gradient(135deg, #c3e6cb 0%, #b3dfbf 100%);
    border-color: #1e7e34;
}

.ubah-jam-time-slot.booked {
    background: linear-gradient(135deg, #f8d7da 0%, #f1c2c7 100%);
    border-color: #dc3545;
    color: #721c24;
    cursor: not-allowed;
    opacity: 0.7;
}

.ubah-jam-time-slot.selected-start {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border-color: #0a58ca;
    color: white;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
}

.ubah-jam-time-slot.selected-end {
    background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
    border-color: #4c2a85;
    color: white;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(111, 66, 193, 0.4);
}

.ubah-jam-time-slot.in-range {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-color: #ffc107;
    color: #856404;
}

.ubah-jam-time-slot.original-time {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    border-color: #117a8b;
    color: white;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(23, 162, 184, 0.4);
}

.time-selection-display .selected-time {
    background: white;
    padding: 10px 12px;
    border-radius: 6px;
    border: 2px solid #dee2e6;
    text-align: center;
    font-weight: 600;
    color: #495057;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.legend {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 4px 8px;
    background: white;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    font-size: 0.8rem;
}

.legend-color {
    width: 18px;
    height: 18px;
    border-radius: 3px;
    border: 2px solid #333;
}

.legend-color.available {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-color: #28a745;
}

.legend-color.booked {
    background: linear-gradient(135deg, #f8d7da 0%, #f1c2c7 100%);
    border-color: #dc3545;
}

.legend-color.selected-start {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border-color: #0a58ca;
}

.legend-color.selected-end {
    background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
    border-color: #4c2a85;
}

.legend-color.original-time {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    border-color: #117a8b;
}

/* Responsive */
@media (max-width: 768px) {
    .ubah-jam-time-grid {
        grid-template-columns: repeat(auto-fit, minmax(65px, 1fr));
        gap: 6px;
        padding: 15px;
    }
    
    .ubah-jam-time-slot {
        padding: 8px 4px;
        font-size: 0.75rem;
        min-height: 35px;
    }
    
    .legend {
        gap: 8px;
    }
    
    .legend-item {
        font-size: 0.7rem;
        padding: 3px 6px;
    }
    
    .legend-color {
        width: 14px;
        height: 14px;
    }
}

@media (max-width: 480px) {
    .ubah-jam-time-grid {
        grid-template-columns: repeat(auto-fit, minmax(55px, 1fr));
        gap: 4px;
        padding: 10px;
    }
    
    .ubah-jam-time-slot {
        padding: 6px 2px;
        font-size: 0.7rem;
        min-height: 32px;
    }
}
</style>

    <!-- Container Notifikasi Booking -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">🔔 Pemberitahuan Booking Ruangan</h5>
    <button class="btn btn-sm btn-outline-primary" id="toggleBookingNotice">
        <i class="bi bi-eye"></i> Tampilkan
    </button>
</div>
        <div class="card-body">
            <div id="bookingNotice" style="display:none;">
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

<!-- Modal Booking Ruangan - PENTING: TAMBAHKAN INI -->
<div class="modal fade" id="modalBookingRuangan" tabindex="-1" aria-labelledby="modalBookingRuanganLabel" aria-hidden="true">
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

const LOKASI_GEDUNG = "<?= $lokasi ?>";
document.addEventListener('DOMContentLoaded', function() {

loadKetersediaanHariIni();
    // Event listener untuk tombol booking langsung
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-booking-ruangan')) {
            const button = e.target.closest('.btn-booking-ruangan');
            const ruanganId = button.getAttribute('data-ruangan-id');
            const ruanganNama = button.getAttribute('data-ruangan-nama');
            const ruanganKapasitas = button.getAttribute('data-ruangan-kapasitas');
            const ruanganFasilitas = button.getAttribute('data-ruangan-fasilitas');
            
            // Function akan di-handle oleh booking-ruangan.js
            if (typeof bukaBookingModal === 'function') {
                bukaBookingModal(ruanganId, ruanganNama, ruanganKapasitas, ruanganFasilitas);
            } else {
                console.error('bukaBookingModal function not found in booking-ruangan.js');
                // Fallback jika function belum loaded
                setTimeout(() => {
                    if (typeof bukaBookingModal === 'function') {
                        bukaBookingModal(ruanganId, ruanganNama, ruanganKapasitas, ruanganFasilitas);
                    }
                }, 100);
            }
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
            
            // Function akan di-handle oleh pinjam-ruangan.js
            if (typeof bukaPinjamModal === 'function') {
                bukaPinjamModal(ruanganId, ruanganNama, ruanganKapasitas, ruanganFasilitas);
            } else {
                console.error('bukaPinjamModal function not found in pinjam-ruangan.js');
                // Fallback jika function belum loaded
                setTimeout(() => {
                    if (typeof bukaPinjamModal === 'function') {
                        bukaPinjamModal(ruanganId, ruanganNama, ruanganKapasitas, ruanganFasilitas);
                    }
                }, 100);
            }
        }
    });
    
    // Load booking notices
    loadBookingNotices();
    
    // Initialize filter functions
    initializeFilters();
});

// HAPUS function placeholder - biarkan JS file yang handle
// Function bukaBookingModal() dan bukaPinjamModal() akan di-handle oleh file JS eksternal

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
    fetch("<?= base_url('User/Ruangan/getBookingPublik') ?>/" + encodeURIComponent(LOKASI_GEDUNG))
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
                const tanggal = formatTanggal(item.tanggal);

                const mulai = item.waktu_mulai.substring(0,5);
                const selesai = item.waktu_selesai.substring(0,5);

                return `
                    <div class="alert custom-alert-booking shadow-sm mb-2">
                        <i class="bi bi-calendar-event me-2 fs-4"></i>
                        <div>
                            <strong>${item.nama_ruangan}</strong> telah dibooking<br>
                            Hari: <strong>${hari}</strong><br>
                            Tanggal: <strong>${tanggal}</strong><br>
                            Waktu: <strong>${mulai} - ${selesai} WIB</strong><br>
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

// Toggle show / hide pemberitahuan booking
document.getElementById("toggleBookingNotice").addEventListener("click", function () {

    const notice = document.getElementById("bookingNotice");
    const btn = this;

    if (notice.style.display === "none") {
        notice.style.display = "block";
        btn.innerHTML = '<i class="bi bi-eye-slash"></i> Sembunyikan';
    } else {
        notice.style.display = "none";
        btn.innerHTML = '<i class="bi bi-eye"></i> Tampilkan';
    }

});


function loadKetersediaanHariIni() {

    const now = new Date();
    const nowTime = now.toTimeString().substring(0,5);
    const endTime = "17:30";

    fetch(baseUrl + "/user/ruangan/getPinjamHariIni/" + encodeURIComponent(LOKASI_GEDUNG))
    .then(res => res.json())
    .then(response => {

        if (!response.success) return;

        const bookings = response.data || [];

        // ambil semua container ketersediaan di card ruangan
        const containers = document.querySelectorAll(".ketersediaan-ruangan");

        containers.forEach(box => {

            const namaRuangan = box.dataset.ruangan;
            const content = box.querySelector(".ketersediaan-content");

            const bookingRuangan = bookings.filter(b => b.nama_ruangan === namaRuangan);

            let html = "";

            if (bookingRuangan.length === 0) {

                html += `<small class="text-success">● Tersedia ${nowTime} - ${endTime}</small>`;

            } else {

                bookingRuangan.forEach(b => {

    const start = addMinutes(b.waktu_mulai.substring(0,5), -30);
    const end = addMinutes(b.waktu_selesai.substring(0,5), 30);

    // hanya tampilkan slot sebelum booking jika masih valid
    if (nowTime < start) {
        html += `<small class="text-success d-block">● ${nowTime} - ${start}</small>`;
    }

    // tampilkan slot setelah booking jika masih dalam jam operasional
    if (end < endTime) {
        html += `<small class="text-success d-block">● ${end} - ${endTime}</small>`;
    }

});

            }

            content.innerHTML = html;

        });

    });

}

function addMinutes(time, minutes) {

    const [h,m] = time.split(":").map(Number);

    const date = new Date();
    date.setHours(h);
    date.setMinutes(m + minutes);

    return date.toTimeString().substring(0,5);
}

// Set base URL untuk JavaScript
const baseUrl = '<?= base_url() ?>';
</script>



<?= $this->endSection() ?>