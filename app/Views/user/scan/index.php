<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<title>Scan Barcode - Peminjaman Barang</title>


<!-- Modern Header Section -->
<div class="header-section py-4 px-3 mb-4 rounded-lg" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="text-white mb-1 display-6 fw-bold">
                <i class="bi bi-qr-code-scan me-3"></i>Scan Barcode
            </h2>
            <p class="text-light mb-0 opacity-90">Scan QR Code untuk peminjaman barang dengan mudah</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-stats">
                <div class="stat-item text-white">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <span class="fw-bold">Proses Cepat</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Alert Messages -->
    <div id="alertContainer"></div>
    
    <div class="row">
        <!-- Scanner Section -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #133E87, #1e5bb8);">
                    <h5 class="mb-0">
                        <i class="bi bi-camera-fill me-2"></i>QR Code Scanner
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Scanner Container -->
                    <div class="scanner-container mb-4">
                        <div id="scanner-area" class="position-relative">
                            <video id="qr-video" class="w-100 rounded-3 shadow-sm" style="max-height: 400px; display: none;"></video>
                            <canvas id="qr-canvas" style="display: none;"></canvas>
                            
                            <!-- Overlay dengan crosshair -->
                            <div id="scanner-overlay" class="position-absolute top-0 start-0 w-100 h-100 rounded-3" style="display: none;">
                                <div class="scanner-crosshair">
                                    <div class="crosshair-corner top-left"></div>
                                    <div class="crosshair-corner top-right"></div>
                                    <div class="crosshair-corner bottom-left"></div>
                                    <div class="crosshair-corner bottom-right"></div>
                                </div>
                                <div class="scanner-line"></div>
                            </div>
                            
                            <!-- Placeholder when camera not active -->
                            <div id="camera-placeholder" class="text-center py-5">
                                <i class="bi bi-camera-video display-1 text-muted mb-3"></i>
                                <p class="text-muted">Klik tombol untuk memulai kamera</p>
                            </div>
                        </div>
                    </div>

                    <!-- Scanner Controls -->
                    <div class="text-center mb-3">
                        <button id="start-scan" class="btn btn-success btn-lg me-2">
                            <i class="bi bi-camera-fill me-2"></i>Mulai Scan
                        </button>
                        <button id="stop-scan" class="btn btn-danger btn-lg" style="display: none;">
                            <i class="bi bi-stop-circle me-2"></i>Berhenti
                        </button>
                    </div>

                    <!-- Manual Input Option -->
                    <div class="mt-4">
                        <label class="form-label fw-bold">Atau masukkan kode QR secara manual:</label>
                        <div class="input-group">
                            <input type="text" id="manual-qr" class="form-control form-control-lg" placeholder="Masukkan kode QR...">
                            <button id="validate-manual" class="btn btn-primary">
                                <i class="bi bi-search me-2"></i>Cari
                            </button>
                        </div>
                    </div>

                    <!-- Scan Status -->
                    <div id="scan-status" class="mt-3" style="display: none;">
                        <div class="alert alert-info d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm me-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div>Memvalidasi QR Code...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Result Section -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #6f42c1, #8b5cf6);">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle-fill me-2"></i>Hasil Scan
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Default State -->
                    <div id="default-state" class="text-center py-5">
                        <i class="bi bi-qr-code display-1 text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada QR Code yang di-scan</h5>
                        <p class="text-muted">Gunakan kamera atau masukkan kode secara manual</p>
                    </div>

                    <!-- Success Result -->
                    <div id="scan-result" style="display: none;">
                        <div class="alert alert-success border-0 shadow-sm">
                            <h6 class="alert-heading">
                                <i class="bi bi-check-circle-fill me-2"></i>QR Code Valid!
                            </h6>
                            <p class="mb-0">Barang ditemukan dan tersedia untuk dipinjam</p>
                        </div>

                        <!-- Barang Details -->
                        <div class="barang-details">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary" id="barang-nama">-</h6>
                                            <div class="row g-2 small">
                                                <div class="col-sm-6">
                                                    <strong>Merk:</strong> <span id="barang-merk">-</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <strong>Kelompok:</strong> <span id="barang-kelompok">-</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <strong>Kondisi:</strong> <span id="barang-kondisi">-</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <strong>QR Code:</strong> <code id="barang-qr">-</code>
                                                </div>
                                                <div class="col-12" id="spek-details" style="display: none;">
                                                    <small class="text-muted">
                                                        <div id="barang-processor"></div>
                                                        <div id="barang-memori"></div>
                                                        <div id="barang-hardisk"></div>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Peminjaman Form -->
                        <div class="mt-4">
                            <form id="pinjam-form">
                                <input type="hidden" id="barang-id" name="barang_id">
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Tanggal Pinjam <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="tanggal_pinjam" required min="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Tanggal Kembali <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="tanggal_kembali" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Keperluan <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="keperluan" rows="3" placeholder="Jelaskan untuk apa barang ini dipinjam..." required minlength="10"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Penanggung Jawab <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="penanggung_jawab" placeholder="Nama penanggung jawab..." required minlength="3">
                                    </div>
                                </div>

                                <div class="mt-4 d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-send-fill me-2"></i>Ajukan Peminjaman
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Error Result -->
                    <div id="scan-error" style="display: none;">
                        <div class="alert alert-danger border-0 shadow-sm">
                            <h6 class="alert-heading">
                                <i class="bi bi-x-circle-fill me-2"></i>QR Code Tidak Valid
                            </h6>
                            <p class="mb-0" id="error-message">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #fd7e14, #f8961e);">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Riwayat Peminjaman Saya
                    </h5>
                </div>
                <div class="card-body">
                    <div id="history-container">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-5">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-success mb-3">Pengajuan Berhasil!</h4>
                <p class="text-muted mb-4">Pengajuan peminjaman Anda telah dikirim dan menunggu verifikasi admin.</p>
                <div class="alert alert-light border">
                    <strong>Kode Peminjaman:</strong><br>
                    <code id="modal-kode-peminjaman" class="fs-5">-</code>
                </div>
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- CSS Styles -->
<style>
/* Scanner Styles */
.scanner-container {
    position: relative;
    min-height: 300px;
}

#scanner-area {
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    overflow: hidden;
    background: #f8f9fa;
}

.scanner-crosshair {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 250px;
    height: 250px;
    border: 2px solid rgba(255, 255, 255, 0.8);
    border-radius: 12px;
}

.crosshair-corner {
    position: absolute;
    width: 30px;
    height: 30px;
    border: 3px solid #28a745;
}

.crosshair-corner.top-left {
    top: -3px;
    left: -3px;
    border-right: none;
    border-bottom: none;
    border-radius: 12px 0 0 0;
}

.crosshair-corner.top-right {
    top: -3px;
    right: -3px;
    border-left: none;
    border-bottom: none;
    border-radius: 0 12px 0 0;
}

.crosshair-corner.bottom-left {
    bottom: -3px;
    left: -3px;
    border-right: none;
    border-top: none;
    border-radius: 0 0 0 12px;
}

.crosshair-corner.bottom-right {
    bottom: -3px;
    right: -3px;
    border-left: none;
    border-top: none;
    border-radius: 0 0 12px 0;
}

.scanner-line {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #28a745, transparent);
    animation: scanLine 2s linear infinite;
}

@keyframes scanLine {
    0% { top: 0; }
    100% { top: 100%; }
}

/* Card Gradients */
.bg-gradient {
    background: linear-gradient(135deg, var(--bs-primary), var(--bs-info)) !important;
}

/* Success Animation */
@keyframes checkmark {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.animate-check {
    animation: checkmark 0.6s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
    .scanner-crosshair {
        width: 200px;
        height: 200px;
    }
    
    .crosshair-corner {
        width: 20px;
        height: 20px;
    }
}
</style>

<!-- QR Code Scanner Scripts -->
<script src="https://unpkg.com/@zxing/library@latest/umd/index.min.js"></script>

<script>
let codeReader = null;
let scanning = false;
let currentBarangData = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize QR Code Reader
    codeReader = new ZXing.BrowserQRCodeReader();
    
    // Event listeners
    document.getElementById('start-scan').addEventListener('click', startScanning);
    document.getElementById('stop-scan').addEventListener('click', stopScanning);
    document.getElementById('validate-manual').addEventListener('click', validateManualQR);
    document.getElementById('pinjam-form').addEventListener('submit', submitPinjam);
    
    // Load history
    loadHistory();
});

async function startScanning() {
    try {
        const videoElement = document.getElementById('qr-video');
        const overlayElement = document.getElementById('scanner-overlay');
        const placeholderElement = document.getElementById('camera-placeholder');
        const startBtn = document.getElementById('start-scan');
        const stopBtn = document.getElementById('stop-scan');
        
        // Show video and overlay, hide placeholder
        videoElement.style.display = 'block';
        overlayElement.style.display = 'block';
        placeholderElement.style.display = 'none';
        
        // Update buttons
        startBtn.style.display = 'none';
        stopBtn.style.display = 'inline-block';
        
        scanning = true;
        
        // Start scanning
        const result = await codeReader.decodeFromVideoDevice(null, videoElement, (result, err) => {
            if (result && scanning) {
                // QR Code detected
                const qrData = result.getText();
                console.log('QR Code detected:', qrData);
                stopScanning();
                validateQR(qrData);
            }
            if (err && !(err instanceof ZXing.NotFoundException)) {
                console.error('Scanner error:', err);
            }
        });
        
    } catch (err) {
        console.error('Error starting camera:', err);
        showAlert('error', 'Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
        stopScanning();
    }
}

function stopScanning() {
    scanning = false;
    
    if (codeReader) {
        codeReader.reset();
    }
    
    const videoElement = document.getElementById('qr-video');
    const overlayElement = document.getElementById('scanner-overlay');
    const placeholderElement = document.getElementById('camera-placeholder');
    const startBtn = document.getElementById('start-scan');
    const stopBtn = document.getElementById('stop-scan');
    
    // Hide video and overlay, show placeholder
    videoElement.style.display = 'none';
    overlayElement.style.display = 'none';
    placeholderElement.style.display = 'block';
    
    // Update buttons
    startBtn.style.display = 'inline-block';
    stopBtn.style.display = 'none';
}

function validateManualQR() {
    const qrData = document.getElementById('manual-qr').value.trim();
    if (!qrData) {
        showAlert('warning', 'Silakan masukkan kode QR terlebih dahulu');
        return;
    }
    validateQR(qrData);
}

async function validateQR(qrData) {
    showScanStatus(true);
    hideAllResults();
    
    try {
        const response = await fetch('<?= base_url("user/scan/validateQR") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `qr_data=${encodeURIComponent(qrData)}`
        });
        
        const data = await response.json();
        
        showScanStatus(false);
        
        if (data.success) {
            showSuccessResult(data.barang);
            currentBarangData = data.barang;
        } else {
            showErrorResult(data.message);
        }
        
    } catch (error) {
        console.error('Error validating QR:', error);
        showScanStatus(false);
        showErrorResult('Terjadi kesalahan saat memvalidasi QR Code');
    }
}

function showSuccessResult(barang) {
    document.getElementById('default-state').style.display = 'none';
    document.getElementById('scan-error').style.display = 'none';
    document.getElementById('scan-result').style.display = 'block';
    
    // Populate barang details
    document.getElementById('barang-id').value = barang.id;
    document.getElementById('barang-nama').textContent = barang.nama_barang || '-';
    document.getElementById('barang-merk').textContent = barang.merk || '-';
    document.getElementById('barang-kelompok').textContent = barang.kelompok || '-';
    document.getElementById('barang-kondisi').textContent = barang.kondisi || '-';
    document.getElementById('barang-qr').textContent = barang.qr_code || '-';
    
    // Show specs if available
    const hasSpecs = barang.processor || barang.memori || barang.hardisk;
    if (hasSpecs) {
        document.getElementById('spek-details').style.display = 'block';
        document.getElementById('barang-processor').textContent = barang.processor ? `Processor: ${barang.processor}` : '';
        document.getElementById('barang-memori').textContent = barang.memori ? `Memori: ${barang.memori}` : '';
        document.getElementById('barang-hardisk').textContent = barang.hardisk ? `Hardisk: ${barang.hardisk}` : '';
    } else {
        document.getElementById('spek-details').style.display = 'none';
    }
    
    // Clear form
    document.getElementById('pinjam-form').reset();
    document.getElementById('barang-id').value = barang.id;
}

function showErrorResult(message) {
    document.getElementById('default-state').style.display = 'none';
    document.getElementById('scan-result').style.display = 'none';
    document.getElementById('scan-error').style.display = 'block';
    
    document.getElementById('error-message').textContent = message;
}

function hideAllResults() {
    document.getElementById('default-state').style.display = 'none';
    document.getElementById('scan-result').style.display = 'none';
    document.getElementById('scan-error').style.display = 'none';
}

function showScanStatus(show) {
    document.getElementById('scan-status').style.display = show ? 'block' : 'none';
}

async function submitPinjam(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    
    try {
        const response = await fetch('<?= base_url("user/scan/submitPinjam") ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success modal
            document.getElementById('modal-kode-peminjaman').textContent = data.kode_peminjaman;
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            
            // Reset form and results
            form.reset();
            hideAllResults();
            document.getElementById('default-state').style.display = 'block';
            document.getElementById('manual-qr').value = '';
            
            // Reload history
            loadHistory();
            
        } else {
            showAlert('error', data.message);
        }
        
    } catch (error) {
        console.error('Error submitting pinjam:', error);
        showAlert('error', 'Terjadi kesalahan saat mengirim pengajuan');
    } finally {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-send-fill me-2"></i>Ajukan Peminjaman';
    }
}

async function loadHistory() {
    try {
        const response = await fetch('<?= base_url("user/scan/getMyHistory") ?>', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayHistory(data.history);
        } else {
            document.getElementById('history-container').innerHTML = '<p class="text-muted text-center">Tidak dapat memuat riwayat</p>';
        }
        
    } catch (error) {
        console.error('Error loading history:', error);
        document.getElementById('history-container').innerHTML = '<p class="text-muted text-center">Gagal memuat riwayat</p>';
    }
}

function displayHistory(history) {
    const container = document.getElementById('history-container');
    
    if (history.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">Belum ada riwayat peminjaman</p>';
        return;
    }
    
    let html = '<div class="table-responsive">';
    html += '<table class="table table-hover">';
    html += '<thead class="table-light">';
    html += '<tr>';
    html += '<th>Tanggal</th>';
    html += '<th>Barang</th>';
    html += '<th>Jadwal</th>';
    html += '<th>Keperluan</th>';
    html += '<th>Status</th>';
    html += '<th>Aksi</th>'; // ← TAMBAH KOLOM AKSI
    html += '</tr>';
    html += '</thead><tbody>';
    
    history.forEach(item => {
        const statusClass = getStatusClass(item.status);
        const statusText = getStatusText(item.status);
        
        html += `<tr>
            <td>
                <small>${formatDateTime(item.created_at)}</small>
            </td>
            <td>
                <div class="fw-bold text-primary">${item.nama_barang || 'Barang ID ' + item.barang_id}</div>
                <small class="text-muted">Merk: ${item.merk || '-'}</small>
            </td>
            <td>
                <div><strong>${formatDate(item.tanggal)}</strong></div>
                <small class="text-muted">
                    <i class="bi bi-clock me-1"></i>
                    ${item.waktu_mulai || '08:00'} - ${item.waktu_selesai || '17:00'}
                </small>
            </td>
            <td>
                <small>${item.keperluan || '-'}</small>
            </td>
            <td>
                <span class="badge ${statusClass}">${statusText}</span>
                ${item.keterangan_status ? `<br><small class="text-muted fst-italic">${item.keterangan_status}</small>` : ''}
            </td>
            <td>
                ${getActionButtons(item)}
            </td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

// UPDATE function getStatusClass untuk status scan barang
function getStatusClass(status) {
    const classes = {
        'diajukan': 'bg-warning text-dark',
        'dipinjam': 'bg-success',
        'ditolak': 'bg-danger',
        'proses_pengembalian': 'bg-info',
        'selesai': 'bg-primary'
    };
    return classes[status] || 'bg-secondary';
}

// UPDATE function getStatusText untuk status scan barang
function getStatusText(status) {
    const texts = {
        'diajukan': 'Menunggu Verifikasi',
        'dipinjam': 'Sedang Dipinjam',
        'ditolak': 'Ditolak',
        'proses_pengembalian': 'Proses Pengembalian',
        'selesai': 'Selesai'
    };
    return texts[status] || status;
}

// TAMBAH function untuk generate action buttons
function getActionButtons(item) {
    if (item.status === 'dipinjam') {
        return `
            <button class="btn btn-sm btn-warning" 
                    onclick="kembalikanBarang(${item.id})"
                    title="Kembalikan barang ini">
                <i class="bi bi-arrow-return-left me-1"></i>Kembalikan
            </button>
        `;
    } else if (item.status === 'proses_pengembalian') {
        return `
            <small class="text-info">
                <i class="bi bi-hourglass-split me-1"></i>
                Menunggu verifikasi
            </small>
        `;
    } else if (item.status === 'diajukan') {
        return `
            <small class="text-warning">
                <i class="bi bi-clock me-1"></i>
                Menunggu persetujuan
            </small>
        `;
    } else {
        return '<small class="text-muted">-</small>';
    }
}

// TAMBAH function kembalikanBarang
function kembalikanBarang(pinjamId) {
    Swal.fire({
        title: 'Konfirmasi Pengembalian',
        text: 'Apakah Anda yakin ingin mengembalikan barang ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Kembalikan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang memproses pengembalian',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send AJAX request
            const formData = new FormData();
            formData.append('pinjam_id', pinjamId);

            fetch('<?= base_url('/user/barang/kembalikan') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        // Reload history untuk update data
                        loadHistory();
                    });
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message,
                    confirmButtonText: 'Tutup'
                });
            });
        }
    });
}

function formatDate(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID');
}

function formatDateTime(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('id-ID');
}

function showAlert(type, message) {
    const alertClass = type === 'error' ? 'danger' : type;
    const alert = `
        <div class="alert alert-${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.getElementById('alertContainer').innerHTML = alert;
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        const alertElement = document.querySelector('#alertContainer .alert');
        if (alertElement) {
            const bsAlert = new bootstrap.Alert(alertElement);
            bsAlert.close();
        }
    }, 5000);
}
</script>

<?= $this->endSection() ?>