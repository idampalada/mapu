// TAMBAHKAN: public/assets/js/admin_dashboard.js

// Buka modal ubah jam
function bukaModalUbahJam(pinjamId) {
  console.log(`🕒 Opening modal ubah jam for ID: ${pinjamId}`);

  // Reset form
  document.getElementById("formUbahJam").reset();
  document.getElementById("ubah_pinjam_id").value = pinjamId;
  document.getElementById("warning_konflik").style.display = "none";
  document.getElementById("preview_ubah").style.display = "none";
  document.getElementById("btnUbahSetujui").disabled = true;

  // Fetch data peminjaman
  fetch(`/admin/verifikasi-ruangan/getDetailPeminjaman/${pinjamId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const p = data.data;

        // Populate info
        document.getElementById("nama_pemohon").textContent =
          p.nama_penanggung_jawab;
        document.getElementById("nama_ruangan").textContent = p.nama_ruangan;
        document.getElementById("tanggal_pinjam").textContent = formatTanggal(
          p.tanggal
        );
        document.getElementById(
          "waktu_original"
        ).textContent = `${p.waktu_mulai} - ${p.waktu_selesai}`;

        // Set default values ke waktu original
        document.getElementById("waktu_mulai_baru").value = p.waktu_mulai;
        document.getElementById("waktu_selesai_baru").value = p.waktu_selesai;

        // Show modal
        const modal = new bootstrap.Modal(
          document.getElementById("modalUbahJam")
        );
        modal.show();

        // Setup event listeners
        setupEventListenersUbahJam(p.ruangan_id, p.tanggal);
      } else {
        Swal.fire("Error", data.message, "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire("Error", "Gagal memuat data peminjaman", "error");
    });
}
/**
 * DASHBOARD UBAH JAM - FIXED VERSION
 * Fix untuk error "Modal tidak ditemukan" dan "Table body not found"
 */

// ✅ Function: bukaModalUbahJamAdmin - UNTUK ADMIN DASHBOARD
function bukaModalUbahJamAdmin(ruanganId) {
  console.log(
    `Opening ubah jam modal for ruangan ${ruanganId}: Auditorium KEMENPU`
  );

  // PERBAIKAN: Cek modal ada atau tidak
  const modalElement = document.getElementById("modalUbahJam");
  if (!modalElement) {
    console.error("Modal #modalUbahJam tidak ditemukan!");
    Swal.fire(
      "Error",
      "Modal tidak ditemukan. Halaman mungkin belum ter-load dengan benar.",
      "error"
    );
    return;
  }

  // Reset form dengan error handling yang lebih baik
  resetUbahJamForm();

  // Load peminjaman aktif untuk ruangan ini
  loadPeminjamanAktifRuangan(ruanganId);

  // Show modal
  const modal = new bootstrap.Modal(modalElement);
  modal.show();
}

/**
 * ✅ Reset form ubah jam - FIXED VERSION dengan NULL SAFETY
 */
function resetUbahJamForm() {
  console.log("Resetting ubah jam form...");

  // Reset time pickers dengan ID yang benar dan null safety
  const waktuMulai = document.getElementById("waktu_mulai_baru");
  const waktuSelesai = document.getElementById("waktu_selesai_baru");
  const alasanUbah = document.getElementById("alasan_ubah_jam");

  if (waktuMulai) {
    waktuMulai.value = "";
  } else {
    console.warn("Element waktu_mulai_baru tidak ditemukan");
  }

  if (waktuSelesai) {
    waktuSelesai.value = "";
  } else {
    console.warn("Element waktu_selesai_baru tidak ditemukan");
  }

  if (alasanUbah) {
    alasanUbah.value = "";
  } else {
    console.warn("Element alasan_ubah_jam tidak ditemukan");
  }

  // Reset durasi display
  const durasiBaru = document.getElementById("durasi_baru");
  if (durasiBaru) {
    durasiBaru.textContent = "-";
    durasiBaru.className = "badge bg-info";
  } else {
    console.warn("Element durasi_baru tidak ditemukan");
  }

  // Reset warning konflik
  const warningKonflik = document.getElementById("warning_konflik");
  if (warningKonflik) {
    warningKonflik.style.display = "none";
  } else {
    console.warn("Element warning_konflik tidak ditemukan");
  }

  // Reset preview
  const previewUbah = document.getElementById("preview_ubah");
  if (previewUbah) {
    previewUbah.style.display = "none";
  } else {
    console.warn("Element preview_ubah tidak ditemukan");
  }

  // Reset submit button dengan ID yang benar
  const submitBtn = document.getElementById("btnUbahSetujui");
  if (submitBtn) {
    submitBtn.disabled = true;
  } else {
    console.warn("Element btnUbahSetujui tidak ditemukan");
  }

  // Reset info peminjaman
  const infoPeminjaman = document.getElementById("info_peminjaman_ubah");
  if (infoPeminjaman) {
    const spans = infoPeminjaman.querySelectorAll("span");
    spans.forEach((span) => (span.textContent = "-"));
  } else {
    console.warn("Element info_peminjaman_ubah tidak ditemukan");
  }

  console.log("Form reset completed");
}

/**
 * ✅ Load peminjaman aktif ruangan - FIXED VERSION dengan BETTER ERROR HANDLING
 */
function loadPeminjamanAktifRuangan(ruanganId) {
  console.log(`Loading peminjaman aktif untuk ruangan ${ruanganId}`);

  const tableBody = document.getElementById("tabelPeminjamanAktif");
  if (!tableBody) {
    console.error("Table body not found: tabelPeminjamanAktif");
    console.error("Modal mungkin belum ter-load atau ID element salah");

    // Show friendly error message
    Swal.fire({
      icon: "error",
      title: "Element Tidak Ditemukan",
      text: "Table peminjaman tidak ditemukan. Pastikan modal sudah ter-load dengan benar.",
      footer: "Silakan refresh halaman dan coba lagi.",
    });
    return;
  }

  // Show loading
  tableBody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span>Memuat data peminjaman...</span>
                </div>
            </td>
        </tr>
    `;

  // ✅ URL yang benar sesuai dengan route backend
  const url = `/admin/verifikasi-ruangan/getPeminjamanByRuangan/${ruanganId}`;
  console.log("Fetching data from:", url);

  // Fetch data dengan error handling yang lebih baik
  fetch(url, {
    method: "GET",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      "Content-Type": "application/json",
    },
  })
    .then((response) => {
      console.log("Response status:", response.status);
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }
      return response.json();
    })
    .then((data) => {
      console.log("API Response:", data);

      if (data.success && data.data && data.data.length > 0) {
        displayPeminjamanAktif(data.data);
      } else {
        tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-calendar-x fs-2 mb-2 d-block"></i>
                        <div>Tidak ada peminjaman aktif untuk ruangan ini</div>
                        <small class="text-muted">Silakan pilih peminjaman dari verifikasi pending</small>
                    </td>
                </tr>
            `;
      }
    })
    .catch((error) => {
      console.error("Error loading peminjaman:", error);
      tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger py-4">
                    <i class="bi bi-exclamation-triangle fs-2 mb-2 d-block"></i>
                    <div><strong>Error memuat data</strong></div>
                    <div class="small">${error.message}</div>
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadPeminjamanAktifRuangan(${ruanganId})">
                        <i class="bi bi-arrow-clockwise"></i> Coba Lagi
                    </button>
                </td>
            </tr>
        `;
    });
}

/**
 * ✅ Display peminjaman aktif - HELPER FUNCTION
 */
function displayPeminjamanAktif(data) {
  console.log("Displaying peminjaman aktif:", data);

  const tableBody = document.getElementById("tabelPeminjamanAktif");
  if (!tableBody) {
    console.error("Table body not found when trying to display data");
    return;
  }

  let html = "";
  data.forEach((item) => {
    const statusClass = getStatusClass(item.status);
    html += `
            <tr>
                <td>
                    <div class="fw-bold">${
                      item.nama_penanggung_jawab || "-"
                    }</div>
                    <small class="text-muted">${item.nip_nrp || ""}</small>
                </td>
                <td>${formatDate(item.tanggal)}</td>
                <td>
                    <small>${item.waktu_mulai || "-"} - ${
      item.waktu_selesai || "-"
    }</small>
                </td>
                <td>
                    <small>${item.keperluan || "-"}</small>
                </td>
                <td>
                    <span class="badge ${statusClass}">${
      item.status || "Unknown"
    }</span>
                </td>
                <td>
                    ${
                      item.status === "disetujui"
                        ? `
                        <button class="btn btn-xs btn-warning" onclick="ubahJamPeminjaman(${item.id})" 
                                title="Ubah jam peminjaman ini">
                            <i class="bi bi-clock-history"></i>
                        </button>
                    `
                        : `
                        <button class="btn btn-xs btn-success" onclick="setujuiDanUbahJam(${item.id})" 
                                title="Setujui dengan jam baru">
                            <i class="bi bi-check-circle"></i>
                        </button>
                    `
                    }
                </td>
            </tr>
        `;
  });

  tableBody.innerHTML = html;
}

/**
 * Helper functions
 */
function getStatusClass(status) {
  const classes = {
    pending: "bg-warning",
    disetujui: "bg-success",
    ditolak: "bg-danger",
    selesai: "bg-info",
  };
  return classes[status] || "bg-secondary";
}

function formatDate(dateString) {
  if (!dateString) return "-";
  try {
    return new Date(dateString).toLocaleDateString("id-ID");
  } catch (e) {
    return dateString;
  }
}

/**
 * Event handlers untuk time input
 */
document.addEventListener("DOMContentLoaded", function () {
  const waktuMulai = document.getElementById("waktu_mulai_baru");
  const waktuSelesai = document.getElementById("waktu_selesai_baru");

  if (waktuMulai && waktuSelesai) {
    waktuMulai.addEventListener("change", hitungDurasi);
    waktuSelesai.addEventListener("change", hitungDurasi);
  }
});

function hitungDurasi() {
  const waktuMulai = document.getElementById("waktu_mulai_baru");
  const waktuSelesai = document.getElementById("waktu_selesai_baru");
  const durasiBaru = document.getElementById("durasi_baru");

  if (!waktuMulai || !waktuSelesai || !durasiBaru) return;

  if (waktuMulai.value && waktuSelesai.value) {
    const start = new Date(`1970-01-01 ${waktuMulai.value}`);
    const end = new Date(`1970-01-01 ${waktuSelesai.value}`);

    if (end > start) {
      const diffMs = end - start;
      const diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
      const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

      durasiBaru.textContent = `${diffHrs}j ${diffMins}m`;
      durasiBaru.className = "badge bg-success";
    } else {
      durasiBaru.textContent = "Invalid";
      durasiBaru.className = "badge bg-danger";
    }
  } else {
    durasiBaru.textContent = "-";
    durasiBaru.className = "badge bg-info";
  }
}

console.log("Dashboard ubah jam functionality loaded successfully");

// Setup event listeners
function setupEventListenersUbahJam(ruanganId, tanggal) {
  const waktuMulai = document.getElementById("waktu_mulai_baru");
  const waktuSelesai = document.getElementById("waktu_selesai_baru");

  // Update durasi real-time
  [waktuMulai, waktuSelesai].forEach((input) => {
    input.addEventListener("input", () => {
      hitungDurasi();
      resetWarningKonflik();
      document.getElementById("btnUbahSetujui").disabled = true;
      document.getElementById("preview_ubah").style.display = "none";
    });
  });
}

// Hitung durasi
function hitungDurasi() {
  const mulai = document.getElementById("waktu_mulai_baru").value;
  const selesai = document.getElementById("waktu_selesai_baru").value;

  if (mulai && selesai) {
    const start = new Date(`2000-01-01T${mulai}:00`);
    const end = new Date(`2000-01-01T${selesai}:00`);
    const diffMs = end - start;

    if (diffMs > 0) {
      const hours = Math.floor(diffMs / (1000 * 60 * 60));
      const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

      let durasi = "";
      if (hours > 0) durasi += `${hours} jam `;
      if (minutes > 0) durasi += `${minutes} menit`;

      document.getElementById("durasi_baru").textContent = durasi || "0 menit";
      document.getElementById("durasi_baru").className = "badge bg-success";
    } else {
      document.getElementById("durasi_baru").textContent = "Tidak valid";
      document.getElementById("durasi_baru").className = "badge bg-danger";
    }
  }
}

// Cek ketersediaan jam
function cekKetersediaanJam() {
  const pinjamId = document.getElementById("ubah_pinjam_id").value;
  const waktuMulai = document.getElementById("waktu_mulai_baru").value;
  const waktuSelesai = document.getElementById("waktu_selesai_baru").value;
  const alasan = document.getElementById("alasan_ubah_jam").value;

  // Validasi basic
  if (!waktuMulai || !waktuSelesai || !alasan.trim()) {
    Swal.fire(
      "Peringatan",
      "Mohon lengkapi waktu baru dan alasan perubahan",
      "warning"
    );
    return;
  }

  if (waktuSelesai <= waktuMulai) {
    Swal.fire("Error", "Waktu selesai harus setelah waktu mulai", "error");
    return;
  }

  // Validasi durasi minimum 30 menit
  const start = new Date(`2000-01-01T${waktuMulai}:00`);
  const end = new Date(`2000-01-01T${waktuSelesai}:00`);
  const diffMinutes = (end - start) / (1000 * 60);

  if (diffMinutes < 30) {
    Swal.fire("Error", "Durasi minimum adalah 30 menit", "error");
    return;
  }

  // Show loading
  document.getElementById("btnUbahSetujui").disabled = true;
  document.getElementById("btnUbahSetujui").innerHTML =
    '<i class="bi bi-hourglass-split"></i> Mengecek...';

  // Cek via API
  fetch("/admin/verifikasi-ruangan/cekKetersediaan", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify({
      pinjam_id: pinjamId,
      waktu_mulai: waktuMulai,
      waktu_selesai: waktuSelesai,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      document.getElementById("btnUbahSetujui").innerHTML =
        '<i class="bi bi-check-circle"></i> Ubah & Setujui';

      if (data.success) {
        if (data.available) {
          // Available
          tampilkanPreview();
          document.getElementById("btnUbahSetujui").disabled = false;
          resetWarningKonflik();

          Swal.fire({
            icon: "success",
            title: "Waktu Tersedia!",
            text: "Jam yang dipilih tersedia untuk booking",
            timer: 2000,
            showConfirmButton: false,
          });
        } else {
          // Conflict
          tampilkanWarningKonflik(
            data.message || "Waktu bentrok dengan peminjaman lain"
          );
          document.getElementById("btnUbahSetujui").disabled = true;
          document.getElementById("preview_ubah").style.display = "none";
        }
      } else {
        throw new Error(data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      document.getElementById("btnUbahSetujui").innerHTML =
        '<i class="bi bi-check-circle"></i> Ubah & Setujui';
      document.getElementById("btnUbahSetujui").disabled = true;
      Swal.fire("Error", "Gagal mengecek ketersediaan ruangan", "error");
    });
}

// Tampilkan preview
function tampilkanPreview() {
  const waktuMulai = document.getElementById("waktu_mulai_baru").value;
  const waktuSelesai = document.getElementById("waktu_selesai_baru").value;
  const waktuOriginal = document.getElementById("waktu_original").textContent;
  const alasan = document.getElementById("alasan_ubah_jam").value;

  document.getElementById("preview_content_ubah").innerHTML = `
        <div><strong>Waktu Lama:</strong> ${waktuOriginal}</div>
        <div><strong>Waktu Baru:</strong> ${waktuMulai} - ${waktuSelesai}</div>
        <div><strong>Alasan:</strong> ${alasan}</div>
    `;
  document.getElementById("preview_ubah").style.display = "block";
}

// Submit form ubah jam
document.getElementById("formUbahJam").addEventListener("submit", function (e) {
  e.preventDefault();

  Swal.fire({
    title: "Konfirmasi Perubahan",
    text: "Anda yakin ingin mengubah jam dan menyetujui peminjaman ini?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Ya, Ubah & Setujui",
    cancelButtonText: "Batal",
    confirmButtonColor: "#28a745",
  }).then((result) => {
    if (result.isConfirmed) {
      submitUbahJam();
    }
  });
});

// Submit ubah jam
function submitUbahJam() {
  const formData = new FormData(document.getElementById("formUbahJam"));

  fetch("/admin/verifikasi-ruangan/ubahJamSetujui", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: data.message,
          timer: 3000,
          showConfirmButton: false,
        }).then(() => {
          // Close modal & refresh
          bootstrap.Modal.getInstance(
            document.getElementById("modalUbahJam")
          ).hide();
          location.reload();
        });
      } else {
        throw new Error(data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire("Error", error.message, "error");
    });
}

// Helper functions
function resetWarningKonflik() {
  document.getElementById("warning_konflik").style.display = "none";
}

function tampilkanWarningKonflik(pesan) {
  document.getElementById("pesan_konflik").textContent = pesan;
  document.getElementById("warning_konflik").style.display = "block";
}

function formatTanggal(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("id-ID");
}
