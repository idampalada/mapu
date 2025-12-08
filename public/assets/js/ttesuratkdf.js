$(document).ready(function () {
  // Menangani submit utama
  $("#btnSubmitAll").on("click", function () {
    const buatSuratPenanggungJawab = $("#buatSuratPenanggungJawab").is(
      ":checked"
    );
    const buatSuratJalan = $("#buatSuratJalan").is(":checked");

    // Validasi minimal satu jenis surat dipilih
    if (!buatSuratPenanggungJawab && !buatSuratJalan) {
      Swal.fire({
        icon: "warning",
        title: "Perhatian",
        text: "Pilih minimal satu jenis surat yang akan dibuat",
        confirmButtonText: "OK",
        confirmButtonColor: "#198754",
      });
      return;
    }

    // Auto-fill field kepala satuan kerja jika kosong
    if (buatSuratPenanggungJawab) {
      if (!$("#nama_kepala_satuan_kerja").val()) {
        $("#nama_kepala_satuan_kerja").val("Kepala Satuan");
      }
      if (!$("#nip_kepala_satuan_kerja").val()) {
        $("#nip_kepala_satuan_kerja").val("123456");
      }
    }

    // Validasi field yang diperlukan untuk surat yang dipilih
    let isValid = true;

    // FIX UNTUK MASALAH DUPLICATE ELEMENT nomor_surat
    // Replace di homepage.js bagian validation

    if (buatSuratPenanggungJawab) {
      // Reset validasi terlebih dahulu
      $(".surat-penanggung-field").removeClass("is-invalid");

      // PERBAIKAN: Smart selector untuk field yang mungkin duplicate
      function getFieldValue(fieldIds, fieldName) {
        for (const fieldId of fieldIds) {
          // Coba selector ID dulu
          const elementById = $("#" + fieldId);
          if (elementById.length > 0) {
            const value = elementById.val();
            if (value && value.trim() !== "") {
              console.log(
                `✅ ${fieldName} found by ID #${fieldId}: "${value}"`
              );
              return { value, element: elementById, found: true };
            }
          }

          // Jika kosong, coba selector by name yang visible dan tidak disabled
          const elementsByName = $(`[name="${fieldId}"]:visible:enabled`);
          if (elementsByName.length > 0) {
            for (let i = 0; i < elementsByName.length; i++) {
              const elem = $(elementsByName[i]);
              const value = elem.val();
              if (value && value.trim() !== "") {
                console.log(
                  `✅ ${fieldName} found by name [name="${fieldId}"] index ${i}: "${value}"`
                );
                return { value, element: elem, found: true };
              }
            }
          }
        }

        console.log(`❌ ${fieldName} tidak ditemukan atau kosong`);
        return { value: "", element: null, found: false };
      }

      const fields = [
        { ids: ["nomor_surat"], name: "Nomor Surat" },
        { ids: ["tanggal_surat"], name: "Tanggal Surat" },
        { ids: ["tempat_surat"], name: "Tempat Surat" },
        {
          ids: ["nama_penanggung_jawab_kendaraan"],
          name: "Nama Penanggung Jawab Kendaraan",
        },
        {
          ids: ["nip_penanggung_jawab_kendaraan"],
          name: "NIP Penanggung Jawab Kendaraan",
        },
        { ids: ["nama_kepala_satuan_kerja"], name: "Nama Kepala Satuan Kerja" },
        { ids: ["nip_kepala_satuan_kerja"], name: "NIP Kepala Satuan Kerja" },
      ];

      let missingFields = [];

      console.log("=== VALIDASI SURAT PENANGGUNG JAWAB (SMART) ===");

      fields.forEach((field) => {
        const result = getFieldValue(field.ids, field.name);

        if (!result.found || !result.value) {
          missingFields.push(field.name);
          if (result.element) {
            result.element.addClass("is-invalid");
          }
        }
      });

      // KHUSUS DEBUG UNTUK NOMOR SURAT
      if (missingFields.includes("Nomor Surat")) {
        console.log("🔍 SPECIAL DEBUG UNTUK NOMOR SURAT:");
        console.log("All nomor_surat elements:");
        $('[name="nomor_surat"]').each(function (index, element) {
          console.log(`  Element ${index}:`, {
            id: element.id,
            value: $(element).val(),
            visible: $(element).is(":visible"),
            disabled: $(element).is(":disabled"),
            inModal: $(element).closest(".modal").length > 0,
            modalId: $(element).closest(".modal").attr("id"),
          });
        });

        // LAST RESORT: Ambil yang pertama yang ada value
        const allNomorSurat = $('[name="nomor_surat"]');
        for (let i = 0; i < allNomorSurat.length; i++) {
          const elem = $(allNomorSurat[i]);
          const val = elem.val();
          if (val && val.trim() !== "") {
            console.log(
              `🚀 FALLBACK: Using nomor_surat element ${i} with value: "${val}"`
            );
            missingFields = missingFields.filter(
              (field) => field !== "Nomor Surat"
            );
            break;
          }
        }
      }

      if (missingFields.length > 0) {
        console.log("❌ Missing fields:", missingFields);
        Swal.fire({
          icon: "error",
          title: "Error",
          html: `
        <p>Field yang bermasalah:</p>
        <ul style="text-align: left;">
          ${missingFields.map((field) => `<li>${field}</li>`).join("")}
        </ul>
        <p><small>Cek Console (F12) untuk detail debug</small></p>
      `,
          confirmButtonText: "OK",
          confirmButtonColor: "#dc3545",
        });
        isValid = false;
        return;
      }

      console.log("✅ VALIDATION SURAT PENANGGUNG JAWAB PASSED");
    }

    if (buatSuratJalan) {
      // Reset validasi terlebih dahulu
      $(".surat-jalan-field").removeClass("is-invalid");

      // Cek field-field penting
      const fields = [
        { id: "tanggal_mulai", name: "Tanggal Mulai" },
        { id: "jam_mulai", name: "Jam Mulai" },
        { id: "tanggal_selesai", name: "Tanggal Selesai" },
        { id: "jam_selesai", name: "Jam Selesai" },
        { id: "urusan_kedinasan", name: "Urusan Kedinasan" },
      ];

      let missingFields = [];
      fields.forEach((field) => {
        const value = $("#" + field.id).val();
        if (!value || value.trim() === "") {
          $("#" + field.id).addClass("is-invalid");
          missingFields.push(field.name);
        }
      });

      if (missingFields.length > 0) {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Lengkapi semua field untuk Surat Jalan",
          confirmButtonText: "OK",
          confirmButtonColor: "#dc3545",
        });
        isValid = false;
        return;
      }
    }

    if (!isValid) {
      return;
    }

    // Tampilkan loading
    Swal.fire({
      title: "Mohon Tunggu",
      text: "Sedang memproses data...",
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    // Buat array promises untuk menyimpan semua AJAX request
    const promises = [];
    const pinjamId = $("#pinjamId").val();

    // ========================================
    // 🔥 PATCH: TAMBAHKAN TTE SUPPORT DI SINI!
    // ========================================

    // Proses Surat Penanggung Jawab jika dipilih
    if (buatSuratPenanggungJawab) {
      const nomorSurat =
        $("#nomor_surat").val() ||
        $('input[name="nomor_surat"]:visible').val() ||
        "";
      console.log("NOMOR SURAT yang dikirim:", nomorSurat); // 👈 debug untuk pastikan terisi

      const suratPenanggungData = new FormData();

      // Basic data (EXISTING)
      suratPenanggungData.append("pinjam_id", pinjamId);
      suratPenanggungData.append("nomor_surat", nomorSurat);
      suratPenanggungData.append("tanggal_surat", $("#tanggal_surat").val());
      suratPenanggungData.append("tempat_surat", $("#tempat_surat").val());
      suratPenanggungData.append(
        "nama_penanggung_jawab_kendaraan",
        $("#nama_penanggung_jawab_kendaraan").val()
      );
      suratPenanggungData.append(
        "nip_penanggung_jawab_kendaraan",
        $("#nip_penanggung_jawab_kendaraan").val()
      );
      suratPenanggungData.append(
        "nama_kepala_satuan_kerja",
        $("#nama_kepala_satuan_kerja").val()
      );
      suratPenanggungData.append(
        "nip_kepala_satuan_kerja",
        $("#nip_kepala_satuan_kerja").val()
      );

      // 🆕 TTE DATA SUPPORT (PATCH TAMBAHAN)
      // Cek apakah TTE checkbox dicentang
      if ($("#enableTTEKdf").length > 0 && $("#enableTTEKdf").is(":checked")) {
        console.log("🔒 TTE ENABLED - Adding TTE data to FormData");

        suratPenanggungData.append("enable_tte", "on");
        suratPenanggungData.append("tte_nik", $("#tte_nik_kdf").val() || "");
        suratPenanggungData.append(
          "tte_passphrase",
          $("#tte_passphrase_kdf").val() || ""
        );
        suratPenanggungData.append(
          "tte_qr_link",
          $("#tte_qr_link_kdf").val() || "https://s.pu.go.id"
        );
        suratPenanggungData.append(
          "tte_position",
          $("#tte_position_kdf").val() || "visible_bottom"
        );
        suratPenanggungData.append(
          "tte_x",
          $('input[name="tte_x"]').val() || "250"
        );
        suratPenanggungData.append(
          "tte_y",
          $('input[name="tte_y"]').val() || "730"
        );
        suratPenanggungData.append(
          "tte_width",
          $('input[name="tte_width"]').val() || "150"
        );
        suratPenanggungData.append(
          "tte_height",
          $('input[name="tte_height"]').val() || "55"
        );
        suratPenanggungData.append(
          "tte_reason",
          $("#tte_reason_kdf").val() ||
            "Surat Penanggung Jawab KDF telah disetujui dan ditandatangani"
        );
        suratPenanggungData.append(
          "tte_location",
          $("#tte_location_kdf").val() || "Jakarta"
        );

        console.log("✅ TTE data added to FormData");
      } else {
        console.log(
          "📄 TTE NOT ENABLED or ELEMENT NOT FOUND - No TTE data added"
        );
      }

      // Debug FormData entries untuk verify TTE data
      console.log("=== FINAL FORMDATA ENTRIES ===");
      for (let [key, value] of suratPenanggungData.entries()) {
        if (key === "tte_passphrase") {
          console.log(key + ":", "HAS_VALUE");
        } else {
          console.log(key + ":", value);
        }
      }

      const suratPenanggungPromise = $.ajax({
        url: "/AsetKendaraan/generateSuratPenanggungJawabKdf",
        type: "POST",
        data: suratPenanggungData,
        processData: false,
        contentType: false,
      });

      promises.push(suratPenanggungPromise);
    }

    // Proses Surat Jalan jika dipilih (EXISTING - TIDAK BERUBAH)
    if (buatSuratJalan) {
      const suratJalanData = new FormData();
      suratJalanData.append("pinjam_id", pinjamId);
      suratJalanData.append("tanggal_mulai", $("#tanggal_mulai").val());
      suratJalanData.append("tanggal_selesai", $("#tanggal_selesai").val());
      suratJalanData.append("jam_mulai", $("#jam_mulai").val());
      suratJalanData.append("jam_selesai", $("#jam_selesai").val());
      suratJalanData.append("urusan_kedinasan", $("#urusan_kedinasan").val());

      // TAMBAHKAN DUA BARIS INI:
      suratJalanData.append(
        "nama_pemegang_surat",
        $("#nama_pemegang_surat").val()
      );
      suratJalanData.append(
        "nip_pemegang_surat",
        $("#nip_pemegang_surat").val()
      );

      const suratJalanPromise = $.ajax({
        url: "/AsetKendaraan/generateSuratJalan",
        type: "POST",
        data: suratJalanData,
        processData: false,
        contentType: false,
      });

      promises.push(suratJalanPromise);
    }

    // Tunggu semua promises selesai (EXISTING - TIDAK BERUBAH)
    Promise.all(promises)
      .then((responses) => {
        // Cek apakah semua responses success
        let allSuccess = true;
        let errorMessage = "";
        let successDocuments = []; // TAMBAHAN: Track dokumen yang berhasil

        for (const response of responses) {
          if (!response.success) {
            allSuccess = false;
            errorMessage += (response.error || "Terjadi kesalahan") + "\n";
          }
        }

        // TAMBAHAN: Tentukan jenis dokumen yang berhasil dibuat
        if (allSuccess) {
          if (buatSuratPenanggungJawab) {
            successDocuments.push("Penomoran Surat Penanggung Jawab");
          }
          if (buatSuratJalan) {
            successDocuments.push("Surat Jalan");
          }
        }

        if (allSuccess) {
          // PERBAIKAN: Buat pesan sukses yang spesifik
          let successMessage = "";
          if (successDocuments.length === 1) {
            if (successDocuments[0] === "Penomoran Surat Penanggung Jawab") {
              successMessage = "Penomoran Berhasil";
            } else {
              successMessage = successDocuments[0] + " berhasil dibuat";
            }
          } else if (successDocuments.length > 1) {
            successMessage =
              successDocuments.join(" dan ") + " berhasil dibuat";
          } else {
            successMessage = "Dokumen berhasil dibuat";
          }

          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: successMessage, // GUNAKAN PESAN SPESIFIK
            confirmButtonText: "OK",
            confirmButtonColor: "#198754",
          }).then(() => {
            // PERBAIKAN: Tutup modal dulu, baru reload
            $("#modalSetuju").modal("hide");
            setTimeout(() => {
              location.reload();
            }, 300);
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: "Terjadi kesalahan: " + errorMessage,
            confirmButtonText: "Tutup",
            confirmButtonColor: "#dc3545",
          });
        }
      })
      .catch((error) => {
        console.error("Error dalam Promise.all:", error); // TAMBAHAN: Console log untuk debugging
        Swal.fire({
          icon: "error",
          title: "Gagal!",
          text:
            "Terjadi kesalahan saat memproses data: " +
            (error.message || "Unknown error"),
          confirmButtonText: "Tutup",
          confirmButtonColor: "#dc3545",
        });
      });
  });

  // Toggle required attribute berdasarkan checkbox
  // DI AKHIR $(document).ready(function () { ... })

  // Toggle required attribute berdasarkan checkbox
  $("#buatSuratPenanggungJawab").on("change", function () {
    const isChecked = $(this).is(":checked");
    $(".surat-penanggung-field").prop("required", isChecked);
  });

  $("#buatSuratJalan").on("change", function () {
    const isChecked = $(this).is(":checked");
    $(".surat-jalan-field").prop("required", isChecked);
  });

  // TAMBAHKAN INI - EVENT HANDLER UNTUK FORM EDIT SURAT YANG HILANG!
  $("#formEditSurat").on("submit", function (e) {
    e.preventDefault(); // CRUCIAL: Prevent default form submission

    // Tampilkan loading
    Swal.fire({
      title: "Mohon Tunggu",
      text: "Sedang memproses penomoran surat...",
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    // Ambil data form
    const formData = new FormData(this);

    // Kirim AJAX request
    $.ajax({
      url: $(this).attr("action"),
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: "Penomoran Berhasil", // PESAN SPESIFIK YANG DIINGINKAN
            confirmButtonText: "OK",
            confirmButtonColor: "#198754",
          }).then(() => {
            // Tutup modal
            $("#modalEditSurat").modal("hide");

            // Reload halaman setelah modal tertutup
            setTimeout(() => {
              location.reload();
            }, 300);
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text:
              response.error || "Terjadi kesalahan saat memproses penomoran",
            confirmButtonText: "Tutup",
            confirmButtonColor: "#dc3545",
          });
        }
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: "Gagal!",
          text: "Terjadi kesalahan: " + error,
          confirmButtonText: "Tutup",
          confirmButtonColor: "#dc3545",
        });
      },
    });
  });

  // 🆕 TAMBAHKAN TTE EVENT HANDLERS DI AKHIR DOCUMENT READY
  // Toggle TTE options
  $(document).on("change", "#enableTTEKdf", function () {
    const $tteOptions = $("#tteOptionsKdf");
    if ($(this).is(":checked")) {
      $tteOptions.slideDown(300);
      console.log("TTE Options shown");
    } else {
      $tteOptions.slideUp(300);
      console.log("TTE Options hidden");
    }
  });

  // Toggle password
  $(document).on("click", "#togglePassphraseKdf", function () {
    const $passInput = $("#tte_passphrase_kdf");
    const $icon = $(this).find("i");

    if ($passInput.attr("type") === "password") {
      $passInput.attr("type", "text");
      $icon.removeClass("bi-eye").addClass("bi-eye-slash");
    } else {
      $passInput.attr("type", "password");
      $icon.removeClass("bi-eye-slash").addClass("bi-eye");
    }
  });

  // NIK Validation
  $(document).on("input", "#tte_nik_kdf", function () {
    let value = $(this).val().replace(/\D/g, "");
    if (value.length > 16) {
      value = value.substring(0, 16);
    }
    $(this).val(value);

    if (value.length === 16) {
      $(this).removeClass("is-invalid").addClass("is-valid");
    } else {
      $(this).removeClass("is-valid");
    }
  });

  // Position Toggle
  $(document).on("change", "#tte_position_kdf", function () {
    const $customPosition = $("#customPositionKdf");
    if ($(this).val() === "visible_custom") {
      $customPosition.slideDown(300);
    } else {
      $customPosition.slideUp(300);
    }
  });

  console.log("TTE Event handlers loaded");
}); // AKHIR $(document).ready

// Function untuk menampilkan modal setuju dengan dual tab dan checkbox
function showSetujuModal(pinjamId) {
  // Reset form
  $(".surat-penanggung-field, .surat-jalan-field").removeClass("is-invalid");
  $("#buatSuratPenanggungJawab, #buatSuratJalan").prop("checked", true);

  // Set ID peminjaman
  $("#pinjamId, #pinjamId2").val(pinjamId);

  // Set tanggal hari ini sebagai default
  const today = new Date().toISOString().split("T")[0];
  $("#tanggal_surat").val(today);
  $("#tanggal_mulai").val(today);
  $("#tanggal_selesai").val(today);

  // Set jam default
  $("#jam_mulai").val("08:00");
  $("#jam_selesai").val("17:00");

  // Ambil data peminjaman
  $.ajax({
    url: "/AsetKendaraan/getPeminjamanData",
    type: "POST",
    data: { pinjam_id: pinjamId },
    dataType: "json",
    success: function (data) {
      if (data.success) {
        // Isi data peminjam di tab 1
        $("#nama_penanggung_jawab").val(data.pinjam.nama_penanggung_jawab);
        $("#nip_nrp").val(data.pinjam.nip_nrp);
        $("#pangkat_golongan").val(data.pinjam.pangkat_golongan);
        $("#jabatan").val(data.pinjam.jabatan);
        $("#kode_barang").val(data.pinjam.kode_barang);
        $("#no_polisi").val(data.asset.no_polisi);

        // Isi data peminjam di tab 2
        $("#nama_penanggung_jawab2").val(data.pinjam.nama_penanggung_jawab);
        $("#nip_nrp2").val(data.pinjam.nip_nrp);
        $("#pangkat_golongan2").val(data.pinjam.pangkat_golongan);
        $("#jabatan2").val(data.pinjam.jabatan);
        $("#kode_barang2").val(data.pinjam.kode_barang);
        $("#no_polisi2").val(data.asset.no_polisi);

        // Isi field data penandatangan dengan data peminjam
        $("#nama_penanggung_jawab_kendaraan").val(
          data.pinjam.nama_penanggung_jawab_kendaraan ||
            data.pinjam.nama_penanggung_jawab
        );
        $("#nip_penanggung_jawab_kendaraan").val(
          data.pinjam.nip_penanggung_jawab_kendaraan || data.pinjam.nip_nrp
        );

        // TAMBAHKAN: Set nilai default untuk field kepala satuan kerja
        $("#nama_kepala_satuan_kerja").val(
          data.pinjam.nama_kepala_satuan_kerja || "Kepala Satuan"
        );
        $("#nip_kepala_satuan_kerja").val(
          data.pinjam.nip_kepala_satuan_kerja || "123456"
        );

        // Isi tanggal dan data lainnya
        $("#tanggal_mulai").val(
          data.pinjam.tanggal_pinjam
            ? data.pinjam.tanggal_pinjam.split("T")[0]
            : today
        );
        $("#tanggal_selesai").val(
          data.pinjam.tanggal_kembali
            ? data.pinjam.tanggal_kembali.split("T")[0]
            : today
        );
        $("#urusan_kedinasan").val(data.pinjam.urusan_kedinasan || "");

        // 🆕 TAMBAHKAN: Set default TTE values jika ada saved credentials
        try {
          const savedCredentials = localStorage.getItem(
            "tte_last_credentials_kdf"
          );
          if (savedCredentials) {
            const credentials = JSON.parse(savedCredentials);
            if (credentials.nik) {
              $("#tte_nik_kdf").val(credentials.nik);
            }
            if (credentials.qr_link) {
              $("#tte_qr_link_kdf").val(credentials.qr_link);
            }
          }
        } catch (e) {
          console.log("No saved TTE credentials found");
        }

        // Tampilkan modal
        const modal = new bootstrap.Modal(
          document.getElementById("modalSetuju")
        );
        modal.show();
      } else {
        Swal.fire({
          icon: "error",
          title: "Gagal!",
          text: data.error || "Gagal mengambil data peminjaman",
          confirmButtonText: "Tutup",
          confirmButtonColor: "#dc3545",
        });
      }
    },
    error: function () {
      Swal.fire({
        icon: "error",
        title: "Gagal!",
        text: "Terjadi kesalahan saat mengambil data peminjaman",
        confirmButtonText: "Tutup",
        confirmButtonColor: "#dc3545",
      });
    },
  });
}
