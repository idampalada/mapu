// JAVASCRIPT SIMPLE UNTUK MODAL BARANG SCAN - PERBAIKAN

// Global variables untuk modal scan
let currentPeminjamanScanId = null;
let eventListenersAttached = false;

// Initialize saat document ready - PINDAHKAN EVENT LISTENER KE SINI
document.addEventListener("DOMContentLoaded", function () {
  // Attach event listener untuk tab sekali saja
  const pengembalianTab = document.getElementById("pengembalian-barang-tab");
  if (pengembalianTab && !eventListenersAttached) {
    pengembalianTab.addEventListener("click", function () {
      loadPengembalianScan();
    });
    eventListenersAttached = true;
  }
});

// Initialize saat modal dibuka - PERBAIKAN
function showVerifikasiBarang() {
  const modal = new bootstrap.Modal(
    document.getElementById("modalVerifikasiBarang")
  );
  modal.show();

  // Load data peminjaman pending langsung
  loadPendingScan();

  // JANGAN tambah event listener di sini - sudah ada di DOMContentLoaded
}

// Load pending peminjaman scan - PERBAIKAN ERROR HANDLING
async function loadPendingScan() {
  const container = document.getElementById("pending-scan-container");
  const countBadge = document.getElementById("pending-scan-count");

  // Show loading
  container.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat data peminjaman...</p>
        </div>
    `;

  try {
    console.log("🔍 Fetching pending scan data..."); // Debug log

    const response = await fetch("/admin/user/barang/getPendingScan", {
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    });

    console.log("📡 Response status:", response.status); // Debug log

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    const data = await response.json();
    console.log("📊 Response data:", data); // Debug log

    if (data.success) {
      displayPendingScan(data.data);
      if (countBadge) {
        countBadge.textContent = data.data.length;
      }
      console.log("✅ Data loaded successfully:", data.data.length + " items"); // Debug log
    } else {
      throw new Error(data.message || "Response success = false");
    }
  } catch (error) {
    console.error("❌ Error loading pending scan:", error);

    container.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Gagal memuat data:</strong> ${error.message}
                <br><small>Periksa console untuk detail error</small>
            </div>
        `;

    if (countBadge) {
      countBadge.textContent = "!";
      countBadge.className = "badge bg-warning"; // Change to warning color
    }
  }
}

// Load pengembalian scan - PERBAIKAN ERROR HANDLING
async function loadPengembalianScan() {
  const container = document.getElementById("pengembalian-scan-container");
  const countBadge = document.getElementById("pengembalian-scan-count");

  // Show loading
  container.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat data pengembalian...</p>
        </div>
    `;

  try {
    console.log("🔍 Fetching pengembalian scan data..."); // Debug log

    const response = await fetch("/admin/user/barang/getPengembalianScan", {
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    });

    console.log("📡 Pengembalian response status:", response.status); // Debug log

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    const data = await response.json();
    console.log("📊 Pengembalian data:", data); // Debug log

    if (data.success) {
      displayPengembalianScan(data.data);
      if (countBadge) {
        countBadge.textContent = data.data.length;
      }
      console.log("✅ Pengembalian data loaded:", data.data.length + " items"); // Debug log
    } else {
      throw new Error(data.message || "Response success = false");
    }
  } catch (error) {
    console.error("❌ Error loading pengembalian scan:", error);

    container.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Gagal memuat data:</strong> ${error.message}
                <br><small>Periksa console untuk detail error</small>
            </div>
        `;

    if (countBadge) {
      countBadge.textContent = "!";
      countBadge.className = "badge bg-warning";
    }
  }
}

// Display pending peminjaman scan - SAMA SEPERTI SEBELUMNYA
function displayPendingScan(data) {
  const container = document.getElementById("pending-scan-container");

  if (data.length === 0) {
    container.innerHTML =
      '<div class="alert alert-info">Tidak ada peminjaman barang yang menunggu verifikasi</div>';
    return;
  }

  let html = '<div class="table-responsive">';
  html += '<table class="table table-bordered">';
  html += "<thead>";
  html += "<tr>";
  html += "<th>Tanggal</th>";
  html += "<th>Nama Peminjam</th>";
  html += "<th>Barang</th>";
  html += "<th>Keperluan</th>";
  html += "<th>Jadwal</th>";
  html += "<th>Status</th>";
  html += "<th>Aksi</th>";
  html += "</tr>";
  html += "</thead>";
  html += "<tbody>";

  data.forEach((item) => {
    html += "<tr>";
    html += `<td>${formatDate(item.created_at)}</td>`;
    html += `<td>${item.nama_peminjam}</td>`;
    html += `<td>${item.nama_barang}</td>`;
    html += `<td>${item.keperluan}</td>`;
    html += `<td>`;
    html += `${formatDate(item.tanggal)}<br>`;
    html += `${item.waktu_mulai} - ${item.waktu_selesai}`;
    html += `</td>`;
    html += `<td><span class="badge bg-warning">Pending</span></td>`;
    html += `<td>`;
    html += `<button class="btn btn-sm btn-success" onclick="verifikasiPeminjamanBarang(${item.id}, 'disetujui')">Setujui</button> `;
    html += `<button class="btn btn-sm btn-danger" data-tipe="barang" data-id="${item.id}" onclick="showTolakModal('barang', ${item.id})">Tolak</button>`;
    html += `</td>`;
    html += "</tr>";
  });

  html += "</tbody></table></div>";
  container.innerHTML = html;
}

// Display pengembalian scan - SAMA SEPERTI SEBELUMNYA
// UPDATE method displayPengembalianScan di index.php JavaScript

// Display pengembalian scan - SIMPLE VERSION seperti peminjaman
// UPDATE displayPengembalianScan untuk admin dashboard - DENGAN KOLOM TANGGAL & JAM KEMBALI
// SIMPLE FIX - GANTI FUNCTION displayPengembalianScan DENGAN ONCLICK YANG BENAR

function displayPengembalianScan(data) {
  const container = document.getElementById("pengembalian-scan-container");

  if (data.length === 0) {
    container.innerHTML =
      '<div class="alert alert-info mt-3">Tidak ada pengembalian barang yang pending saat ini.</div>';
    return;
  }

  let html = '<div class="table-responsive">';
  html += '<table class="table table-striped">';
  html += "<thead>";
  html += "<tr>";
  html += "<th>Nama Peminjam</th>";
  html += "<th>Barang</th>";
  html += "<th>Kondisi</th>";
  html += "<th>Tanggal Kembali</th>";
  html += "<th>Jam Kembali</th>";
  html += "<th>Lihat Foto</th>";
  html += "<th>Aksi</th>";
  html += "</tr>";
  html += "</thead>";
  html += "<tbody>";

  data.forEach((item) => {
    // Format tanggal dan jam kembali
    let tanggalKembali = "-";
    let jamKembali = "-";

    if (item.tanggal_kembali) {
      const dateObj = new Date(item.tanggal_kembali);
      tanggalKembali = dateObj.toLocaleDateString("id-ID");
      jamKembali = dateObj.toLocaleTimeString("id-ID");
    }

    // Badge kondisi
    let kondisiBadge = "bg-secondary";
    if (item.kondisi_pengembalian === "Baik") kondisiBadge = "bg-success";
    else if (item.kondisi_pengembalian === "Rusak Ringan")
      kondisiBadge = "bg-warning text-dark";
    else if (item.kondisi_pengembalian === "Rusak Berat")
      kondisiBadge = "bg-danger";

    // Foto button dengan escape JSON yang benar
    let fotoButton = '<span class="text-muted">-</span>';
    if (item.foto_pengembalian) {
      try {
        const fotoArray = JSON.parse(item.foto_pengembalian);
        if (fotoArray && fotoArray.length > 0) {
          // Escape JSON untuk onclick
          const escapedJson = item.foto_pengembalian.replace(/"/g, "&quot;");
          fotoButton = `<button class="btn btn-sm btn-outline-primary" onclick="showFotoModal('${escapedJson}')">
                                     <i class="bi bi-images me-1"></i>${fotoArray.length} Foto
                                  </button>`;
        }
      } catch (e) {
        console.error("Error parsing foto_pengembalian:", e);
      }
    }

    html += "<tr>";
    html += `<td>
                    <div class="fw-bold">${item.nama_peminjam}</div>
                    <small class="text-muted">${
                      item.email_peminjam || ""
                    }</small>
                 </td>`;
    html += `<td>
                    <div class="fw-bold text-primary">${item.nama_barang}</div>
                    <small class="text-muted">Dipinjam: ${formatDate(
                      item.tanggal
                    )}</small>
                 </td>`;
    html += `<td>
                    <span class="badge ${kondisiBadge}">${
      item.kondisi_pengembalian || "Tidak disebutkan"
    }</span>
                    ${
                      item.keterangan
                        ? '<br><small class="text-muted mt-1 d-block">' +
                          item.keterangan +
                          "</small>"
                        : ""
                    }
                 </td>`;
    html += `<td><strong>${tanggalKembali}</strong></td>`;
    html += `<td><strong>${jamKembali}</strong></td>`;
    html += `<td>${fotoButton}</td>`;
    html += `<td>
                    <button class="btn btn-sm btn-success me-1" onclick="verifikasiPengembalianBarang(${item.id}, 'disetujui')">Setujui</button>
                    <button class="btn btn-sm btn-danger" onclick="verifikasiPengembalianBarang(${item.id}, 'ditolak')">Tolak</button>
                 </td>`;
    html += "</tr>";
  });

  html += "</tbody></table></div>";
  container.innerHTML = html;
}

// OPTION 1: FIX PATH FOTO DENGAN BASE_URL YANG BENAR

function showFotoModal(fotoJson) {
  try {
    const unescapedJson = fotoJson.replace(/&quot;/g, '"');
    const fotoArray = JSON.parse(unescapedJson);

    if (!fotoArray || fotoArray.length === 0) {
      alert("Tidak ada foto untuk ditampilkan");
      return;
    }

    let modalContent = `
            <div class="modal fade" id="fotoModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Foto Pengembalian (${fotoArray.length} foto)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
        `;

    fotoArray.forEach((foto, index) => {
      // FIX: Gunakan controller untuk serve foto
      const fotoUrl = `/admin/user/barang/getFoto/${foto}`;

      modalContent += `
                <div class="col-md-3 col-6 mb-3">
                    <img src="${fotoUrl}" 
                         class="img-fluid rounded" 
                         style="width: 100%; height: 200px; object-fit: cover; cursor: pointer;"
                         onclick="window.open('${fotoUrl}', '_blank')"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjBmMGYwIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZpbGw9IiM5OTkiPkltYWdlIG5vdCBmb3VuZDwvdGV4dD48L3N2Zz4='">
                    <small class="d-block text-center mt-1">Foto ${
                      index + 1
                    }</small>
                </div>
            `;
    });

    modalContent += `
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

    // Remove existing modal
    const existingModal = document.getElementById("fotoModal");
    if (existingModal) {
      existingModal.remove();
    }

    // Add new modal
    document.body.insertAdjacentHTML("beforeend", modalContent);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById("fotoModal"));
    modal.show();
  } catch (error) {
    console.error("Error showing foto modal:", error);
    alert("Gagal menampilkan foto: " + error.message);
  }
}

// Function untuk lihat foto pengembalian - UPDATED
function lihatFotoPengembalian(pinjamId, fotoJson) {
  try {
    const fotoArray = JSON.parse(fotoJson);

    if (!fotoArray || fotoArray.length === 0) {
      alert("Tidak ada foto untuk ditampilkan");
      return;
    }

    let modalHtml = `
            <div class="modal fade" id="modalFotoPengembalian" tabindex="-1" aria-labelledby="modalFotoPengembalianLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalFotoPengembalianLabel">
                                <i class="bi bi-images me-2"></i>Dokumentasi Pengembalian Barang
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted mb-3">Total ${fotoArray.length} foto dokumentasi pengembalian barang</p>
                            <div class="row">
        `;

    fotoArray.forEach((foto, index) => {
      const fotoUrl =
        "<?= base_url() ?>/writable/uploads/barang_returns/" + foto;
      modalHtml += `
                <div class="col-md-4 col-6 mb-3">
                    <div class="card shadow-sm">
                        <img src="${fotoUrl}" class="card-img-top" style="height: 250px; object-fit: cover; cursor: pointer;" 
                             alt="Foto ${
                               index + 1
                             }" onclick="window.open('${fotoUrl}', '_blank')">
                        <div class="card-body py-2 text-center">
                            <small class="text-muted">Foto ${index + 1}</small>
                        </div>
                    </div>
                </div>
            `;
    });

    modalHtml += `
                            </div>
                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Tip:</strong> Klik pada foto untuk membuka dalam tab baru dan melihat detail lebih jelas.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

    // Remove existing modal
    const existingModal = document.getElementById("modalFotoPengembalian");
    if (existingModal) {
      existingModal.remove();
    }

    // Add modal to body
    document.body.insertAdjacentHTML("beforeend", modalHtml);

    // Show modal
    const modal = new bootstrap.Modal(
      document.getElementById("modalFotoPengembalian")
    );
    modal.show();
  } catch (error) {
    console.error("Error displaying photos:", error);
    alert("Gagal menampilkan foto");
  }
}

// Helper function untuk format tanggal yang konsisten
function formatDate(dateString) {
  if (!dateString) return "-";
  const date = new Date(dateString);
  return date.toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
}

function formatDateTime(dateString) {
  if (!dateString) return "-";
  const date = new Date(dateString);
  return date.toLocaleString("id-ID", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}
// UPDATE method verifikasiPengembalianBarang juga
function verifikasiPengembalianBarang(id, status) {
  console.log("verifikasiPengembalianBarang called:", id, status); // Debug log

  const statusText = status === "disetujui" ? "menyetujui" : "menolak";

  Swal.fire({
    title: "Konfirmasi",
    text: `Apakah Anda yakin ingin ${statusText} pengembalian barang ini?`,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: `Ya, ${status === "disetujui" ? "Setuju" : "Tolak"}`,
    cancelButtonText: "Batal",
    confirmButtonColor: status === "disetujui" ? "#198754" : "#dc3545",
    cancelButtonColor: "#6c757d",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Memproses...",
        text: "Mohon tunggu sebentar",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      const formData = new FormData();
      formData.append("id", id); // Note: untuk pengembalian pakai 'id'
      formData.append("status", status);

      fetch("/admin/user/barang/verifikasiPengembalian", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => response.json())
        .then((data) => {
          console.log("Pengembalian response:", data); // Debug log

          if (data.success) {
            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: data.message,
              showConfirmButton: false,
              timer: 1500,
            }).then(() => {
              // Refresh data
              loadPengembalianScan();
              loadStatistikScan(); // Jika ada method ini
            });
          } else {
            throw new Error(data.error || "Terjadi kesalahan saat verifikasi");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: error.message,
            confirmButtonText: "Tutup",
          });
        });
    }
  });
}
// SISA FUNCTION SAMA SEPERTI SEBELUMNYA...
// verifikasiPeminjamanBarang(), formatDate(), formatDateTime()

// Verifikasi peminjaman barang - PERBAIKAN URL
function verifikasiPeminjamanBarang(id, status) {
  if (status === "ditolak") {
    showTolakModal("barang", id);
    return;
  }

  Swal.fire({
    title: "Konfirmasi",
    text: "Apakah Anda yakin ingin menyetujui peminjaman barang ini?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Ya, Setujui",
    cancelButtonText: "Batal",
    confirmButtonColor: "#198754",
    cancelButtonColor: "#dc3545",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Memproses...",
        text: "Mohon tunggu sebentar",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      const formData = new FormData();
      formData.append("pinjam_id", id);
      formData.append("status", "disetujui");

      // ✅ PERBAIKAN: Gunakan URL yang benar sesuai routes
      fetch("/admin/user/barang/verifikasiPeminjaman", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: data.message || "Peminjaman barang telah disetujui.",
              showConfirmButton: false,
              timer: 1500,
            }).then(() => {
              // Refresh data
              loadPendingScan();
            });
          } else {
            throw new Error(data.error || "Terjadi kesalahan saat verifikasi");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: error.message,
            confirmButtonText: "Tutup",
          });
        });
    }
  });
}

// Helper functions
function formatDate(dateString) {
  if (!dateString) return "-";
  return new Date(dateString).toLocaleDateString("id-ID");
}

function formatDateTime(dateString) {
  if (!dateString) return "-";
  const date = new Date(dateString);
  return (
    date.toLocaleDateString("id-ID") + " " + date.toLocaleTimeString("id-ID")
  );
}

// DEBUG FUNCTION - PASTE DI CONSOLE BROWSER UNTUK TEST
function debugFotoPreview() {
  console.log("=== DEBUG FOTO PREVIEW ===");

  // Test 1: Cek apakah function ada
  if (typeof lihatFotoPengembalian === "function") {
    console.log("✅ Function lihatFotoPengembalian exists");
  } else {
    console.log("❌ Function lihatFotoPengembalian NOT FOUND!");
    return;
  }

  // Test 2: Cek button yang diklik
  const buttons = document.querySelectorAll(
    '[onclick*="lihatFotoPengembalian"]'
  );
  console.log("📷 Found foto buttons:", buttons.length);

  if (buttons.length > 0) {
    const button = buttons[0];
    const onclickAttr = button.getAttribute("onclick");
    console.log("🔗 Button onclick:", onclickAttr);

    // Extract parameters from onclick
    const match = onclickAttr.match(
      /lihatFotoPengembalian\('(\d+)',\s*'(.+)'\)/
    );
    if (match) {
      const pinjamId = match[1];
      const fotoJson = match[2];
      console.log("🆔 Pinjam ID:", pinjamId);
      console.log("📸 Foto JSON:", fotoJson);

      try {
        const fotoArray = JSON.parse(fotoJson);
        console.log("📁 Parsed foto array:", fotoArray);

        // Test foto URLs
        fotoArray.forEach((foto, index) => {
          const fotoUrl = `/writable/uploads/barang_returns/${foto}`;
          console.log(`📸 Foto ${index + 1} URL:`, fotoUrl);
        });
      } catch (e) {
        console.log("❌ Error parsing foto JSON:", e.message);
      }
    }
  }
}

// Manual test function
function testLihatFoto() {
  // Ambil data dari button yang ada
  const button = document.querySelector('[onclick*="lihatFotoPengembalian"]');
  if (button) {
    const onclickAttr = button.getAttribute("onclick");
    console.log("Testing with onclick:", onclickAttr);

    // Execute onclick manually
    try {
      eval(onclickAttr);
    } catch (e) {
      console.error("Error executing onclick:", e);
    }
  }
}

console.log("Debug functions loaded. Run:");
console.log("debugFotoPreview() - untuk debug");
console.log("testLihatFoto() - untuk test manual");
