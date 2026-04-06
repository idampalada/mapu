// booking-ruangan.js
// Modal Booking Ruangan yang selaras dengan Request Confirm - TANPA DISABLE & TANPA UPLOAD FILE
// ===== TAMBAHKAN DI SINI =====
// Data mapping unit organisasi ke unit kerja
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
};
// ===== AKHIR PENAMBAHAN =====
// BOOKING TIME PICKER VARIABLES

// MAIN MODAL FUNCTION
function bukaBookingModal(ruanganId, namaRuangan, kapasitas, keterangan = "") {
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
                        <style>
              #formBookingRuanganModal .form-control::placeholder {
                color: #9CA3AF;
                opacity: 1;
              }
            </style>
                <div class="modal-header">
                    <h5 class="modal-title">Booking Ruangan: ${cleanNamaRuangan}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formBookingRuanganModal" action="${baseUrl}/user/ruangan/bookingLangsung" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="ruangan_id" value="${cleanRuanganId}">
                        
                        ${keteranganSection}
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="booking_nama_penanggung_jawab" class="form-label">Nama Penanggung Jawab<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" id="booking_nama_penanggung_jawab" name="nama_penanggung_jawab" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="booking_nomor_hp_penanggung_jawab" class="form-label">Nomor HP Penanggung Jawab<span class="text-danger"> *</span></label>
                                    <input type="tel" class="form-control" id="booking_nomor_hp_penanggung_jawab" name="nomor_hp_penanggung_jawab" required>
                                </div>

                                <div class="form-group mb-3">
    <label for="booking_unit_organisasi">Unit Organisasi<span class="text-danger"> *</span></label>
    <select class="form-control" id="booking_unit_organisasi" name="unit_organisasi" required>
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

<div class="form-group mb-3">
    <label for="booking_unit_kerja">Unit Kerja<span class="text-danger"> *</span></label>
    <select class="form-control" id="booking_unit_kerja" name="unit_kerja" required disabled>
        <option value="">Pilih Unit Kerja</option>
    </select>
    <small class="text-muted">Pilih unit organisasi terlebih dahulu</small>
</div>
                                       

                                <div class="mb-3">
                                    <label for="booking_tanggal" class="form-label">Tanggal Peminjaman<span class="text-danger"> *</span></label>
                                    <input type="date" class="form-control" id="booking_tanggal" name="tanggal" required
                                           min="${
                                             new Date()
                                               .toISOString()
                                               .split("T")[0]
                                           }">
                                </div>
                                
<div class="mb-3">
    <label for="booking_jumlah_peserta" class="form-label">
        Jumlah Peserta<span class="text-danger"> *</span>
    </label>
    <input type="number" 
           class="form-control" 
           id="booking_jumlah_peserta" 
           name="jumlah_peserta" 
           required 
           min="1" 
           max="${cleanKapasitas}">
    <div class="form-text">Maksimal ${cleanKapasitas} orang</div>
</div>

                                
                                <div class="mb-3">
                                    <label for="booking_keperluan" class="form-label">Keperluan<span class="text-danger"> *</span></label>
                                    <textarea class="form-control" id="booking_keperluan" name="keperluan" 
                                              rows="3" required></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <input type="hidden" id="booking_waktu_mulai" name="waktu_mulai" required>
                                <input type="hidden" id="booking_waktu_selesai" name="waktu_selesai" required>
                                
                                <div id="booking_existing_bookings" class="existing-bookings mb-3" style="display: none;">
                                    <h6><i class="bi bi-info-circle"></i> Booking yang Sudah Ada:</h6>
                                    <div id="booking_booking_list"></div>
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
                                    
                                    <div class="time-ruler" id="booking_time_ruler">
                                    </div>
                                    
                                    <div class="booking-info mt-3">
                                        <div class="row">
                                            <div class="col-6">
                                                <label class="form-label">Waktu Mulai:</label>
                                                <div class="selected-time" id="booking_display_waktu_mulai">Belum dipilih</div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Waktu Selesai:</label>
                                                <div class="selected-time" id="booking_display_waktu_selesai">Belum dipilih</div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle"></i>
                                                Klik untuk memilih waktu mulai, klik lagi untuk waktu selesai
                                            </small>
                                        </div>
                                        <div id="booking_duration_display" class="mt-2" style="display: none;">
                                            <span class="badge bg-info">Durasi: <span id="booking_duration_text"></span></span>
                                        </div>
                                    </div>
                                    
                                    <div id="booking_conflict_warning" class="conflict-warning" style="display: none;">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <strong>Konflik Waktu!</strong>
                                        <div id="booking_conflict_message"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="booking_submit_booking" style="background-color: #133E87;" disabled>
                            Booking Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;

  const modalElement = document.getElementById("modalBookingRuangan");
  modalElement.innerHTML = modalContent;

  addBookingTimePickerStyles();

  const modal = new bootstrap.Modal(modalElement);

  modalElement.addEventListener("shown.bs.modal", function () {
    initializeBookingTimePicker(cleanRuanganId);

    // ===== GANTI DENGAN BARIS INI =====
    setupBookingUnitKerjaDropdown();
    // ===== AKHIR PENGGANTIAN =====

    const form = document.getElementById("formBookingRuanganModal");
    if (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();

        const jumlahPeserta = parseInt(
          document.getElementById("booking_jumlah_peserta").value,
        );
        if (jumlahPeserta > 10) {
          if (jumlahPeserta > cleanKapasitas) {
            Swal.fire({
              icon: "error",
              title: "Error!",
              text: `Jumlah peserta tidak boleh melebihi kapasitas ruangan (${cleanKapasitas} orang)`,
              confirmButtonColor: "#dc3545",
            });
            return;
          }
        }

        const waktuMulai = document.getElementById("booking_waktu_mulai").value;
        const waktuSelesai = document.getElementById(
          "booking_waktu_selesai",
        ).value;

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
        handleBookingRuanganSubmit(e, formData);
      });

      // PERBAIKAN: Auto load booking untuk tanggal hari ini
      const tanggalInput = document.getElementById("booking_tanggal");

      // Set tanggal hari ini sebagai default
      const today = new Date().toISOString().split("T")[0];
      tanggalInput.value = today;

      // Auto load booking untuk hari ini
      console.log(
        `Auto-loading bookings for ruangan ${cleanRuanganId} on ${today}`,
      );
      loadBookingExistingBookings(cleanRuanganId, today);

      // Event listener untuk perubahan tanggal
      tanggalInput.addEventListener("change", function () {
        const selectedDate = this.value;
        if (selectedDate) {
          console.log(`Date changed to: ${selectedDate}`);
          resetBookingTimeSelection(); // Reset selection saat ganti tanggal
          loadBookingExistingBookings(cleanRuanganId, selectedDate);
        }
      });
    }
  });

  modal.show();
}

// Time picker styles untuk booking
function addBookingTimePickerStyles() {
  const existingStyle = document.getElementById("booking-time-picker-styles");
  if (existingStyle) {
    existingStyle.remove();
  }

  const style = document.createElement("style");
  style.id = "booking-time-picker-styles";
  style.textContent = `
    .time-picker-container {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      background-color: #f9f9f9;
    }

    .legend {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 0.85rem;
    }

    .legend-color {
      width: 20px;
      height: 20px;
      border-radius: 4px;
      border: 2px solid #333;
    }

    .legend-color.available {
      background-color: #d4edda;
      border-color: #28a745;
    }

    .legend-color.booked {
      background-color: #f8d7da;
      border-color: #dc3545;
    }

    .legend-color.selected-start {
      background-color: #cce5ff;
      border-color: #007bff;
    }

    .legend-color.selected-end {
      background-color: #e6ccff;
      border-color: #6f42c1;
    }

    .time-ruler {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(60px, 1fr));
      gap: 4px;
      margin: 20px 0;
      max-height: 300px;
      overflow-y: auto;
      padding: 10px;
      background: white;
      border-radius: 6px;
      border: 1px solid #dee2e6;
    }

    .time-slot {
      padding: 8px 4px;
      text-align: center;
      border: 2px solid #dee2e6;
      border-radius: 4px;
      cursor: pointer;
      font-size: 0.75rem;
      font-weight: 500;
      transition: all 0.2s;
      background: white;
      min-height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .time-slot:hover {
      border-color: #adb5bd;
      transform: translateY(-1px);
    }

    .time-slot.available {
      background-color: #d4edda;
      border-color: #28a745;
      color: #155724;
    }

    .time-slot.available:hover {
      background-color: #c3e6cb;
      border-color: #1e7e34;
    }

    .time-slot.booked {
      background-color: #f8d7da;
      border-color: #dc3545;
      color: #721c24;
      cursor: not-allowed;
      opacity: 0.7;
    }

    .time-slot.booked:hover {
      transform: none;
    }

    .time-slot.selected-start {
      background-color: #cce5ff;
      border-color: #007bff;
      color: #003d82;
      font-weight: 600;
    }

    .time-slot.selected-end {
      background-color: #e6ccff;
      border-color: #6f42c1;
      color: #3d1a5c;
      font-weight: 600;
    }

    .time-slot.in-range {
      background-color: #fff3cd;
      border-color: #ffc107;
      color: #856404;
    }

    .selected-time {
      background: white;
      padding: 8px 12px;
      border-radius: 4px;
      border: 2px solid #dee2e6;
      text-align: center;
      font-weight: 500;
      color: #495057;
    }

    .conflict-warning {
      background-color: #f8d7da;
      border: 1px solid #f5c6cb;
      border-radius: 4px;
      padding: 10px;
      color: #721c24;
      margin-top: 10px;
    }

    .existing-bookings {
      background-color: #d1ecf1;
      border: 1px solid #bee5eb;
      border-radius: 4px;
      padding: 10px;
      margin-bottom: 15px;
    }

    .existing-bookings h6 {
      margin-bottom: 8px;
      color: #0c5460;
    }

    .booking-item {
      background: white;
      padding: 6px 10px;
      margin: 4px 0;
      border-radius: 4px;
      border-left: 4px solid #17a2b8;
      font-size: 0.85rem;
    }

    @media (max-width: 768px) {
      .time-ruler {
        grid-template-columns: repeat(auto-fit, minmax(50px, 1fr));
        gap: 3px;
      }
      
      .time-slot {
        padding: 6px 2px;
        font-size: 0.7rem;
        min-height: 28px;
      }
      
      .legend {
        gap: 10px;
      }
    }
  `;
  document.head.appendChild(style);
}

// Initialize booking time picker
function initializeBookingTimePicker(ruanganId) {
  console.log("Initializing booking time picker for ruangan:", ruanganId);

  window.bookingTimePickerState = {
    ruanganId: ruanganId,
    currentRuanganId: ruanganId,
    selectedStart: null,
    selectedEnd: null,
    isSelectingEnd: false,
    bookedSlots: [],
    timeSlots: generateBookingTimeSlots(),
  };
}
function generateBookingTimeSlots() {
  const slots = [];
  for (let hour = 7; hour <= 17; hour++) {
    for (let minute = 0; minute < 60; minute += 30) {
      const timeString =
        String(hour).padStart(2, "0") + ":" + String(minute).padStart(2, "0");
      slots.push(timeString);
    }
  }
  return slots;
}

function renderBookingTimeSlots() {
  const timeRuler = document.getElementById("booking_time_ruler");
  if (!timeRuler) return;

  timeRuler.innerHTML = "";

  const state = window.bookingTimePickerState;

  const tanggalInput = document.getElementById("booking_tanggal");
  const selectedDate = tanggalInput ? tanggalInput.value : null;

  const today = new Date().toISOString().split("T")[0];
  const now = new Date().toTimeString().substring(0, 5);

  state.timeSlots.forEach((timeSlot) => {
    const slotElement = document.createElement("div");

    slotElement.className = "time-slot";
    slotElement.textContent = timeSlot;
    slotElement.dataset.time = timeSlot;

    // 🔴 jika waktu sudah lewat (hari ini)
    if (selectedDate === today && timeSlot <= now) {
      slotElement.classList.add("booked");
      slotElement.title = "Waktu sudah lewat";
    }

    // 🔴 jika sudah dibooking
    else if (state.bookedSlots.includes(timeSlot)) {
      slotElement.classList.add("booked");
      slotElement.title = "Waktu sudah dibooking";
    }

    // 🟢 tersedia
    else {
      slotElement.classList.add("available");
      slotElement.addEventListener("click", handleBookingTimeSlotClick);
    }

    timeRuler.appendChild(slotElement);
  });
}

function handleBookingTimeSlotClick(event) {
  const clickedTime = event.target.dataset.time;
  const state = window.bookingTimePickerState;

  if (event.target.classList.contains("booked")) {
    return;
  }

  if (!state.isSelectingEnd) {
    // Selecting start time
    clearBookingTimeSelection();
    state.selectedStart = clickedTime;
    state.isSelectingEnd = true;

    event.target.classList.add("selected-start");

    document.getElementById("booking_display_waktu_mulai").textContent =
      clickedTime;
    document.getElementById("booking_waktu_mulai").value = clickedTime;

    updateBookingSubmitButton();
  } else {
    // Selecting end time
    if (clickedTime <= state.selectedStart) {
      Swal.fire({
        icon: "warning",
        title: "Waktu Tidak Valid",
        text: "Waktu selesai harus lebih besar dari waktu mulai",
      });
      return;
    }

    // Check if any slot between start and end is booked
    const startIndex = state.timeSlots.indexOf(state.selectedStart);
    const endIndex = state.timeSlots.indexOf(clickedTime);
    let hasBookedInRange = false;

    for (let i = startIndex; i <= endIndex; i++) {
      if (state.bookedSlots.includes(state.timeSlots[i])) {
        hasBookedInRange = true;
        break;
      }
    }

    if (hasBookedInRange) {
      Swal.fire({
        icon: "warning",
        title: "Waktu Tidak Tersedia",
        text: "Terdapat waktu yang sudah dibooking dalam rentang yang dipilih",
      });
      return;
    }

    state.selectedEnd = clickedTime;
    state.isSelectingEnd = false;

    event.target.classList.add("selected-end");

    document.getElementById("booking_display_waktu_selesai").textContent =
      clickedTime;
    document.getElementById("booking_waktu_selesai").value = clickedTime;

    highlightBookingTimeRange();
    updateBookingDuration();
    updateBookingSubmitButton();
  }
}

function highlightBookingTimeRange() {
  const state = window.bookingTimePickerState;
  const startIndex = state.timeSlots.indexOf(state.selectedStart);
  const endIndex = state.timeSlots.indexOf(state.selectedEnd);

  const timeSlots = document.querySelectorAll("#booking_time_ruler .time-slot");

  for (let i = startIndex + 1; i < endIndex; i++) {
    if (timeSlots[i] && !timeSlots[i].classList.contains("booked")) {
      timeSlots[i].classList.add("in-range");
    }
  }
}

function updateBookingDuration() {
  const state = window.bookingTimePickerState;
  if (state.selectedStart && state.selectedEnd) {
    const start = new Date("2000-01-01 " + state.selectedStart);
    const end = new Date("2000-01-01 " + state.selectedEnd);
    const diffMs = end - start;
    const diffHours = diffMs / (1000 * 60 * 60);

    const durationDisplay = document.getElementById("booking_duration_display");
    const durationText = document.getElementById("booking_duration_text");

    if (durationDisplay && durationText) {
      durationText.textContent = diffHours + " jam";
      durationDisplay.style.display = "block";
    }
  }
}

function clearBookingTimeSelection() {
  const state = window.bookingTimePickerState;

  // Clear state
  state.selectedStart = null;
  state.selectedEnd = null;
  state.isSelectingEnd = false;

  // Clear UI
  document
    .querySelectorAll("#booking_time_ruler .time-slot")
    .forEach((slot) => {
      slot.classList.remove("selected-start", "selected-end", "in-range");
    });

  document.getElementById("booking_display_waktu_mulai").textContent =
    "Belum dipilih";
  document.getElementById("booking_display_waktu_selesai").textContent =
    "Belum dipilih";
  document.getElementById("booking_waktu_mulai").value = "";
  document.getElementById("booking_waktu_selesai").value = "";

  const durationDisplay = document.getElementById("booking_duration_display");
  if (durationDisplay) {
    durationDisplay.style.display = "none";
  }

  updateBookingSubmitButton();
}

function resetBookingTimeSelection() {
  clearBookingTimeSelection();
  renderBookingTimeSlots();
}

function updateBookingSubmitButton() {
  const submitButton = document.getElementById("booking_submit_booking");
  const state = window.bookingTimePickerState;

  if (submitButton) {
    if (state.selectedStart && state.selectedEnd) {
      submitButton.disabled = false;
    } else {
      submitButton.disabled = true;
    }
  }
}

function loadBookingExistingBookings(ruanganId, tanggal) {
  console.log(
    `Loading existing bookings for ruangan ${ruanganId} on ${tanggal}`,
  );

  const state = window.bookingTimePickerState;

  // 🔥 simpan reference state saat ini
  const currentStateRef = state;

  // reset booked slots
  state.bookedSlots = [];

  const baseUrl =
    document.querySelector("base")?.href || window.location.origin;

  fetch(
    `${baseUrl}/user/ruangan/getBookingByDate?ruangan_id=${ruanganId}&tanggal=${tanggal}`,
  )
    .then((response) => response.json())
    .then((data) => {
      // 🔥 kalau state sudah berubah → skip total
      if (window.bookingTimePickerState !== currentStateRef) {
        console.log("⛔ Skip: state sudah berubah (pindah ruangan)");
        return;
      }

      console.log("Booking data received:", data);

      const existingBookingsDiv = document.getElementById(
        "booking_existing_bookings",
      );
      const bookingList = document.getElementById("booking_booking_list");

      if (bookingList) bookingList.innerHTML = "";

      if (data.success && Array.isArray(data.data) && data.data.length > 0) {
        if (existingBookingsDiv) existingBookingsDiv.style.display = "block";

        data.data.forEach((booking) => {
          let startTime = booking.waktu_mulai.substring(0, 5);
          let endTime = booking.waktu_selesai.substring(0, 5);

          markBookingTimeSlots(startTime, endTime);
        });
      } else {
        if (existingBookingsDiv) {
          existingBookingsDiv.style.display = "none";
        }
      }

      console.log("Booked slots after processing:", state.bookedSlots);

      renderBookingTimeSlots();
    })
    .catch((error) => {
      console.error("Error loading existing bookings:", error);

      if (window.bookingTimePickerState === currentStateRef) {
        renderBookingTimeSlots();
      }
    });
}
function markBookingTimeSlots(startTime, endTime) {
  if (!window.bookingTimePickerState) {
    window.bookingTimePickerState = { bookedSlots: [] };
  }

  const start = startTime.substring(0, 5);
  const end = endTime.substring(0, 5);

  let current = start;

  while (current <= end) {
    // 🔥 PERBAIKAN DI SINI
    if (!window.bookingTimePickerState.bookedSlots.includes(current)) {
      window.bookingTimePickerState.bookedSlots.push(current);
    }

    const parts = current.split(":");
    let hour = parseInt(parts[0]);
    let minute = parseInt(parts[1]);

    minute += 30;

    if (minute >= 60) {
      hour += 1;
      minute = 0;
    }

    current =
      String(hour).padStart(2, "0") + ":" + String(minute).padStart(2, "0");
  }
}
function handleBookingRuanganSubmit(event, formData) {
  event.preventDefault();

  // TAMBAHKAN VALIDASI NOMOR HP DI SINI
  if (!validateNomorHP()) {
    return; // Stop jika validasi gagal
  }

  const submitButton = document.getElementById("booking_submit_booking");
  const originalText = submitButton.innerHTML;

  // Show loading state
  submitButton.innerHTML =
    '<i class="spinner-border spinner-border-sm me-2"></i>Memproses...';
  submitButton.disabled = true;

  fetch(event.target.action, {
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
          title: "Booking Berhasil!",
          text: data.message || "Ruangan berhasil dibooking",
          showConfirmButton: false,
          timer: 2000,
        }).then(() => {
          const modal = bootstrap.Modal.getInstance(
            document.getElementById("modalBookingRuangan"),
          );
          modal.hide();

          // Tidak perlu reload halaman
          // Kalau mau refresh data saja:
          const tanggal = document.getElementById("booking_tanggal").value;
          loadBookingExistingBookings(
            window.bookingTimePickerState.ruanganId,
            tanggal,
          );
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Booking Gagal",
          text: data.error || "Terjadi kesalahan saat booking",
        });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Terjadi kesalahan saat memproses booking",
      });
    })
    .finally(() => {
      // Reset button
      submitButton.innerHTML = originalText;
      submitButton.disabled = false;
    });
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

// ===== TAMBAHKAN DI SINI =====

// ===== TAMBAH FUNCTION BARU INI SETELAH setupUnitKerjaDropdown() =====
// Setup unit kerja dropdown KHUSUS untuk booking modal
function setupBookingUnitKerjaDropdown() {
  const unitOrgSelect = document.getElementById("booking_unit_organisasi");
  const unitKerjaSelect = document.getElementById("booking_unit_kerja");

  if (unitOrgSelect && unitKerjaSelect) {
    unitOrgSelect.addEventListener("change", function () {
      const selectedUnitOrg = this.value;

      // Clear unit kerja dropdown
      unitKerjaSelect.innerHTML = '<option value="">Pilih Unit Kerja</option>';

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
      } else {
        // Disable unit kerja dropdown if no unit organisasi selected
        unitKerjaSelect.disabled = true;
        unitKerjaSelect.required = false;
      }
    });

    console.log("✅ Booking unit kerja dropdown setup complete");
  } else {
    console.warn("⚠️ Booking unit organisasi or unit kerja select not found");
  }
}
// ===== AKHIR FUNCTION BARU =====
// ===== AKHIR PENAMBAHAN =====
// Force setup when modal opens
document.addEventListener("DOMContentLoaded", function () {
  const modalElement = document.getElementById("modalBookingRuangan");
  if (modalElement) {
    modalElement.addEventListener("shown.bs.modal", function () {
      console.log("🪟 Modal opened, setting up HP validation...");

      // Setup HP validation
      const hpInput = document.getElementById(
        "booking_nomor_hp_penanggung_jawab",
      );
      if (hpInput) {
        // Add validation
        hpInput.addEventListener("input", function (e) {
          // Only allow numbers
          e.target.value = e.target.value.replace(/[^0-9]/g, "");

          // Validate length
          if (e.target.value.length < 10 || e.target.value.length > 15) {
            e.target.classList.add("is-invalid");
            e.target.classList.remove("is-valid");
          } else {
            e.target.classList.remove("is-invalid");
            e.target.classList.add("is-valid");
          }
        });

        console.log("✅ HP validation setup complete");
      } else {
        console.warn("⚠️ HP input not found during modal setup");
      }
    });
  }
});

// 2. VALIDASI NOMOR HP
function validateNomorHP() {
  const nomorHP = document.getElementById(
    "booking_nomor_hp_penanggung_jawab",
  ).value;
  const hpPattern = /^[0-9]{10,15}$/;

  if (!nomorHP || nomorHP.trim() === "") {
    Swal.fire({
      icon: "error",
      title: "Nomor HP Kosong",
      text: "Nomor HP Penanggung Jawab wajib diisi",
      confirmButtonColor: "#dc3545",
    });
    return false;
  }

  if (!hpPattern.test(nomorHP)) {
    Swal.fire({
      icon: "error",
      title: "Format Nomor HP Tidak Valid",
      text: "Nomor HP harus berupa 10-15 digit angka (contoh: 08123456789)",
      confirmButtonColor: "#dc3545",
    });
    return false;
  }
  return true;
}

// 3. SETUP VALIDASI REAL-TIME UNTUK NOMOR HP
function setupNomorHPValidation() {
  const modalElement = document.getElementById("modalBookingRuangan");
  if (modalElement) {
    modalElement.addEventListener("shown.bs.modal", function () {
      const nomorHPInput = document.getElementById(
        "booking_nomor_hp_penanggung_jawab",
      );

      if (nomorHPInput) {
        // ✅ HANYA VALIDASI
        nomorHPInput.addEventListener("input", function (e) {
          e.target.value = e.target.value.replace(/[^0-9]/g, "");

          if (e.target.value.length < 10 || e.target.value.length > 15) {
            e.target.classList.add("is-invalid");
            e.target.classList.remove("is-valid");
          } else {
            e.target.classList.remove("is-invalid");
            e.target.classList.add("is-valid");
          }
        });

        nomorHPInput.setAttribute("placeholder", "Contoh: 08123456789");
        nomorHPInput.setAttribute("pattern", "[0-9]{10,15}");
      }
      // ❌ HAPUS SEMUA AUTO-FILL CODE
    });
  }
}

document.addEventListener("DOMContentLoaded", function () {
  setupNomorHPValidation();
});
