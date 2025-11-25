// PERBAIKAN LENGKAP: ubah-jam.js dengan Time Picker Grid
// Mengganti input time HTML dengan grid button + mempertahankan fungsi yang sudah ada

// UBAH JAM TIME PICKER VARIABLES
let ubahJamSelectedStartTime = null;
let ubahJamSelectedEndTime = null;
let ubahJamExistingBookings = [];
let ubahJamOriginalStartTime = null;
let ubahJamOriginalEndTime = null;
let ubahJamTimePickerState = null;

// PERBAIKAN: Generate time slots 30 menit untuk ubah jam
function generateUbahJamTimeSlots() {
  const slots = [];
  // Mulai dari 07:30 sampai 21:00
  for (let hour = 7; hour <= 21; hour++) {
    for (let minute = 0; minute < 60; minute += 30) {
      // Skip 21:30, stop at 21:00
      if (hour === 21 && minute === 30) break;

      const timeString =
        String(hour).padStart(2, "0") + ":" + String(minute).padStart(2, "0");
      slots.push(timeString);
    }
  }
  return slots;
}

// FUNGSI ASLI: Buka modal ubah jam (dari kode lama) - DIPERBAIKI
function bukaModalUbahJam(pinjamId) {
  console.log(`🕒 Opening modal ubah jam for ID: ${pinjamId}`);

  // Reset state time picker
  resetUbahJamTimeSelection();

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

        // PERBAIKAN: Set waktu original untuk marking dan set default values
        ubahJamOriginalStartTime = p.waktu_mulai.substring(0, 5);
        ubahJamOriginalEndTime = p.waktu_selesai.substring(0, 5);

        // Set nilai default ke hidden input untuk compatibility
        document.getElementById("waktu_mulai_baru").value = p.waktu_mulai;
        document.getElementById("waktu_selesai_baru").value = p.waktu_selesai;

        // PERBAIKAN: Initialize time picker dengan data ruangan
        initializeUbahJamTimePicker(p.ruangan_id, p.tanggal);

        // Show modal
        const modal = new bootstrap.Modal(
          document.getElementById("modalUbahJam")
        );
        modal.show();

        // PERBAIKAN: Load existing bookings dan setup event listeners
        loadUbahJamExistingBookings(p.ruangan_id, p.tanggal, pinjamId);
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

// FUNGSI ASLI: bukaModalUbahJamAdmin (dari kode lama) - DIPERBAIKI
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

// FUNGSI ASLI: resetUbahJamForm - DIPERBAIKI dengan time picker support
function resetUbahJamForm() {
  console.log("Resetting ubah jam form...");

  // PERBAIKAN: Reset time picker state
  resetUbahJamTimeSelection();

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

// FUNGSI ASLI: loadPeminjamanAktifRuangan - DIPERTAHANKAN
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

// FUNGSI ASLI: displayPeminjamanAktif - DIPERBAIKI dengan tombol ubah jam per item
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
      <tr data-pinjam-id="${item.id}">
        <td>
          <div class="fw-bold">${item.nama_penanggung_jawab || "-"}</div>
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
          <span class="badge ${statusClass}">${item.status || "Unknown"}</span>
        </td>
        <td>
          ${
            item.status === "disetujui"
              ? `<button class="btn btn-xs btn-warning" onclick="bukaModalUbahJam('${item.id}')" 
                       title="Ubah jam peminjaman ${item.nama_penanggung_jawab}">
                 <i class="bi bi-clock-history"></i> Ubah Jam
               </button>`
              : `<button class="btn btn-xs btn-success" onclick="setujuiDanUbahJam('${item.id}')" 
                       title="Setujui dengan jam baru">
                 <i class="bi bi-check-circle"></i>
               </button>`
          }
        </td>
      </tr>
    `;
  });

  tableBody.innerHTML = html;
  console.log("✅ Table updated with ubah jam buttons for each person");
}

// ==================== PERBAIKAN: TIME PICKER FUNCTIONS ====================

// PERBAIKAN: Initialize time picker untuk ubah jam
function initializeUbahJamTimePicker(ruanganId, tanggal) {
  console.log("Initializing ubah jam time picker for ruangan:", ruanganId);

  ubahJamTimePickerState = {
    ruanganId: ruanganId,
    tanggal: tanggal,
    selectedStart: null,
    selectedEnd: null,
    isSelectingEnd: false,
    bookedSlots: [],
    timeSlots: generateUbahJamTimeSlots(),
  };

  // Add CSS for time picker
  addUbahJamTimePickerStyles();

  renderUbahJamTimeSlots();
}

// PERBAIKAN: Add CSS styles for time picker
function addUbahJamTimePickerStyles() {
  // Check if styles already added
  if (document.getElementById("ubah-jam-time-picker-styles")) return;

  const style = document.createElement("style");
  style.id = "ubah-jam-time-picker-styles";
  style.innerHTML = `
    .ubah-jam-time-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
      gap: 8px;
      padding: 20px;
      background: white;
      border-radius: 8px;
      border: 2px solid #dee2e6;
      margin: 15px 0;
      max-height: 400px;
      overflow-y: auto;
    }
    
    .ubah-jam-time-slot {
      padding: 10px 8px;
      border: 2px solid #dee2e6;
      border-radius: 6px;
      background: white;
      color: #495057;
      font-weight: 500;
      font-size: 0.85rem;
      cursor: pointer;
      transition: all 0.2s ease;
      min-height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
    
    .ubah-jam-time-slot:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      border-color: #ffc107;
    }
    
    .ubah-jam-time-slot.available {
      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
      border-color: #28a745;
      color: #155724;
    }
    
    .ubah-jam-time-slot.available:hover {
      background: linear-gradient(135deg, #c3e6cb 0%, #b3dfbf 100%);
      border-color: #1e7e34;
    }
    
    .ubah-jam-time-slot.booked {
      background: linear-gradient(135deg, #f8d7da 0%, #f1c2c7 100%);
      border-color: #dc3545;
      color: #721c24;
      cursor: not-allowed;
      opacity: 0.7;
    }
    
    .ubah-jam-time-slot.selected-start {
      background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
      border-color: #0a58ca;
      color: white;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
    }
    
    .ubah-jam-time-slot.selected-end {
      background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
      border-color: #4c2a85;
      color: white;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(111, 66, 193, 0.4);
    }
    
    .ubah-jam-time-slot.in-range {
      background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
      border-color: #ffc107;
      color: #856404;
    }
    
    .ubah-jam-time-slot.original-time {
      background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
      border-color: #117a8b;
      color: white;
      font-weight: 600;
      box-shadow: 0 2px 8px rgba(23, 162, 184, 0.4);
    }
    
    @media (max-width: 768px) {
      .ubah-jam-time-grid {
        grid-template-columns: repeat(auto-fit, minmax(65px, 1fr));
        gap: 6px;
        padding: 15px;
      }
      
      .ubah-jam-time-slot {
        padding: 8px 4px;
        font-size: 0.75rem;
        min-height: 35px;
      }
    }
  `;
  document.head.appendChild(style);
}

// PERBAIKAN: Render time slots untuk ubah jam
function renderUbahJamTimeSlots() {
  const timeRuler = document.getElementById("ubah_jam_time_ruler");
  if (!timeRuler) {
    console.warn(
      "Time ruler element not found, falling back to traditional inputs"
    );
    return;
  }

  timeRuler.innerHTML = "";
  timeRuler.className = "ubah-jam-time-grid";

  ubahJamTimePickerState.timeSlots.forEach((timeSlot) => {
    const slotElement = document.createElement("button");
    slotElement.type = "button";
    slotElement.className = "ubah-jam-time-slot";
    slotElement.textContent = timeSlot;
    slotElement.dataset.time = timeSlot;

    // Check if this is original time
    if (isOriginalTimeSlot(timeSlot)) {
      slotElement.classList.add("original-time");
      slotElement.title = "Waktu saat ini (klik untuk mengubah)";
      // PERBAIKAN: Tetap bisa diklik!
      slotElement.addEventListener("click", handleUbahJamTimeSlotClick);
    }
    // Check if this slot is booked by others (bukan original)
    else if (
      ubahJamTimePickerState.bookedSlots.includes(timeSlot) ||
      isUbahJamTimeBookedByOthers(timeSlot)
    ) {
      slotElement.classList.add("booked");
      slotElement.disabled = true;
      slotElement.title = "Waktu sudah dibooking orang lain";
    } else {
      slotElement.classList.add("available");
      slotElement.addEventListener("click", handleUbahJamTimeSlotClick);
      slotElement.title = "Klik untuk memilih waktu";
    }

    timeRuler.appendChild(slotElement);
  });

  console.log(
    "✅ Time slots rendered:",
    ubahJamTimePickerState.timeSlots.length,
    "slots (original time clickable)"
  );
}

function enableUbahJamButton() {
  const waktuMulai =
    ubahJamSelectedStartTime ||
    document.getElementById("waktu_mulai_baru").value;
  const waktuSelesai =
    ubahJamSelectedEndTime ||
    document.getElementById("waktu_selesai_baru").value;
  const alasan = document.getElementById("alasan_ubah_jam").value.trim();

  const btnUbahSetujui = document.getElementById("btnUbahSetujui");

  if (waktuMulai && waktuSelesai && alasan && waktuSelesai > waktuMulai) {
    btnUbahSetujui.disabled = false;
    btnUbahSetujui.classList.remove("disabled");
    console.log("✅ Button enabled - all conditions met");
  } else {
    btnUbahSetujui.disabled = true;
    btnUbahSetujui.classList.add("disabled");
    console.log("❌ Button disabled - missing:", {
      waktuMulai: !!waktuMulai,
      waktuSelesai: !!waktuSelesai,
      alasan: !!alasan,
      validRange: waktuSelesai > waktuMulai,
    });
  }
}

// PERBAIKAN: Handle time slot click yang membolehkan klik waktu asli
function handleUbahJamTimeSlotClick(event) {
  const clickedTime = event.target.dataset.time;
  const slotElement = event.target;

  console.log("🕒 Time slot clicked:", clickedTime);

  // Prevent selection of booked slots (tapi BOLEHKAN original time)
  if (slotElement.classList.contains("booked") || slotElement.disabled) {
    console.log("❌ Slot blocked - booked or disabled");
    return;
  }

  // PERBAIKAN: Biarkan original time bisa diklik
  if (slotElement.classList.contains("original-time")) {
    console.log("✅ Original time clicked - allowing selection");
    // Jangan remove class, biarkan tetap cyan tapi bisa diklik
  }

  // Reset atau set waktu mulai
  if (
    !ubahJamSelectedStartTime ||
    (ubahJamSelectedStartTime && ubahJamSelectedEndTime)
  ) {
    // Reset selection dan mulai baru
    resetUbahJamTimeSelection();
    ubahJamSelectedStartTime = clickedTime;
    ubahJamTimePickerState.selectedStart = clickedTime;
    ubahJamTimePickerState.isSelectingEnd = true;

    // PERBAIKAN: Jika original time, tetap cyan tapi tambah border biru
    if (isOriginalTimeSlot(clickedTime)) {
      slotElement.classList.add("original-time", "selected-start");
      slotElement.style.border = "3px solid #0d6efd";
    } else {
      slotElement.classList.add("selected-start");
    }

    updateUbahJamTimeDisplay();
    enableUbahJamButton();

    console.log("✅ Start time selected:", clickedTime);
  } else if (ubahJamSelectedStartTime && !ubahJamSelectedEndTime) {
    // Set waktu selesai
    if (clickedTime <= ubahJamSelectedStartTime) {
      Swal.fire({
        icon: "warning",
        title: "Waktu Tidak Valid",
        text: "Waktu selesai harus lebih besar dari waktu mulai",
        confirmButtonColor: "#dc3545",
      });
      return;
    }

    // Cek apakah ada konflik di antara range yang dipilih (kecuali dengan original time)
    const hasConflictInRange = checkUbahJamRangeConflictExcludingOriginal(
      ubahJamSelectedStartTime,
      clickedTime
    );
    if (hasConflictInRange) {
      Swal.fire({
        icon: "warning",
        title: "Ada Konflik Waktu",
        text: "Terdapat waktu yang sudah dibooking dalam rentang yang dipilih",
        confirmButtonColor: "#dc3545",
      });
      return;
    }

    ubahJamSelectedEndTime = clickedTime;
    ubahJamTimePickerState.selectedEnd = clickedTime;
    ubahJamTimePickerState.isSelectingEnd = false;

    // PERBAIKAN: Jika original time, tetap cyan tapi tambah border ungu
    if (isOriginalTimeSlot(clickedTime)) {
      slotElement.classList.add("original-time", "selected-end");
      slotElement.style.border = "3px solid #6f42c1";
    } else {
      slotElement.classList.add("selected-end");
    }

    highlightUbahJamTimeRange();
    updateUbahJamTimeDisplay();
    enableUbahJamButton();

    console.log("✅ End time selected:", clickedTime);
    console.log(
      "📅 Range:",
      ubahJamSelectedStartTime,
      "-",
      ubahJamSelectedEndTime
    );
  }

  updateUbahJamTimeSlotStyles();
}

// PERBAIKAN: Check if time slot is part of original booking
function isOriginalTimeSlot(time) {
  if (!ubahJamOriginalStartTime || !ubahJamOriginalEndTime) return false;

  const timeMinutes = timeToMinutes(time);
  const startMinutes = timeToMinutes(ubahJamOriginalStartTime);
  const endMinutes = timeToMinutes(ubahJamOriginalEndTime);

  return timeMinutes >= startMinutes && timeMinutes < endMinutes;
}

// PERBAIKAN: Check conflict in range
function checkUbahJamRangeConflictExcludingOriginal(startTime, endTime) {
  if (!ubahJamExistingBookings || ubahJamExistingBookings.length === 0) {
    return false;
  }

  const startMinutes = timeToMinutes(startTime);
  const endMinutes = timeToMinutes(endTime);
  const originalStartMinutes = timeToMinutes(ubahJamOriginalStartTime);
  const originalEndMinutes = timeToMinutes(ubahJamOriginalEndTime);

  return ubahJamExistingBookings.some((booking) => {
    const bookingStartMinutes = timeToMinutes(
      booking.waktu_mulai.substring(0, 5)
    );
    const bookingEndMinutes = timeToMinutes(
      booking.waktu_selesai.substring(0, 5)
    );

    // Skip jika ini adalah original booking yang sedang diubah
    if (
      bookingStartMinutes === originalStartMinutes &&
      bookingEndMinutes === originalEndMinutes
    ) {
      return false;
    }

    // Cek apakah ada overlap dengan booking lain
    return startMinutes < bookingEndMinutes && endMinutes > bookingStartMinutes;
  });
}

// PERBAIKAN: Konversi waktu ke menit
function timeToMinutes(timeStr) {
  const [hours, minutes] = timeStr.split(":").map(Number);
  return hours * 60 + minutes;
}

// PERBAIKAN: Highlight range waktu yang dipilih
function highlightUbahJamTimeRange() {
  const timeSlots = document.querySelectorAll(
    "#ubah_jam_time_ruler .ubah-jam-time-slot"
  );
  const startIndex = ubahJamTimePickerState.timeSlots.indexOf(
    ubahJamSelectedStartTime
  );
  const endIndex = ubahJamTimePickerState.timeSlots.indexOf(
    ubahJamSelectedEndTime
  );

  timeSlots.forEach((slot, index) => {
    if (index > startIndex && index < endIndex) {
      if (!slot.classList.contains("booked") && !slot.disabled) {
        slot.classList.add("in-range");
      }
    }
  });
}

// PERBAIKAN: Reset time selection
function resetUbahJamTimeSelection() {
  ubahJamSelectedStartTime = null;
  ubahJamSelectedEndTime = null;

  if (ubahJamTimePickerState) {
    ubahJamTimePickerState.selectedStart = null;
    ubahJamTimePickerState.selectedEnd = null;
    ubahJamTimePickerState.isSelectingEnd = false;
  }

  // Remove selection classes
  document
    .querySelectorAll("#ubah_jam_time_ruler .ubah-jam-time-slot")
    .forEach((slot) => {
      slot.classList.remove("selected-start", "selected-end", "in-range");

      // Restore original time marking
      const time = slot.dataset.time;
      if (isOriginalTimeSlot(time)) {
        slot.classList.remove("available");
        slot.classList.add("original-time");
      }
    });

  updateUbahJamTimeDisplay();
}

// PERBAIKAN: Update display waktu yang dipilih
function updateUbahJamTimeDisplay() {
  // Update display elements (jika ada)
  const displayMulai = document.getElementById("ubah_jam_display_waktu_mulai");
  const displaySelesai = document.getElementById(
    "ubah_jam_display_waktu_selesai"
  );

  if (displayMulai) {
    displayMulai.textContent = ubahJamSelectedStartTime || "Belum dipilih";
  }
  if (displaySelesai) {
    displaySelesai.textContent = ubahJamSelectedEndTime || "Belum dipilih";
  }

  // Update hidden inputs untuk compatibility dengan backend
  document.getElementById("waktu_mulai_baru").value =
    ubahJamSelectedStartTime || "";
  document.getElementById("waktu_selesai_baru").value =
    ubahJamSelectedEndTime || "";

  // Update durasi
  hitungDurasi();

  // PERBAIKAN: Enable button jika kondisi terpenuhi
  enableUbahJamButton();
}

// PERBAIKAN: Load existing bookings untuk ubah jam
function loadUbahJamExistingBookings(ruanganId, tanggal, excludePinjamId) {
  fetch(
    `/admin/verifikasi-ruangan/getBookingByDate?ruangan_id=${ruanganId}&tanggal=${tanggal}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Filter out current booking yang sedang diubah
        ubahJamExistingBookings = data.data.filter(
          (booking) => booking.id != excludePinjamId
        );
        updateUbahJamBookedSlots();
        renderUbahJamTimeSlots();
        console.log(
          "✅ Existing bookings loaded:",
          ubahJamExistingBookings.length
        );
      }
    })
    .catch((error) => {
      console.error("Error loading existing bookings:", error);
    });
}

// PERBAIKAN: Update blocked time slots
function updateUbahJamBookedSlots() {
  if (!ubahJamTimePickerState) return;

  ubahJamTimePickerState.bookedSlots = [];

  ubahJamExistingBookings.forEach((booking) => {
    const startMinutes = timeToMinutes(booking.waktu_mulai.substring(0, 5));
    const endMinutes = timeToMinutes(booking.waktu_selesai.substring(0, 5));

    // Block all 30-minute slots in the range
    for (let minutes = startMinutes; minutes < endMinutes; minutes += 30) {
      const hours = Math.floor(minutes / 60);
      const mins = minutes % 60;
      const timeSlot =
        String(hours).padStart(2, "0") + ":" + String(mins).padStart(2, "0");
      ubahJamTimePickerState.bookedSlots.push(timeSlot);
    }
  });
}

// PERBAIKAN: Check if time is booked
function isUbahJamTimeBookedByOthers(time) {
  if (!ubahJamExistingBookings || ubahJamExistingBookings.length === 0) {
    return false;
  }

  // PERBAIKAN: Jangan block waktu original
  if (isOriginalTimeSlot(time)) {
    return false;
  }

  return ubahJamExistingBookings.some((booking) => {
    const bookingStart = booking.waktu_mulai.substring(0, 5);
    const bookingEnd = booking.waktu_selesai.substring(0, 5);

    // Skip original booking
    if (
      bookingStart === ubahJamOriginalStartTime &&
      bookingEnd === ubahJamOriginalEndTime
    ) {
      return false;
    }

    return time >= bookingStart && time < bookingEnd;
  });
}

// PERBAIKAN: Update time slot styles
function updateUbahJamTimeSlotStyles() {
  document
    .querySelectorAll("#ubah_jam_time_ruler .ubah-jam-time-slot")
    .forEach((slot) => {
      const time = slot.dataset.time;

      // Reset non-state classes
      slot.classList.remove("available", "booked");

      // Determine base state
      if (
        isOriginalTimeSlot(time) &&
        !slot.classList.contains("selected-start") &&
        !slot.classList.contains("selected-end")
      ) {
        slot.classList.add("original-time");
      } else if (isUbahJamTimeBooked(time)) {
        slot.classList.add("booked");
        slot.disabled = true;
      } else {
        slot.classList.add("available");
        slot.disabled = false;
      }
    });
}

// ==================== FUNGSI ASLI YANG DIPERBAIKI ====================

// FUNGSI ASLI: setupEventListenersUbahJam - DIPERBAIKI untuk support time picker
function setupEventListenersUbahJam(ruanganId, tanggal) {
  const waktuMulai = document.getElementById("waktu_mulai_baru");
  const waktuSelesai = document.getElementById("waktu_selesai_baru");

  // Update durasi real-time (untuk fallback input time jika time picker tidak ada)
  [waktuMulai, waktuSelesai].forEach((input) => {
    if (input) {
      input.addEventListener("input", () => {
        hitungDurasi();
        resetWarningKonflik();
        document.getElementById("btnUbahSetujui").disabled = true;
        document.getElementById("preview_ubah").style.display = "none";
      });
    }
  });
}

// FUNGSI ASLI: hitungDurasi - DIPERBAIKI untuk support time picker
function hitungDurasi() {
  const mulaiValue =
    ubahJamSelectedStartTime ||
    document.getElementById("waktu_mulai_baru").value;
  const selesaiValue =
    ubahJamSelectedEndTime ||
    document.getElementById("waktu_selesai_baru").value;

  if (mulaiValue && selesaiValue) {
    const start = new Date(`2000-01-01T${mulaiValue}:00`);
    const end = new Date(`2000-01-01T${selesaiValue}:00`);
    const diffMs = end - start;

    const durasiElement = document.getElementById("durasi_baru");
    if (!durasiElement) return;

    if (diffMs > 0) {
      const hours = Math.floor(diffMs / (1000 * 60 * 60));
      const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

      let durasi = "";
      if (hours > 0) durasi += `${hours} jam `;
      if (minutes > 0) durasi += `${minutes} menit`;

      durasiElement.textContent = durasi || "0 menit";
      durasiElement.className = "badge bg-success";
    } else {
      durasiElement.textContent = "Tidak valid";
      durasiElement.className = "badge bg-danger";
    }
  }
}

// FUNGSI ASLI: cekKetersediaanJam - DIPERBAIKI untuk support time picker
function cekKetersediaanJam() {
  const pinjamId = document.getElementById("ubah_pinjam_id").value;
  const waktuMulai =
    ubahJamSelectedStartTime ||
    document.getElementById("waktu_mulai_baru").value;
  const waktuSelesai =
    ubahJamSelectedEndTime ||
    document.getElementById("waktu_selesai_baru").value;
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

// FUNGSI ASLI: tampilkanPreview - DIPERBAIKI untuk time picker
function tampilkanPreview() {
  const waktuMulai =
    ubahJamSelectedStartTime ||
    document.getElementById("waktu_mulai_baru").value;
  const waktuSelesai =
    ubahJamSelectedEndTime ||
    document.getElementById("waktu_selesai_baru").value;
  const waktuOriginal = document.getElementById("waktu_original").textContent;
  const alasan = document.getElementById("alasan_ubah_jam").value;

  document.getElementById("preview_content_ubah").innerHTML = `
    <div><strong>Waktu Lama:</strong> ${waktuOriginal}</div>
    <div><strong>Waktu Baru:</strong> ${waktuMulai} - ${waktuSelesai}</div>
    <div><strong>Alasan:</strong> ${alasan}</div>
  `;
  document.getElementById("preview_ubah").style.display = "block";
}

// FUNGSI ASLI: submitUbahJam - DIPERTAHANKAN
function submitUbahJam(event) {
  if (event) event.preventDefault();

  const formEl = document.getElementById("formUbahJam");
  const formData = new FormData(formEl);

  // Convert FormData -> object biasa
  const payload = Object.fromEntries(formData.entries());

  fetch("/admin/verifikasi-ruangan/ubahJamSetujui", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify(payload),
  })
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) throw new Error(data.message || "Gagal mengubah jam");

      Swal.fire({
        icon: "success",
        title: "Berhasil!",
        text: data.message,
        timer: 3000,
        showConfirmButton: false,
      }).then(() => {
        bootstrap.Modal.getInstance(
          document.getElementById("modalUbahJam")
        ).hide();
        location.reload();
      });
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire("Error", error.message, "error");
    });
}

// ==================== HELPER FUNCTIONS ====================

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

// ==================== EVENT LISTENERS ====================

// PERBAIKAN: Event listener untuk textarea alasan
document.addEventListener("DOMContentLoaded", function () {
  console.log("🚀 Ubah jam script loading...");

  // Event listener untuk textarea alasan - PERBAIKAN
  const alasanTextarea = document.getElementById("alasan_ubah_jam");
  if (alasanTextarea) {
    alasanTextarea.addEventListener("input", function () {
      const waktuMulai =
        ubahJamSelectedStartTime ||
        document.getElementById("waktu_mulai_baru").value;
      const waktuSelesai =
        ubahJamSelectedEndTime ||
        document.getElementById("waktu_selesai_baru").value;

      if (waktuMulai && waktuSelesai && this.value.trim()) {
        tampilkanPreview();
      }

      // PERBAIKAN: Enable button saat alasan diisi
      enableUbahJamButton();
    });
  }

  console.log("✅ Ubah jam script loaded successfully");
});

console.log(
  "✅ Enhanced ubah jam functionality with time picker grid loaded successfully"
);

function cekKetersediaanJamOtomatis() {
  const pinjamId = document.getElementById("ubah_pinjam_id").value;
  const waktuMulai =
    ubahJamSelectedStartTime ||
    document.getElementById("waktu_mulai_baru").value;
  const waktuSelesai =
    ubahJamSelectedEndTime ||
    document.getElementById("waktu_selesai_baru").value;

  if (!waktuMulai || !waktuSelesai) return;

  console.log("🔍 Auto checking availability...");

  // Validasi durasi minimum 30 menit
  const start = new Date(`2000-01-01T${waktuMulai}:00`);
  const end = new Date(`2000-01-01T${waktuSelesai}:00`);
  const diffMinutes = (end - start) / (1000 * 60);

  if (diffMinutes < 30) {
    console.log("❌ Duration too short");
    return;
  }

  // Cek via API tapi jangan show loading
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
      if (data.success && data.available) {
        console.log("✅ Auto check: Time available");
        resetWarningKonflik();
        // Jangan auto enable button di sini, biarkan enableUbahJamButton() yang handle
      } else {
        console.log("⚠️ Auto check: Time conflict");
        tampilkanWarningKonflik(
          data.message || "Waktu bentrok dengan peminjaman lain"
        );
      }
    })
    .catch((error) => {
      console.log("❌ Auto check failed:", error);
    });
}

console.log(
  "🔧 PATCH: Original time now clickable + button auto-enable loaded"
);
