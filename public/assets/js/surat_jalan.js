$(document).ready(function () {
  // Tetapkan tanggal minimum untuk input tanggal ke hari ini
  const today = new Date().toISOString().split("T")[0];
  $("#tanggal_mulai").attr("min", today);
  $("#tanggal_selesai").attr("min", today);

  // Set nilai default
  $("#tanggal_mulai").val(today);
  $("#tanggal_selesai").val(today);
  $("#jam_mulai").val("08:00");
  $("#jam_selesai").val("17:00");

  // Handler untuk submission form
  $("#formBuatOtomatis").submit(function (e) {
    e.preventDefault();
    submitSuratJalan();
  });

  $("#formUploadFile").submit(function (e) {
    e.preventDefault();
    submitUploadSuratJalan();
  });
});

// Fungsi untuk membuka modal dan mengisi data peminjam
function showSetujuModal(pinjamId) {
  // Reset form
  $("#formBuatOtomatis")[0].reset();
  $("#formUploadFile")[0].reset();

  // Set nilai pinjam_id di kedua form
  $("#pinjamId").val(pinjamId);
  $("#upload_pinjam_id").val(pinjamId);

  // Set default values untuk tanggal dan waktu
  const today = new Date().toISOString().split("T")[0];
  $("#tanggal_mulai").val(today);
  $("#tanggal_selesai").val(today);
  $("#jam_mulai").val("08:00");
  $("#jam_selesai").val("17:00");

  // Tampilkan loading spinner
  const loadingHtml =
    '<div id="formLoading" class="d-flex justify-content-center my-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
  $("#formBuatOtomatis").prepend(loadingHtml);

  // Ambil data peminjaman
  $.ajax({
    url: baseURL + "/AsetKendaraan/getPeminjamanData",
    type: "POST",
    data: { pinjam_id: pinjamId },
    dataType: "json",
    success: function (response) {
      // Hapus loading spinner
      $("#formLoading").remove();

      if (response.success) {
        const data = response.data;
        console.log("Data peminjaman diterima:", data);

        // Isi form dengan data
        $("#nama_penanggung_jawab").val(data.nama_penanggung_jawab || "");
        $("#nip_nrp").val(data.nip_nrp || "");
        $("#pangkat_golongan").val(data.pangkat_golongan || "");
        $("#jabatan").val(data.jabatan || "");
        $("#kode_barang").val(data.kode_barang || "");
        $("#no_polisi").val(data.no_polisi || "");

        // Isi urusan kedinasan dari data jika tersedia
        if (data.urusan_kedinasan) {
          $("#urusan_kedinasan").val(data.urusan_kedinasan);
        }

        // Aktifkan tab Buat Otomatis
        $("#buat-otomatis-tab").tab("show");
      } else {
        alert(response.error || "Gagal memuat data peminjaman");
      }
    },
    error: function (xhr, status, error) {
      // Hapus loading spinner
      $("#formLoading").remove();

      console.error("Ajax error:", xhr.responseText);
      alert("Terjadi kesalahan saat memuat data peminjaman");
    },
  });

  // Tampilkan modal
  $("#modalSetuju").modal("show");
}

// Submit form Buat Otomatis
function submitSuratJalan() {
  // Validasi form
  if (
    !$("#tanggal_mulai").val() ||
    !$("#jam_mulai").val() ||
    !$("#tanggal_selesai").val() ||
    !$("#jam_selesai").val() ||
    !$("#urusan_kedinasan").val()
  ) {
    alert("Semua field harus diisi");
    return;
  }

  // Kumpulkan data form
  const formData = new FormData(document.getElementById("formBuatOtomatis"));

  // Tampilkan loading
  $('#formBuatOtomatis button[type="submit"]')
    .prop("disabled", true)
    .html(
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...'
    );

  // Kirim request
  $.ajax({
    url: baseURL + "/SuratJalan/generate",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      // Reset button
      $('#formBuatOtomatis button[type="submit"]')
        .prop("disabled", false)
        .text("Setujui Peminjaman");

      if (response.success) {
        // Tutup modal
        $("#modalSetuju").modal("hide");

        // Tampilkan pesan sukses
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text:
            response.message ||
            "Surat Jalan berhasil dibuat dan peminjaman disetujui",
          showConfirmButton: false,
          timer: 2000,
        }).then(() => {
          // Buka PDF jika ada URL
          if (response.file_url) {
            window.open(response.file_url, "_blank");
          }

          // Refresh halaman
          location.reload();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Gagal",
          text: response.error || "Terjadi kesalahan saat membuat Surat Jalan",
          confirmButtonText: "OK",
        });
      }
    },
    error: function (xhr, status, error) {
      // Reset button
      $('#formBuatOtomatis button[type="submit"]')
        .prop("disabled", false)
        .text("Setujui Peminjaman");

      // Tampilkan pesan error
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Terjadi kesalahan saat memproses permintaan",
        confirmButtonText: "OK",
      });

      console.error("Ajax error:", xhr.responseText);
    },
  });
}

// Submit form Upload File
function submitUploadSuratJalan() {
  // Validasi form
  if (!$("#surat_jalan_admin").val()) {
    alert("File Surat Jalan harus diupload");
    return;
  }

  // Kumpulkan data form
  const formData = new FormData(document.getElementById("formUploadFile"));
  formData.append("status", "disetujui");

  // Tampilkan loading
  $('#formUploadFile button[type="submit"]')
    .prop("disabled", true)
    .html(
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...'
    );

  // Kirim request
  $.ajax({
    url: baseURL + "/AsetKendaraan/verifikasiPeminjaman",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      // Reset button
      $('#formUploadFile button[type="submit"]')
        .prop("disabled", false)
        .text("Setujui Peminjaman");

      if (response.success) {
        // Tutup modal
        $("#modalSetuju").modal("hide");

        // Tampilkan pesan sukses
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: response.message || "Peminjaman berhasil disetujui",
          showConfirmButton: false,
          timer: 2000,
        }).then(() => {
          // Refresh halaman
          location.reload();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Gagal",
          text: response.error || "Gagal menyetujui peminjaman",
          confirmButtonText: "OK",
        });
      }
    },
    error: function (xhr, status, error) {
      // Reset button
      $('#formUploadFile button[type="submit"]')
        .prop("disabled", false)
        .text("Setujui Peminjaman");

      // Tampilkan pesan error
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Terjadi kesalahan saat memproses permintaan",
        confirmButtonText: "OK",
      });

      console.error("Ajax error:", xhr.responseText);
    },
  });
}
