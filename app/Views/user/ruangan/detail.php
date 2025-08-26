<?= $this->extend('admin/layouts/app') ?>

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

    <div class="filter-section mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Cari Nama Ruangan</label>
                        <input type="text" class="form-control" id="filterNama" placeholder="Cari ruangan...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Filter Kapasitas</label>
                        <select class="form-select" id="filterKapasitas">
                            <option value="">Semua Kapasitas</option>
                            <option value="1-10">1-10 Orang</option>
                            <option value="11-30">11-30 Orang</option>
                            <option value="31-50">31-50 Orang</option>
                            <option value="50+">>50 Orang</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Filter Status</label>
                        <select class="form-select" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="Tersedia">Tersedia</option>
                            <option value="Dibooking">Sedang Dipinjam</option>
                            <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Filter Fasilitas</label>
                        <select class="form-select" id="filterFasilitas">
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
                        <button class="btn btn-primary" onclick="resetFilter()">
                            <i class="bi bi-arrow-clockwise"></i> Reset Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($ruangans)): ?>
        <div class="alert text-center text-white" style="background-color: #074799; border-radius: 1rem;">
            Belum ada ruangan yang ditambahkan untuk lokasi ini.
        </div>
    <?php else: ?>
        <section class="section">
            <div class="card-grid">
                <?php foreach ($ruangans as $ruangan): ?>
                    <div class="card h-300 room-card" 
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

                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?= $ruangan['nama_ruangan'] ?></h5>
                            <div class="mb-3">
                                <p class="mb-1">
                                    <small class="text-muted">Kapasitas:</small>
                                    <?= $ruangan['kapasitas'] ?> orang
                                </p>
                                <?php if (!empty($ruangan['fasilitas'])): ?>
                                    <p class="mb-1">
                                        <small class="text-muted">Fasilitas & Keterangan:</small>
                                        <?= htmlspecialchars($ruangan['fasilitas']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($ruangan['jam_mulai']) && !empty($ruangan['jam_selesai'])): ?>
                                <p class="mb-1">
                                    <small class="text-muted">Dipinjam:</small>
                                    <?= substr($ruangan['jam_mulai'], 0, 5) ?> - <?= substr($ruangan['jam_selesai'], 0, 5) ?> WIB
                                </p>
                            <?php else: ?>
                                <p class="mb-1 text-success fw-bold">Tersedia Hari Ini</p>
                            <?php endif; ?>
                        </div>

                        <div class="card-footer bg-white border-0">
                            <div class="d-grid">
                                <?php 
                                $isAvailable = $ruangan['status'] === 'Tersedia' && !isset($ruangan['peminjam_id']);
                                $isPending = $ruangan['status'] === 'Menunggu Verifikasi';
                                $isCurrentUserBorrowing = isset($ruangan['peminjam_id']) && $ruangan['peminjam_id'] == user_id();
                                
                                // PERBAIKAN: Sanitasi data untuk JavaScript dengan lebih aman
                                $cleanRuanganName = htmlspecialchars($ruangan['nama_ruangan'], ENT_QUOTES);
                                $cleanFasilitas = htmlspecialchars($ruangan['fasilitas'] ?? '', ENT_QUOTES);
                                // Escape quotes dan newlines untuk JavaScript
                                $cleanFasilitas = str_replace(["\r\n", "\n", "\r"], ' ', $cleanFasilitas);
                                $cleanFasilitas = str_replace(["'", '"'], ["\\'", '\\"'], $cleanFasilitas);
                                ?>



<!-- Cek status aktif ruangan -->
    <?php 
    // Cek status aktif untuk PostgreSQL (support 't', 'f', true, false)
    $isRuanganActive = ($ruangan['is_active'] === true || $ruangan['is_active'] === 't' || $ruangan['is_active'] === '1' || $ruangan['is_active'] === 1);
    ?>
    
    <?php if ($isRuanganActive): ?>
        <!-- TOMBOL PINJAM AKTIF -->
        <button class="btn btn-primary btn-sm rounded-pill shadow-sm hover-effect d-flex align-items-center justify-content-center gap-2 btn-pinjam-ruangan"
                style="background-color: #133E87; color: white; border: none; height: 2.2rem;" 
                data-ruangan-id="<?= $ruangan['id'] ?>"
                data-ruangan-nama="<?= $cleanRuanganName ?>"
                data-ruangan-kapasitas="<?= $ruangan['kapasitas'] ?>"
                data-ruangan-fasilitas="<?= $cleanFasilitas ?>">
            <i class="bi bi-calendar-plus"></i>
            <span>Pinjam</span>
        </button>
    
    <!-- Info Status untuk Reference User -->
    <?php if (!empty($ruangan['jam_mulai']) && !empty($ruangan['jam_selesai'])): ?>
        <small class="text-center mt-2 text-muted">
            <i class="bi bi-info-circle"></i>
            Dipinjam hari ini: <?= substr($ruangan['jam_mulai'], 0, 5) ?> - <?= substr($ruangan['jam_selesai'], 0, 5) ?> WIB
        </small>
    <?php elseif ($isPending): ?>
        <small class="text-center mt-2 text-warning">
            <i class="bi bi-clock"></i>
            Ada booking menunggu verifikasi
        </small>
    <?php else: ?>
        <small class="text-center mt-2 text-success">
            <i class="bi bi-check-circle"></i>
            Tersedia untuk booking
        </small>
    <?php endif; ?>

    <?php else: ?>
        <!-- TOMBOL MAINTENANCE - LOCKED -->
        <button class="btn btn-secondary btn-sm rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2"
                style="height: 2.2rem; cursor: not-allowed;" disabled>
            <i class="bi bi-tools"></i>
            <span>Maintenance</span>
        </button>
    
        <small class="text-center mt-2 text-warning">
            <i class="bi bi-exclamation-triangle"></i>
            Ruangan sedang maintenance, tidak dapat dipinjam
        </small>
    <?php endif; ?>

<!-- Admin buttons -->
<?php if (in_groups('admin_gedungutama') || 
    in_groups('admin_pusdatin') || 
    in_groups('admin_binamarga') || 
    in_groups('admin_ciptakarya') || 
    in_groups('admin_sda') || 
    in_groups('admin_gedungg') ||
    in_groups('admin_heritage') ||
    in_groups('admin') ||
    in_groups('admin_auditorium')): ?>
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
<?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
</div>

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

                            <!-- STATUS AKTIF - HANYA SATU INI SAJA -->
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

                            <!-- HAPUS SEMUA YANG INI - DUPLIKAT! -->
                            <!-- 
                            <input type="hidden" name="is_active" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                                <label class="form-check-label" for="edit_is_active">
                                    Aktif (Dapat dipinjam)
                                </label>
                            </div>
                            -->
                            
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

<!-- Container Notifikasi Booking -->
<div class="container mt-4">
    <h5 class="mb-3">🔔 Pemberitahuan Booking Ruangan </h5>
    <div id="bookingNotice">
        <div class="text-muted">Memuat data booking ruangan...</div>
    </div>
</div>

<!-- Kalender Booking Ruangan -->
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">📅 Kalender Booking Ruangan - <?= $lokasi ?></h5>
        <button class="btn btn-primary" id="toggleCalendar" style="background-color: #133E87; border: none;">
            <i class="bi bi-calendar3" id="calendarIcon"></i>
            <span id="calendarButtonText">Tampilkan Kalender</span>
        </button>
    </div>
</div>

<!-- Kalender Booking Ruangan (Hidden by default) -->
<div class="container mt-3" id="calendarContainer" style="display: none;">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // PERBAIKAN: Event listener yang lebih aman untuk tombol pinjam
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-pinjam-ruangan')) {
            const button = e.target.closest('.btn-pinjam-ruangan');
            const ruanganId = button.getAttribute('data-ruangan-id');
            const ruanganNama = button.getAttribute('data-ruangan-nama');
            const ruanganKapasitas = button.getAttribute('data-ruangan-kapasitas');
            const ruanganFasilitas = button.getAttribute('data-ruangan-fasilitas');
            
            // Panggil fungsi dengan parameter yang aman
            bukaPinjamModal(ruanganId, ruanganNama, ruanganKapasitas, ruanganFasilitas);
        }
    });
    
    // Load booking notices
    loadBookingNotices();
});

function getIndonesianDayName(dateStr) {
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const date = new Date(dateStr);
    return days[date.getDay()];
}

function formatDateTime(dateStr, timeStr) {
    const dateTime = new Date(`${dateStr}T${timeStr}`);
    return dateTime.toLocaleString("id-ID", {
        day: "numeric", month: "long", year: "numeric",
        hour: "2-digit", minute: "2-digit"
    });
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
                    <div class="alert alert-warning shadow-sm mb-2">
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

// Set base URL untuk JavaScript
const baseUrl = '<?= base_url() ?>';
</script>

<!-- Load JavaScript files -->
<script src="<?= base_url('assets/js/pinjam-ruangan.js') ?>"></script>

<?= $this->endSection() ?>