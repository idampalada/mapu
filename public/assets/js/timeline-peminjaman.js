// Timeline Modal Functions - PERBAIKAN TOMBOL KEMBALIKAN
document.addEventListener("DOMContentLoaded", function () {
  // Kode original untuk modal timeline
  window.showTimelineModal = function (kendaraanId) {
    // Show the modal
    const timelineModal = new bootstrap.Modal(
      document.getElementById("modalTimeline")
    );
    timelineModal.show();

    // Reset tables
    document.getElementById("peminjamanPendingTable").innerHTML = `
    <tr>
      <td colspan="9" class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3">Memuat data peminjaman...</p>
      </td>
    </tr>
  `;

    document.getElementById("pengembalianPendingTable").innerHTML = `
    <tr>
      <td colspan="9" class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3">Memuat data pengembalian...</p>
      </td>
    </tr>
  `;

    document.getElementById("penolakanHistoryTable").innerHTML = `
    <tr>
      <td colspan="8" class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3">Memuat histori penolakan...</p>
      </td>
    </tr>
  `;

    // Reset counters
    document.getElementById("peminjamanPendingCount").textContent = "0";
    document.getElementById("pengembalianPendingCount").textContent = "0";

    // Fix BASE_URL to prevent double slashes
    const baseUrl = BASE_URL.endsWith("/") ? BASE_URL.slice(0, -1) : BASE_URL;

    // Fetch the timeline data
    fetch(`${baseUrl}/aset/get-timeline-data/${kendaraanId}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error(
            "Network response was not ok: " + response.statusText
          );
        }
        return response.json();
      })
      .then((data) => {
        console.log("Timeline data:", data); // For debugging

        if (data.success) {
          renderTimelineData(data);
        } else {
          showError(
            data.error || "Terjadi kesalahan saat memuat data timeline"
          );
        }
      })
      .catch((error) => {
        console.error("Error fetching timeline data:", error);
        showError(
          "Terjadi kesalahan saat memuat data timeline: " + error.message
        );
      });
  };

  // Function to render timeline data
  function renderTimelineData(data) {
    // Gunakan peminjaman data apa adanya
    const peminjamanAll = data.peminjaman;

    // Untuk pengembalian, kelompokkan berdasarkan pinjam_id
    const pengembalianAll = data.pengembalian;
    const groupedPengembalian = {};

    pengembalianAll.forEach((item) => {
      const pinjamId = item.pinjam_id;
      // Simpan data terbaru (berdasarkan tanggal created_at) untuk setiap pinjam_id
      if (
        !groupedPengembalian[pinjamId] ||
        new Date(item.created_at) >
          new Date(groupedPengembalian[pinjamId].created_at)
      ) {
        groupedPengembalian[pinjamId] = item;
      }
    });

    // Konversi kembali ke array untuk ditampilkan
    const uniquePengembalian = Object.values(groupedPengembalian);

    // Update badge counter
    document.getElementById("peminjamanPendingCount").textContent =
      peminjamanAll.length;
    document.getElementById("pengembalianPendingCount").textContent =
      uniquePengembalian.length;

    // Render peminjaman table dengan passing data pengembalian
    renderPeminjamanPendingTable(peminjamanAll, uniquePengembalian);

    // Render pengembalian table dengan data yang sudah dikelompokkan
    renderPengembalianPendingTable(uniquePengembalian);

    // Render penolakan history table
    if (data.penolakan && Array.isArray(data.penolakan)) {
      renderPenolakanHistoryTable(data.penolakan);
    } else {
      document.getElementById("penolakanHistoryTable").innerHTML = `
        <tr>
          <td colspan="8" class="text-center py-4">
            <div class="text-muted">
              <i class="bi bi-info-circle text-info fs-4"></i>
              <p class="mt-2">Tidak ada data histori penolakan</p>
            </div>
          </td>
        </tr>
      `;
    }
  }

  // Render peminjaman pending table
  function renderPeminjamanPendingTable(peminjaman, pengembalianData = []) {
    const tableBody = document.getElementById("peminjamanPendingTable");

    if (peminjaman.length === 0) {
      tableBody.innerHTML = `
      <tr>
        <td colspan="9" class="text-center py-4">
          <div class="text-muted">
            <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
            <p class="mt-2">Belum ada peminjaman</p>
          </div>
        </td>
      </tr>
    `;
      return;
    }

    let html = "";

    peminjaman.forEach((item) => {
      // Tentukan badge status
      let statusBadge = "";
      switch (item.status) {
        case "pending":
          statusBadge = '<span class="badge bg-warning">Pending</span>';
          break;
        case "disetujui":
          statusBadge = '<span class="badge bg-success">Disetujui</span>';
          break;
        case "ditolak":
          statusBadge = '<span class="badge bg-danger">Ditolak</span>';
          break;
        case "selesai":
          statusBadge = '<span class="badge bg-info">Selesai</span>';
          break;
        default:
          statusBadge = `<span class="badge bg-secondary">${item.status}</span>`;
      }

      // PERBAIKAN: Selalu tampilkan tombol kembalikan pada status disetujui
      let actionButtons = "-";

      // PERBAIKAN: Tanpa mengevaluasi hasReturn dan is_returned
      // Cukup cek status
      if (item.status === "disetujui") {
        actionButtons = `
        <button type="button" class="btn btn-info btn-sm" onclick="openPengembalianModal(${item.kendaraan_id})">
          <i class="bi bi-box-arrow-in-down"></i> Kembalikan
        </button>
      `;
      }

      // Bagian dokumen - menggunakan kode yang sudah diperbarui
      let dokumenLinks = "-";

      if (item.status === "pending") {
        if (item.surat_permohonan) {
          dokumenLinks = `
    <a href="${BASE_URL}/uploads/documents/${item.surat_permohonan}" target="_blank" class="btn btn-sm btn-outline-primary">
      <i class="bi bi-file-earmark-pdf"></i> Draft Surat Izin
    </a>
  `;
        }
      } else if (item.status === "disetujui") {
        // Untuk status disetujui, tampilkan surat permohonan, surat jalan admin, dan surat penanggung jawab
        let suratIzin = "";
        let suratJalan = "";
        let suratPenanggungJawab = "";

        if (item.surat_permohonan) {
          suratIzin = `
    <a href="${BASE_URL}/uploads/documents/${item.surat_permohonan}" target="_blank" class="btn btn-sm btn-outline-primary">
      <i class="bi bi-file-earmark-pdf"></i> Surat Izin Pemakaian
    </a>
  `;
        }

        if (item.surat_jalan_admin) {
          suratJalan = `
    <a href="${BASE_URL}/uploads/documents/${item.surat_jalan_admin}" target="_blank" class="btn btn-sm btn-outline-primary ms-1">
      <i class="bi bi-file-earmark-pdf"></i> Surat Jalan
    </a>
  `;
        }

        // Tambahkan surat penanggung jawab jika tersedia
        if (item.surat_penanggung_jawab) {
          suratPenanggungJawab = `
    <a href="${BASE_URL}/uploads/documents/${item.surat_penanggung_jawab}" target="_blank" class="btn btn-sm btn-outline-primary ms-1">
      <i class="bi bi-file-earmark-pdf"></i> Surat Penanggung Jawab
    </a>
  `;
        }

        dokumenLinks = suratIzin + suratJalan + suratPenanggungJawab;
      } else if (item.status === "selesai") {
        // Tambahkan kondisi khusus untuk status 'selesai'
        let suratPermohonan = "";
        let suratJalan = "";
        let suratPenanggungJawab = "";
        let beritaAcara = "";

        if (item.surat_permohonan) {
          suratPermohonan = `
    <a href="${BASE_URL}/uploads/documents/${item.surat_permohonan}" target="_blank" class="btn btn-sm btn-outline-primary">
      <i class="bi bi-file-earmark-pdf"></i> Surat Permohonan
    </a>
    `;
        }

        if (item.surat_jalan_admin) {
          suratJalan = `
    <a href="${BASE_URL}/uploads/documents/${item.surat_jalan_admin}" target="_blank" class="btn btn-sm btn-outline-primary ms-1">
      <i class="bi bi-file-earmark-pdf"></i> Surat Jalan
    </a>
    `;
        }

        // Tambahkan surat penanggung jawab jika tersedia
        if (item.surat_penanggung_jawab) {
          suratPenanggungJawab = `
    <a href="${BASE_URL}/uploads/documents/${item.surat_penanggung_jawab}" target="_blank" class="btn btn-sm btn-outline-primary ms-1">
      <i class="bi bi-file-earmark-pdf"></i> Surat Penanggung Jawab
    </a>
    `;
        }

        // Tambahkan berita acara jika tersedia
        if (item.berita_acara_pengembalian) {
          beritaAcara = `
    <a href="${BASE_URL}/uploads/documents/${item.berita_acara_pengembalian}" target="_blank" class="btn btn-sm btn-outline-primary ms-1">
      <i class="bi bi-file-earmark-pdf"></i> Berita Acara Pengembalian
    </a>
    `;
        }

        dokumenLinks =
          suratPermohonan + suratJalan + suratPenanggungJawab + beritaAcara;
      } else {
        // Untuk status lainnya
        if (item.surat_permohonan) {
          dokumenLinks = `
    <a href="${BASE_URL}/uploads/documents/${item.surat_permohonan}" target="_blank" class="btn btn-sm btn-outline-primary">
      <i class="bi bi-file-earmark-pdf"></i> Surat Permohonan
    </a>
    `;
        }
      }

      html += `
      <tr>
        <td>${item.tanggal_formatted}</td>
        <td>${item.nama_penanggung_jawab}</td>
        <td>${item.kendaraan_nama}</td>
        <td>${item.urusan_kedinasan || "-"}</td>
        <td>${statusBadge}</td>
        <td>${dokumenLinks || "-"}</td>
        <td>${item.tanggal_pinjam_formatted}</td>
        <td>${item.tanggal_kembali_formatted}</td>
        <td>${actionButtons}</td>
      </tr>
    `;
    });

    tableBody.innerHTML = html;
  }

  // Render pengembalian pending table
  function renderPengembalianPendingTable(pengembalian) {
    const tableBody = document.getElementById("pengembalianPendingTable");

    if (pengembalian.length === 0) {
      tableBody.innerHTML = `
      <tr>
        <td colspan="9" class="text-center py-4">
          <div class="text-muted">
            <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
            <p class="mt-2">Belum ada pengembalian</p>
          </div>
        </td>
      </tr>
    `;
      return;
    }

    let html = "";

    pengembalian.forEach((item) => {
      // Status di tab Pengembalian
      let statusBadge = "";
      switch (item.status) {
        case "pending":
          statusBadge =
            '<span class="badge bg-warning">Menunggu Verifikasi Pengembalian</span>';
          break;
        case "disetujui":
          statusBadge = '<span class="badge bg-success">Disetujui</span>';
          break;
        case "ditolak":
          statusBadge = '<span class="badge bg-danger">Ditolak</span>';
          break;
        case "selesai":
          statusBadge = '<span class="badge bg-info">Selesai</span>';
          break;
        default:
          statusBadge = `<span class="badge bg-secondary">${item.status}</span>`;
      }

      // Tombol aksi - tampilkan tombol kembalikan hanya jika status ditolak
      let actionButtons = "-";

      if (item.status === "ditolak") {
        actionButtons = `
  <button type="button" class="btn btn-info btn-sm" onclick="openPengembalianModal(${item.kendaraan_id})">
    <i class="bi bi-box-arrow-in-down"></i> Kembalikan
  </button>
`;
      }

      // Dokumen links untuk tab pengembalian
      let dokumenLinks = "-";

      if (item.status === "selesai") {
        // Untuk status selesai, tampilkan semua dokumen
        let suratPengembalian = "";
        let beritaAcara = "";
        let suratJalanAdmin = "";

        if (item.surat_pengembalian) {
          suratPengembalian = `
    <a href="${BASE_URL}/uploads/documents/${item.surat_pengembalian}" target="_blank" class="btn btn-sm btn-outline-primary">
      <i class="bi bi-file-earmark-pdf"></i> Surat Pengembalian
    </a>
    `;
        }

        if (item.berita_acara_pengembalian) {
          beritaAcara = `
    <a href="${BASE_URL}/uploads/documents/${item.berita_acara_pengembalian}" target="_blank" class="btn btn-sm btn-outline-primary ms-1">
      <i class="bi bi-file-earmark-pdf"></i> Berita Acara
    </a>
    `;
        }

        if (item.surat_jalan_admin) {
          suratJalanAdmin = `
    <a href="${BASE_URL}/uploads/documents/${item.surat_jalan_admin}" target="_blank" class="btn btn-sm btn-outline-primary ms-1">
      <i class="bi bi-file-earmark-pdf"></i> Surat Jalan Admin
    </a>
    `;
        }

        dokumenLinks = suratPengembalian + beritaAcara + suratJalanAdmin;
      } else {
        // Untuk status lainnya, tampilkan seperti sebelumnya
        if (item.berita_acara_pengembalian) {
          dokumenLinks = `
    <a href="${BASE_URL}/uploads/documents/${item.berita_acara_pengembalian}" target="_blank" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-file-earmark-pdf"></i> Berita Acara
    </a>
    `;
        }
      }

      html += `
      <tr>
        <td>${item.tanggal_formatted}</td>
        <td>${item.nama_penanggung_jawab}</td>
        <td>${item.kendaraan_nama}</td>
        <td>${item.urusan_kedinasan || "-"}</td>
        <td>${statusBadge}</td>
        <td>${dokumenLinks}</td>
        <td>${item.tanggal_pinjam_formatted}</td>
        <td>${item.tanggal_kembali_formatted}</td>
        <td>${actionButtons}</td>
      </tr>
    `;
    });

    tableBody.innerHTML = html;
  }

  // Fungsi baru untuk menampilkan histori penolakan
  function renderPenolakanHistoryTable(penolakan) {
    const tableBody = document.getElementById("penolakanHistoryTable");

    if (!tableBody) {
      console.error("Element penolakanHistoryTable tidak ditemukan");
      return;
    }

    if (penolakan.length === 0) {
      tableBody.innerHTML = `
      <tr>
        <td colspan="8" class="text-center py-4">
          <div class="text-muted">
            <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
            <p class="mt-2">Tidak ada histori penolakan</p>
          </div>
        </td>
      </tr>
      `;
      return;
    }

    let html = "";

    penolakan.forEach((item) => {
      // Dokumen links untuk histori penolakan
      let dokumenLinks = "-";

      if (item.berita_acara_pengembalian) {
        dokumenLinks = `
        <a href="${BASE_URL}/uploads/documents/${item.berita_acara_pengembalian}" target="_blank" class="btn btn-sm btn-outline-primary">
          <i class="bi bi-file-earmark-pdf"></i> Berita Acara
        </a>
        `;
      }

      html += `
      <tr>
        <td>${item.tanggal_formatted}</td>
        <td>${item.nama_penanggung_jawab}</td>
        <td>${item.kendaraan_nama} <br><small class="text-muted">${
        item.no_polisi || "-"
      }</small></td>
        <td>
          <span class="badge bg-danger">
            <i class="bi bi-x-circle me-1"></i> Ditolak
          </span>
        </td>
        <td>
          <div class="text-danger">
            ${item.keterangan || "Tidak ada keterangan"}
          </div>
        </td>
        <td>${dokumenLinks}</td>
        <td>${item.tanggal_pinjam_formatted}</td>
        <td>${item.tanggal_kembali_formatted}</td>
      </tr>
      `;
    });

    tableBody.innerHTML = html;
  }

  // Function to show error
  function showError(message) {
    document.getElementById("peminjamanPendingTable").innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        ${message}
                    </div>
                </td>
            </tr>
        `;

    document.getElementById("pengembalianPendingTable").innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        ${message}
                    </div>
                </td>
            </tr>
        `;

    document.getElementById("penolakanHistoryTable").innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        ${message}
                    </div>
                </td>
            </tr>
        `;
  }
});

// Script untuk fitur keterlambatan pengembalian
$(document).ready(function () {
  // Function untuk mengecek keterlambatan pengembalian
  function checkLateReturn() {
    const tanggalKembaliInput = document.getElementById("tanggal_kembali");
    if (!tanggalKembaliInput || !tanggalKembaliInput.value) return;

    const tanggalKembali = new Date(tanggalKembaliInput.value);
    const today = new Date();

    // Reset waktu untuk membandingkan tanggal saja
    tanggalKembali.setHours(0, 0, 0, 0);
    today.setHours(0, 0, 0, 0);

    // Hitung selisih hari
    const diffTime = today.getTime() - tanggalKembali.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    console.log("Selisih hari:", diffDays);

    // Update hidden fields dan tampilkan/sembunyikan form keterlambatan
    if (diffDays > 0) {
      // Pengembalian terlambat
      document.getElementById("is_late_return").value = "true";
      document.getElementById("days_late").value = diffDays;
      document.getElementById("late_days_display").textContent = diffDays;
      document.getElementById("late_return_section").classList.remove("d-none");

      // Tambahkan section Hitungan Telat jika belum ada
      if (!document.getElementById("hitungan_telat_section")) {
        const hitunganTelatSection = document.createElement("div");
        hitunganTelatSection.id = "hitungan_telat_section";
        hitunganTelatSection.className = "alert alert-danger mb-3";

        const tanggalKembaliFormatted = formatTanggal(tanggalKembali);
        const todayFormatted = formatTanggal(today);

        hitunganTelatSection.innerHTML = `
          <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-circle-fill me-2" style="font-size: 1.5rem; color: #dc3545;"></i>
            <div>
              <h5 class="mb-1">Hitungan Telat: <span class="fw-bold">${diffDays} Hari</span></h5>
              <div>Tanggal kembali seharusnya: <strong>${tanggalKembaliFormatted}</strong></div>
              <div>Tanggal pengembalian aktual: <strong>${todayFormatted}</strong></div>
            </div>
          </div>
        `;

        // Tambahkan section di awal card body
        const cardBody = document.querySelector(
          "#late_return_section .card-body"
        );
        const alertWarning = cardBody.querySelector(".alert-warning");
        cardBody.insertBefore(hitunganTelatSection, alertWarning);
      }

      // Jadikan field tanggal kembali readonly dan tambahkan styling
      tanggalKembaliInput.setAttribute("readonly", "readonly");
      tanggalKembaliInput.style.backgroundColor = "#ffeeee";
      tanggalKembaliInput.style.borderColor = "#dc3545";
      tanggalKembaliInput.style.color = "#dc3545";
      tanggalKembaliInput.style.fontWeight = "bold";

      // Hapus atribut min karena sudah readonly
      tanggalKembaliInput.removeAttribute("min");

      // Alasan keterlambatan wajib diisi
      document
        .getElementById("alasan_keterlambatan")
        .setAttribute("required", "required");
    } else {
      // Pengembalian tepat waktu
      document.getElementById("is_late_return").value = "false";
      document.getElementById("days_late").value = "0";
      document.getElementById("late_return_section").classList.add("d-none");

      // Hapus readonly dari field tanggal kembali
      tanggalKembaliInput.removeAttribute("readonly");
      tanggalKembaliInput.style.backgroundColor = "";
      tanggalKembaliInput.style.borderColor = "";
      tanggalKembaliInput.style.color = "";
      tanggalKembaliInput.style.fontWeight = "";
      tanggalKembaliInput.setAttribute("min", formatDateValue(new Date()));

      // Alasan keterlambatan tidak wajib diisi
      document
        .getElementById("alasan_keterlambatan")
        .removeAttribute("required");

      // Hapus section Hitungan Telat jika ada
      const hitunganTelatSection = document.getElementById(
        "hitungan_telat_section"
      );
      if (hitunganTelatSection) {
        hitunganTelatSection.remove();
      }
    }
  }

  // Helper function untuk format tanggal (DD/MM/YYYY)
  function formatTanggal(date) {
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
  }

  // Helper function untuk format tanggal (YYYY-MM-DD)
  function formatDateValue(date) {
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();
    return `${year}-${month}-${day}`;
  }

  // Panggil function checkLateReturn saat modal dibuka
  $("#modalPengembalian").on("shown.bs.modal", function () {
    checkLateReturn();
  });

  // Panggil function checkLateReturn saat tanggal kembali berubah
  $("#tanggal_kembali").on("change", function () {
    checkLateReturn();
  });
});
