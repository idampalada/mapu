document.addEventListener("DOMContentLoaded", function () {
  initializePinjamRuanganForm();
  initializeVerifikasiHandlers();
  initializeFilters();

  const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  if (tooltips.length > 0) {
    tooltips.forEach((tooltip) => new bootstrap.Tooltip(tooltip));
  }
});

function initializeFilters() {
  const filters = {
    nama: document.getElementById("filterNama"),
    kapasitas: document.getElementById("filterKapasitas"),
    status: document.getElementById("filterStatus"),
    fasilitas: document.getElementById("filterFasilitas"),
  };

  Object.values(filters).forEach((filter) => {
    if (filter) {
      filter.addEventListener("input", () => applyFilters(filters));
      filter.addEventListener("change", () => applyFilters(filters));
    }
  });

  const resetButton = document.querySelector("button[onclick='resetFilter()']");
  if (resetButton) {
    resetButton.removeEventListener("click", resetFilter);
    resetButton.addEventListener("click", () => resetFilter(filters));
  }
}

function getKapasitasRange(range) {
  const ranges = {
    "1-10": [1, 10],
    "11-30": [11, 30],
    "31-50": [31, 50],
    "50+": [50, Infinity],
  };
  return ranges[range] || [0, Infinity];
}

function applyFilters(filters) {
  const searchQuery = filters.nama.value.toLowerCase().trim();
  const kapasitasRange = filters.kapasitas.value;
  const statusFilter = filters.status.value;
  const fasilitasFilter = filters.fasilitas.value.toLowerCase().trim();

  const ruanganCards = document.querySelectorAll(".room-card");
  let visibleCount = 0;

  ruanganCards.forEach((card) => {
    let showCard = true;

    if (searchQuery) {
      const namaRuangan = card.getAttribute("data-nama");
      if (!namaRuangan || !namaRuangan.includes(searchQuery)) {
        showCard = false;
      }
    }

    if (kapasitasRange && showCard) {
      const kapasitas = parseInt(card.getAttribute("data-kapasitas"));
      const [min, max] = getKapasitasRange(kapasitasRange);

      if (
        isNaN(kapasitas) ||
        kapasitas < min ||
        (max !== Infinity && kapasitas > max)
      ) {
        showCard = false;
      }
    }

    if (statusFilter && showCard) {
      const status = card.getAttribute("data-status");
      if (!status || status !== statusFilter) {
        showCard = false;
      }
    }

    if (fasilitasFilter && showCard) {
      const fasilitas = card.getAttribute("data-fasilitas");
      if (!fasilitas || !fasilitas.includes(fasilitasFilter)) {
        showCard = false;
      }
    }

    card.style.display = showCard ? "" : "none";
    if (showCard) visibleCount++;
  });

  updateNoResultsMessage(visibleCount);
}

function updateNoResultsMessage(visibleCount) {
  const cardGrid = document.querySelector(".card-grid");
  let noResultsDiv = document.getElementById("noResultsMessage");

  if (visibleCount === 0) {
    if (!noResultsDiv) {
      noResultsDiv = document.createElement("div");
      noResultsDiv.id = "noResultsMessage";
      noResultsDiv.className = "alert alert-info text-center mt-4";
      noResultsDiv.textContent =
        "Tidak ada ruangan yang sesuai dengan filter yang dipilih";
      cardGrid.appendChild(noResultsDiv);
    }
  } else if (noResultsDiv) {
    noResultsDiv.remove();
  }
}

function resetFilter(filters) {
  Object.values(filters).forEach((filter) => {
    if (filter) filter.value = "";
  });

  const ruanganCards = document.querySelectorAll(".room-card");
  ruanganCards.forEach((card) => (card.style.display = ""));

  const noResultsDiv = document.getElementById("noResultsMessage");
  if (noResultsDiv) noResultsDiv.remove();
}

function initializePinjamRuanganForm() {
  const formPinjamRuangan = document.getElementById("formPinjamRuangan");
  if (formPinjamRuangan) {
    formPinjamRuangan.addEventListener("submit", handlePinjamRuanganSubmit);
  }

  const tanggalMulai = document.getElementById("tanggal_mulai");
  const tanggalSelesai = document.getElementById("tanggal_selesai");
  if (tanggalMulai && tanggalSelesai) {
    tanggalMulai.addEventListener("change", checkRuanganAvailability);
    tanggalSelesai.addEventListener("change", checkRuanganAvailability);
  }

  const ruanganSelect = document.getElementById("ruangan_id");
  if (ruanganSelect) {
    ruanganSelect.addEventListener("change", checkRuanganAvailability);
  }
}

function checkRuanganAvailability() {
  const ruanganId = document.getElementById("ruangan_id").value;
  const tanggalMulai = document.getElementById("tanggal_mulai").value;
  const tanggalSelesai = document.getElementById("tanggal_selesai").value;
  const submitButton = document.querySelector(
    '#formPinjamRuangan button[type="submit"]'
  );

  if (!ruanganId || !tanggalMulai || !tanggalSelesai) {
    return;
  }

  fetch(
    `/user/ruangan/check-availability?ruangan_id=${ruanganId}&tanggal_mulai=${tanggalMulai}&tanggal_selesai=${tanggalSelesai}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        if (!data.available) {
          Swal.fire({
            icon: "error",
            title: "Ruangan Tidak Tersedia",
            text: "Ruangan sudah dibooking untuk waktu yang dipilih",
            confirmButtonColor: "#dc3545",
          });
          submitButton.disabled = true;
        } else {
          submitButton.disabled = false;
        }
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function handlePinjamRuanganSubmit(e, formData) {
  e.preventDefault();

  const fileInput = document.getElementById("surat_permohonan");
  const file = fileInput.files[0];

  if (file.type !== "application/pdf") {
    Swal.fire({
      icon: "error",
      title: "Gagal Upload",
      text: "Mohon upload file dalam format PDF",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  const maxSize = 2 * 1024 * 1024;

  if (file.size > maxSize) {
    Swal.fire({
      icon: "error",
      title: "Ukuran file terlalu besar",
      text: "Ukuran file maksimal 2MB",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  Swal.fire({
    title: "Mohon Tunggu",
    text: "Sedang memproses peminjaman...",
    allowOutsideClick: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  fetch("/user/ruangan/pinjam", {
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
          confirmButtonText: "OK",
          confirmButtonColor: "#198754",
        }).then(() => {
          window.location.reload();
        });
      } else {
        throw new Error(data.error || "Gagal mengajukan peminjaman");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Gagal!",
        text: error.message,
        confirmButtonText: "Tutup",
        confirmButtonColor: "#dc3545",
      });
    });
}

// FUNGSI ESCAPE HTML UNTUK KEAMANAN
function escapeHtml(text) {
  if (typeof text !== "string") {
    return String(text);
  }
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return text.replace(/[&<>"']/g, function (m) {
    return map[m];
  });
}

// TIME PICKER VARIABLES
let selectedStartTime = null;
let selectedEndTime = null;
let existingBookings = [];

// TIME PICKER FUNCTIONS
function addTimePickerStyles() {
  // CSS sudah di-load dari file terpisah, tidak perlu inline CSS lagi
  console.log("Time picker styles loaded from external CSS file");
  return true;
}

function generateTimeSlots() {
  const slots = [];
  const startHour = 7; // 07:00
  const endHour = 17; // 17:00

  for (let hour = startHour; hour <= endHour; hour++) {
    for (let minute = 0; minute < 60; minute += 30) {
      // interval 30 menit
      const timeString = `${hour.toString().padStart(2, "0")}:${minute
        .toString()
        .padStart(2, "0")}`;
      slots.push(timeString);
    }
  }

  return slots;
}

function initializeTimePicker(ruanganId) {
  const timeSlots = generateTimeSlots();
  const timeRuler = document.getElementById("time_ruler");

  timeRuler.innerHTML = "";

  timeSlots.forEach((time) => {
    const slot = document.createElement("div");
    slot.className = "time-slot available";
    slot.textContent = time;
    slot.dataset.time = time;
    slot.tabIndex = 0;

    slot.addEventListener("click", () => handleTimeSlotClick(time, ruanganId));

    slot.addEventListener("keydown", (e) => {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        slot.click();
      }
    });

    timeRuler.appendChild(slot);
  });
}

function handleTimeSlotClick(time, ruanganId) {
  const slot = document.querySelector(`[data-time="${time}"]`);

  // Check if slot is blocked
  if (slot.classList.contains("booked") || isTimeBooked(time)) {
    const booking = getBookingForTime(time);
    const statusText =
      booking?.status === "disetujui"
        ? "sudah disetujui"
        : "menunggu verifikasi";

    Swal.fire({
      icon: "error",
      title: "Waktu Tidak Tersedia",
      html: `Waktu ini sudah dibooking (${statusText}):<br>
                   <strong>${booking?.waktu_mulai} - ${booking?.waktu_selesai}</strong><br>
                   Keperluan: ${booking?.keperluan}<br>
                   PIC: ${booking?.nama_penanggung_jawab}`,
      confirmButtonColor: "#dc3545",
    });
    return false;
  }

  // Continue with normal selection logic
  if (selectedStartTime === time || selectedEndTime === time) {
    resetTimeSelection();
    return;
  }

  if (!selectedStartTime) {
    selectedStartTime = time;
    updateTimeDisplay();
    updateTimeSlotStyles();
  } else if (!selectedEndTime) {
    if (time <= selectedStartTime) {
      Swal.fire({
        icon: "warning",
        title: "Waktu Tidak Valid",
        text: "Waktu selesai harus setelah waktu mulai",
        confirmButtonColor: "#dc3545",
      });
      return;
    }

    selectedEndTime = time;

    const hasConflict = checkTimeConflict(selectedStartTime, selectedEndTime);
    if (hasConflict) {
      showConflictWarning(hasConflict);
      return;
    }

    updateTimeDisplay();
    updateTimeSlotStyles();
    enableSubmitButton();
  } else {
    resetTimeSelection();
    selectedStartTime = time;
    updateTimeDisplay();
    updateTimeSlotStyles();
  }
}

function resetTimeSelection() {
  selectedStartTime = null;
  selectedEndTime = null;
  updateTimeDisplay();
  updateTimeSlotStyles();
  disableSubmitButton();
  hideConflictWarning();
}

function updateTimeDisplay() {
  document.getElementById("display_waktu_mulai").textContent =
    selectedStartTime || "Belum dipilih";
  document.getElementById("display_waktu_selesai").textContent =
    selectedEndTime || "Belum dipilih";

  document.getElementById("waktu_mulai").value = selectedStartTime || "";
  document.getElementById("waktu_selesai").value = selectedEndTime || "";

  if (selectedStartTime && selectedEndTime) {
    const duration = calculateDuration(selectedStartTime, selectedEndTime);
    document.getElementById("duration_text").textContent = duration;
    document.getElementById("duration_display").style.display = "block";
  } else {
    document.getElementById("duration_display").style.display = "none";
  }
}

function updateTimeSlotStyles() {
  document.querySelectorAll(".time-slot").forEach((slot) => {
    const time = slot.dataset.time;

    // Reset classes
    slot.classList.remove(
      "selected-start",
      "selected-end",
      "in-range",
      "conflict-highlight"
    );

    // Set base class berdasarkan availability
    if (isTimeBooked(time)) {
      slot.className = "time-slot booked";
    } else {
      slot.className = "time-slot available";
    }
  });

  // Apply selection styles
  if (selectedStartTime) {
    const startSlot = document.querySelector(
      `[data-time="${selectedStartTime}"]`
    );
    if (startSlot && !startSlot.classList.contains("booked")) {
      startSlot.classList.remove("available");
      startSlot.classList.add("selected-start");
    }
  }

  if (selectedEndTime) {
    const endSlot = document.querySelector(`[data-time="${selectedEndTime}"]`);
    if (endSlot && !endSlot.classList.contains("booked")) {
      endSlot.classList.remove("available");
      endSlot.classList.add("selected-end");
    }
  }

  // Apply range style
  if (selectedStartTime && selectedEndTime) {
    const timeSlots = Array.from(document.querySelectorAll(".time-slot"));
    timeSlots.forEach((slot) => {
      const time = slot.dataset.time;
      if (
        time > selectedStartTime &&
        time < selectedEndTime &&
        !slot.classList.contains("booked")
      ) {
        slot.classList.remove("available");
        slot.classList.add("in-range");
      }
    });
  }
}

function checkTimeConflict(startTime, endTime) {
  for (let booking of existingBookings) {
    const bookingStart = booking.waktu_mulai.substring(0, 5);
    const bookingEnd = booking.waktu_selesai.substring(0, 5);

    // Cek berbagai jenis konflik
    const conflict1 = startTime >= bookingStart && startTime < bookingEnd; // Start time dalam booking
    const conflict2 = endTime > bookingStart && endTime <= bookingEnd; // End time dalam booking
    const conflict3 = startTime <= bookingStart && endTime >= bookingEnd; // Booking dalam range baru

    if (conflict1 || conflict2 || conflict3) {
      return booking;
    }
  }
  return null;
}

function showConflictWarning(conflictBooking) {
  const warningDiv = document.getElementById("conflict_warning");
  const messageDiv = document.getElementById("conflict_message");

  const statusText =
    conflictBooking.status === "disetujui"
      ? "sudah disetujui"
      : "menunggu verifikasi";
  const statusColor =
    conflictBooking.status === "disetujui" ? "text-danger" : "text-warning";

  messageDiv.innerHTML = `
    <div class="d-flex align-items-start">
      <i class="bi bi-exclamation-triangle me-2 mt-1"></i>
      <div>
        <strong>Konflik Waktu!</strong><br>
        Waktu yang dipilih bertabrakan dengan booking yang <span class="${statusColor}">${statusText}</span>:<br>
        <div class="mt-2 p-2 bg-light rounded">
          <strong>⏰ ${conflictBooking.waktu_mulai} - ${conflictBooking.waktu_selesai}</strong><br>
          <small><strong>Keperluan:</strong> ${conflictBooking.keperluan}</small><br>
          <small><strong>PIC:</strong> ${conflictBooking.nama_penanggung_jawab}</small>
        </div>
        <small class="text-muted mt-2 d-block">
          💡 <strong>Saran:</strong> Pilih waktu yang tidak bersinggungan dengan booking di atas.
        </small>
      </div>
    </div>
  `;

  warningDiv.style.display = "block";
  warningDiv.scrollIntoView({ behavior: "smooth", block: "center" });

  selectedEndTime = null;
  updateTimeDisplay();
  updateTimeSlotStyles();
  disableSubmitButton();
}

function hideConflictWarning() {
  document.getElementById("conflict_warning").style.display = "none";
}

function calculateDuration(startTime, endTime) {
  const start = new Date(`2000-01-01T${startTime}:00`);
  const end = new Date(`2000-01-01T${endTime}:00`);
  const diffMs = end - start;
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
  const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

  if (diffHours > 0 && diffMinutes > 0) {
    return `${diffHours} jam ${diffMinutes} menit`;
  } else if (diffHours > 0) {
    return `${diffHours} jam`;
  } else {
    return `${diffMinutes} menit`;
  }
}

function enableSubmitButton() {
  const submitBtn = document.getElementById("submit_booking");
  if (submitBtn) {
    submitBtn.disabled = false;
  }
}

function disableSubmitButton() {
  const submitBtn = document.getElementById("submit_booking");
  if (submitBtn) {
    submitBtn.disabled = true;
  }
}

// ENHANCED loadExistingBookings dengan better error handling
// ENHANCED: loadExistingBookings dengan dual mode (API + fallback)
function loadExistingBookings(ruanganId, tanggal) {
  const baseUrl =
    document.querySelector("base")?.href || window.location.origin;

  console.log(`Loading bookings for ruangan ${ruanganId} on ${tanggal}`);

  document.getElementById("booking_list").innerHTML =
    '<div class="text-center"><i class="bi bi-hourglass-split"></i> Memuat data booking...</div>';

  resetTimeSelection();

  const url = `${baseUrl}/user/ruangan/getBookingByDate?ruangan_id=${ruanganId}&tanggal=${tanggal}`;

  fetch(url, {
    method: "GET",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        existingBookings = data.data || [];

        // Clean time format
        if (existingBookings.length > 0) {
          existingBookings = existingBookings.map((booking) => ({
            ...booking,
            waktu_mulai: booking.waktu_mulai.substring(0, 5),
            waktu_selesai: booking.waktu_selesai.substring(0, 5),
          }));
        }

        // Update displays
        updateExistingBookingsDisplay();
        updateBookedTimeSlots();
        showAvailabilityInfo();
      } else {
        throw new Error(data.message || "Server error");
      }
    })
    .catch((error) => {
      console.error("Error loading bookings:", error);
      existingBookings = [];
      updateExistingBookingsDisplay();
      updateBookedTimeSlots();
      showToast("Gagal memuat data booking: " + error.message, "error", 5000);
    });
}

function validateTimeSlotBlocking() {
  console.log("=== VALIDATING TIME SLOT BLOCKING ===");

  if (!existingBookings || existingBookings.length === 0) {
    console.log("No existing bookings found");
    return;
  }

  // Cek setiap booking dan pastikan time slots terkait ter-block
  existingBookings.forEach((booking, index) => {
    console.log(
      `Booking ${index + 1}: ${booking.waktu_mulai} - ${
        booking.waktu_selesai
      } (${booking.status})`
    );

    // Generate all time slots yang harus ter-block untuk booking ini
    const blockedSlots = generateTimeSlotsBetween(
      booking.waktu_mulai,
      booking.waktu_selesai
    );

    blockedSlots.forEach((timeSlot) => {
      const slotElement = document.querySelector(`[data-time="${timeSlot}"]`);
      if (slotElement) {
        // Force update class jika belum ter-block
        if (!slotElement.classList.contains("booked")) {
          console.log(`FORCE BLOCKING time slot: ${timeSlot}`);
          slotElement.classList.remove("available");
          slotElement.classList.add("booked");
          slotElement.title = `Dibooking: ${booking.waktu_mulai}-${booking.waktu_selesai}\nStatus: ${booking.status}`;
        }
      }
    });
  });
}

function generateTimeSlotsBetween(startTime, endTime) {
  const slots = [];
  const start = convertTimeToMinutes(startTime);
  const end = convertTimeToMinutes(endTime);

  // Generate slot setiap 30 menit dari start sampai sebelum end
  for (let minutes = start; minutes < end; minutes += 30) {
    const timeSlot = convertMinutesToTime(minutes);
    slots.push(timeSlot);
  }

  console.log(
    `Generated blocked slots between ${startTime}-${endTime}:`,
    slots
  );
  return slots;
}

function convertTimeToMinutes(timeStr) {
  const [hours, minutes] = timeStr.split(":").map(Number);
  return hours * 60 + minutes;
}

function convertMinutesToTime(minutes) {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  return `${hours.toString().padStart(2, "0")}:${mins
    .toString()
    .padStart(2, "0")}`;
}

function updateExistingBookingsDisplay() {
  const bookingList = document.getElementById("booking_list");
  const existingBookingsDiv = document.getElementById("existing_bookings");

  if (existingBookings.length === 0) {
    existingBookingsDiv.style.display = "none";
    return;
  }

  existingBookingsDiv.style.display = "block";

  const bookingsHtml = existingBookings
    .map((booking) => {
      const statusColor =
        booking.status === "disetujui" ? "success" : "warning";
      const statusText =
        booking.status === "disetujui" ? "Disetujui" : "Menunggu Verifikasi";

      return `
      <div class="booking-item">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <strong>${booking.waktu_mulai} - ${booking.waktu_selesai}</strong>
            <span class="badge bg-${statusColor} ms-2">${statusText}</span>
          </div>
        </div>
        <div class="mt-1">
          <small><strong>Keperluan:</strong> ${
            booking.keperluan || "Tidak ada keterangan"
          }</small><br>
          <small><strong>PIC:</strong> ${
            booking.nama_penanggung_jawab || "-"
          }</small>
        </div>
      </div>
    `;
    })
    .join("");

  bookingList.innerHTML = bookingsHtml;
}

function updateBookedTimeSlots() {
  document.querySelectorAll(".time-slot").forEach((slot) => {
    const time = slot.dataset.time;
    const isBooked = isTimeBooked(time);

    if (isBooked) {
      slot.classList.remove(
        "available",
        "selected-start",
        "selected-end",
        "in-range"
      );
      slot.classList.add("booked");

      const booking = getBookingForTime(time);
      if (booking) {
        const statusText =
          booking.status === "disetujui" ? "Disetujui" : "Menunggu Verifikasi";
        slot.title = `${statusText}: ${booking.waktu_mulai}-${booking.waktu_selesai}\nKeperluan: ${booking.keperluan}`;
      }
    } else {
      slot.classList.remove("booked");
      slot.classList.add("available");
      slot.title = "Klik untuk pilih waktu";
    }
  });
}

function getBookingForTime(time) {
  return existingBookings.find((booking) => {
    const bookingStart = booking.waktu_mulai.substring(0, 5);
    const bookingEnd = booking.waktu_selesai.substring(0, 5);
    const currentTime = time.substring(0, 5);

    return currentTime >= bookingStart && currentTime < bookingEnd;
  });
}

function showAvailabilityInfo() {
  const totalSlots = document.querySelectorAll(".time-slot").length;
  const bookedSlots = document.querySelectorAll(".time-slot.booked").length;
  const availableSlots = totalSlots - bookedSlots;

  const availabilityPercentage = Math.round(
    (availableSlots / totalSlots) * 100
  );

  let message = "";
  let type = "info";

  if (availabilityPercentage >= 80) {
    message = `🟢 Ruangan sangat tersedia (${availabilityPercentage}% slot kosong)`;
    type = "success";
  } else if (availabilityPercentage >= 50) {
    message = `🟡 Ruangan cukup tersedia (${availabilityPercentage}% slot kosong)`;
    type = "info";
  } else if (availabilityPercentage >= 20) {
    message = `🟠 Ruangan terbatas (${availabilityPercentage}% slot kosong)`;
    type = "warning";
  } else {
    message = `🔴 Ruangan sangat terbatas (${availabilityPercentage}% slot kosong)`;
    type = "danger";
  }

  setTimeout(() => {
    showToast(message, type, 4000);
  }, 500);
}

function showToast(message, type = "info", duration = 3000) {
  const toast = document.createElement("div");
  const typeClass =
    type === "info"
      ? "info"
      : type === "success"
      ? "success"
      : type === "warning"
      ? "warning"
      : "danger";

  toast.className = `alert alert-${typeClass} position-fixed shadow-sm`;
  toast.style.cssText = `
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 350px;
    animation: slideIn 0.3s ease-out;
    border-left: 4px solid var(--bs-${typeClass});
  `;
  toast.innerHTML = `
    <div class="d-flex align-items-start">
      <i class="bi bi-${
        type === "success"
          ? "check-circle"
          : type === "warning"
          ? "exclamation-triangle"
          : type === "danger"
          ? "x-circle"
          : "info-circle"
      } me-2 mt-1"></i>
      <div class="flex-grow-1">${message}</div>
      <button type="button" class="btn-close ms-2" onclick="this.parentElement.parentElement.remove()"></button>
    </div>
  `;

  document.body.appendChild(toast);

  setTimeout(() => {
    if (toast.parentElement) {
      toast.style.animation = "fadeOut 0.3s ease-out forwards";
      setTimeout(() => toast.remove(), 300);
    }
  }, duration);
}

// MAIN MODAL FUNCTION
function bukaPinjamModal(ruanganId, namaRuangan, kapasitas, keterangan = "") {
  const baseUrl =
    document.querySelector("base")?.href || window.location.origin;

  const cleanNamaRuangan = escapeHtml(namaRuangan);
  const cleanKeterangan = escapeHtml(keterangan);
  const cleanRuanganId = parseInt(ruanganId);
  const cleanKapasitas = parseInt(kapasitas);

  if (!cleanRuanganId || !cleanNamaRuangan || !cleanKapasitas) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Data ruangan tidak valid",
    });
    return;
  }

  const keteranganSection = cleanKeterangan
    ? `
        <div class="alert alert-info mb-3">
            <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Informasi Fasilitas</h6>
            <p class="mb-0">${cleanKeterangan}</p>
        </div>
    `
    : "";

  const modalContent = `
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pinjam Ruangan: ${cleanNamaRuangan}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formPinjamRuanganModal" action="${baseUrl}/user/ruangan/pinjam" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="ruangan_id" value="${cleanRuanganId}">
                        
                        ${keteranganSection}
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_penanggung_jawab" class="form-label">Nama Penanggung Jawab</label>
                                    <input type="text" class="form-control" id="nama_penanggung_jawab" name="nama_penanggung_jawab" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="unit_organisasi">Unit Organisasi</label>
                                    <select class="form-control" name="unit_organisasi" required>
                                        <option value="" class="text-muted" disabled selected>Pilih</option>
                                        <option value="Setjen">Sekretariat Jenderal</option>
                                        <option value="Itjen">Inspektorat Jenderal</option>
                                        <option value="Ditjen Sumber Daya Air">Direktorat Jenderal Sumber Daya Air</option>
                                        <option value="Ditjen Bina Marga">Direktorat Jenderal Bina Marga</option>
                                        <option value="Ditjen Cipta Karya">Direktorat Jenderal Cipta Karya</option>
                                        <option value="Ditjen Perumahan">Direktorat Jenderal Perumahan</option>
                                        <option value="Ditjen Bina Konstruksi">Direktorat Jenderal Bina Konstruksi</option>
                                        <option value="Ditjen Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan">Direktorat Jenderal Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan</option>
                                        <option value="BPIW">Badan Pengembangan Infrastruktur Wilayah</option>
                                        <option value="BPSDM">Badan Pengembangan Sumber Daya Manusia</option>
                                        <option value="BPJT">Badan Pengatur Jalan Tol</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">Tanggal Peminjaman</label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required
                                           min="${
                                             new Date()
                                               .toISOString()
                                               .split("T")[0]
                                           }">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="jumlah_peserta" class="form-label">Jumlah Peserta</label>
                                    <input type="number" class="form-control" id="jumlah_peserta" name="jumlah_peserta" 
                                           required min="1" max="${cleanKapasitas}">
                                    <div class="form-text">Maksimal ${cleanKapasitas} orang</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="keperluan" class="form-label">Keperluan</label>
                                    <textarea class="form-control" id="keperluan" name="keperluan" 
                                              rows="3" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="surat_permohonan" class="form-label">Surat Permohonan (PDF)</label>
                                    <input type="file" class="form-control" id="surat_permohonan" name="surat_permohonan" 
                                           accept=".pdf" required>
                                    <div class="form-text">Upload file dalam format PDF. Max 2MB</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <input type="hidden" id="waktu_mulai" name="waktu_mulai" required>
                                <input type="hidden" id="waktu_selesai" name="waktu_selesai" required>
                                
                                <div id="existing_bookings" class="existing-bookings mb-3" style="display: none;">
                                    <h6><i class="bi bi-info-circle"></i> Booking yang Sudah Ada:</h6>
                                    <div id="booking_list"></div>
                                </div>

                                <div class="time-picker-container">
                                    <h6 class="text-center mb-3">
                                        <i class="bi bi-clock"></i>
                                        Pilih Waktu Booking
                                    </h6>
                                    
                                    <div class="legend mb-3">
                                        <div class="legend-item">
                                            <div class="legend-color available"></div>
                                            <span>Tersedia</span>
                                        </div>
                                        <div class="legend-item">
                                            <div class="legend-color booked"></div>
                                            <span>Dibooking</span>
                                        </div>
                                        <div class="legend-item">
                                            <div class="legend-color selected-start"></div>
                                            <span>Mulai</span>
                                        </div>
                                        <div class="legend-item">
                                            <div class="legend-color selected-end"></div>
                                            <span>Selesai</span>
                                        </div>
                                    </div>
                                    
                                    <div class="time-ruler" id="time_ruler">
                                    </div>
                                    
                                    <div class="booking-info mt-3">
                                        <div class="row">
                                            <div class="col-6">
                                                <label class="form-label">Waktu Mulai:</label>
                                                <div class="selected-time" id="display_waktu_mulai">Belum dipilih</div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Waktu Selesai:</label>
                                                <div class="selected-time" id="display_waktu_selesai">Belum dipilih</div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle"></i>
                                                Klik untuk memilih waktu mulai, klik lagi untuk waktu selesai
                                            </small>
                                        </div>
                                        <div id="duration_display" class="mt-2" style="display: none;">
                                            <span class="badge bg-info">Durasi: <span id="duration_text"></span></span>
                                        </div>
                                    </div>
                                    
                                    <div id="conflict_warning" class="conflict-warning" style="display: none;">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <strong>Konflik Waktu!</strong>
                                        <div id="conflict_message"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submit_booking" style="background-color: #133E87;" disabled>
                            Ajukan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;

  const modalElement = document.getElementById("modalPinjamRuangan");
  modalElement.innerHTML = modalContent;

  addTimePickerStyles();

  const modal = new bootstrap.Modal(modalElement);

  modalElement.addEventListener("shown.bs.modal", function () {
    initializeTimePicker(cleanRuanganId);

    const form = document.getElementById("formPinjamRuanganModal");
    if (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();

        const jumlahPeserta = parseInt(
          document.getElementById("jumlah_peserta").value
        );
        if (jumlahPeserta > cleanKapasitas) {
          Swal.fire({
            icon: "error",
            title: "Error!",
            text: `Jumlah peserta tidak boleh melebihi kapasitas ruangan (${cleanKapasitas} orang)`,
            confirmButtonColor: "#dc3545",
          });
          return;
        }

        const waktuMulai = document.getElementById("waktu_mulai").value;
        const waktuSelesai = document.getElementById("waktu_selesai").value;

        if (!waktuMulai || !waktuSelesai) {
          Swal.fire({
            icon: "error",
            title: "Error!",
            text: "Silakan pilih waktu mulai dan waktu selesai terlebih dahulu",
            confirmButtonColor: "#dc3545",
          });
          return;
        }

        const formData = new FormData(this);
        handlePinjamRuanganSubmit(e, formData);
      });

      // PERBAIKAN: Auto load booking untuk tanggal hari ini
      const tanggalInput = document.getElementById("tanggal");

      // Set tanggal hari ini sebagai default
      const today = new Date().toISOString().split("T")[0];
      tanggalInput.value = today;

      // Auto load booking untuk hari ini
      console.log(
        `Auto-loading bookings for ruangan ${cleanRuanganId} on ${today}`
      );
      loadExistingBookings(cleanRuanganId, today);

      // Event listener untuk perubahan tanggal
      tanggalInput.addEventListener("change", function () {
        const selectedDate = this.value;
        if (selectedDate) {
          console.log(`Date changed to: ${selectedDate}`);
          resetTimeSelection(); // Reset selection saat ganti tanggal
          loadExistingBookings(cleanRuanganId, selectedDate);
        }
      });
    }
  });

  modal.show();
}

// EDIT AND DELETE FUNCTIONS
function openEditRuangan(id) {
  const baseUrl =
    document.querySelector("base")?.href || window.location.origin;
  const endpoint = `${baseUrl}/admin/ruangan/getDetail/${id}`;

  fetch(endpoint)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        const ruangan = data.data;
        const modal = new bootstrap.Modal(
          document.getElementById("modalEditRuangan")
        );

        const fasilitasExisting = ruangan.fasilitas || "";
        const fasilitasOptions = [
          "TV",
          "Projector",
          "Papan Tulis",
          "Sound System",
          "AC",
          "Wifi",
        ];

        const fasilitasChecked = [];
        const fasilitasLowerCase = fasilitasExisting.toLowerCase();

        fasilitasOptions.forEach((option) => {
          if (fasilitasLowerCase.includes(option.toLowerCase())) {
            fasilitasChecked.push(option);
          }
        });

        let keteranganExisting = fasilitasExisting;
        fasilitasChecked.forEach((item) => {
          keteranganExisting = keteranganExisting.replace(
            new RegExp(item, "gi"),
            ""
          );
        });

        keteranganExisting = keteranganExisting
          .replace(/^[,.\s]+|[,.\s]+$/g, "")
          .replace(/[,.\s]{2,}/g, " ")
          .trim();

        const form = document.getElementById("formEditRuangan");

        document.getElementById("edit_ruangan_id").value = parseInt(ruangan.id);
        document.querySelector('input[name="nama_ruangan"]').value = escapeHtml(
          ruangan.nama_ruangan
        );
        document.querySelector('input[name="lokasi"]').value = escapeHtml(
          ruangan.lokasi
        );
        document.querySelector('input[name="kapasitas"]').value = parseInt(
          ruangan.kapasitas
        );

        const checkboxes = document.querySelectorAll(
          'input[name="fasilitas[]"]'
        );
        checkboxes.forEach((cb) => (cb.checked = false));

        fasilitasChecked.forEach((fasilitas) => {
          const checkbox = document.querySelector(
            `input[name="fasilitas[]"][value="${fasilitas}"]`
          );
          if (checkbox) {
            checkbox.checked = true;
          }
        });

        document.getElementById("edit_keterangan").value = keteranganExisting;

        form.onsubmit = function (e) {
          e.preventDefault();
          const formData = new FormData(this);

          Swal.fire({
            title: "Mohon Tunggu",
            text: "Sedang menyimpan perubahan...",
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
              Swal.showLoading();
            },
          });

          const editEndpoint = `${baseUrl}/admin/User/Ruangan/edit/${ruangan.id}`;

          fetch(editEndpoint, {
            method: "POST",
            body: formData,
            headers: {
              "X-Requested-With": "XMLHttpRequest",
            },
          })
            .then((response) => response.json())
            .then((result) => {
              if (result.success === true || result.success === "true") {
                Swal.fire({
                  icon: "success",
                  title: "Berhasil!",
                  text: result.message || "Data ruangan berhasil diperbarui",
                  confirmButtonText: "OK",
                }).then(() => {
                  window.location.reload();
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Gagal!",
                  text:
                    result.error ||
                    result.message ||
                    "Gagal memperbarui data ruangan",
                  confirmButtonText: "Tutup",
                });
              }
            })
            .catch((error) => {
              console.error("Error:", error);
              Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Terjadi kesalahan pada server",
                confirmButtonText: "Tutup",
              });
            });
        };

        modal.show();
      } else {
        throw new Error(data.message || "Gagal mengambil data ruangan");
      }
    })
    .catch((error) => {
      console.error("Fetch error:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: error.message || "Gagal mengambil data ruangan",
      });
    });
}

function deleteRuangan(id) {
  const cleanId = parseInt(id);
  if (!cleanId) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "ID ruangan tidak valid",
    });
    return;
  }

  Swal.fire({
    title: "Konfirmasi Hapus",
    text: "Anda yakin ingin menghapus ruangan ini? Data yang dihapus tidak dapat dikembalikan!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Mohon Tunggu",
        text: "Sedang menghapus data...",
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      const baseUrl =
        document.querySelector("base")?.href || window.location.origin;

      fetch(`${baseUrl}/admin/ruangan/delete/${cleanId}`, {
        method: "POST",
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
              text: data.message || "Ruangan berhasil dihapus",
              confirmButtonText: "OK",
            }).then(() => {
              window.location.reload();
            });
          } else {
            throw new Error(data.error || "Gagal menghapus ruangan");
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

function initializeVerifikasiHandlers() {
  const formVerifikasi = document.getElementById("formVerifikasiRuangan");
  if (formVerifikasi) {
    formVerifikasi.addEventListener("submit", handleVerifikasiSubmit);
  }
}

function isTimeBooked(time) {
  if (!existingBookings || existingBookings.length === 0) {
    return false;
  }

  return existingBookings.some((booking) => {
    // Pastikan format waktu konsisten (HH:MM)
    const bookingStart = booking.waktu_mulai.substring(0, 5);
    const bookingEnd = booking.waktu_selesai.substring(0, 5);
    const currentTime = time.substring(0, 5);

    // Debug log untuk melihat perbandingan
    console.log(
      `Checking time ${currentTime} against booking ${bookingStart}-${bookingEnd}`
    );

    // Cek apakah waktu berada dalam range booking (include start, exclude end)
    const isInRange = currentTime >= bookingStart && currentTime < bookingEnd;

    if (isInRange) {
      console.log(
        `Time ${currentTime} is BLOCKED by booking ${bookingStart}-${bookingEnd}`
      );
    }

    return isInRange;
  });
}

function forceBlockTimeSlots() {
  console.log("🔒 FORCE BLOCKING TIME SLOTS...");

  if (!existingBookings || existingBookings.length === 0) {
    console.log("No bookings to block");
    return;
  }

  // Generate all blocked time slots
  const blockedTimes = [];
  existingBookings.forEach((booking) => {
    const start = convertTimeToMinutes(booking.waktu_mulai);
    const end = convertTimeToMinutes(booking.waktu_selesai);

    for (let minutes = start; minutes < end; minutes += 30) {
      const timeSlot = convertMinutesToTime(minutes);
      blockedTimes.push(timeSlot);
    }
  });

  console.log("Generated blocked times:", blockedTimes);

  // Force apply blocking styles
  blockedTimes.forEach((time) => {
    const slot = document.querySelector(`[data-time="${time}"]`);
    if (slot) {
      // Method 1: Update classes
      slot.classList.remove(
        "available",
        "selected-start",
        "selected-end",
        "in-range"
      );
      slot.classList.add("booked");

      // Method 2: Force inline styles for maximum priority
      slot.style.cssText = `
        background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%) !important;
        border: 3px solid #f44336 !important;
        color: #c62828 !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
        opacity: 0.9 !important;
        font-weight: bold !important;
        box-shadow: 0 0 15px rgba(244, 67, 54, 0.5) !important;
      `;

      // Method 3: Add data attributes for extra identification
      slot.dataset.blocked = "true";
      slot.dataset.bookingStatus = getBookingForTime(time)?.status || "unknown";

      // Method 4: Add tooltip
      const booking = getBookingForTime(time);
      if (booking) {
        const statusText =
          booking.status === "disetujui" ? "Disetujui" : "Menunggu Verifikasi";
        slot.title = `${statusText}: ${booking.waktu_mulai}-${booking.waktu_selesai}\nKeperluan: ${booking.keperluan}\nPIC: ${booking.nama_penanggung_jawab}`;
      }

      console.log(`✅ FORCE BLOCKED: ${time}`);
    } else {
      console.log(`❌ Slot not found: ${time}`);
    }
  });

  // Verify blocking worked
  const blockedSlots = document.querySelectorAll(".time-slot.booked");
  console.log(`🎯 Successfully blocked ${blockedSlots.length} time slots`);
}

document.addEventListener("DOMContentLoaded", function () {
  let currentDate = new Date();
  let bookingsData = [];
  let calendarInitialized = false;

  // Base URL untuk API calls
  const BASE_URL = window.location.origin;
  const API_ENDPOINT = BASE_URL + "/User/Ruangan/getBookingPublik";

  // Toggle button functionality
  const toggleBtn = document.getElementById("toggleCalendar");
  const calendarContainer = document.getElementById("calendarContainer");
  const buttonText = document.getElementById("calendarButtonText");
  const calendarIcon = document.getElementById("calendarIcon");

  toggleBtn.addEventListener("click", function () {
    if (calendarContainer.style.display === "none") {
      // Show calendar
      calendarContainer.style.display = "block";
      calendarContainer.classList.add("calendar-slide-down");
      buttonText.textContent = "Sembunyikan Kalender";
      calendarIcon.className = "bi bi-calendar-x";

      // Initialize calendar if not done yet
      if (!calendarInitialized) {
        loadAllBookingsData().then(() => {
          initializeCalendar();
          calendarInitialized = true;
        });
      }
    } else {
      // Hide calendar
      calendarContainer.classList.remove("calendar-slide-down");
      calendarContainer.classList.add("calendar-slide-up");

      setTimeout(() => {
        calendarContainer.style.display = "none";
        calendarContainer.classList.remove("calendar-slide-up");
      }, 300);

      buttonText.textContent = "Tampilkan Kalender";
      calendarIcon.className = "bi bi-calendar3";
    }
  });

  // Event listeners untuk navigasi (hanya jika kalender sudah di-show)
  document.getElementById("prevMonth").addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
  });

  document.getElementById("nextMonth").addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
  });

  function loadAllBookingsData() {
    console.log("Loading booking data from:", API_ENDPOINT);
    return fetch(API_ENDPOINT)
      .then((res) => {
        console.log("Response status:", res.status);
        if (!res.ok) {
          throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
      })
      .then((data) => {
        console.log("Raw API response:", data);
        if (data.success) {
          bookingsData = data.data || [];
          console.log("Loaded bookings data:", bookingsData.length, "bookings");

          // Debug: tampilkan semua tanggal booking
          const uniqueDates = [...new Set(bookingsData.map((b) => b.tanggal))];
          console.log("Unique booking dates:", uniqueDates);
        } else {
          console.error("API returned error:", data);
          bookingsData = [];
        }
      })
      .catch((error) => {
        console.error("Error loading bookings:", error);
        bookingsData = [];
      });
  }

  function initializeCalendar() {
    renderCalendar();
  }

  function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    console.log(`Rendering calendar for ${year}-${month + 1}`);

    // Update header
    const monthNames = [
      "Januari",
      "Februari",
      "Maret",
      "April",
      "Mei",
      "Juni",
      "Juli",
      "Agustus",
      "September",
      "Oktober",
      "November",
      "Desember",
    ];
    document.getElementById(
      "currentMonthYear"
    ).textContent = `${monthNames[month]} ${year}`;

    // Clear calendar grid
    const calendarGrid = document.getElementById("calendarGrid");
    calendarGrid.innerHTML = "";

    // Add day headers
    const dayHeaders = [
      "Minggu",
      "Senin",
      "Selasa",
      "Rabu",
      "Kamis",
      "Jumat",
      "Sabtu",
    ];
    dayHeaders.forEach((day) => {
      const headerDiv = document.createElement("div");
      headerDiv.className = "calendar-day-header";
      headerDiv.textContent = day;
      calendarGrid.appendChild(headerDiv);
    });

    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const firstDayWeekday = firstDay.getDay();
    const daysInMonth = lastDay.getDate();

    // Add empty cells for days before first day of month
    for (let i = 0; i < firstDayWeekday; i++) {
      const emptyDiv = document.createElement("div");
      emptyDiv.className = "calendar-day other-month";
      calendarGrid.appendChild(emptyDiv);
    }

    // Add days of current month
    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
      const dayDiv = document.createElement("div");
      dayDiv.className = "calendar-day";

      // Check if it's today
      if (
        year === today.getFullYear() &&
        month === today.getMonth() &&
        day === today.getDate()
      ) {
        dayDiv.classList.add("today");
      }

      // Add day number
      const dayNumber = document.createElement("div");
      dayNumber.className = "day-number";
      dayNumber.textContent = day;
      dayDiv.appendChild(dayNumber);

      // Format tanggal untuk pencarian (YYYY-MM-DD)
      const currentDateStr = `${year}-${String(month + 1).padStart(
        2,
        "0"
      )}-${String(day).padStart(2, "0")}`;

      // Get bookings for this date
      const dayBookings = getBookingsForDate(currentDateStr);

      // Add booking items (maksimal 3 yang ditampilkan) - TANPA ICON
      const displayedBookings = dayBookings.slice(0, 3);
      displayedBookings.forEach((booking, index) => {
        const bookingDiv = document.createElement("div");

        // Tentukan class berdasarkan status
        let statusClass = "";
        if (booking.status === "disetujui" || booking.status === "dipinjam") {
          statusClass = "booking-item";
        } else if (booking.status === "pending") {
          statusClass = "booking-item pending";
        }

        bookingDiv.className = statusClass;

        // Format waktu mulai (ambil jam:menit saja)
        const timeStart = booking.waktu_mulai
          ? typeof booking.waktu_mulai === "string"
            ? booking.waktu_mulai.substring(0, 5)
            : booking.waktu_mulai
          : "";

        // TANPA ICON - hanya waktu + nama ruangan
        const roomName = booking.nama_ruangan || "Ruangan";
        const shortRoomName =
          roomName.length > 12 ? roomName.substring(0, 12) + "..." : roomName;
        const displayText = `${timeStart} ${shortRoomName}`;
        bookingDiv.textContent = displayText;
        bookingDiv.title = `${booking.nama_ruangan} - ${
          booking.keperluan || "Meeting"
        } (${booking.status})`;

        bookingDiv.addEventListener("click", (e) => {
          e.stopPropagation();
          showBookingDetails(currentDateStr, dayBookings);
        });
        dayDiv.appendChild(bookingDiv);
      });

      // Add booking count if there are more bookings
      if (dayBookings.length > 3) {
        const countDiv = document.createElement("div");
        countDiv.className = "booking-count";
        countDiv.textContent = `+${dayBookings.length - 3}`;
        dayDiv.appendChild(countDiv);
      } else if (dayBookings.length > 0 && dayBookings.length <= 3) {
        const countDiv = document.createElement("div");
        countDiv.className = "booking-count";
        countDiv.textContent = dayBookings.length;
        dayDiv.appendChild(countDiv);
      }

      // Add click event for day
      dayDiv.addEventListener("click", () => {
        if (dayBookings.length > 0) {
          showBookingDetails(currentDateStr, dayBookings);
        }
      });

      calendarGrid.appendChild(dayDiv);
    }

    // Fill remaining cells jika diperlukan
    const totalCells = calendarGrid.children.length - 7;
    const remainingCells = 42 - totalCells;
    for (let i = 0; i < remainingCells && totalCells < 35; i++) {
      const emptyDiv = document.createElement("div");
      emptyDiv.className = "calendar-day other-month";
      calendarGrid.appendChild(emptyDiv);
    }

    console.log("Calendar rendered successfully");
  }

  function getBookingsForDate(date) {
    const matchingBookings = bookingsData.filter((booking) => {
      const bookingDate = booking.tanggal;
      const matches = bookingDate === date;
      return matches;
    });

    return matchingBookings;
  }

  function showBookingDetails(date, bookings) {
    const modal = new bootstrap.Modal(
      document.getElementById("modalDetailBooking")
    );
    const modalContent = document.getElementById("modalBookingContent");

    let content = `
            <div class="mb-3">
                <h6><i class="bi bi-calendar3 me-2"></i>Tanggal: ${formatIndonesianDate(
                  date
                )}</h6>
                <p class="text-muted">Total ${
                  bookings.length
                } booking pada hari ini</p>
            </div>
        `;

    if (bookings.length === 0) {
      content +=
        '<div class="alert alert-info">Tidak ada booking pada tanggal ini.</div>';
    } else {
      bookings.forEach((booking) => {
        const statusClass = `status-${booking.status}`;
        const timeStart = booking.waktu_mulai
          ? booking.waktu_mulai.substring(0, 5)
          : "";
        const timeEnd = booking.waktu_selesai
          ? booking.waktu_selesai.substring(0, 5)
          : "";

        content += `
                    <div class="booking-detail-item">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-1">${
                              booking.nama_ruangan || "Ruangan"
                            }</h6>
                            <span class="status-badge ${statusClass}">${
          booking.status
        }</span>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <small class="text-muted">Waktu:</small><br>
                                <strong>${timeStart} - ${timeEnd} WIB</strong>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted">Keperluan:</small><br>
                                <span>${booking.keperluan || "Meeting"}</span>
                            </div>
                        </div>
                        ${
                          booking.nama_penanggung_jawab &&
                          booking.nama_penanggung_jawab !== "User Lain"
                            ? `
                            <div class="mt-2">
                                <small class="text-muted">Penanggung Jawab:</small><br>
                                <span>${booking.nama_penanggung_jawab}</span>
                                ${
                                  booking.unit_organisasi &&
                                  booking.unit_organisasi !== "***"
                                    ? ` - ${booking.unit_organisasi}`
                                    : ""
                                }
                            </div>
                        `
                            : ""
                        }
                        ${
                          booking.jumlah_peserta
                            ? `
                            <div class="mt-1">
                                <small class="text-muted">Jumlah Peserta:</small>
                                <span class="badge bg-secondary ms-2">${booking.jumlah_peserta} orang</span>
                            </div>
                        `
                            : ""
                        }
                    </div>
                `;
      });
    }

    modalContent.innerHTML = content;
    modal.show();
  }

  function formatIndonesianDate(dateStr) {
    const date = new Date(dateStr + "T00:00:00");
    const options = {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    };
    return date.toLocaleDateString("id-ID", options);
  }
});

function openEditRuangan(id) {
  const cleanId = parseInt(id);
  if (!cleanId) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "ID ruangan tidak valid",
    });
    return;
  }

  fetch(`${baseUrl}/admin/User/Ruangan/detail/${cleanId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const ruangan = data.data;

        // Populate existing fields
        document.getElementById("edit_nama_ruangan").value =
          ruangan.nama_ruangan || "";
        document.getElementById("edit_lokasi").value = ruangan.lokasi || "";
        document.getElementById("edit_kapasitas").value =
          ruangan.kapasitas || "";

        // NEW: Handle status aktif field dengan PostgreSQL support
        const isActiveCheckbox = document.getElementById("edit_is_active");
        const statusLabel = document.getElementById("status_label");

        // DEBUG: Log nilai is_active dari database
        console.log("DB is_active value:", ruangan.is_active);
        console.log("DB is_active type:", typeof ruangan.is_active);

        // Set checkbox berdasarkan database value (support PostgreSQL 't'/'f')
        const isActive =
          ruangan.is_active === true ||
          ruangan.is_active === "t" ||
          ruangan.is_active === "1" ||
          ruangan.is_active === 1;

        console.log("Converted isActive:", isActive);

        if (isActive) {
          isActiveCheckbox.checked = true;
          statusLabel.innerHTML =
            '<i class="bi bi-check-circle text-success"></i> Aktif (Dapat dipinjam)';
          statusLabel.className = "form-check-label text-success fw-bold";
        } else {
          isActiveCheckbox.checked = false;
          statusLabel.innerHTML =
            '<i class="bi bi-x-circle text-warning"></i> Non-aktif (Maintenance)';
          statusLabel.className = "form-check-label text-warning fw-bold";
        }

        // Add change event listener untuk update label real-time
        isActiveCheckbox.addEventListener("change", function () {
          console.log("Checkbox changed to:", this.checked);
          if (this.checked) {
            statusLabel.innerHTML =
              '<i class="bi bi-check-circle text-success"></i> Aktif (Dapat dipinjam)';
            statusLabel.className = "form-check-label text-success fw-bold";
          } else {
            statusLabel.innerHTML =
              '<i class="bi bi-x-circle text-warning"></i> Non-aktif (Maintenance)';
            statusLabel.className = "form-check-label text-warning fw-bold";
          }
        });

        // Handle fasilitas (existing code)
        const fasilitasCheckboxes = document.querySelectorAll(
          '#modalEditRuangan input[name="fasilitas[]"]'
        );
        fasilitasCheckboxes.forEach((checkbox) => (checkbox.checked = false));

        const fasilitasText = ruangan.fasilitas || "";
        const fasilitasItems = [
          "Proyektor",
          "Whiteboard",
          "Microphone",
          "Sound System",
          "AC",
          "Wifi",
        ];
        let keteranganExisting = fasilitasText;

        fasilitasItems.forEach((item) => {
          const checkbox = document.getElementById(
            `edit_fasilitas_${item
              .toLowerCase()
              .replace(/\s+/g, "_")
              .replace("microphone", "mic")}`
          );
          if (checkbox && fasilitasText.includes(item)) {
            checkbox.checked = true;
            keteranganExisting = keteranganExisting.replace(
              new RegExp(item + ",?\\s*", "gi"),
              ""
            );
          }
        });

        keteranganExisting = keteranganExisting.replace(
          /^[,.\s]+|[,.\s]+$/g,
          ""
        );
        document.getElementById("edit_keterangan").value = keteranganExisting;

        const modal = new bootstrap.Modal(
          document.getElementById("modalEditRuangan")
        );
        const form = document.getElementById("formEditRuangan");

        form.onsubmit = function (e) {
          e.preventDefault();

          // Get fresh checkbox state
          const isActiveCheckbox = document.getElementById("edit_is_active");
          const isActiveValue = isActiveCheckbox.checked ? "1" : "0";

          // DEBUG: Log checkbox state sebelum submit
          console.log(
            "Before submit - Checkbox checked:",
            isActiveCheckbox.checked
          );
          console.log("Before submit - Will send is_active:", isActiveValue);

          const formData = new FormData(this);

          // Force set is_active value untuk memastikan
          formData.set("is_active", isActiveValue);

          // DEBUG: Verify formData
          console.log("FormData is_active:", formData.get("is_active"));

          // Show loading
          Swal.fire({
            title: "Mohon Tunggu",
            text: "Sedang menyimpan perubahan...",
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
              Swal.showLoading();
            },
          });

          const editEndpoint = `${baseUrl}/admin/User/Ruangan/edit/${ruangan.id}`;

          fetch(editEndpoint, {
            method: "POST",
            body: formData,
            headers: {
              "X-Requested-With": "XMLHttpRequest",
            },
          })
            .then((response) => response.json())
            .then((result) => {
              console.log("Server response:", result);

              if (result.success === true || result.success === "true") {
                Swal.fire({
                  icon: "success",
                  title: "Berhasil!",
                  text: result.message || "Data ruangan berhasil diperbarui",
                  confirmButtonText: "OK",
                }).then(() => {
                  window.location.reload();
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Gagal!",
                  text:
                    result.error ||
                    result.message ||
                    "Gagal memperbarui data ruangan",
                  confirmButtonText: "Tutup",
                });
              }
            })
            .catch((error) => {
              console.error("Error:", error);
              Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Terjadi kesalahan pada server",
                confirmButtonText: "Tutup",
              });
            });
        };

        modal.show();
      } else {
        throw new Error(data.message || "Gagal mengambah data ruangan");
      }
    })
    .catch((error) => {
      console.error("Fetch error:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: error.message || "Gagal mengambil data ruangan",
      });
    });
}

// Optional: Function untuk quick toggle status (tanpa buka modal)
function toggleRuanganStatus(id) {
  const cleanId = parseInt(id);
  if (!cleanId) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "ID ruangan tidak valid",
    });
    return;
  }

  Swal.fire({
    title: "Konfirmasi",
    text: "Apakah Anda yakin ingin mengubah status ruangan?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Ya, Ubah Status",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading
      Swal.fire({
        title: "Mohon Tunggu",
        text: "Sedang mengubah status ruangan...",
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      fetch(`${baseUrl}/admin/User/Ruangan/toggleActive/${cleanId}`, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Content-Type": "application/json",
        },
      })
        .then((response) => response.json())
        .then((result) => {
          if (result.success) {
            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: result.message,
              confirmButtonText: "OK",
            }).then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Gagal!",
              text: result.error || "Gagal mengubah status ruangan",
              confirmButtonText: "Tutup",
            });
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Error!",
            text: "Terjadi kesalahan pada server",
            confirmButtonText: "Tutup",
          });
        });
    }
  });
}
