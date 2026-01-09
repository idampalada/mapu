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
function displayPengembalianScan(data) {
  const container = document.getElementById("pengembalian-scan-container");

  if (data.length === 0) {
    container.innerHTML =
      '<div class="alert alert-info mt-3">Tidak ada pengembalian barang yang pending saat ini.</div>';
    return;
  }

  let html = '<div class="table-responsive">';
  html += '<table class="table">';
  html += "<thead>";
  html += "<tr>";
  html += "<th>Tanggal</th>";
  html += "<th>Nama Peminjam</th>";
  html += "<th>Barang</th>";
  html += "<th>Status</th>";
  html += "<th>Tanggal Pinjam</th>";
  html += "<th>Tanggal Kembali</th>";
  html += "<th>Aksi</th>";
  html += "</tr>";
  html += "</thead>";
  html += "<tbody>";

  data.forEach((item) => {
    html += "<tr>";
    html += `<td>${formatDate(item.created_at)}</td>`;
    html += `<td>${item.nama_peminjam}</td>`;
    html += `<td>${item.nama_barang}</td>`;
    html += `<td><span class="badge bg-warning">Pending</span></td>`;
    html += `<td>${formatDate(item.tanggal)}</td>`;
    html += `<td>${formatDateTime(item.tanggal_kembali)}</td>`;
    html += `<td>`;
    html += `<button class="btn btn-sm btn-success" onclick="verifikasiPengembalianBarang(${item.id}, 'disetujui')">Setujui</button> `;
    html += `<button class="btn btn-sm btn-danger" onclick="verifikasiPengembalianBarang(${item.id}, 'ditolak')">Tolak</button>`;
    html += `</td>`;
    html += "</tr>";
  });

  html += "</tbody></table></div>";
  container.innerHTML = html;
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
