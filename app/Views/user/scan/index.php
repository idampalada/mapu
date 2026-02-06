<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<title>Scan Barcode - Peminjaman Barang</title>


<!-- Simple Header Section -->
<div class="header-section py-3 px-3 mb-4">
    <div class="row align-items-center">
        <div class="col-12">
            <h4 class="text-dark mb-1 fw-bold">Scan Barcode - Peminjaman Barang</h4>
            <p class="text-muted mb-0">Scan QR Code untuk peminjaman barang dengan mudah</p>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Alert Messages -->
    <div id="alertContainer"></div>
    
    <div class="row">
        <!-- Scanner Section -->
        <div class="col-lg-6 mb-4">
            <div class="card border shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 text-dark fw-semibold">
                        <i class="bi bi-camera-fill me-2 text-primary"></i>QR Code Scanner
                    </h6>
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
                        <button id="start-scan" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-camera-fill me-2"></i>Mulai Scan
                        </button>
                        <button id="stop-scan" class="btn btn-secondary btn-lg" style="display: none;">
                            <i class="bi bi-stop-circle me-2"></i>Berhenti
                        </button>
                    </div>

                    <!-- Manual Input Option -->
                    <div class="mt-4">
                        <label class="form-label fw-bold">Atau masukkan kode QR secara manual:</label>
                        <div class="input-group">
                            <input type="text" id="manual-qr" class="form-control form-control-lg" placeholder="Masukkan kode QR...">
                            <button id="validate-manual" class="btn btn-outline-primary">
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
            <div class="card border shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 text-dark fw-semibold">
                        <i class="bi bi-info-circle-fill me-2 text-info"></i>Hasil Scan
                    </h6>
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
            <div class="card border shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 text-dark fw-semibold">
                        <i class="bi bi-clock-history me-2 text-warning"></i>Riwayat Peminjaman Saya
                    </h6>
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
    border: 3px solid #3182ce;
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
    background: linear-gradient(90deg, transparent, #3182ce, transparent);
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

// COMPLETE REPLACEMENT - COPY PASTE LANGSUNG
function kembalikanBarang(pinjamId) {
    // Buat modal form pengembalian dengan camera interface
    const modalHtml = `
        <div class="modal fade" id="modalPengembalianBarang" tabindex="-1" aria-labelledby="modalPengembalianBarangLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPengembalianBarangLabel">
                            <i class="bi bi-arrow-return-left me-2"></i>Form Pengembalian Barang
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formPengembalianBarang">
                        <div class="modal-body">
                            <input type="hidden" name="pinjam_id" value="${pinjamId}">
                            
                            <!-- Kondisi Barang -->
                            <div class="mb-3">
                                <label for="kondisi_barang" class="form-label fw-bold">
                                    Kondisi Barang <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="kondisi_barang" name="kondisi_barang" required>
                                    <option value="">Pilih kondisi barang</option>
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>

                            <!-- Keterangan -->
                            <div class="mb-3">
                                <label for="keterangan_pengembalian" class="form-label fw-bold">
                                    Keterangan Pengembalian
                                </label>
                                <textarea class="form-control" id="keterangan_pengembalian" name="keterangan" rows="3" 
                                          placeholder="Jelaskan kondisi barang..."></textarea>
                            </div>

                            <!-- Camera Interface -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Foto Barang <span class="text-danger">*</span>
                                </label>
                                
                                <!-- Camera Controls -->
                                <div class="d-flex gap-2 mb-3">
                                    <button type="button" class="btn btn-primary" id="startCamera">
                                        <i class="bi bi-camera-fill me-1"></i>Buka Kamera
                                    </button>
                                    <button type="button" class="btn btn-success" id="capturePhoto" style="display:none;">
                                        <i class="bi bi-camera2 me-1"></i>Ambil Foto
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="stopCamera" style="display:none;">
                                        <i class="bi bi-stop-circle me-1"></i>Tutup Kamera
                                    </button>
                                </div>
                                
                                <!-- Camera Video -->
                                <div id="cameraContainer" class="mb-3" style="display:none;">
                                    <video id="cameraVideo" class="w-100 rounded" style="max-height: 300px;" autoplay playsinline></video>
                                    <canvas id="cameraCanvas" style="display:none;"></canvas>
                                </div>
                                
                                <!-- Photo Preview -->
                                <div id="photoPreview" class="mb-3">
                                    <div class="row" id="capturedPhotos"></div>
                                </div>
                                
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> 
                                    Ambil foto barang saat pengembalian (maksimal 5 foto)
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-warning" id="btnSubmitPengembalian">
                                <i class="bi bi-arrow-return-left me-1"></i>Kembalikan Barang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal
    const existingModal = document.getElementById('modalPengembalianBarang');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('modalPengembalianBarang'));
    modal.show();
    
    // Setup camera functionality
    setupCameraControls();
    
    // Setup form submit
    document.getElementById('formPengembalianBarang').addEventListener('submit', submitPengembalianBarang);
}

// Global variables for camera
let currentStream = null;
let capturedPhotos = [];

// Setup camera controls
function setupCameraControls() {
    const startBtn = document.getElementById('startCamera');
    const captureBtn = document.getElementById('capturePhoto');
    const stopBtn = document.getElementById('stopCamera');
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    const container = document.getElementById('cameraContainer');
    
    // Start Camera
    startBtn.addEventListener('click', async function() {
        try {
            const constraints = {
                video: {
                    facingMode: 'environment',
                    width: { ideal: 1920 },
                    height: { ideal: 1080 }
                }
            };
            
            currentStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = currentStream;
            
            container.style.display = 'block';
            startBtn.style.display = 'none';
            captureBtn.style.display = 'inline-block';
            stopBtn.style.display = 'inline-block';
            
        } catch (error) {
            console.error('Error accessing camera:', error);
            alert('Tidak dapat mengakses kamera. Pastikan memberikan izin akses kamera.');
        }
    });
    
    // Capture Photo
    captureBtn.addEventListener('click', function() {
        if (capturedPhotos.length >= 5) {
            alert('Maksimal 5 foto saja');
            return;
        }
        
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0);
        
        canvas.toBlob(function(blob) {
            const photoFile = new File([blob], `foto_${Date.now()}.jpg`, { type: 'image/jpeg' });
            capturedPhotos.push(photoFile);
            displayCapturedPhotos();
        }, 'image/jpeg', 0.8);
    });
    
    // Stop Camera
    stopBtn.addEventListener('click', function() {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
        
        container.style.display = 'none';
        startBtn.style.display = 'inline-block';
        captureBtn.style.display = 'none';
        stopBtn.style.display = 'none';
    });
}

// Display captured photos
function displayCapturedPhotos() {
    const container = document.getElementById('capturedPhotos');
    container.innerHTML = '';
    
    capturedPhotos.forEach((photo, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-md-4 col-6 mb-2';
            col.innerHTML = `
                <div class="position-relative">
                    <img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 120px; object-fit: cover;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" 
                            onclick="removePhoto(${index})" style="margin: 2px;">
                        <i class="bi bi-x"></i>
                    </button>
                    <small class="text-center d-block mt-1">Foto ${index + 1}</small>
                </div>
            `;
            container.appendChild(col);
        };
        reader.readAsDataURL(photo);
    });
}

// Remove photo
function removePhoto(index) {
    capturedPhotos.splice(index, 1);
    displayCapturedPhotos();
}

// Submit form with captured photos
async function submitPengembalianBarang(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData();
    const submitBtn = document.getElementById('btnSubmitPengembalian');
    
    const kondisi = document.getElementById('kondisi_barang').value;
    const keterangan = document.getElementById('keterangan_pengembalian').value;
    const pinjamId = form.querySelector('input[name="pinjam_id"]').value;
    
    if (!kondisi) {
        alert('Pilih kondisi barang terlebih dahulu');
        return;
    }
    
    if (capturedPhotos.length === 0) {
        alert('Ambil minimal 1 foto barang');
        return;
    }
    
    formData.append('pinjam_id', pinjamId);
    formData.append('kondisi_barang', kondisi);
    formData.append('keterangan', keterangan);
    
    capturedPhotos.forEach((photo, index) => {
        formData.append('foto_barang[]', photo);
    });
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';
    
    try {
        const response = await fetch('/user/barang/kembalikanWithForm', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
            }
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalPengembalianBarang'));
            modal.hide();
            
            capturedPhotos = [];
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: `Pengembalian berhasil diajukan dengan dokumentasi foto`,
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                loadHistory();
            });
            
        } else {
            throw new Error(data.message || 'Terjadi kesalahan');
        }
        
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: error.message,
            confirmButtonText: 'Tutup'
        });
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-arrow-return-left me-1"></i>Kembalikan Barang';
    }
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