// ===== FUNCTION UNTUK POPULATE UNIT KERJA =====
// ✅ UBAH function setupUnitKerjaDropdown menjadi self-contained:
function setupUnitKerjaDropdown() {
  // ✅ GUARD: Cek apakah element ada
  const unitOrgSelect = document.getElementById("unit_organisasi");
  const unitKerjaSelect = document.getElementById("unit_kerja");

  if (!unitOrgSelect || !unitKerjaSelect) {
    console.warn("⚠️ Unit organisasi or unit kerja select not found");
    return false;
  }

  // ✅ GUARD: Jangan setup jika sudah ada event listener
  if (unitOrgSelect._setupCompleted) {
    console.log("⏭️ Unit kerja dropdown already setup, skipping");
    return true;
  }

  // ✅ PERBAIKAN: Data mapping dengan support BOTH database dan display value
  const unitKerjaMapping = {
    // Database values (short form)
    Setjen: [
      "Biro Perencanaan",
      "Biro Kepegawaian",
      "Biro Keuangan",
      "Biro Hukum",
      "Biro Umum",
      "Pusdatin",
    ],
    Itjen: [
      "Sekretariat Itjen",
      "Inspektorat I",
      "Inspektorat II",
      "Inspektorat III",
      "Inspektorat IV",
    ],
    "Ditjen Sumber Daya Air": [
      "Sekretariat Ditjen SDA",
      "Dit. Bina Operasi dan Pemeliharaan",
      "Dit. Sungai dan Pantai",
      "Dit. Irigasi",
    ],
    "Ditjen Bina Marga": [
      "Sekretariat Ditjen Bina Marga",
      "Dit. Jalan Bebas Hambatan",
      "Dit. Jalan Nasional",
    ],
    "Ditjen Cipta Karya": [
      "Sekretariat Ditjen Cipta Karya",
      "Dit. Pengembangan Kawasan Permukiman",
      "Dit. Air Minum",
    ],
    "Ditjen Perumahan": [
      "Sekretariat Ditjen Perumahan",
      "Dit. Rumah Umum",
      "Dit. Rumah Susun",
    ],
    "Ditjen Bina Konstruksi": [
      "Sekretariat Ditjen Bina Konstruksi",
      "Dit. Kompetensi dan Produktivitas Konstruksi",
      "Dit. Pengembangan Jasa Konstruksi",
    ],
    "Ditjen Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan": [
      "Sekretariat DJPI",
      "Dit. Pembiayaan Perumahan",
      "Dit. Pembiayaan Infrastruktur",
    ],
    BPIW: [
      "Sekretariat BPIW",
      "Pusat Pengembangan Kawasan Strategis",
      "Pusat Pengembangan Kawasan Perkotaan",
    ],
    BPSDM: [
      "Sekretariat BPSDM",
      "Pusat Pendidikan dan Pelatihan",
      "Pusat Pembinaan Kompetensi",
    ],
    BPJT: ["Sekretariat BPJT", "Divisi Pengembangan", "Divisi Operasi"],

    // ✅ TAMBAH: Display values (full form) sebagai alias
    "Sekretariat Jenderal": [
      "Biro Perencanaan",
      "Biro Kepegawaian",
      "Biro Keuangan",
      "Biro Hukum",
      "Biro Umum",
      "Pusdatin",
    ],
    "Inspektorat Jenderal": [
      "Sekretariat Itjen",
      "Inspektorat I",
      "Inspektorat II",
      "Inspektorat III",
      "Inspektorat IV",
    ],
    "Direktorat Jenderal Sumber Daya Air": [
      "Sekretariat Ditjen SDA",
      "Dit. Bina Operasi dan Pemeliharaan",
      "Dit. Sungai dan Pantai",
      "Dit. Irigasi",
    ],
    "Direktorat Jenderal Bina Marga": [
      "Sekretariat Ditjen Bina Marga",
      "Dit. Jalan Bebas Hambatan",
      "Dit. Jalan Nasional",
    ],
    "Direktorat Jenderal Cipta Karya": [
      "Sekretariat Ditjen Cipta Karya",
      "Dit. Pengembangan Kawasan Permukiman",
      "Dit. Air Minum",
    ],
    "Direktorat Jenderal Perumahan": [
      "Sekretariat Ditjen Perumahan",
      "Dit. Rumah Umum",
      "Dit. Rumah Susun",
    ],
    "Direktorat Jenderal Bina Konstruksi": [
      "Sekretariat Ditjen Bina Konstruksi",
      "Dit. Kompetensi dan Produktivitas Konstruksi",
      "Dit. Pengembangan Jasa Konstruksi",
    ],
    "Direktorat Jenderal Pembiayaan Infrastruktur PU dan Perumahan": [
      "Sekretariat DJPI",
      "Dit. Pembiayaan Perumahan",
      "Dit. Pembiayaan Infrastruktur",
    ],
    "Badan Pengembangan Infrastruktur Wilayah": [
      "Sekretariat BPIW",
      "Pusat Pengembangan Kawasan Strategis",
      "Pusat Pengembangan Kawasan Perkotaan",
    ],
    "Badan Pengembangan Sumber Daya Manusia": [
      "Sekretariat BPSDM",
      "Pusat Pendidikan dan Pelatihan",
      "Pusat Pembinaan Kompetensi",
    ],
    "Badan Pengatur Jalan Tol": [
      "Sekretariat BPJT",
      "Divisi Pengembangan",
      "Divisi Operasi",
    ],
  };

  // ✅ CRITICAL: Remove existing listeners to prevent duplicates
  unitOrgSelect.removeEventListener("change", unitOrgSelect._changeHandler);

  // ✅ Create new change handler
  const changeHandler = function () {
    const selectedUnitOrg = this.value;
    console.log("Unit organisasi changed to:", selectedUnitOrg);

    // Clear existing options
    unitKerjaSelect.innerHTML = '<option value="">Pilih Unit Kerja</option>';

    if (selectedUnitOrg && unitKerjaMapping[selectedUnitOrg]) {
      // Populate unit kerja options
      unitKerjaMapping[selectedUnitOrg].forEach(function (unitKerja) {
        const option = document.createElement("option");
        option.value = unitKerja;
        option.textContent = unitKerja;
        unitKerjaSelect.appendChild(option);
      });

      // Enable dropdown
      unitKerjaSelect.disabled = false;
      unitKerjaSelect.required = true;

      console.log(
        `✅ Populated ${unitKerjaMapping[selectedUnitOrg].length} unit kerja options for: ${selectedUnitOrg}`
      );
    } else {
      // Disable if no valid unit organisasi
      unitKerjaSelect.disabled = true;
      unitKerjaSelect.required = false;
      console.log("❌ No unit kerja mapping found for:", selectedUnitOrg);
    }
  };

  // ✅ Store handler reference for removal later
  unitOrgSelect._changeHandler = changeHandler;
  unitOrgSelect.addEventListener("change", changeHandler);

  // ✅ Mark as completed to prevent duplicate setup
  unitOrgSelect._setupCompleted = true;

  console.log(
    "✅ Unit kerja dropdown setup completed with dual mapping support"
  );
  return true;
}
// ===== INITIALIZE UNIT KERJA DROPDOWN =====
// Tambahkan ini ke event listener modal shown

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
  // Mulai dari 07:30
  let current = new Date("2000-01-01T07:30:00");
  // Sampai 17:30
  const end = new Date("2000-01-01T21:30:00");

  while (current <= end) {
    const hour = current.getHours().toString().padStart(2, "0");
    const minute = current.getMinutes().toString().padStart(2, "0");
    slots.push(`${hour}:${minute}`);

    // Tambah 30 menit
    current.setMinutes(current.getMinutes() + 30);
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
      // Ubah class menjadi booked
      slot.className = "time-slot booked";

      // Cek apakah ini adalah periode cooldown
      if (isTimeInCooldownPeriod(time, existingBookings)) {
        // Jika waktu dalam cooldown period, tambahkan class khusus
        slot.classList.add("cooldown-period");
        slot.title = "Periode jeda untuk persiapan ruangan";
      } else {
        // Jika waktu dalam booking biasa
        const booking = getBookingForTime(time);
        if (booking) {
          const statusText =
            booking.status === "disetujui"
              ? "Disetujui"
              : "Menunggu Verifikasi";
          slot.title = `${statusText}: ${booking.waktu_mulai}-${booking.waktu_selesai}\nKeperluan: ${booking.keperluan}`;
        }
      }
    } else {
      slot.className = "time-slot available";
      slot.title = "Klik untuk pilih waktu";
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

// TAMBAHKAN INI: Fungsi untuk memperbarui style CSS untuk cooldown period
function addCooldownPeriodStyles() {
  // Tambahkan style CSS untuk cooldown period
  const styleElement = document.createElement("style");
  styleElement.innerHTML = `
    .time-slot.booked.cooldown-period {
      background: linear-gradient(135deg, #fff9c4 0%, #ffecb3 100%) !important;
      border: 2px dashed #ffa000 !important;
      color: #ff6f00 !important;
      cursor: not-allowed !important;
      position: relative;
    }
    
    .time-slot.booked.cooldown-period:before {
      content: "⏱️";
      position: absolute;
      right: 2px;
      top: 2px;
      font-size: 8px;
    }
    
    /* Tambahkan ini jika perlu efek tambahan */
    @keyframes cooldownPulse {
      0% { opacity: 0.8; }
      50% { opacity: 1; }
      100% { opacity: 0.8; }
    }
    
    .time-slot.booked.cooldown-period {
      animation: cooldownPulse 2s infinite;
    }
  `;
  document.head.appendChild(styleElement);

  return true;
}

// TAMBAHKAN INI: Fungsi untuk memuat time picker dengan periode cooldown
function loadTimePickerWithCooldown(
  ruanganId,
  tanggalElement,
  waktuMulaiElement,
  waktuSelesaiElement
) {
  const tanggalInput = document.getElementById(tanggalElement);
  const tanggal = tanggalInput.value;

  if (!tanggal) {
    return false;
  }

  // Tambahkan style untuk cooldown period
  addCooldownPeriodStyles();

  // Load existing bookings
  loadExistingBookings(ruanganId, tanggal);

  // Inisialisasi time picker
  initializeTimePicker(ruanganId);

  // Update time slots berdasarkan booking yang ada
  updateBookedTimeSlots();

  // Validasi time slot blocking
  setTimeout(validateTimeSlotBlocking, 500);

  // Force block time slots jika perlu
  setTimeout(forceBlockTimeSlots, 700);

  return true;
}

function checkTimeConflict(startTime, endTime) {
  for (let booking of existingBookings) {
    const bookingStart = booking.waktu_mulai.substring(0, 5);
    const bookingEnd = booking.waktu_selesai.substring(0, 5);

    // Konversi ke menit untuk perhitungan
    const bookingEndMinutes = convertTimeToMinutes(bookingEnd);

    // Tambahkan 30 menit sebagai cooldown period
    const cooldownEnd = convertMinutesToTime(bookingEndMinutes + 30);

    // Cek berbagai jenis konflik
    const conflict1 = startTime >= bookingStart && startTime < bookingEnd; // Start time dalam booking
    const conflict2 = endTime > bookingStart && endTime <= bookingEnd; // End time dalam booking
    const conflict3 = startTime <= bookingStart && endTime >= bookingEnd; // Booking dalam range baru

    // BARU: Cek konflik dengan periode cooldown
    const cooldownConflict1 =
      startTime >= bookingEnd && startTime < cooldownEnd; // Start time dalam cooldown
    const cooldownConflict2 = endTime > bookingEnd && endTime <= cooldownEnd; // End time dalam cooldown
    const cooldownConflict3 = startTime <= bookingEnd && endTime >= cooldownEnd; // Cooldown dalam range baru

    const hasConflict =
      conflict1 ||
      conflict2 ||
      conflict3 ||
      cooldownConflict1 ||
      cooldownConflict2 ||
      cooldownConflict3;

    if (hasConflict) {
      console.log(
        `Conflict detected with booking: ${bookingStart}-${bookingEnd}`
      );
      console.log(
        `Conflicts: Regular(${conflict1},${conflict2},${conflict3}) Cooldown(${cooldownConflict1},${cooldownConflict2},${cooldownConflict3})`
      );

      // Jika konflika dengan cooldown period, tampilkan pesan khusus
      if (cooldownConflict1 || cooldownConflict2 || cooldownConflict3) {
        booking.isCooldownConflict = true;
      }

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

        // TAMBAH HANDLING LUAS RUANGAN - INI YANG BARU!
        const luasRuanganInput = document.getElementById("edit_luas_ruangan");
        if (luasRuanganInput) {
          luasRuanganInput.value = ruangan.luas_ruangan || "";
        }

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

        // TAMBAH HANDLING IS_ACTIVE CHECKBOX - FITUR BARU!
        const isActiveCheckbox = document.getElementById("edit_is_active");
        const statusLabel = document.getElementById("status_label");

        if (isActiveCheckbox) {
          if (
            ruangan.is_active == true ||
            ruangan.is_active == "t" ||
            ruangan.is_active == "1" ||
            ruangan.is_active === true
          ) {
            isActiveCheckbox.checked = true;
            if (statusLabel) statusLabel.textContent = "Aktif (Dapat dipinjam)";
          } else {
            isActiveCheckbox.checked = false;
            if (statusLabel)
              statusLabel.textContent = "Non-aktif (Maintenance)";
          }
        }

        form.onsubmit = function (e) {
          e.preventDefault();
          const formData = new FormData(this);

          // DEBUG: Log form data yang dikirim
          console.log("Form data being sent:");
          for (let [key, value] of formData.entries()) {
            console.log(key, value);
          }

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
              console.log("Server response:", result); // DEBUG
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

// TAMBAH EVENT LISTENER UNTUK IS_ACTIVE CHECKBOX
document.addEventListener("DOMContentLoaded", function () {
  const isActiveCheckbox = document.getElementById("edit_is_active");
  const statusLabel = document.getElementById("status_label");

  if (isActiveCheckbox && statusLabel) {
    isActiveCheckbox.addEventListener("change", function () {
      if (this.checked) {
        statusLabel.textContent = "Aktif (Dapat dipinjam)";
      } else {
        statusLabel.textContent = "Non-aktif (Maintenance)";
      }
    });
  }
});

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

// TAMBAHKAN INI: Fungsi untuk memastikan waktu akhir booking (cooldown period) juga diblokir
function isTimeInCooldownPeriod(time, bookings) {
  if (!bookings || bookings.length === 0) {
    return false;
  }

  return bookings.some((booking) => {
    // Format waktu selesai dari booking
    const bookingEnd = booking.waktu_selesai.substring(0, 5);
    // Konversi waktu ke menit untuk perhitungan
    const bookingEndMinutes = convertTimeToMinutes(bookingEnd);
    const currentTimeMinutes = convertTimeToMinutes(time);

    // Cek apakah waktu berada tepat di waktu selesai booking
    // atau dalam 30 menit setelah waktu selesai (cooldown period)
    const isEndTime = bookingEnd === time;

    // Waktu berada dalam rentang 30 menit setelah waktu akhir
    const isInCooldownPeriod =
      currentTimeMinutes > bookingEndMinutes &&
      currentTimeMinutes <= bookingEndMinutes + 30;

    if (isEndTime || isInCooldownPeriod) {
      console.log(
        `Time ${time} is in COOLDOWN PERIOD after booking ending at ${bookingEnd}`
      );
    }

    return isEndTime || isInCooldownPeriod;
  });
}

// MODIFIKASI: Fungsi isTimeBooked untuk juga mendeteksi waktu dalam cooldown period
// Modifikasi fungsi isTimeBooked untuk juga memblokir waktu selesai
function isTimeBooked(time) {
  if (!existingBookings || existingBookings.length === 0) {
    return false;
  }

  return existingBookings.some((booking) => {
    // Pastikan format waktu konsisten (HH:MM)
    const bookingStart = booking.waktu_mulai.substring(0, 5);
    const bookingEnd = booking.waktu_selesai.substring(0, 5);
    const currentTime = time.substring(0, 5);

    // Cek apakah waktu berada dalam range booking (include start AND include end)
    const isInRange =
      (currentTime >= bookingStart && currentTime < bookingEnd) ||
      currentTime === bookingEnd; // Tambahkan pengecekan untuk waktu selesai

    if (isInRange) {
      console.log(
        `Time ${currentTime} is BLOCKED by booking ${bookingStart}-${bookingEnd}`
      );
    }

    return isInRange;
  });
}

function forceBlockTimeSlots() {
  console.log("🔒 FORCE BLOCKING TIME SLOTS & COOLDOWN PERIODS...");

  if (!existingBookings || existingBookings.length === 0) {
    console.log("No bookings to block");
    return;
  }

  // Generate all blocked time slots (termasuk booking dan cooldown)
  const blockedTimes = [];
  const cooldownTimes = [];

  existingBookings.forEach((booking) => {
    const start = convertTimeToMinutes(booking.waktu_mulai);
    const end = convertTimeToMinutes(booking.waktu_selesai);

    // Generate normal booking slots (start hingga SEBELUM end)
    for (let minutes = start; minutes < end; minutes += 30) {
      const timeSlot = convertMinutesToTime(minutes);
      blockedTimes.push(timeSlot);
    }

    // Tambahkan waktu selesai ke cooldown times
    cooldownTimes.push(booking.waktu_selesai.substring(0, 5));

    // Tambahkan 30 menit setelah waktu selesai ke cooldown times (jika ada)
    const cooldownEnd = convertMinutesToTime(end + 30);
    if (cooldownEnd !== booking.waktu_selesai) {
      cooldownTimes.push(cooldownEnd);
    }
  });

  console.log("Generated blocked times:", blockedTimes);
  console.log("Generated cooldown times:", cooldownTimes);

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

      // Method 2: Force inline styles untuk booking
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

      // Method 3: Add data attributes
      slot.dataset.blocked = "true";
      slot.dataset.blockType = "booking";

      // Method 4: Add tooltip
      const booking = getBookingForTime(time);
      if (booking) {
        const statusText =
          booking.status === "disetujui" ? "Disetujui" : "Menunggu Verifikasi";
        slot.title = `${statusText}: ${booking.waktu_mulai}-${booking.waktu_selesai}\nKeperluan: ${booking.keperluan}\nPIC: ${booking.nama_penanggung_jawab}`;
      }

      console.log(`✅ FORCE BLOCKED BOOKING: ${time}`);
    }
  });

  // Force apply cooldown styles
  cooldownTimes.forEach((time) => {
    const slot = document.querySelector(`[data-time="${time}"]`);
    if (slot) {
      // Update classes
      slot.classList.remove(
        "available",
        "selected-start",
        "selected-end",
        "in-range"
      );
      slot.classList.add("booked");
      slot.classList.add("cooldown-period");

      // Force inline styles untuk cooldown
      slot.style.cssText = `
        background: linear-gradient(135deg, #fff9c4 0%, #ffecb3 100%) !important;
        border: 2px dashed #ffa000 !important;
        color: #ff6f00 !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
        opacity: 0.9 !important;
        font-weight: bold !important;
        position: relative !important;
      `;

      // Add data attributes
      slot.dataset.blocked = "true";
      slot.dataset.blockType = "cooldown";

      // Add tooltip
      slot.title = "Periode jeda untuk persiapan ruangan (30 menit)";

      console.log(`✅ FORCE BLOCKED COOLDOWN: ${time}`);
    }
  });

  // Verify blocking worked
  const blockedSlots = document.querySelectorAll(".time-slot.booked");
  console.log(
    `🎯 Successfully blocked ${blockedSlots.length} time slots (including cooldown periods)`
  );
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
// COMPLETE REPLACEMENT untuk pinjam-ruangan.js
// Includes: Auto-fill HP + Cache Bug Fix + Tanggal Minimum Hari Ini

// Global variables untuk cache management
window.currentModalRuanganId = null;
window.lastAutoFillRuanganId = null;

function clearGlobalAutoFillCache() {
  console.log("🗑️ Clearing global auto-fill cache...");

  // Clear local storage cache jika ada
  if (typeof localStorage !== "undefined") {
    const keysToRemove = [];
    for (let i = 0; i < localStorage.length; i++) {
      const key = localStorage.key(i);
      if (key && (key.includes("autofill") || key.includes("booking_data"))) {
        keysToRemove.push(key);
      }
    }
    keysToRemove.forEach((key) => localStorage.removeItem(key));
  }

  // Clear global variables
  window.currentModalRuanganId = null;
  window.lastAutoFillRuanganId = null;

  console.log("✅ Global cache cleared");
}

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

  // ✅ CRITICAL: Clear cache SEBELUM modal dibuka
  console.log(`🎯 Opening modal for ruangan ${cleanRuanganId}`);
  clearGlobalAutoFillCache();

  // Set ruangan yang sedang aktif
  window.currentModalRuanganId = cleanRuanganId;

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
      <h5 class="modal-title fw-semibold text-dark">
        <i class="bi bi-clipboard-check me-2"></i>
        Form Request Confirm - ${cleanNamaRuangan}
      </h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <style>
      #formPinjamRuanganModal .form-control::placeholder {
        color: #9CA3AF;
        opacity: 1;
      }
    </style>
    <form id="formPinjamRuangan" action="${baseUrl}/user/ruangan/pinjam" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="ruangan_id" value="${cleanRuanganId}">
                        
                        ${keteranganSection}
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_penanggung_jawab" class="form-label">
                                        <i class="bi bi-person-fill me-1"></i>
                                        Nama Penanggung Jawab <span class="text-danger"> *</span>
                                    </label>
                                    <input type="text" class="form-control" id="nama_penanggung_jawab" name="nama_penanggung_jawab" required
                                           placeholder="Masukkan nama penanggung jawab">
                                    <div class="invalid-feedback"></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="nomor_hp_penanggung_jawab" class="form-label">
                                        <i class="bi bi-telephone-fill me-1"></i>
                                        Nomor HP Penanggung Jawab <span class="text-danger"> *</span>
                                    </label>
                                    <input type="tel" class="form-control" id="nomor_hp_penanggung_jawab" name="nomor_hp_penanggung_jawab" required
                                           pattern="[0-9]{10,15}" placeholder="Contoh: 081234567890">
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="unit_organisasi" class="form-label">
                                        <i class="bi bi-building me-1"></i>
                                        Unit Organisasi <span class="text-danger"> *</span>
                                    </label>
                                    <select class="form-control" id="unit_organisasi" name="unit_organisasi" required>
                                        <option value="" disabled selected>Pilih Unit Organisasi</option>
                                        <option value="Setjen">Sekretariat Jenderal</option>
                                        <option value="Itjen">Inspektorat Jenderal</option>
                                        <option value="Ditjen Sumber Daya Air">Direktorat Jenderal Sumber Daya Air</option>
                                        <option value="Ditjen Bina Marga">Direktorat Jenderal Bina Marga</option>
                                        <option value="Ditjen Cipta Karya">Direktorat Jenderal Cipta Karya</option>
                                        <option value="Ditjen Perumahan">Direktorat Jenderal Perumahan</option>
                                        <option value="Ditjen Bina Konstruksi">Direktorat Jenderal Bina Konstruksi</option>
                                        <option value="Ditjen Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan">Direktorat Jenderal Pembiayaan Infrastruktur PU dan Perumahan</option>
                                        <option value="BPIW">Badan Pengembangan Infrastruktur Wilayah</option>
                                        <option value="BPSDM">Badan Pengembangan Sumber Daya Manusia</option>
                                        <option value="BPJT">Badan Pengatur Jalan Tol</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group mb-3">
    <label for="unit_kerja" class="form-label">
        <i class="bi bi-building me-1"></i>
        Unit Kerja <span class="text-danger"> *</span>
    </label>
    <select class="form-control" id="unit_kerja" name="unit_kerja" required disabled>
        <option value="" disabled selected>Pilih Unit Kerja</option>
    </select>
    <small class="text-muted">Pilih unit organisasi terlebih dahulu</small>
</div>

                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        Tanggal Peminjaman <span class="text-danger"> *</span>
                                    </label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required
                                           min="${
                                             new Date()
                                               .toISOString()
                                               .split("T")[0]
                                           }">
                                    <div class="invalid-feedback"></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="jumlah_peserta" class="form-label">
                                        <i class="bi bi-people-fill me-1"></i>
                                        Jumlah Peserta <span class="text-danger"> *</span>
                                    </label>
                                    <input type="number" class="form-control" id="jumlah_peserta" name="jumlah_peserta" 
                                           required min="1" max="${cleanKapasitas}" placeholder="Max: ${cleanKapasitas} orang">
                                    <div class="invalid-feedback"></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="keperluan" class="form-label">
                                        <i class="bi bi-card-text me-1"></i>
                                        Keperluan <span class="text-danger"> *</span>
                                    </label>
                                    <textarea class="form-control" id="keperluan" name="keperluan" 
                                              rows="3" required placeholder="Jelaskan keperluan penggunaan ruangan"></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="surat_permohonan" class="form-label">
                                        <i class="bi bi-file-earmark-pdf me-1"></i>
                                        Surat Permohonan <span class="text-danger"> *</span>
                                    </label>
                                    <input type="file" class="form-control" id="surat_permohonan" name="surat_permohonan" 
                                           required accept=".pdf" max="2048">
                                    <small class="text-muted">Format: PDF, Maksimal 2MB</small>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Auto-fill Settings -->
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" id="enable_autofill" checked>
                                    <label class="form-check-label text-muted" for="enable_autofill">
                                        <small>
                                            <i class="bi bi-magic me-1"></i>
                                            Isi otomatis data dari booking terakhir di ruangan ini
                                        </small>
                                    </label>
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
                        
                        <!-- Info Ruangan -->
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Informasi Ruangan:</strong>
                            <div class="mt-1">
                                <small>
                                    <strong>Kapasitas:</strong> ${cleanKapasitas} orang
                                    ${
                                      cleanKeterangan
                                        ? ` | <strong>Fasilitas:</strong> ${cleanKeterangan}`
                                        : ""
                                    }
                                </small>
                            </div>
                        </div>
                        
                        <!-- Warning untuk confirm -->
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Perhatian:</strong> 
                            Request ini memerlukan persetujuan admin. Status akan menjadi "Pending" hingga admin melakukan verifikasi.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>
                            Batal
                        </button>
<button type="submit" class="btn btn-primary" id="submit_booking" disabled>
    <i class="bi bi-send-fill me-1"></i>
    Kirim Request
</button>

                    </div>
                </form>
            </div>
        </div>
    `;

  const modalElement = document.getElementById("modalPinjamRuangan");
  modalElement.innerHTML = modalContent;

  addTimePickerStyles();
  initializeAutoFillStyles();

  const modal = new bootstrap.Modal(modalElement);

  modalElement.addEventListener("shown.bs.modal", function () {
    console.log(`📋 Modal opened for ruangan ${cleanRuanganId}`);

    // ✅ PERBAIKAN: Setup unit kerja untuk modal request confirm
    // ✅ PERBAIKAN: Setup unit kerja dropdown dengan ID yang benar
    setupRequestConfirmUnitKerja();

    function setupRequestConfirmUnitKerja() {
      const unitOrgSelect = document.getElementById("unit_organisasi");
      const unitKerjaSelect = document.getElementById("unit_kerja");

      console.log("🔍 Checking elements:", {
        unitOrg: unitOrgSelect,
        unitKerja: unitKerjaSelect,
      });

      if (!unitOrgSelect || !unitKerjaSelect) {
        console.error("❌ Elements not found, retrying...");
        setTimeout(() => {
          setupRequestConfirmUnitKerja();
        }, 500);
        return false;
      }

      // Clear existing event listener
      if (unitOrgSelect._changeHandler) {
        unitOrgSelect.removeEventListener(
          "change",
          unitOrgSelect._changeHandler
        );
      }

      // Unit kerja mapping
      const unitKerjaMapping = {
        Setjen: [
          "Biro Perencanaan",
          "Biro Kepegawaian",
          "Biro Keuangan",
          "Biro Hukum",
          "Biro Umum",
          "Pusdatin",
        ],
        Itjen: [
          "Sekretariat Itjen",
          "Inspektorat I",
          "Inspektorat II",
          "Inspektorat III",
          "Inspektorat IV",
        ],
        "Ditjen Sumber Daya Air": [
          "Sekretariat Ditjen SDA",
          "Dit. Bina Operasi dan Pemeliharaan",
          "Dit. Sungai dan Pantai",
          "Dit. Irigasi",
        ],
        "Ditjen Bina Marga": [
          "Sekretariat Ditjen Bina Marga",
          "Dit. Jalan Bebas Hambatan",
          "Dit. Jalan Nasional",
        ],
        "Ditjen Cipta Karya": [
          "Sekretariat Ditjen Cipta Karya",
          "Dit. Pengembangan Kawasan Permukiman",
          "Dit. Air Minum",
        ],
        "Ditjen Perumahan": [
          "Sekretariat Ditjen Perumahan",
          "Dit. Rumah Umum",
          "Dit. Rumah Susun",
        ],
        "Ditjen Bina Konstruksi": [
          "Sekretariat Ditjen Bina Konstruksi",
          "Dit. Kompetensi dan Produktivitas Konstruksi",
          "Dit. Pengembangan Jasa Konstruksi",
        ],
        "Ditjen Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan": [
          "Sekretariat DJPI",
          "Dit. Pembiayaan Perumahan",
          "Dit. Pembiayaan Infrastruktur",
        ],
        BPIW: [
          "Sekretariat BPIW",
          "Pusat Pengembangan Kawasan Strategis",
          "Pusat Pengembangan Kawasan Perkotaan",
        ],
        BPSDM: [
          "Sekretariat BPSDM",
          "Pusat Pendidikan dan Pelatihan",
          "Pusat Pembinaan Kompetensi",
        ],
        BPJT: ["Sekretariat BPJT", "Divisi Pengembangan", "Divisi Operasi"],
        // Aliases
        "Sekretariat Jenderal": [
          "Biro Perencanaan",
          "Biro Kepegawaian",
          "Biro Keuangan",
          "Biro Hukum",
          "Biro Umum",
          "Pusdatin",
        ],
        "Inspektorat Jenderal": [
          "Sekretariat Itjen",
          "Inspektorat I",
          "Inspektorat II",
          "Inspektorat III",
          "Inspektorat IV",
        ],
      };

      // Create change handler
      const changeHandler = function () {
        const selectedUnitOrg = this.value;
        console.log("🔄 Unit organisasi changed to:", selectedUnitOrg);

        // Clear unit kerja dropdown
        unitKerjaSelect.innerHTML =
          '<option value="">Pilih Unit Kerja</option>';

        if (selectedUnitOrg && unitKerjaMapping[selectedUnitOrg]) {
          // Populate unit kerja options
          unitKerjaMapping[selectedUnitOrg].forEach(function (unitKerja) {
            const option = document.createElement("option");
            option.value = unitKerja;
            option.textContent = unitKerja;
            unitKerjaSelect.appendChild(option);
          });

          // Enable unit kerja dropdown
          unitKerjaSelect.disabled = false;
          unitKerjaSelect.required = true;
          console.log("✅ Unit kerja options populated for:", selectedUnitOrg);
        } else {
          // Disable unit kerja dropdown
          unitKerjaSelect.disabled = true;
          unitKerjaSelect.required = false;
          console.log("❌ No unit kerja mapping found for:", selectedUnitOrg);
        }
      };

      // Add event listener
      unitOrgSelect._changeHandler = changeHandler;
      unitOrgSelect.addEventListener("change", changeHandler);

      console.log("✅ Request confirm unit kerja dropdown setup completed");
      return true;
    }

    // ✅ TRIPLE CHECK: Clear lagi setelah modal terbuka
    clearAllPreviousData();

    // ✅ SECURITY: Pastikan ruangan ID konsisten
    if (window.currentModalRuanganId !== cleanRuanganId) {
      console.error(
        `❌ RUANGAN MISMATCH: Expected ${cleanRuanganId}, current ${window.currentModalRuanganId}`
      );
      clearAllPreviousData();
      window.currentModalRuanganId = cleanRuanganId;
    }

    // Auto-fill dengan delay untuk memastikan clear selesai
    setTimeout(() => {
      if (window.currentModalRuanganId === cleanRuanganId) {
        loadPreviousBookingDataIfEnabled(cleanRuanganId);
      } else {
        console.warn(`⚠️ Skipping auto-fill: ruangan mismatch`);
      }
    }, 200);

    initializeTimePicker(cleanRuanganId);

    // Auto-fill handler untuk checkbox
    const autoFillCheckbox = document.getElementById("enable_autofill");
    if (autoFillCheckbox) {
      autoFillCheckbox.checked = isAutoFillEnabled();

      autoFillCheckbox.addEventListener("change", function () {
        localStorage.setItem("ruangan_autofill_enabled", this.checked);

        if (this.checked) {
          clearAllPreviousData();
          setTimeout(() => {
            if (window.currentModalRuanganId === cleanRuanganId) {
              loadPreviousBookingData(cleanRuanganId);
            }
          }, 100);
        } else {
          clearAutoFilledDataFixed();
        }
      });
    }

    const form = document.getElementById("formPinjamRuangan");
    if (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Enhanced Validation
        const requiredFields = [
          "nama_penanggung_jawab",
          "nomor_hp_penanggung_jawab",
          "unit_organisasi",
          "tanggal",
          "jumlah_peserta",
          "keperluan",
          "surat_permohonan",
        ];
        let isValid = true;

        requiredFields.forEach((field) => {
          const input = document.getElementById(field);
          if (!input || !input.value.trim()) {
            if (input) input.classList.add("is-invalid");
            isValid = false;
          } else {
            if (input) input.classList.remove("is-invalid");
          }
        });

        // Validate HP number format
        const hpInput = document.getElementById("nomor_hp_penanggung_jawab");
        if (hpInput) {
          const hpPattern = /^[0-9]{10,15}$/;
          if (!hpPattern.test(hpInput.value)) {
            hpInput.classList.add("is-invalid");
            isValid = false;
          }
        }

        if (!isValid) {
          Swal.fire({
            icon: "error",
            title: "Form Tidak Valid!",
            text: "Mohon lengkapi semua field yang required dengan benar",
            confirmButtonColor: "#dc3545",
          });
          return;
        }

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

      // ✅ FIX: Set tanggal hari ini (bukan besok)
      const tanggalInput = document.getElementById("tanggal");
      const today = new Date().toISOString().split("T")[0];
      tanggalInput.value = today;

      // Event listener untuk perubahan tanggal
      tanggalInput.addEventListener("change", function () {
        const selectedDate = this.value;
        if (selectedDate) {
          console.log(`Date changed to: ${selectedDate}`);
          resetTimeSelection();
          loadExistingBookings(cleanRuanganId, selectedDate);
        }
      });
    }
  });

  // ✅ CLEANUP saat modal ditutup
  modalElement.addEventListener("hidden.bs.modal", function () {
    console.log(`🔄 Modal closed for ruangan ${cleanRuanganId}`);
    clearGlobalAutoFillCache();
    clearAllPreviousData();
    window.currentModalRuanganId = null;
  });

  modal.show();
}

function clearAllPreviousData() {
  console.log("🧹 Clearing ALL previous data to prevent cross-room cache");

  // 1. Clear form fields dengan lebih aggressive
  const formFields = [
    "nama_penanggung_jawab",
    "nomor_hp_penanggung_jawab", // ✅ TAMBAHKAN NOMOR HP
    "unit_organisasi",
    "jumlah_peserta",
    "keperluan",
  ];

  formFields.forEach((fieldId) => {
    const field = document.getElementById(fieldId);
    if (field) {
      if (field.tagName === "SELECT") {
        field.selectedIndex = 0;
        field.value = "";
      } else {
        field.value = "";
        field.defaultValue = "";
      }
      field.classList.remove("is-invalid", "is-valid", "auto-filled");

      if (field.dataset.autoFilled) {
        delete field.dataset.autoFilled;
      }
    }
  });

  // 2. Clear all notifications sebelumnya
  const modalBody = document.querySelector("#modalPinjamRuangan .modal-body");
  if (modalBody) {
    const notifications = modalBody.querySelectorAll(".alert");
    notifications.forEach((notification) => {
      if (
        notification.classList.contains("alert-success") ||
        notification.classList.contains("alert-info") ||
        notification.classList.contains("alert-warning")
      ) {
        notification.remove();
      }
    });
  }

  // 3. Reset waktu dengan lebih aggressive
  const waktuMulaiField = document.getElementById("waktu_mulai");
  const waktuSelesaiField = document.getElementById("waktu_selesai");
  const displayMulai = document.getElementById("display_waktu_mulai");
  const displaySelesai = document.getElementById("display_waktu_selesai");

  if (waktuMulaiField) {
    waktuMulaiField.value = "";
    waktuMulaiField.removeAttribute("data-auto-filled");
  }
  if (waktuSelesaiField) {
    waktuSelesaiField.value = "";
    waktuSelesaiField.removeAttribute("data-auto-filled");
  }
  if (displayMulai) displayMulai.textContent = "Belum dipilih";
  if (displaySelesai) displaySelesai.textContent = "Belum dipilih";

  // 4. ✅ FIX: Reset tanggal ke hari ini (bukan besok)
  const tanggalField = document.getElementById("tanggal");
  if (tanggalField) {
    const today = new Date().toISOString().split("T")[0];
    tanggalField.value = today;
  }

  // 5. Reset time selection
  if (typeof resetTimeSelection === "function") {
    resetTimeSelection();
  }

  // 6. Hide duration display
  const durationDisplay = document.getElementById("duration_display");
  if (durationDisplay) {
    durationDisplay.style.display = "none";
  }

  // 7. Clear visual highlighting
  clearAllTimeSlotHighlights();

  // 8. Reset global variables
  if (typeof window !== "undefined") {
    window.selectedStartTime = null;
    window.selectedEndTime = null;
    window.isSelectingTime = true;
  }

  // 9. Disable submit button
  const submitBtn = document.getElementById("submit_booking");
  if (submitBtn) {
    submitBtn.disabled = true;
  }

  console.log("✅ All previous data cleared aggressively");
}

function clearAllTimeSlotHighlights() {
  const selectors = [
    ".time-slot",
    "[data-time]",
    ".time-ruler .btn",
    ".time-ruler button",
    'button[onclick*="selectTime"]',
    ".btn-outline-primary",
    ".btn-outline-secondary",
    ".btn-outline-info",
    "#time_ruler button",
    "#time_ruler .btn",
    ".time-picker button",
    ".time-ruler .time-slot",
    'button[class*="btn"]',
  ];

  selectors.forEach((selector) => {
    const elements = document.querySelectorAll(selector);
    elements.forEach((el) => {
      el.classList.remove(
        "selected",
        "selected-start",
        "selected-end",
        "selected-range"
      );

      // Remove inline styles
      [
        "background-color",
        "color",
        "border-color",
        "font-weight",
        "box-shadow",
        "transform",
        "z-index",
        "position",
      ].forEach((prop) => {
        el.style.removeProperty(prop);
      });

      if (el.dataset.autoProcessed) {
        delete el.dataset.autoProcessed;
      }
    });
  });

  console.log("🎨 All time slot highlights cleared");
}

function loadPreviousBookingData(ruanganId) {
  const requestedRuanganId = parseInt(ruanganId);

  // ✅ SECURITY CHECK: Pastikan ini ruangan yang benar
  if (window.currentModalRuanganId !== requestedRuanganId) {
    console.error(
      `❌ CROSS-ROOM CACHE DETECTED: Current modal for ${window.currentModalRuanganId}, but requesting ${requestedRuanganId}`
    );
    clearAllPreviousData();
    return;
  }

  // ✅ PREVENT DUPLICATE CALLS
  if (window.lastAutoFillRuanganId === requestedRuanganId) {
    console.log(
      `ℹ️ Auto-fill already processed for ruangan ${requestedRuanganId}, skipping`
    );
    return;
  }

  console.log(`🔍 Loading auto-fill for ruangan ${requestedRuanganId}`);
  window.lastAutoFillRuanganId = requestedRuanganId;

  const url = `${window.location.origin}/user/ruangan/getUserLatestBookingData?ruangan_id=${requestedRuanganId}`;

  fetch(url)
    .then((response) => response.json())
    .then((data) => {
      // ✅ FINAL SECURITY CHECK setelah response
      if (window.currentModalRuanganId !== requestedRuanganId) {
        console.error(
          `❌ RUANGAN CHANGED during fetch: Expected ${requestedRuanganId}, current ${window.currentModalRuanganId}`
        );
        return;
      }

      if (data.success && data.data) {
        const responseRuanganId = parseInt(data.data.ruangan_id);

        // ✅ TRIPLE CHECK ruangan ID
        if (responseRuanganId !== requestedRuanganId) {
          console.error(
            `❌ API MISMATCH: Requested ${requestedRuanganId}, got ${responseRuanganId}`
          );
          showAutoFillErrorNotification(requestedRuanganId, responseRuanganId);
          return;
        }

        console.log(`✅ Auto-fill SUCCESS for ruangan ${requestedRuanganId}`);

        // Auto-fill form fields TERMASUK NOMOR HP
        const fields = {
          nama_penanggung_jawab: data.data.nama_penanggung_jawab,
          nomor_hp_penanggung_jawab: data.data.nomor_hp_penanggung_jawab, // ✅ TAMBAHKAN HP
          unit_organisasi: data.data.unit_organisasi,
          unit_kerja: data.data.unit_kerja,
          jumlah_peserta: data.data.jumlah_peserta,
          keperluan: data.data.keperluan,
        };

        let filledCount = 0;
        Object.entries(fields).forEach(([fieldId, value]) => {
          const field = document.getElementById(fieldId);
          if (field && value) {
            // Special handling untuk nomor HP
            if (fieldId === "nomor_hp_penanggung_jawab") {
              const cleanHP = value.toString().replace(/[^0-9]/g, "");
              field.value = cleanHP;
              field.dataset.autoFilled = "true";
              console.log(`✅ Auto-filled HP: ${cleanHP}`);
            } else {
              field.value = value;
              field.dataset.autoFilled = "true";
              console.log(`✅ Auto-filled ${fieldId}: ${value}`);
            }
            filledCount++;
          }
        });
        // Auto-fill unit kerja berdasarkan unit organisasi
        if (data.data.unit_organisasi && data.data.unit_kerja) {
          setTimeout(() => {
            const unitOrgSelect = document.getElementById("unit_organisasi");
            const unitKerjaSelect = document.getElementById("unit_kerja");

            if (unitOrgSelect && unitKerjaSelect) {
              // ✅ PERBAIKAN: Force call setupUnitKerjaDropdown dulu
              setupUnitKerjaDropdown();

              // ✅ Lalu trigger change event
              setTimeout(() => {
                if (unitOrgSelect.value === data.data.unit_organisasi) {
                  const changeEvent = new Event("change");
                  unitOrgSelect.dispatchEvent(changeEvent);

                  // ✅ Set unit kerja value
                  setTimeout(() => {
                    if (unitKerjaSelect) {
                      unitKerjaSelect.value = data.data.unit_kerja;
                      unitKerjaSelect.dataset.autoFilled = "true";
                      console.log(
                        `✅ Auto-filled unit_kerja dropdown: ${data.data.unit_kerja}`
                      );
                    }
                  }, 100);
                }
              }, 100);
            }
          }, 400);
        }

        // Auto-set tanggal dari booking terakhir
        if (data.data.tanggal) {
          const tanggalField = document.getElementById("tanggal");
          if (tanggalField) {
            tanggalField.value = data.data.tanggal;
            console.log(`📅 Auto-set tanggal: ${data.data.tanggal}`);
            loadExistingBookings(parseInt(ruanganId), data.data.tanggal);
          }
        }

        // Auto-fill waktu booking dengan visual selection
        if (data.data.waktu_mulai && data.data.waktu_selesai) {
          const waktuMulai = data.data.waktu_mulai.substring(0, 5);
          const waktuSelesai = data.data.waktu_selesai.substring(0, 5);

          console.log(
            `⏰ Auto-selecting time: ${waktuMulai} - ${waktuSelesai}`
          );

          setTimeout(() => {
            autoSelectTimeSlotsVisual(waktuMulai, waktuSelesai);
          }, 2500);
        }

        showAutoFillNotificationCompleteFixed(
          data.data.source_type,
          data.data,
          requestedRuanganId
        );
        console.log(
          `🎉 Auto-fill completed for ruangan ${requestedRuanganId}. Fields filled: ${filledCount}`
        );
      } else {
        console.log(
          `ℹ️ No previous booking data found for ruangan ${requestedRuanganId}:`,
          data.message
        );
        showNoDataNotification(requestedRuanganId);
      }
    })
    .catch((error) => {
      console.error(
        `❌ Auto-fill error for ruangan ${requestedRuanganId}:`,
        error
      );
      window.lastAutoFillRuanganId = null; // Reset pada error
      showErrorNotification(requestedRuanganId, error.message);
    });
}

function loadPreviousBookingDataIfEnabled(ruanganId) {
  if (!isAutoFillEnabled()) {
    console.log("ℹ️ Auto-fill is disabled by user preference");
    return;
  }

  if (!ruanganId || isNaN(ruanganId)) {
    console.error(
      `❌ No valid ruangan_id provided: ${ruanganId}, skipping auto-fill`
    );
    return;
  }

  console.log(`🚀 Starting auto-fill process for ruangan: ${ruanganId}`);
  loadPreviousBookingData(ruanganId);
}

function isAutoFillEnabled() {
  return localStorage.getItem("ruangan_autofill_enabled") !== "false";
}

function clearAutoFilledDataFixed() {
  const fields = [
    "nama_penanggung_jawab",
    "nomor_hp_penanggung_jawab", // ✅ TAMBAHKAN INI!
    "unit_organisasi",
    "jumlah_peserta",
    "keperluan",
  ];

  fields.forEach((fieldId) => {
    const field = document.getElementById(fieldId);
    if (field) {
      if (field.tagName === "SELECT") {
        field.selectedIndex = 0;
      } else {
        field.value = "";
      }
    }
  });

  // ✅ FIX: Reset tanggal ke hari ini (bukan besok)
  const tanggalField = document.getElementById("tanggal");
  if (tanggalField) {
    const today = new Date().toISOString().split("T")[0];
    tanggalField.value = today;
  }

  if (typeof resetTimeSelection === "function") {
    resetTimeSelection();
  }

  clearAllTimeSlotHighlights();

  const notification = document.querySelector(
    ".alert-success.alert-dismissible"
  );
  if (notification) {
    notification.remove();
  }

  console.log("✅ Auto-filled data cleared completely including HP");
}

function showAutoFillNotificationCompleteFixed(sourceType, data, ruanganId) {
  const sourceText =
    sourceType === "booking" ? "booking langsung" : "confirm request";
  const modalBody = document.querySelector("#modalPinjamRuangan .modal-body");

  if (modalBody) {
    const notification = document.createElement("div");
    notification.className = "alert alert-success alert-dismissible fade show";
    notification.innerHTML = `
      <i class="bi bi-check-circle-fill me-2"></i>
      <strong>✨ Auto-fill berhasil!</strong> Data diisi otomatis dari ${sourceText} terakhir di ruangan ini:
      <div class="mt-2">
        <small>
          🏢 <strong>Ruangan ID:</strong> ${ruanganId} (${data.ruangan_id})<br>
          👤 <strong>Nama:</strong> ${
            data.nama_penanggung_jawab || "Tidak ada"
          }<br>
          📱 <strong>Nomor HP:</strong> ${
            data.nomor_hp_penanggung_jawab
              ? data.nomor_hp_penanggung_jawab.toString().replace(/[^0-9]/g, "")
              : "Tidak ada"
          }<br>
          🏢 <strong>Unit:</strong> ${data.unit_organisasi || "Tidak ada"}<br>
          👥 <strong>Peserta:</strong> ${
            data.jumlah_peserta || "Tidak ada"
          } orang<br>
          📅 <strong>Tanggal:</strong> ${
            data.tanggal || "Default (hari ini)"
          }<br>
          📝 <strong>Keperluan:</strong> ${
            data.keperluan
              ? data.keperluan.length > 30
                ? data.keperluan.substring(0, 30) + "..."
                : data.keperluan
              : "Tidak ada"
          }<br>
          ⏰ <strong>Waktu:</strong> ${
            data.waktu_mulai && data.waktu_selesai
              ? `${data.waktu_mulai.substring(
                  0,
                  5
                )} - ${data.waktu_selesai.substring(0, 5)}`
              : "Tidak ada"
          }
        </small>
      </div>
      <div class="mt-2">
        <small class="text-info">
          🎯 <strong>Smart Fill:</strong> Data khusus untuk ruangan ${ruanganId} termasuk nomor HP!
        </small>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    modalBody.insertBefore(notification, modalBody.firstChild);

    setTimeout(() => {
      const alert = notification.querySelector(".btn-close");
      if (alert) alert.click();
    }, 15000);
  }
}

function showNoDataNotification(ruanganId) {
  const modalBody = document.querySelector("#modalPinjamRuangan .modal-body");
  if (modalBody) {
    const notification = document.createElement("div");
    notification.className = "alert alert-info alert-dismissible fade show";
    notification.innerHTML = `
      <i class="bi bi-info-circle-fill me-2"></i>
      <strong>📝 Auto-fill tidak tersedia</strong> untuk ruangan ini.
      <div class="mt-2">
        <small>
          🏢 <strong>Ruangan ID:</strong> ${ruanganId}<br>
          ℹ️ Tidak ada data booking sebelumnya di ruangan ini.<br>
          💡 Silakan isi form secara manual.
        </small>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    modalBody.insertBefore(notification, modalBody.firstChild);

    setTimeout(() => {
      const alert = notification.querySelector(".btn-close");
      if (alert) alert.click();
    }, 5000);
  }
}

function showAutoFillErrorNotification(requested, received) {
  const modalBody = document.querySelector("#modalPinjamRuangan .modal-body");
  if (modalBody) {
    const notification = document.createElement("div");
    notification.className = "alert alert-warning alert-dismissible fade show";
    notification.innerHTML = `
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <strong>⚠️ Auto-fill error</strong>
      <div class="mt-2">
        <small>
          🏢 <strong>Expected ruangan:</strong> ${requested}<br>
          🏢 <strong>Received ruangan:</strong> ${received}<br>
          💡 Data tidak sesuai ruangan. Auto-fill dibatalkan.
        </small>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    modalBody.insertBefore(notification, modalBody.firstChild);

    setTimeout(() => {
      const alert = notification.querySelector(".btn-close");
      if (alert) alert.click();
    }, 8000);
  }
}

function showErrorNotification(ruanganId, errorMessage) {
  const modalBody = document.querySelector("#modalPinjamRuangan .modal-body");
  if (modalBody) {
    const notification = document.createElement("div");
    notification.className = "alert alert-danger alert-dismissible fade show";
    notification.innerHTML = `
      <i class="bi bi-x-circle-fill me-2"></i>
      <strong>❌ Auto-fill gagal</strong>
      <div class="mt-2">
        <small>
          🏢 <strong>Ruangan ID:</strong> ${ruanganId}<br>
          ❌ <strong>Error:</strong> ${errorMessage}<br>
          💡 Silakan isi form secara manual.
        </small>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    modalBody.insertBefore(notification, modalBody.firstChild);

    setTimeout(() => {
      const alert = notification.querySelector(".btn-close");
      if (alert) alert.click();
    }, 10000);
  }
}

// Helper functions untuk time selection dan visual
function autoSelectTimeSlotsVisual(waktuMulai, waktuSelesai) {
  console.log(
    `🎯 Auto-selecting time slots VISUALLY: ${waktuMulai} - ${waktuSelesai}`
  );

  try {
    if (typeof resetTimeSelection === "function") {
      resetTimeSelection();
    }

    if (typeof window !== "undefined") {
      window.selectedStartTime = waktuMulai;
      window.selectedEndTime = waktuSelesai;
      window.isSelectingTime = false;
    }

    const waktuMulaiField = document.getElementById("waktu_mulai");
    const waktuSelesaiField = document.getElementById("waktu_selesai");
    if (waktuMulaiField) waktuMulaiField.value = waktuMulai;
    if (waktuSelesaiField) waktuSelesaiField.value = waktuSelesai;

    const displayMulai = document.getElementById("display_waktu_mulai");
    const displaySelesai = document.getElementById("display_waktu_selesai");
    if (displayMulai) displayMulai.textContent = waktuMulai;
    if (displaySelesai) displaySelesai.textContent = waktuSelesai;

    setTimeout(() => highlightTimeSlotsMultiple(waktuMulai, waktuSelesai), 500);
    updateDurationDisplay(waktuMulai, waktuSelesai);

    const submitBtn = document.getElementById("submit_booking");
    if (submitBtn) {
      submitBtn.disabled = false;
    }

    console.log(
      `✅ Auto-selected time VISUALLY: ${waktuMulai} - ${waktuSelesai}`
    );
  } catch (error) {
    console.error("Error in autoSelectTimeSlotsVisual:", error);
  }
}

function highlightTimeSlotsMultiple(waktuMulai, waktuSelesai) {
  console.log(`🎨 Highlighting time slots: ${waktuMulai} - ${waktuSelesai}`);

  const selectors = [
    ".time-slot",
    "[data-time]",
    ".time-ruler .btn",
    ".time-ruler button",
    "#time_ruler button",
    "#time_ruler .btn",
  ];

  selectors.forEach((selector) => {
    const timeSlots = document.querySelectorAll(selector);

    timeSlots.forEach((slot) => {
      if (slot.dataset.autoProcessed) return;
      slot.dataset.autoProcessed = "true";

      slot.classList.remove(
        "selected",
        "selected-start",
        "selected-end",
        "selected-range"
      );

      let slotTime = getTimeFromElement(slot);

      if (slotTime) {
        slotTime = normalizeTimeFormat(slotTime);

        if (slotTime === waktuMulai) {
          slot.classList.add("selected", "selected-start");
          applyStartTimeStyles(slot);
        } else if (slotTime === waktuSelesai) {
          slot.classList.add("selected", "selected-end");
          applyEndTimeStyles(slot);
        } else if (isTimeInRange(slotTime, waktuMulai, waktuSelesai)) {
          slot.classList.add("selected", "selected-range");
          applyRangeTimeStyles(slot);
        }
      }
    });
  });

  setTimeout(() => {
    document.querySelectorAll("[data-auto-processed]").forEach((el) => {
      delete el.dataset.autoProcessed;
    });
  }, 100);
}

function applyStartTimeStyles(element) {
  element.style.setProperty("background-color", "#28a745", "important");
  element.style.setProperty("color", "white", "important");
  element.style.setProperty("border-color", "#1e7e34", "important");
  element.style.setProperty("font-weight", "bold", "important");
  element.style.setProperty(
    "box-shadow",
    "0 0 0 3px rgba(40,167,69,0.3)",
    "important"
  );
  element.style.setProperty("transform", "scale(1.05)", "important");
  element.style.setProperty("z-index", "10", "important");
  element.style.setProperty("position", "relative", "important");
}

function applyEndTimeStyles(element) {
  element.style.setProperty("background-color", "#dc3545", "important");
  element.style.setProperty("color", "white", "important");
  element.style.setProperty("border-color", "#bd2130", "important");
  element.style.setProperty("font-weight", "bold", "important");
  element.style.setProperty(
    "box-shadow",
    "0 0 0 3px rgba(220,53,69,0.3)",
    "important"
  );
  element.style.setProperty("transform", "scale(1.05)", "important");
  element.style.setProperty("z-index", "10", "important");
  element.style.setProperty("position", "relative", "important");
}

function applyRangeTimeStyles(element) {
  element.style.setProperty("background-color", "#ffc107", "important");
  element.style.setProperty("color", "black", "important");
  element.style.setProperty("border-color", "#d39e00", "important");
  element.style.setProperty(
    "box-shadow",
    "0 0 0 2px rgba(255,193,7,0.3)",
    "important"
  );
  element.style.setProperty("transform", "scale(1.02)", "important");
  element.style.setProperty("z-index", "5", "important");
  element.style.setProperty("position", "relative", "important");
}

function getTimeFromElement(element) {
  return (
    element.textContent.trim() ||
    element.dataset.time ||
    element.getAttribute("data-time") ||
    element.getAttribute("onclick")?.match(/[\d:]+/)?.[0] ||
    element.value ||
    ""
  );
}

function normalizeTimeFormat(timeStr) {
  if (!timeStr) return "";

  timeStr = timeStr.replace(/[^\d:]/g, "");

  if (timeStr.length === 5 && timeStr.includes(":")) {
    return timeStr;
  } else if (timeStr.length === 4 && !timeStr.includes(":")) {
    return timeStr.substring(0, 2) + ":" + timeStr.substring(2);
  } else if (timeStr.length === 2) {
    return timeStr + ":00";
  } else if (timeStr.length === 1) {
    return "0" + timeStr + ":00";
  }

  return timeStr;
}

function isTimeInRange(time, start, end) {
  const timeToMinutes = (timeStr) => {
    const [hours, minutes] = timeStr.split(":").map(Number);
    return hours * 60 + minutes;
  };

  const timeMin = timeToMinutes(time);
  const startMin = timeToMinutes(start);
  const endMin = timeToMinutes(end);

  return timeMin > startMin && timeMin < endMin;
}

function updateDurationDisplay(waktuMulai, waktuSelesai) {
  const durationDisplay = document.getElementById("duration_display");
  const durationText = document.getElementById("duration_text");

  if (durationDisplay && durationText) {
    const start = new Date(`2000-01-01T${waktuMulai}:00`);
    const end = new Date(`2000-01-01T${waktuSelesai}:00`);
    const diffMs = end - start;
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

    let durationStr = "";
    if (diffHours > 0) {
      durationStr += `${diffHours} jam`;
    }
    if (diffMinutes > 0) {
      if (durationStr) durationStr += " ";
      durationStr += `${diffMinutes} menit`;
    }

    durationText.textContent = durationStr || "0 menit";
    durationDisplay.style.display = "block";
  }
}

function initializeAutoFillStyles() {
  const style = document.createElement("style");
  style.textContent = `
    .time-slot.selected-start, .btn.selected-start {
      background-color: #28a745 !important;
      color: white !important;
      border-color: #1e7e34 !important;
      font-weight: bold !important;
      box-shadow: 0 0 0 3px rgba(40,167,69,0.3) !important;
      transform: scale(1.05) !important;
      z-index: 10 !important;
      position: relative !important;
    }
    
    .time-slot.selected-end, .btn.selected-end {
      background-color: #dc3545 !important;
      color: white !important;
      border-color: #bd2130 !important;
      font-weight: bold !important;
      box-shadow: 0 0 0 3px rgba(220,53,69,0.3) !important;
      transform: scale(1.05) !important;
      z-index: 10 !important;
      position: relative !important;
    }
    
    .time-slot.selected-range, .btn.selected-range {
      background-color: #ffc107 !important;
      color: black !important;
      border-color: #d39e00 !important;
      box-shadow: 0 0 0 2px rgba(255,193,7,0.3) !important;
      transform: scale(1.02) !important;
      z-index: 5 !important;
      position: relative !important;
    }
    
    .time-slot.selected, .btn.selected {
      transition: all 0.3s ease !important;
    }
  `;

  if (!document.querySelector("#time-slot-highlight-styles")) {
    style.id = "time-slot-highlight-styles";
    document.head.appendChild(style);
  }
}

// Escape HTML function untuk keamanan
function escapeHtml(unsafe) {
  return unsafe
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

// Debug function
function debugAutoFillCache() {
  console.log("=== AUTO-FILL CACHE DEBUG ===");
  console.log("Current modal ruangan:", window.currentModalRuanganId);
  console.log("Last auto-fill ruangan:", window.lastAutoFillRuanganId);

  const fields = [
    "nama_penanggung_jawab",
    "nomor_hp_penanggung_jawab",
    "unit_organisasi",
  ];
  fields.forEach((fieldId) => {
    const field = document.getElementById(fieldId);
    if (field) {
      console.log(
        `${fieldId}:`,
        field.value,
        field.dataset.autoFilled ? "(auto-filled)" : "(manual)"
      );
    }
  });
}

// Call debugAutoFillCache() di console untuk debug
