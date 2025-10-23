// Timeline Modal Functions
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
    // Gunakan semua peminjaman
    const peminjamanAll = data.peminjaman;
    const pengembalianAll = data.pengembalian;

    // Update counters
    document.getElementById("peminjamanPendingCount").textContent =
      peminjamanAll.length;
    document.getElementById("pengembalianPendingCount").textContent =
      pengembalianAll.length;

    // Render peminjaman table
    renderPeminjamanPendingTable(peminjamanAll);

    // Render pengembalian table
    renderPengembalianPendingTable(pengembalianAll);
  }

  // Render peminjaman pending table
  function renderPeminjamanPendingTable(peminjaman) {
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

      // Tombol aksi - PENTING: menggunakan fungsi showModalKembalikan yang sudah didefinisikan di window
      let actionButtons = "-";

      if (item.status === "disetujui") {
        // Gunakan openPengembalianModal yang sudah ada, bukan showModalKembalikan
        actionButtons = `
        <button type="button" class="btn btn-info btn-sm" onclick="openPengembalianModal(${item.kendaraan_id})">
          <i class="bi bi-box-arrow-in-down"></i> Kembalikan
        </button>
      `;
      }

      html += `
            <tr>
                <td>${item.tanggal_formatted}</td>
                <td>${item.nama_penanggung_jawab}</td>
                <td>${item.kendaraan_nama}</td>
                <td>${item.urusan_kedinasan || "-"}</td>
                <td>${statusBadge}</td>
                <td>
                    ${
                      item.surat_permohonan
                        ? `
                        <a href="${BASE_URL}/uploads/documents/${item.surat_permohonan}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-earmark-pdf"></i> Surat
                        </a>
                    `
                        : "-"
                    }
                    
                    ${
                      item.surat_jalan_admin
                        ? `
                        <a href="${BASE_URL}/uploads/documents/${item.surat_jalan_admin}" target="_blank" class="btn btn-sm btn-outline-primary ms-1">
                            <i class="bi bi-file-earmark-pdf"></i> Jalan
                        </a>
                    `
                        : ""
                    }
                </td>
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

      html += `
                <tr>
                    <td>${item.tanggal_formatted}</td>
                    <td>${item.nama_penanggung_jawab}</td>
                    <td>${item.kendaraan_nama}</td>
                    <td>${item.urusan_kedinasan || "-"}</td>
                    <td>${statusBadge}</td>
                    <td>
                        ${
                          item.surat_permohonan
                            ? `
                            <a href="${BASE_URL}/uploads/documents/${item.surat_permohonan}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark-pdf"></i> Surat
                            </a>
                        `
                            : "-"
                        }
                        
                        ${
                          item.surat_jalan_admin
                            ? `
                            <a href="${BASE_URL}/uploads/documents/${item.surat_jalan_admin}" target="_blank" class="btn btn-sm btn-outline-primary ms-1">
                                <i class="bi bi-file-earmark-pdf"></i> Jalan
                            </a>
                        `
                            : ""
                        }
                    </td>
                    <td>${item.tanggal_pinjam_formatted}</td>
                    <td>${item.tanggal_kembali_formatted}</td>
                    <td>-</td>
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
  }
});
