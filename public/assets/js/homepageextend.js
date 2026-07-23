/**
 * homepageextend.js
 * Dipisah dari homepage.php (inline <script>)
 * Pastikan BASE_URL sudah didefinisikan sebelum file ini di-load, contoh di homepage.php:
 *
 *   <script>
 *     const BASE_URL = '<?= base_url() ?>';
 *   </script>
 *   <script src="<?= base_url('assets/js/homepageextend.js') ?>"></script>
 */

// ============================================================
// Bagian 1: Tooltip, hover image, filter kendaraan
// ============================================================
document.addEventListener("DOMContentLoaded", function () {
  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]'),
  );
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Image hover zoom effect
  const vehicleImages = document.querySelectorAll(".vehicle-image-wrapper img");
  vehicleImages.forEach((img) => {
    img.addEventListener("mouseover", () => {
      img.style.transform = "scale(1.05)";
    });

    img.addEventListener("mouseout", () => {
      img.style.transform = "scale(1)";
    });
  });

  // Filter functionality
  const searchInput = document.getElementById("searchKendaraan");
  if (searchInput) {
    searchInput.addEventListener("input", filterVehicles);
  }

  const filterKategori = document.getElementById("filterKategori");
  if (filterKategori) {
    filterKategori.addEventListener("change", filterVehicles);
  }

  const filterStatus = document.getElementById("filterStatus");
  if (filterStatus) {
    filterStatus.addEventListener("change", filterVehicles);
  }

  function filterVehicles() {
    const searchValue = searchInput ? searchInput.value.toLowerCase() : "";
    const kategoriValue = filterKategori
      ? filterKategori.value.toLowerCase()
      : "";
    const statusValue = filterStatus ? filterStatus.value.toLowerCase() : "";

    const vehicles = document.querySelectorAll(".vehicle-card");

    vehicles.forEach((vehicle) => {
      const vehicleText = vehicle.textContent.toLowerCase();
      const kategoriText = vehicle.querySelector(
        ".detail-content:nth-child(4) span",
      )
        ? vehicle
            .querySelector(".detail-content:nth-child(4) span")
            .textContent.toLowerCase()
        : "";
      const statusText = vehicle.querySelector(".badge")
        ? vehicle.querySelector(".badge").textContent.toLowerCase()
        : "";

      const matchesSearch =
        searchValue === "" || vehicleText.includes(searchValue);
      const matchesKategori =
        kategoriValue === "" || kategoriText.includes(kategoriValue);
      const matchesStatus =
        statusValue === "" || statusText.includes(statusValue);

      if (matchesSearch && matchesKategori && matchesStatus) {
        vehicle.style.display = "";
      } else {
        vehicle.style.display = "none";
      }
    });
  }
});

// ============================================================
// Bagian 2: Kamera, tab navigation, form pengembalian (jQuery)
// ============================================================
$(document).ready(function () {
  let stream;

  // Tombol navigasi tab
  $("#btn-next-tab").click(function () {
    $("#detail-kendaraan-tab").tab("show");
  });

  $("#btn-prev-tab").click(function () {
    $("#pihak-kesatu-tab").tab("show");
  });

  // Tombol untuk membuka kamera
  $("#btn-camera-capture").click(function () {
    startCamera();
  });

  // Tombol untuk mengambil foto
  $("#btn-take-photo").click(function () {
    takePhoto();
  });

  // Tombol untuk membatalkan kamera
  $("#btn-cancel-camera").click(function () {
    stopCamera();
  });

  // Tombol untuk mengambil ulang foto
  $("#btn-retake-photo").click(function () {
    $("#photo-preview").hide();
    startCamera();
  });

  // Fungsi untuk memulai kamera
  function startCamera() {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
      navigator.mediaDevices
        .getUserMedia({ video: true })
        .then(function (mediaStream) {
          stream = mediaStream;
          const video = document.getElementById("camera-feed");
          video.srcObject = mediaStream;
          video.play();
          $("#camera-container").show();
        })
        .catch(function (error) {
          console.error("Tidak dapat mengakses kamera:", error);
          alert(
            "Tidak dapat mengakses kamera. Pastikan kamera tersedia dan izin diberikan.",
          );
        });
    } else {
      alert("Browser Anda tidak mendukung akses kamera");
    }
  }

  // Fungsi untuk mengambil foto
  function takePhoto() {
    const video = document.getElementById("camera-feed");
    const canvas = document.getElementById("photo-canvas");
    const context = canvas.getContext("2d");

    // Set ukuran canvas sama dengan video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    // Gambar video ke canvas
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Tambahkan timestamp pada foto
    context.fillStyle = "#ffffff";
    context.fillRect(0, canvas.height - 30, canvas.width, 30);
    context.fillStyle = "#000000";
    context.font = "14px Arial";
    const timestamp = new Date().toLocaleString();
    context.fillText("Timestamp: " + timestamp, 10, canvas.height - 10);

    // Dapatkan data image
    const photoData = canvas.toDataURL("image/jpeg");

    // Simpan data foto ke input hidden
    $("#photo-data").val(photoData);

    // Tampilkan preview
    $("#captured-photo").attr("src", photoData);
    $("#photo-preview").show();

    // Sembunyikan kamera
    $("#camera-container").hide();

    // Stop kamera
    stopCamera();
  }

  // Fungsi untuk menghentikan kamera
  function stopCamera() {
    if (stream) {
      stream.getTracks().forEach((track) => {
        track.stop();
      });
      stream = null;
    }
    $("#camera-container").hide();
  }

  // Mengisi form pengembalian saat kendaraan dipilih
  $("#kendaraan_id_kembali").on("change", function () {
    const kendaraanId = $(this).val();
    if (kendaraanId) {
      $("#kendaraan_id_hidden").val(kendaraanId);
      $.ajax({
        url: "/AsetKendaraan/getPeminjamanInfo",
        type: "POST",
        data: { kendaraan_id: kendaraanId },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            // Isi data pihak kesatu
            $("#nama_penanggung_jawab").val(
              response.data.nama_penanggung_jawab,
            );
            $("#nip_nrp").val(response.data.nip_nrp);
            $("#pangkat_golongan").val(response.data.pangkat_golongan);
            $("#jabatan").val(response.data.jabatan);
            $("#unit_organisasi").val(response.data.unit_organisasi);
            $("#alamat_rumah").val(response.data.alamat_rumah || "");
            $("#no_ktp").val(response.data.no_ktp || "");
            $("#pengemudi").val(response.data.pengemudi);
            $("#no_hp").val(response.data.no_hp);
            $("#tanggal_pinjam").val(response.data.tanggal_pinjam);
            $("#tanggal_kembali").val(response.data.tanggal_kembali);

            // Isi data kendaraan
            $("#kategori_id").val(response.asset.kategori_id);
            $("#no_polisi_detail").val(response.asset.no_polisi);
            $("#kode_barang_detail").val(response.asset.kode_barang);
            $("#nup_detail").val(response.asset.nup || "-");
            $("#tahun_pembuatan").val(response.asset.tahun_pembuatan || "-");
            $("#merk_detail").val(response.asset.merk);
            $("#warna").val(response.asset.warna || "-");
            $("#nomor_mesin").val(response.asset.nomor_mesin || "-");
            $("#nomor_rangka").val(response.asset.no_rangka || "-");
          } else {
            alert("Gagal mendapatkan data peminjaman");
          }
        },
        error: function () {
          alert("Terjadi kesalahan saat mengambil data");
        },
      });
    }
  });

  // Load kendaraan yang dipinjam
  function loadKendaraanDipinjam() {
    $.ajax({
      url: "/AsetKendaraan/getKendaraanDipinjam",
      type: "GET",
      dataType: "json",
      success: function (response) {
        const select = $("#kendaraan_id_kembali");
        select.find("option:not(:first)").remove();

        if (response.length > 0) {
          $.each(response, function (i, item) {
            select.append(
              $("<option>", {
                value: item.id,
                text: item.merk + " - " + item.no_polisi,
              }),
            );
          });
        } else {
          select.append(
            $("<option>", {
              disabled: true,
              text: "Tidak ada kendaraan yang dipinjam",
            }),
          );
        }
      },
      error: function () {
        alert("Gagal memuat daftar kendaraan");
      },
    });
  }

  // Inisialisasi
  loadKendaraanDipinjam();

  // Form submit
  $("#formPengembalian").on("submit", function (e) {
    e.preventDefault();

    // Validasi foto
    if (!$("#photo-data").val()) {
      Swal.fire({
        icon: "error",
        title: "Perhatian",
        text: "Silahkan ambil foto kendaraan terlebih dahulu",
        confirmButtonColor: "#dc3545",
      });
      $("#pihak-kesatu-tab").tab("show");
      return false;
    }

    // Validasi nomor SIP
    if (!$("#nomor_sip").val()) {
      Swal.fire({
        icon: "error",
        title: "Perhatian",
        text: "Nomor SIP / Surat Penanggung Jawab harus diisi",
        confirmButtonColor: "#dc3545",
      });
      return false;
    }

    const formData = new FormData(this);

    // Tambahkan hidden field untuk berita_acara_pengembalian dari foto
    if (
      !formData.has("berita_acara_pengembalian") ||
      !formData.get("berita_acara_pengembalian").size
    ) {
      // Gunakan foto dari kamera sebagai berita acara jika tidak ada file yang diupload
      formData.append(
        "berita_acara_pengembalian",
        "auto_generated_" + new Date().getTime() + ".jpg",
      );
    }

    $.ajax({
      url: $(this).attr("action"),
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      beforeSend: function () {
        $('button[type="submit"]')
          .prop("disabled", true)
          .html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...',
          );
      },
      success: function (response) {
        $('button[type="submit"]')
          .prop("disabled", false)
          .html("Konfirmasi Pengembalian");

        if (response.success) {
          $("#modalPengembalian").modal("hide");

          // Tampilkan modal sukses dengan ikon ceklis
          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: "Pengajuan pengembalian berhasil dikirim",
            confirmButtonText: "OK",
            confirmButtonColor: "#198754",
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Gagal",
            text: response.error || "Gagal melakukan pengembalian",
            confirmButtonColor: "#dc3545",
          });
        }
      },
      error: function () {
        $('button[type="submit"]')
          .prop("disabled", false)
          .html("Konfirmasi Pengembalian");
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Terjadi kesalahan saat memproses pengembalian",
          confirmButtonColor: "#dc3545",
        });
      },
    });
  });
});

// ============================================================
// Bagian 3: Form peminjaman (page1/page2), load kendaraan tersedia,
// dan dynamic jabatan berdasarkan unit organisasi
// ============================================================
document.addEventListener("DOMContentLoaded", function () {
  const page1 = document.getElementById("page1");
  const page2 = document.getElementById("page2");
  const nextBtn = document.getElementById("nextBtn");
  const prevBtn = document.getElementById("prevBtn");
  const kendaraanSelect = document.getElementById("kendaraan_id_pinjam");

  // Navigasi antar halaman
  nextBtn.addEventListener("click", function () {
    // Validasi form halaman 1
    const requiredFields = page1.querySelectorAll("[required]");
    let valid = true;

    requiredFields.forEach((field) => {
      if (!field.value) {
        valid = false;
        field.classList.add("is-invalid");
      } else {
        field.classList.remove("is-invalid");
      }
    });

    if (!valid) {
      alert("Mohon lengkapi semua field yang diperlukan");
      return;
    }

    // Jika valid, load data kendaraan dan pindah ke halaman 2
    const selectedKendaraanId = kendaraanSelect.value;
    if (selectedKendaraanId) {
      loadKendaraanDetails(selectedKendaraanId);
      page1.style.display = "none";
      page2.style.display = "block";
    } else {
      alert("Silahkan pilih kendaraan terlebih dahulu");
    }
  });

  prevBtn.addEventListener("click", function () {
    page2.style.display = "none";
    page1.style.display = "block";
  });

  // Load detail kendaraan
  function loadKendaraanDetails(kendaraanId) {
    fetch(`${BASE_URL}AsetKendaraan/getAsetById/${kendaraanId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const kendaraan = data.data;

          // Konversi kategori_id ke jenis kendaraan
          let jenisKendaraan = "Tidak Diketahui";
          switch (kendaraan.kategori_id) {
            case "KDJ":
              jenisKendaraan = "Kendaraan Dinamis Jalan (KDJ)";
              break;
            case "KDO":
              jenisKendaraan = "Kendaraan Dinamis Off-road (KDO)";
              break;
            case "KDF":
              jenisKendaraan = "Kendaraan Dinamis Fasilitas (KDF)";
              break;
            default:
              jenisKendaraan = kendaraan.kategori_id || "Tidak Diketahui";
          }

          // Isi form dengan data kendaraan
          document.getElementById("detail_jenis_kendaraan").value =
            jenisKendaraan;
          document.getElementById("detail_nopol").value =
            kendaraan.no_polisi || "-";
          document.getElementById("detail_merk").value = kendaraan.merk || "-";
          document.getElementById("detail_warna").value =
            kendaraan.warna || "-";
          document.getElementById("detail_nomor_mesin").value =
            kendaraan.nomor_mesin || "-";
          document.getElementById("detail_no_rangka").value =
            kendaraan.no_rangka || "-";
          document.getElementById("detail_kode_barang").value =
            kendaraan.kode_barang || "-";
          document.getElementById("detail_nup").value = kendaraan.nup || "-";
          document.getElementById("detail_tahun_pembuatan").value =
            kendaraan.tahun_pembuatan || "-";
        } else {
          alert(
            "Gagal memuat detail kendaraan: " +
              (data.error || "Terjadi kesalahan"),
          );
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat memuat detail kendaraan");
      });
  }

  // Load daftar kendaraan tersedia saat form dibuka
  function loadAvailableKendaraan() {
    fetch(`${BASE_URL}AsetKendaraan/getKendaraan`)
      .then((response) => response.json())
      .then((data) => {
        kendaraanSelect.innerHTML =
          '<option value="" disabled selected>Pilih Kendaraan</option>';

        data.forEach((kendaraan) => {
          if (kendaraan.status_pinjam === "Tersedia") {
            const option = document.createElement("option");
            option.value = kendaraan.id;
            option.textContent = `${kendaraan.merk} - ${kendaraan.no_polisi}`;
            kendaraanSelect.appendChild(option);
          }
        });
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Gagal memuat daftar kendaraan");
      });
  }

  // Initialize
  loadAvailableKendaraan();

  // Dynamic jabatan options based on unit organisasi selection
  const unitOrganisasiSelect = document.getElementById("unit_organisasi");
  const jabatanSelect = document.getElementById("jabatan");

  unitOrganisasiSelect.addEventListener("change", function () {
    // Reset jabatan options
    jabatanSelect.innerHTML =
      '<option value="" disabled selected>Pilih</option>';

    // Add jabatan options based on selected unit
    const unit = this.value;
    let jabatanOptions = [];

    switch (unit) {
      case "Setjen":
        jabatanOptions = [
          "Sekretaris Jenderal",
          "Kepala Biro",
          "Kepala Bagian",
          "Kepala Subbagian",
          "Staff",
        ];
        break;
      case "Itjen":
        jabatanOptions = [
          "Inspektur Jenderal",
          "Sekretaris Inspektorat Jenderal",
          "Inspektur",
          "Staff",
        ];
        break;
      // Add more cases as needed
      default:
        jabatanOptions = [
          "Direktur Jenderal",
          "Sekretaris Direktorat Jenderal",
          "Direktur",
          "Staff",
        ];
    }

    jabatanOptions.forEach((jabatan) => {
      const option = document.createElement("option");
      option.value = jabatan;
      option.textContent = jabatan;
      jabatanSelect.appendChild(option);
    });
  });
});

/* Pengalih tampilan kartu/daftar — satu-satunya JS baru untuk sinkronisasi
   desain ini: toggle class .vehicle-grid--list saat tombol diklik.
   Tidak ada localStorage/persist. */
(function initViewToggle() {
  var grid = document.querySelector(".vehicle-grid");
  var buttons = document.querySelectorAll(
    ".view-toggle .btn-view[data-kv-view]",
  );
  if (!grid || !buttons.length) return;

  buttons.forEach(function (btn) {
    btn.addEventListener("click", function () {
      var mode = btn.dataset.kvView;
      buttons.forEach(function (other) {
        var on = other === btn;
        other.classList.toggle("active", on);
        other.setAttribute("aria-pressed", on ? "true" : "false");
      });
      grid.classList.toggle("vehicle-grid--list", mode === "list");
    });
  });
})();
