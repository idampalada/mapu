/**
 * calendar.js
 * Fungsional kalender booking ruangan (standalone)
 *
 * Dependensi:
 * - Bootstrap 5 (modal)
 * - Bootstrap Icons
 *
 * HTML yang dibutuhkan:
 * - #toggleCalendar        (button toggle)
 * - #calendarButtonText    (teks dalam button)
 * - #calendarIcon          (icon dalam button)
 * - #calendarContainer     (wrapper kalender, display:none by default)
 * - #prevMonth             (button bulan sebelumnya)
 * - #nextMonth             (button bulan berikutnya)
 * - #currentMonthYear      (label bulan & tahun)
 * - #calendarGrid          (grid hari-hari kalender)
 * - #modalDetailBooking    (Bootstrap modal)
 * - #modalBookingContent   (isi konten modal booking)
 *
 * API endpoint yang dibutuhkan:
 * - GET /User/Ruangan/getBookingPublik
 *   Response: { success: true, data: [ { tanggal, waktu_mulai, waktu_selesai, nama_ruangan, keperluan, status, nama_penanggung_jawab, unit_organisasi, jumlah_peserta } ] }
 */

(function () {
  "use strict";

  // ─── STATE ───────────────────────────────────────────────────────────────────
  let currentDate = new Date();
  let bookingsData = [];
  let calendarInitialized = false;

  const BASE_URL = window.location.origin;
  const API_ENDPOINT =
    BASE_URL +
    "/User/Ruangan/getBookingPublik/" +
    encodeURIComponent(LOKASI_GEDUNG);

  // ─── INIT ─────────────────────────────────────────────────────────────────────
  document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("toggleCalendar");
    const calendarContainer = document.getElementById("calendarContainer");
    const buttonText = document.getElementById("calendarButtonText");
    const calendarIcon = document.getElementById("calendarIcon");

    if (!toggleBtn || !calendarContainer) return;

    // Toggle show/hide kalender
    toggleBtn.addEventListener("click", function () {
      if (calendarContainer.style.display === "none") {
        calendarContainer.style.display = "block";
        calendarContainer.classList.add("calendar-slide-down");
        if (buttonText) buttonText.textContent = "Sembunyikan Kalender";
        if (calendarIcon) calendarIcon.className = "bi bi-calendar-x";

        if (!calendarInitialized) {
          loadAllBookingsData().then(function () {
            renderCalendar();
            calendarInitialized = true;
          });
        }
      } else {
        calendarContainer.classList.remove("calendar-slide-down");
        calendarContainer.classList.add("calendar-slide-up");

        setTimeout(function () {
          calendarContainer.style.display = "none";
          calendarContainer.classList.remove("calendar-slide-up");
        }, 300);

        if (buttonText) buttonText.textContent = "Tampilkan Kalender";
        if (calendarIcon) calendarIcon.className = "bi bi-calendar3";
      }
    });

    // Navigasi bulan
    var prevBtn = document.getElementById("prevMonth");
    var nextBtn = document.getElementById("nextMonth");
    if (prevBtn) {
      prevBtn.addEventListener("click", function () {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
      });
    }
    if (nextBtn) {
      nextBtn.addEventListener("click", function () {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
      });
    }
  });

  // ─── API ──────────────────────────────────────────────────────────────────────
  function loadAllBookingsData() {
    return fetch(API_ENDPOINT)
      .then(function (res) {
        if (!res.ok) throw new Error("HTTP error! status: " + res.status);
        return res.json();
      })
      .then(function (data) {
        if (data.success) {
          bookingsData = data.data || [];
        } else {
          bookingsData = [];
        }
      })
      .catch(function (error) {
        console.error("Error loading bookings:", error);
        bookingsData = [];
      });
  }

  // ─── RENDER KALENDER ─────────────────────────────────────────────────────────
  function renderCalendar() {
    var year = currentDate.getFullYear();
    var month = currentDate.getMonth();

    var monthNames = [
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

    var label = document.getElementById("currentMonthYear");
    if (label) label.textContent = monthNames[month] + " " + year;

    var calendarGrid = document.getElementById("calendarGrid");
    if (!calendarGrid) return;
    calendarGrid.innerHTML = "";

    // Header hari
    var dayHeaders = [
      "Minggu",
      "Senin",
      "Selasa",
      "Rabu",
      "Kamis",
      "Jumat",
      "Sabtu",
    ];
    dayHeaders.forEach(function (day) {
      var headerDiv = document.createElement("div");
      headerDiv.className = "calendar-day-header";
      headerDiv.textContent = day;
      calendarGrid.appendChild(headerDiv);
    });

    var firstDay = new Date(year, month, 1);
    var lastDay = new Date(year, month + 1, 0);
    var firstDayWeekday = firstDay.getDay();
    var daysInMonth = lastDay.getDate();
    var today = new Date();

    // Sel kosong sebelum hari pertama
    for (var i = 0; i < firstDayWeekday; i++) {
      var emptyDiv = document.createElement("div");
      emptyDiv.className = "calendar-day other-month";
      calendarGrid.appendChild(emptyDiv);
    }

    // Render tiap hari
    for (var day = 1; day <= daysInMonth; day++) {
      var dayDiv = document.createElement("div");
      dayDiv.className = "calendar-day";

      if (
        year === today.getFullYear() &&
        month === today.getMonth() &&
        day === today.getDate()
      ) {
        dayDiv.classList.add("today");
      }

      var dayNumber = document.createElement("div");
      dayNumber.className = "day-number";
      dayNumber.textContent = day;
      dayDiv.appendChild(dayNumber);

      var dateStr =
        year +
        "-" +
        String(month + 1).padStart(2, "0") +
        "-" +
        String(day).padStart(2, "0");

      var dayBookings = getBookingsForDate(dateStr);

      // Tampilkan max 3 booking
      dayBookings.slice(0, 3).forEach(function (booking) {
        var bookingDiv = document.createElement("div");
        var statusClass =
          booking.status === "disetujui" || booking.status === "dipinjam"
            ? "booking-item"
            : "booking-item pending";
        bookingDiv.className = statusClass;

        var timeStart = booking.waktu_mulai
          ? booking.waktu_mulai.substring(0, 5)
          : "";
        var roomName = booking.nama_ruangan || "Ruangan";
        var shortName =
          roomName.length > 12 ? roomName.substring(0, 12) + "..." : roomName;
        bookingDiv.textContent = timeStart + " " + shortName;
        bookingDiv.title =
          booking.nama_ruangan +
          " - " +
          (booking.keperluan || "Meeting") +
          " (" +
          booking.status +
          ")";

        bookingDiv.addEventListener("click", function (e) {
          e.stopPropagation();
          showBookingDetails(dateStr, dayBookings);
        });

        dayDiv.appendChild(bookingDiv);
      });

      // Counter booking
      if (dayBookings.length > 0) {
        var countDiv = document.createElement("div");
        countDiv.className = "booking-count";
        countDiv.textContent =
          dayBookings.length > 3
            ? "+" + (dayBookings.length - 3)
            : dayBookings.length;
        dayDiv.appendChild(countDiv);
      }

      // Klik hari untuk lihat detail
      (function (ds, db) {
        dayDiv.addEventListener("click", function () {
          if (db.length > 0) showBookingDetails(ds, db);
        });
      })(dateStr, dayBookings);

      calendarGrid.appendChild(dayDiv);
    }

    // Isi sisa sel agar grid penuh (42 sel = 6 minggu)
    var totalCells = calendarGrid.children.length - 7; // kurangi header
    var remaining = 42 - totalCells;
    for (var r = 0; r < remaining && totalCells < 35; r++) {
      var emptyEnd = document.createElement("div");
      emptyEnd.className = "calendar-day other-month";
      calendarGrid.appendChild(emptyEnd);
    }
  }

  // ─── HELPER ───────────────────────────────────────────────────────────────────
  function getBookingsForDate(date) {
    return bookingsData.filter(function (booking) {
      // hanya cocokkan tanggal
      if (booking.tanggal !== date) {
        return false;
      }

      return true;
    });
  }

  function formatIndonesianDate(dateStr) {
    var date = new Date(dateStr + "T00:00:00");
    var options = {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    };
    return date.toLocaleDateString("id-ID", options);
  }

  // ─── MODAL DETAIL BOOKING ─────────────────────────────────────────────────────
  function showBookingDetails(date, bookings) {
    var modalEl = document.getElementById("modalDetailBooking");
    var modalContent = document.getElementById("modalBookingContent");
    if (!modalEl || !modalContent) return;

    var content =
      '<div class="mb-3">' +
      '<h6><i class="bi bi-calendar3 me-2"></i>Tanggal: ' +
      formatIndonesianDate(date) +
      "</h6>" +
      '<p class="text-muted">Total ' +
      bookings.length +
      " booking pada hari ini</p>" +
      "</div>";

    if (bookings.length === 0) {
      content +=
        '<div class="alert alert-info">Tidak ada booking pada tanggal ini.</div>';
    } else {
      bookings.forEach(function (booking) {
        var statusClass = "status-" + booking.status;
        var timeStart = booking.waktu_mulai
          ? booking.waktu_mulai.substring(0, 5)
          : "";
        var timeEnd = booking.waktu_selesai
          ? booking.waktu_selesai.substring(0, 5)
          : "";

        content +=
          '<div class="booking-detail-item">' +
          '<div class="d-flex justify-content-between align-items-start mb-2">' +
          '<h6 class="mb-1">' +
          (booking.nama_ruangan || "Ruangan") +
          "</h6>" +
          '<span class="status-badge ' +
          statusClass +
          '">' +
          booking.status +
          "</span>" +
          "</div>" +
          '<div class="row">' +
          '<div class="col-sm-6"><small class="text-muted">Waktu:</small><br>' +
          "<strong>" +
          timeStart +
          " - " +
          timeEnd +
          " WIB</strong></div>" +
          '<div class="col-sm-6"><small class="text-muted">Keperluan:</small><br>' +
          "<span>" +
          (booking.keperluan || "Meeting") +
          "</span></div>" +
          "</div>";

        if (
          booking.nama_penanggung_jawab &&
          booking.nama_penanggung_jawab !== "User Lain"
        ) {
          content +=
            '<div class="mt-2"><small class="text-muted">Penanggung Jawab:</small><br>' +
            "<span>" +
            booking.nama_penanggung_jawab +
            (booking.unit_organisasi && booking.unit_organisasi !== "***"
              ? " - " + booking.unit_organisasi
              : "") +
            "</span></div>";
        }

        if (booking.jumlah_peserta) {
          content +=
            '<div class="mt-1"><small class="text-muted">Jumlah Peserta:</small>' +
            '<span class="badge bg-secondary ms-2">' +
            booking.jumlah_peserta +
            " orang</span></div>";
        }

        content += "</div>"; // .booking-detail-item
      });
    }

    modalContent.innerHTML = content;

    var modal = new bootstrap.Modal(modalEl);
    modal.show();
  }
})();
