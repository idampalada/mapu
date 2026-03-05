/**
 * daftar-booking.js
 * File JavaScript untuk menangani tab "Daftar Booking Saya"
 */

// Global Variables
let daftarBookingData = [];
let filteredData = [];

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
  initializeDaftarBooking();
});

// Initialize Daftar Booking functionality
function initializeDaftarBooking() {
  // Setup tab event listener
  setupTabListener();

  // Setup filter listeners
  setupFilterListeners();
}

// Setup tab change listener
function setupTabListener() {
  const daftarBookingTab = document.getElementById("daftar-booking-tab");
  if (daftarBookingTab) {
    daftarBookingTab.addEventListener("shown.bs.tab", function () {
      loadDaftarBookingSaya();
    });
  }
}

// Setup filter event listeners
function setupFilterListeners() {
  const filterNama = document.getElementById("filterNamaBookingSaya");
  const filterStatus = document.getElementById("filterStatusBookingSaya");
  const filterTanggal = document.getElementById("filterTanggalBookingSaya");

  if (filterNama) {
    filterNama.addEventListener("input", applyFilters);
  }

  if (filterStatus) {
    filterStatus.addEventListener("change", applyFilters);
  }

  if (filterTanggal) {
    filterTanggal.addEventListener("change", applyFilters);
  }
}

// Load data booking user
function loadDaftarBookingSaya() {
  console.log("Loading daftar booking...");

  const container = document.getElementById("daftarBookingSayaContainer");
  if (!container) {
    console.error("Container daftarBookingSayaContainer not found");
    return;
  }

  showLoadingState(container);

  // Check if baseUrl is defined
  const apiUrl =
    (typeof baseUrl !== "undefined" ? baseUrl : "/") +
    "user/ruangan/getDaftarBookingSaya";

  fetch(apiUrl, {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((response) => {
      console.log("Response status:", response.status);
      if (!response.ok) {
        throw new Error("Network response was not ok: " + response.status);
      }
      return response.json();
    })
    .then((data) => {
      console.log("Data received:", data);

      // Store user role for later use in filters
      if (data && data.user_role) {
        window.currentUserRole = data.user_role;
      }

      if (data && data.success && data.bookings && data.bookings.length > 0) {
        daftarBookingData = data.bookings;
        filteredData = JSON.parse(JSON.stringify(daftarBookingData)); // Deep copy
        displayBookings(filteredData, data); // Pass the full response data
      } else {
        showNoBookingsMessage(container);
      }
    })
    .catch((error) => {
      console.error("Error loading bookings:", error);
      showErrorMessage(container);
    });
}

// Show loading state
function showLoadingState(container) {
  container.innerHTML = `
        <div class="col-12">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-3">Memuat data booking Anda...</div>
            </div>
        </div>
    `;
}

// Display bookings as cards
function displayBookings(bookings, responseData) {
  const container = document.getElementById("daftarBookingSayaContainer");

  if (!bookings || bookings.length === 0) {
    showNoBookingsMessage(container);
    return;
  }

  // Update title jika ada di response
  if (responseData && responseData.title) {
    const alertInfo = document.querySelector(".alert-info");
    if (alertInfo) {
      alertInfo.innerHTML =
        '<i class="bi bi-info-circle me-2"></i>' +
        responseData.title +
        ": Kelola semua booking ruangan yang telah dibuat. Anda dapat melihat status booking dan melakukan request confirm untuk approval admin.";
    }
  }

  let cardsHTML = "";

  bookings.forEach(function (booking) {
    const statusBadge = getStatusBadge(booking.status);
    const actionButton = getActionButton(booking);
    const safeRuanganName = escapeHtml(booking.nama_ruangan || "");
    const safePenanggungJawab = escapeHtml(booking.nama_penanggung_jawab || "");

    // Show additional info for admin roles
    let additionalInfo = "";
    if (
      responseData &&
      responseData.user_role &&
      responseData.user_role !== "user"
    ) {
      additionalInfo = `
                <div class="mb-2">
                    <small class="text-muted">Email User:</small><br>
                    <span class="fw-semibold text-primary">${escapeHtml(
                      booking.email || "",
                    )}</span>
                </div>
                ${
                  booking.fullname
                    ? `
                <div class="mb-2">
                    <small class="text-muted">Nama User:</small><br>
                    <span class="fw-semibold">${escapeHtml(
                      booking.fullname,
                    )}</span>
                </div>`
                    : ""
                }
                ${
                  booking.gedung
                    ? `
                <div class="mb-2">
                    <small class="text-muted">Gedung:</small><br>
                    <span class="fw-semibold text-info">${escapeHtml(
                      booking.gedung,
                    )}</span>
                </div>`
                    : ""
                }
            `;
    }

    cardsHTML += `
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card h-100 shadow-sm booking-card" data-booking-id="${
                  booking.id
                }">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-truncate" style="max-width: 70%;">${safeRuanganName}</h6>
                            ${statusBadge}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="booking-info">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <small class="text-muted">Tanggal:</small><br>
                                    <span class="fw-semibold">${formatTanggal(
                                      booking.tanggal,
                                    )}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Waktu:</small><br>
                                    <span class="fw-semibold">${formatWaktu(
                                      booking.waktu_mulai,
                                    )} - ${formatWaktu(
                                      booking.waktu_selesai,
                                    )}</span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Penanggung Jawab:</small><br>
                                <span class="fw-semibold">${safePenanggungJawab}</span>
                            </div>
                            ${additionalInfo}
                            ${
                              booking.keperluan
                                ? `
                                <div class="mb-2">
                                    <small class="text-muted">Keperluan:</small><br>
                                    <span class="text-truncate d-block" style="max-height: 2.5em; overflow: hidden;">${escapeHtml(
                                      booking.keperluan,
                                    )}</span>
                                </div>
                            `
                                : ""
                            }
                            ${
                              booking.catatan_admin
                                ? `
                                <div class="mb-2">
                                    <small class="text-muted">Catatan Admin:</small><br>
                                    <span class="text-warning">${escapeHtml(
                                      booking.catatan_admin,
                                    )}</span>
                                </div>
                            `
                                : ""
                            }
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0">
                        ${actionButton}
                    </div>
                </div>
            </div>
        `;
  });

  container.innerHTML = cardsHTML;
}

// Get status badge HTML
function getStatusBadge(status) {
  const statusConfig = {
    aktif: { class: "bg-secondary", text: "Pending" }, // Status default dari database
    pending: { class: "bg-warning text-dark", text: "Menunggu Approval" }, // Ubah dari 'pending_approval'
    disetujui: { class: "bg-success", text: "Disetujui" },
    ditolak: { class: "bg-danger", text: "Ditolak" },
    // 'selesai' tidak akan muncul karena di-exclude di backend
  };

  const config = statusConfig[status] || {
    class: "bg-secondary",
    text: status,
  };
  return '<span class="badge ' + config.class + '">' + config.text + "</span>";
}

// Get action button HTML - Updated untuk handle 2 tabel
function getActionButton(booking) {
  const safeId = escapeHtml(booking.id || "");
  const safeNamaRuangan = escapeHtml(booking.nama_ruangan || "").replace(
    /'/g,
    "\\'",
  );
  const sourceTable = booking.source_table || "booking";

  // Berdasarkan status dan source table
  switch (booking.status) {
    case "aktif":
      // 🔴 jika sudah diambil user lain
      if (booking.taken_by_other) {
        return `
        <button class="btn btn-secondary btn-sm w-100" disabled>
            <i class="bi bi-lock me-1"></i>
            Telah dipinjam user lain
        </button>
    `;
      }

      // 🟡 jika masih bisa request confirm
      if (sourceTable === "booking") {
        return `
        <button class="btn btn-warning btn-sm w-100" onclick="requestConfirmBooking('${safeId}', '${safeNamaRuangan}')">
            <i class="bi bi-check-circle me-1"></i>
            Request Confirm
        </button>
    `;
      }

      break;
    case "pending":
      // Dari pinjam_ruangan yang sudah di-request confirm
      return `
                <div class="d-flex align-items-center">
                    <button class="btn btn-secondary btn-sm flex-grow-1" disabled>
                        <i class="bi bi-clock me-1"></i>
                        Menunggu Approval Admin
                    </button>
                    ${
                      booking.surat_permohonan
                        ? `
                    <a href="/uploads/documents/${booking.surat_permohonan}" target="_blank" class="btn btn-outline-info btn-sm ms-2" title="Lihat Surat">
                        <i class="bi bi-file-pdf"></i>
                    </a>`
                        : ""
                    }
                </div>
            `;
    case "disetujui":
      return `
                <div class="d-flex align-items-center">
                    <button class="btn btn-success btn-sm flex-grow-1" onclick="lihatDetailBooking('${safeId}', '${sourceTable}')">
                        <i class="bi bi-check2-circle me-1"></i>
                        Disetujui
                    </button>
                    ${
                      booking.surat_permohonan
                        ? `
                    <a href="/uploads/documents/${booking.surat_permohonan}" target="_blank" class="btn btn-outline-info btn-sm ms-2" title="Lihat Surat">
                        <i class="bi bi-file-pdf"></i>
                    </a>`
                        : ""
                    }
                </div>
            `;
    case "ditolak":
      return `
                <div class="d-grid gap-2">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-danger btn-sm flex-grow-1" onclick="lihatDetailBooking('${safeId}', '${sourceTable}')">
                            <i class="bi bi-x-circle me-1"></i>
                            Ditolak
                        </button>
                        ${
                          booking.surat_permohonan
                            ? `
                        <a href="/uploads/documents/${booking.surat_permohonan}" target="_blank" class="btn btn-outline-info btn-sm ms-2" title="Lihat Surat">
                            <i class="bi bi-file-pdf"></i>
                        </a>`
                            : ""
                        }
                    </div>
                    <button class="btn btn-outline-primary btn-sm" onclick="bookingUlang('${safeId}')">
                        <i class="bi bi-arrow-repeat me-1"></i>
                        Booking Ulang
                    </button>
                </div>
            `;
    default:
      return `
                <button class="btn btn-outline-secondary btn-sm w-100" disabled>
                    <i class="bi bi-question-circle me-1"></i>
                    Status: ${booking.status}
                </button>
            `;
  }
}

// Request confirm booking
function requestConfirmBooking(bookingId, namaRuangan) {
  console.log("Request confirm for booking:", bookingId);

  const confirmMessage =
    'Apakah Anda yakin ingin melakukan request confirm untuk booking ruangan "' +
    namaRuangan +
    '"?';

  if (typeof Swal !== "undefined") {
    Swal.fire({
      title: "Konfirmasi Request Confirm",
      text: confirmMessage,
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#ffc107",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Ya, Request Confirm",
      cancelButtonText: "Batal",
    }).then(function (result) {
      if (result.isConfirmed) {
        processRequestConfirm(bookingId);
      }
    });
  } else {
    if (confirm(confirmMessage)) {
      processRequestConfirm(bookingId);
    }
  }
}

// Process request confirm
function processRequestConfirm(bookingId) {
  console.log("Processing request confirm for:", bookingId);

  const apiUrl =
    (typeof baseUrl !== "undefined" ? baseUrl : "/") +
    "user/ruangan/requestConfirm";

  fetch(apiUrl, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify({
      booking_id: bookingId,
    }),
  })
    .then(function (response) {
      return response.json();
    })
    .then(function (data) {
      if (data && data.success) {
        const successMessage =
          "Request confirm berhasil dikirim. Menunggu approval dari admin.";

        if (typeof Swal !== "undefined") {
          Swal.fire("Berhasil!", successMessage, "success");
        } else {
          alert(successMessage);
        }

        loadDaftarBookingSaya();
      } else {
        const errorMessage =
          (data && data.message) || "Gagal melakukan request confirm.";

        if (typeof Swal !== "undefined") {
          Swal.fire("Gagal!", errorMessage, "error");
        } else {
          alert(errorMessage);
        }
      }
    })
    .catch(function (error) {
      console.error("Error:", error);

      const errorMessage = "Terjadi kesalahan sistem.";

      if (typeof Swal !== "undefined") {
        Swal.fire("Error!", errorMessage, "error");
      } else {
        alert(errorMessage);
      }
    });
}

// View booking detail (placeholder)
function lihatDetailBooking(bookingId) {
  console.log("Lihat detail booking:", bookingId);

  const message = "Fitur detail booking akan segera diimplementasikan.";

  if (typeof Swal !== "undefined") {
    Swal.fire({
      title: "Detail Booking",
      text: message,
      icon: "info",
    });
  } else {
    alert(message);
  }
}

// Booking ulang (placeholder)
function bookingUlang(bookingId) {
  console.log("Booking ulang:", bookingId);

  const message = "Fitur booking ulang akan segera diimplementasikan.";

  if (typeof Swal !== "undefined") {
    Swal.fire({
      title: "Booking Ulang",
      text: message,
      icon: "info",
    });
  } else {
    alert(message);
  }
}

// Format tanggal
function formatTanggal(tanggal) {
  if (!tanggal) return "-";

  try {
    const date = new Date(tanggal);
    return date.toLocaleDateString("id-ID", {
      day: "numeric",
      month: "short",
      year: "numeric",
    });
  } catch (error) {
    return tanggal;
  }
}

// Format waktu
function formatWaktu(waktu) {
  if (!waktu) return "-";

  if (waktu.length >= 5) {
    return waktu.substring(0, 5);
  }
  return waktu;
}

// Escape HTML
function escapeHtml(text) {
  if (!text) return "";

  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

// Show no bookings message
function showNoBookingsMessage(container) {
  container.innerHTML = `
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="bi bi-calendar-x fs-1 mb-3 d-block"></i>
                <h5>Belum Ada Booking</h5>
                <p class="mb-0">Anda belum memiliki booking ruangan. Silakan lakukan booking melalui tab "Booking Ruangan".</p>
            </div>
        </div>
    `;
}

// Show error message
function showErrorMessage(container) {
  container.innerHTML = `
        <div class="col-12">
            <div class="alert alert-danger text-center">
                <i class="bi bi-exclamation-triangle fs-1 mb-3 d-block"></i>
                <h5>Error Loading Data</h5>
                <p class="mb-3">Gagal memuat data booking. Silakan coba lagi.</p>
                <button class="btn btn-outline-danger" onclick="loadDaftarBookingSaya()">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Muat Ulang
                </button>
            </div>
        </div>
    `;
}

// Apply filters
function applyFilters() {
  const filterNama = document.getElementById("filterNamaBookingSaya");
  const filterStatus = document.getElementById("filterStatusBookingSaya");
  const filterTanggal = document.getElementById("filterTanggalBookingSaya");

  const namaValue = filterNama ? filterNama.value.toLowerCase() : "";
  const statusValue = filterStatus ? filterStatus.value : "";
  const tanggalValue = filterTanggal ? filterTanggal.value : "";

  filteredData = daftarBookingData.filter(function (booking) {
    const namaMatch =
      !namaValue ||
      (booking.nama_ruangan &&
        booking.nama_ruangan.toLowerCase().indexOf(namaValue) !== -1);
    const statusMatch = !statusValue || booking.status === statusValue;
    const tanggalMatch = !tanggalValue || booking.tanggal === tanggalValue;

    return namaMatch && statusMatch && tanggalMatch;
  });

  displayBookings(filteredData, {
    user_role: window.currentUserRole || "user",
  });
}

// Reset filters
function resetFilterBookingSaya() {
  const filterNama = document.getElementById("filterNamaBookingSaya");
  const filterStatus = document.getElementById("filterStatusBookingSaya");
  const filterTanggal = document.getElementById("filterTanggalBookingSaya");

  if (filterNama) filterNama.value = "";
  if (filterStatus) filterStatus.value = "";
  if (filterTanggal) filterTanggal.value = "";

  filteredData = JSON.parse(JSON.stringify(daftarBookingData));
  displayBookings(filteredData);
}

// Export functions to global scope for onclick handlers
window.requestConfirmBooking = requestConfirmBooking;
window.lihatDetailBooking = lihatDetailBooking;
window.bookingUlang = bookingUlang;
window.resetFilterBookingSaya = resetFilterBookingSaya;
window.loadDaftarBookingSaya = loadDaftarBookingSaya;

// ===== REQUEST CONFIRM WITH FILE UPLOAD FUNCTIONS =====

// Function untuk request confirm dengan modal upload file
function requestConfirmBooking(bookingId, namaRuangan) {
  console.log("Request confirm for booking:", bookingId);

  // Create modal HTML untuk upload file
  const modalHtml = `
        <div class="modal fade" id="requestConfirmModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Request Confirm - ${namaRuangan}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="requestConfirmForm" enctype="multipart/form-data">
                            <input type="hidden" name="booking_id" value="${bookingId}">
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Untuk melanjutkan request confirm, silakan upload surat permohonan dalam format PDF.
                            </div>
                            
                            <div class="mb-3">
                                <label for="surat_permohonan" class="form-label">
                                    <i class="bi bi-file-pdf me-1"></i>
                                    Surat Permohonan <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" id="surat_permohonan" name="surat_permohonan" 
                                       accept=".pdf" required>
                                <div class="form-text">
                                    Format: PDF, Maksimal 2MB
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-warning" onclick="submitRequestConfirm()">
                            <i class="bi bi-upload me-1"></i>
                            Upload & Request Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

  // Remove existing modal jika ada
  const existingModal = document.getElementById("requestConfirmModal");
  if (existingModal) {
    existingModal.remove();
  }

  // Add modal ke body
  document.body.insertAdjacentHTML("beforeend", modalHtml);

  // Show modal
  const modal = new bootstrap.Modal(
    document.getElementById("requestConfirmModal"),
  );
  modal.show();
}

// Function untuk submit form request confirm dengan file
function submitRequestConfirm() {
  const form = document.getElementById("requestConfirmForm");
  const fileInput = document.getElementById("surat_permohonan");
  const submitBtn = document.querySelector("#requestConfirmModal .btn-warning");

  // Validasi file
  if (!fileInput.files.length) {
    alert("Silakan pilih file surat permohonan terlebih dahulu.");
    return;
  }

  const file = fileInput.files[0];

  // Validasi format PDF
  if (file.type !== "application/pdf") {
    alert("File harus dalam format PDF.");
    return;
  }

  // Validasi ukuran file (2MB)
  if (file.size > 2 * 1024 * 1024) {
    alert("Ukuran file tidak boleh lebih dari 2MB.");
    return;
  }

  // Disable button dan show loading
  const originalText = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML =
    '<i class="spinner-border spinner-border-sm me-1"></i>Uploading...';

  // Prepare FormData
  const formData = new FormData(form);

  // Check if baseUrl is defined
  const apiUrl =
    (typeof baseUrl !== "undefined" ? baseUrl : "/") +
    "user/ruangan/requestConfirm";

  // Submit form dengan file upload
  fetch(apiUrl, {
    method: "POST",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
    },
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok: " + response.status);
      }
      return response.json();
    })
    .then((data) => {
      console.log("Request confirm response:", data);

      if (data.success) {
        // Close modal
        const modal = bootstrap.Modal.getInstance(
          document.getElementById("requestConfirmModal"),
        );
        modal.hide();

        // Show success message
        if (typeof Swal !== "undefined") {
          Swal.fire({
            title: "Berhasil!",
            text: data.message,
            icon: "success",
            confirmButtonColor: "#28a745",
          }).then(() => {
            // Reload data booking
            loadDaftarBookingSaya();
          });
        } else {
          alert(data.message);
          // Reload data booking
          loadDaftarBookingSaya();
        }
      } else {
        throw new Error(data.message || "Gagal melakukan request confirm");
      }
    })
    .catch((error) => {
      console.error("Error request confirm:", error);

      if (typeof Swal !== "undefined") {
        Swal.fire({
          title: "Gagal!",
          text: error.message,
          icon: "error",
          confirmButtonColor: "#dc3545",
        });
      } else {
        alert("Error: " + error.message);
      }
    })
    .finally(() => {
      // Reset button
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    });
}

// Update lihatDetailBooking untuk handle source table
function lihatDetailBooking(bookingId, sourceTable) {
  console.log("Lihat detail booking:", bookingId, "from table:", sourceTable);
  // Implementation untuk detail booking
  alert(
    "Detail booking " + bookingId + " dari tabel " + (sourceTable || "booking"),
  );
}

// Ensure global access
window.requestConfirmBooking = requestConfirmBooking;
window.submitRequestConfirm = submitRequestConfirm;
