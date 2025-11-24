// booking-ruangan.js
// Modal Booking Ruangan yang selaras dengan Request Confirm - TANPA DISABLE & TANPA UPLOAD FILE

// BOOKING TIME PICKER VARIABLES
let bookingSelectedStartTime = null;
let bookingSelectedEndTime = null;
let bookingExistingBookings = [];

// BOOKING TIME PICKER FUNCTIONS
function addBookingTimePickerStyles() {
  // CSS sudah di-load dari file terpisah, tidak perlu inline CSS lagi
  console.log("Booking time picker styles loaded from external CSS file");
  return true;
}

function generateBookingTimeSlots() {
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

function initializeBookingTimePicker(ruanganId) {
  const timeSlots = generateBookingTimeSlots();
  const timeRuler = document.getElementById("booking_time_ruler");

  timeRuler.innerHTML = "";

  timeSlots.forEach((time) => {
    const slot = document.createElement("div");
    slot.className = "time-slot available";
    slot.textContent = time;
    slot.dataset.time = time;
    slot.tabIndex = 0;

    slot.addEventListener("click", () =>
      handleBookingTimeSlotClick(time, ruanganId)
    );

    slot.addEventListener("keydown", (e) => {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        slot.click();
      }
    });

    timeRuler.appendChild(slot);
  });
}

function handleBookingTimeSlotClick(time, ruanganId) {
  const slot = document.querySelector(`[data-time="${time}"]`);

  // Check if slot is blocked
  if (slot.classList.contains("booked") || isBookingTimeBooked(time)) {
    const booking = getBookingBookingForTime(time);
    const statusText = "sudah dibooking";

    Swal.fire({
      icon: "error",
      title: "Waktu Tidak Tersedia",
      html: `Waktu ini ${statusText}:<br>
                   <strong>${booking?.waktu_mulai} - ${booking?.waktu_selesai}</strong><br>
                   Keperluan: ${booking?.keperluan}<br>
                   PIC: ${booking?.nama_penanggung_jawab}`,
      confirmButtonColor: "#dc3545",
    });
    return false;
  }

  // Continue with normal selection logic
  if (bookingSelectedStartTime === time || bookingSelectedEndTime === time) {
    resetBookingTimeSelection();
    return;
  }

  if (!bookingSelectedStartTime) {
    bookingSelectedStartTime = time;
    updateBookingTimeDisplay();
    updateBookingTimeSlotStyles();
  } else if (!bookingSelectedEndTime) {
    if (time <= bookingSelectedStartTime) {
      Swal.fire({
        icon: "warning",
        title: "Waktu Tidak Valid",
        text: "Waktu selesai harus setelah waktu mulai",
        confirmButtonColor: "#dc3545",
      });
      return;
    }

    bookingSelectedEndTime = time;

    const hasConflict = checkBookingTimeConflict(
      bookingSelectedStartTime,
      bookingSelectedEndTime
    );
    if (hasConflict) {
      showBookingConflictWarning(hasConflict);
      return;
    }

    updateBookingTimeDisplay();
    updateBookingTimeSlotStyles();
    enableBookingSubmitButton();
  } else {
    resetBookingTimeSelection();
    bookingSelectedStartTime = time;
    updateBookingTimeDisplay();
    updateBookingTimeSlotStyles();
  }
}

function resetBookingTimeSelection() {
  bookingSelectedStartTime = null;
  bookingSelectedEndTime = null;
  updateBookingTimeDisplay();
  updateBookingTimeSlotStyles();
  disableBookingSubmitButton();
  hideBookingConflictWarning();
}

function updateBookingTimeDisplay() {
  document.getElementById("booking_display_waktu_mulai").textContent =
    bookingSelectedStartTime || "Belum dipilih";
  document.getElementById("booking_display_waktu_selesai").textContent =
    bookingSelectedEndTime || "Belum dipilih";

  document.getElementById("booking_waktu_mulai").value =
    bookingSelectedStartTime || "";
  document.getElementById("booking_waktu_selesai").value =
    bookingSelectedEndTime || "";

  if (bookingSelectedStartTime && bookingSelectedEndTime) {
    const duration = calculateBookingDuration(
      bookingSelectedStartTime,
      bookingSelectedEndTime
    );
    document.getElementById("booking_duration_text").textContent = duration;
    document.getElementById("booking_duration_display").style.display = "block";
  } else {
    document.getElementById("booking_duration_display").style.display = "none";
  }
}

function updateBookingTimeSlotStyles() {
  document
    .querySelectorAll("#booking_time_ruler .time-slot")
    .forEach((slot) => {
      const time = slot.dataset.time;

      // Reset classes
      slot.classList.remove(
        "selected-start",
        "selected-end",
        "in-range",
        "conflict-highlight"
      );

      // Set base class berdasarkan availability
      if (isBookingTimeBooked(time)) {
        slot.className = "time-slot booked";
        const booking = getBookingBookingForTime(time);
        if (booking) {
          slot.title = `Dibooking: ${booking.waktu_mulai}-${booking.waktu_selesai}\nKeperluan: ${booking.keperluan}`;
        }
      } else {
        slot.className = "time-slot available";
        slot.title = "Klik untuk pilih waktu";
      }
    });

  // Apply selection styles
  if (bookingSelectedStartTime) {
    const startSlot = document.querySelector(
      `#booking_time_ruler [data-time="${bookingSelectedStartTime}"]`
    );
    if (startSlot && !startSlot.classList.contains("booked")) {
      startSlot.classList.remove("available");
      startSlot.classList.add("selected-start");
    }
  }

  if (bookingSelectedEndTime) {
    const endSlot = document.querySelector(
      `#booking_time_ruler [data-time="${bookingSelectedEndTime}"]`
    );
    if (endSlot && !endSlot.classList.contains("booked")) {
      endSlot.classList.remove("available");
      endSlot.classList.add("selected-end");
    }
  }

  // Apply range style
  if (bookingSelectedStartTime && bookingSelectedEndTime) {
    const timeSlots = Array.from(
      document.querySelectorAll("#booking_time_ruler .time-slot")
    );
    timeSlots.forEach((slot) => {
      const time = slot.dataset.time;
      if (
        time > bookingSelectedStartTime &&
        time < bookingSelectedEndTime &&
        !slot.classList.contains("booked")
      ) {
        slot.classList.remove("available");
        slot.classList.add("in-range");
      }
    });
  }
}

function checkBookingTimeConflict(startTime, endTime) {
  for (let booking of bookingExistingBookings) {
    const bookingStart = booking.waktu_mulai.substring(0, 5);
    const bookingEnd = booking.waktu_selesai.substring(0, 5);

    // Cek berbagai jenis konflik
    const conflict1 = startTime >= bookingStart && startTime < bookingEnd;
    const conflict2 = endTime > bookingStart && endTime <= bookingEnd;
    const conflict3 = startTime <= bookingStart && endTime >= bookingEnd;

    const hasConflict = conflict1 || conflict2 || conflict3;

    if (hasConflict) {
      console.log(
        `Conflict detected with booking: ${bookingStart}-${bookingEnd}`
      );
      return booking;
    }
  }

  return null;
}

function showBookingConflictWarning(conflictBooking) {
  const warningDiv = document.getElementById("booking_conflict_warning");
  const messageDiv = document.getElementById("booking_conflict_message");

  messageDiv.innerHTML = `
    <div class="d-flex align-items-start">
      <i class="bi bi-exclamation-triangle me-2 mt-1"></i>
      <div>
        <strong>Konflik Waktu!</strong><br>
        Waktu yang dipilih bertabrakan dengan booking yang sudah ada:<br>
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

  bookingSelectedEndTime = null;
  updateBookingTimeDisplay();
  updateBookingTimeSlotStyles();
  disableBookingSubmitButton();
}

function hideBookingConflictWarning() {
  document.getElementById("booking_conflict_warning").style.display = "none";
}

function calculateBookingDuration(startTime, endTime) {
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

function enableBookingSubmitButton() {
  const submitBtn = document.getElementById("booking_submit_booking");
  if (submitBtn) {
    submitBtn.disabled = false;
  }
}

function disableBookingSubmitButton() {
  const submitBtn = document.getElementById("booking_submit_booking");
  if (submitBtn) {
    submitBtn.disabled = true;
  }
}

function loadBookingExistingBookings(ruanganId, tanggal) {
  const baseUrl =
    document.querySelector("base")?.href || window.location.origin;

  console.log(`Loading bookings for ruangan ${ruanganId} on ${tanggal}`);

  document.getElementById("booking_booking_list").innerHTML =
    '<div class="text-center"><i class="bi bi-hourglass-split"></i> Memuat data booking...</div>';

  resetBookingTimeSelection();

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
        bookingExistingBookings = data.data || [];

        // Clean time format
        if (bookingExistingBookings.length > 0) {
          bookingExistingBookings = bookingExistingBookings.map((booking) => ({
            ...booking,
            waktu_mulai: booking.waktu_mulai.substring(0, 5),
            waktu_selesai: booking.waktu_selesai.substring(0, 5),
          }));
        }

        // Update displays
        updateBookingExistingBookingsDisplay();
        updateBookingBookedTimeSlots();
        showBookingAvailabilityInfo();
      } else {
        throw new Error(data.message || "Server error");
      }
    })
    .catch((error) => {
      console.error("Error loading bookings:", error);
      bookingExistingBookings = [];
      updateBookingExistingBookingsDisplay();
      updateBookingBookedTimeSlots();
      showBookingToast(
        "Gagal memuat data booking: " + error.message,
        "error",
        5000
      );
    });
}

function updateBookingExistingBookingsDisplay() {
  const bookingList = document.getElementById("booking_booking_list");
  const existingBookingsDiv = document.getElementById(
    "booking_existing_bookings"
  );

  if (bookingExistingBookings.length === 0) {
    existingBookingsDiv.style.display = "none";
    return;
  }

  existingBookingsDiv.style.display = "block";

  const bookingsHtml = bookingExistingBookings
    .map((booking) => {
      return `
      <div class="booking-item">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <strong>${booking.waktu_mulai} - ${booking.waktu_selesai}</strong>
            <span class="badge bg-success ms-2">Dibooking</span>
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

function updateBookingBookedTimeSlots() {
  document
    .querySelectorAll("#booking_time_ruler .time-slot")
    .forEach((slot) => {
      const time = slot.dataset.time;
      const isBooked = isBookingTimeBooked(time);

      if (isBooked) {
        slot.classList.remove(
          "available",
          "selected-start",
          "selected-end",
          "in-range"
        );
        slot.classList.add("booked");

        const booking = getBookingBookingForTime(time);
        if (booking) {
          slot.title = `Dibooking: ${booking.waktu_mulai}-${booking.waktu_selesai}\nKeperluan: ${booking.keperluan}`;
        }
      } else {
        slot.classList.remove("booked");
        slot.classList.add("available");
        slot.title = "Klik untuk pilih waktu";
      }
    });
}

function getBookingBookingForTime(time) {
  return bookingExistingBookings.find((booking) => {
    const bookingStart = booking.waktu_mulai.substring(0, 5);
    const bookingEnd = booking.waktu_selesai.substring(0, 5);
    const currentTime = time.substring(0, 5);

    return currentTime >= bookingStart && currentTime < bookingEnd;
  });
}

function isBookingTimeBooked(time) {
  if (!bookingExistingBookings || bookingExistingBookings.length === 0) {
    return false;
  }

  return bookingExistingBookings.some((booking) => {
    const bookingStart = booking.waktu_mulai.substring(0, 5);
    const bookingEnd = booking.waktu_selesai.substring(0, 5);
    const currentTime = time.substring(0, 5);

    // Cek apakah waktu berada dalam range booking (include start AND include end)
    const isInRange =
      (currentTime >= bookingStart && currentTime < bookingEnd) ||
      currentTime === bookingEnd;

    if (isInRange) {
      console.log(
        `Time ${currentTime} is BLOCKED by booking ${bookingStart}-${bookingEnd}`
      );
    }

    return isInRange;
  });
}

function showBookingAvailabilityInfo() {
  const totalSlots = document.querySelectorAll(
    "#booking_time_ruler .time-slot"
  ).length;
  const bookedSlots = document.querySelectorAll(
    "#booking_time_ruler .time-slot.booked"
  ).length;
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
    showBookingToast(message, type, 4000);
  }, 500);
}

function showBookingToast(message, type = "info", duration = 3000) {
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
                                    <label for="booking_tanggal" class="form-label">Tanggal Peminjaman<span class="text-danger"> *</span></label>
                                    <input type="date" class="form-control" id="booking_tanggal" name="tanggal" required
                                           min="${
                                             new Date()
                                               .toISOString()
                                               .split("T")[0]
                                           }">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="booking_jumlah_peserta" class="form-label">Jumlah Peserta<span class="text-danger"> *</span></label>
                                    <input type="number" class="form-control" id="booking_jumlah_peserta" name="jumlah_peserta" 
                                           required min="1" max="10">
                                    <div class="form-text">Maksimal 10 orang</div>
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

    const form = document.getElementById("formBookingRuanganModal");
    if (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();

        const jumlahPeserta = parseInt(
          document.getElementById("booking_jumlah_peserta").value
        );
        if (jumlahPeserta > 10) {
          Swal.fire({
            icon: "error",
            title: "Error!",
            text: `Jumlah peserta tidak boleh lebih dari 10 orang`,
            confirmButtonColor: "#dc3545",
          });
          return;
        }

        const waktuMulai = document.getElementById("booking_waktu_mulai").value;
        const waktuSelesai = document.getElementById(
          "booking_waktu_selesai"
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
        `Auto-loading bookings for ruangan ${cleanRuanganId} on ${today}`
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
    selectedStart: null,
    selectedEnd: null,
    isSelectingEnd: false,
    bookedSlots: [],
    timeSlots: generateBookingTimeSlots(),
  };

  renderBookingTimeSlots();
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

  window.bookingTimePickerState.timeSlots.forEach((timeSlot) => {
    const slotElement = document.createElement("div");
    slotElement.className = "time-slot";
    slotElement.textContent = timeSlot;
    slotElement.dataset.time = timeSlot;

    // Check if this slot is booked
    if (window.bookingTimePickerState.bookedSlots.includes(timeSlot)) {
      slotElement.classList.add("booked");
      slotElement.title = "Waktu sudah dibooking";
    } else {
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
    `Loading existing bookings for ruangan ${ruanganId} on ${tanggal}`
  );

  // Clear previous bookings
  window.bookingTimePickerState.bookedSlots = [];

  const baseUrl =
    document.querySelector("base")?.href || window.location.origin;

  fetch(
    `${baseUrl}/user/ruangan/getBookingByDate?ruangan_id=${ruanganId}&tanggal=${tanggal}`
  )
    .then((response) => response.json())
    .then((data) => {
      console.log("Booking data received:", data);

      if (data.success && data.bookings && data.bookings.length > 0) {
        // Show existing bookings
        const existingBookingsDiv = document.getElementById(
          "booking_existing_bookings"
        );
        const bookingList = document.getElementById("booking_booking_list");

        if (existingBookingsDiv && bookingList) {
          existingBookingsDiv.style.display = "block";
          bookingList.innerHTML = "";

          data.bookings.forEach((booking) => {
            const bookingItem = document.createElement("div");
            bookingItem.className = "booking-item";
            bookingItem.innerHTML = `
              <strong>${booking.waktu_mulai} - ${booking.waktu_selesai}</strong><br>
              <small>${booking.nama_penanggung_jawab} (${booking.keperluan})</small>
            `;
            bookingList.appendChild(bookingItem);

            // Mark time slots as booked
            const startTime = booking.waktu_mulai;
            const endTime = booking.waktu_selesai;
            markBookingTimeSlots(startTime, endTime);
          });
        }
      } else {
        // Hide existing bookings if none
        const existingBookingsDiv = document.getElementById(
          "booking_existing_bookings"
        );
        if (existingBookingsDiv) {
          existingBookingsDiv.style.display = "none";
        }
      }

      // Re-render time slots with new booking data
      renderBookingTimeSlots();
    })
    .catch((error) => {
      console.error("Error loading existing bookings:", error);
      // Still render time slots even if loading fails
      renderBookingTimeSlots();
    });
}

function markBookingTimeSlots(startTime, endTime) {
  const state = window.bookingTimePickerState;
  const startIndex = state.timeSlots.indexOf(startTime);
  const endIndex = state.timeSlots.indexOf(endTime);

  if (startIndex !== -1 && endIndex !== -1) {
    // Mark all slots from start to end (exclusive) as booked
    for (let i = startIndex; i < endIndex; i++) {
      if (state.timeSlots[i]) {
        state.bookedSlots.push(state.timeSlots[i]);
      }
    }
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
          // Close modal
          const modal = bootstrap.Modal.getInstance(
            document.getElementById("modalBookingRuangan")
          );
          modal.hide();

          // Reload page
          location.reload();
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

// Force setup when modal opens
document.addEventListener("DOMContentLoaded", function () {
  const modalElement = document.getElementById("modalBookingRuangan");
  if (modalElement) {
    modalElement.addEventListener("shown.bs.modal", function () {
      console.log("🪟 Modal opened, setting up HP validation...");

      // Setup HP validation
      const hpInput = document.getElementById(
        "booking_nomor_hp_penanggung_jawab"
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
    "booking_nomor_hp_penanggung_jawab"
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
        "booking_nomor_hp_penanggung_jawab"
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
