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

    // ===== NOMOR SURAT GLOBAL RESOLVER =====
    let resolvedNomorSurat = "";

    // Function untuk mencari nomor surat dengan prioritas
    function findNomorSurat() {
      // Priority 1: ID selector
      const byId = $("#nomor_surat").val();
      if (byId && byId.trim() !== "") {
        console.log("📋 Found nomor_surat by ID:", byId);
        return byId.trim();
      }

      // Priority 2: Name selector (visible dan enabled)
      const visibleElements = $('input[name="nomor_surat"]:visible:enabled');
      for (let i = 0; i < visibleElements.length; i++) {
        const value = $(visibleElements[i]).val();
        if (value && value.trim() !== "") {
          console.log(`📋 Found nomor_surat by name selector [${i}]:`, value);
          return value.trim();
        }
      }

      // Priority 3: All elements with value (fallback)
      const allElements = $('input[name="nomor_surat"]');
      for (let i = 0; i < allElements.length; i++) {
        const value = $(allElements[i]).val();
        if (value && value.trim() !== "") {
          console.log(`📋 Found nomor_surat by fallback [${i}]:`, value);
          return value.trim();
        }
      }

      return "";
    }

    if (buatSuratPenanggungJawab) {
      // Reset validasi terlebih dahulu
      $(".surat-penanggung-field").removeClass("is-invalid");

      // ===== RESOLVE NOMOR SURAT DULU =====
      resolvedNomorSurat = findNomorSurat();
      console.log("🎯 RESOLVED NOMOR SURAT:", resolvedNomorSurat);

      // PERBAIKAN: Smart selector untuk field yang mungkin duplicate
      function getFieldValue(fieldIds, fieldName) {
        // Special handling untuk nomor_surat
        if (fieldName === "Nomor Surat") {
          return {
            value: resolvedNomorSurat,
            element: $("#nomor_surat"),
            found: resolvedNomorSurat !== "",
          };
        }

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

      // ===== NEW: VALIDASI TTE SURAT KDF JIKA AKTIF =====
      if ($("#enableTTEKdf").length > 0 && $("#enableTTEKdf").is(":checked")) {
        console.log("🔒 Validating TTE KDF credentials...");

        const tteValidationErrors = validateTTECredentialsKdf();
        if (tteValidationErrors.length > 0) {
          missingFields = missingFields.concat(tteValidationErrors);
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

      // ===== NEW: VALIDASI TTE SURAT JALAN JIKA AKTIF =====
      if (
        $("#enableTTESuratJalan").length > 0 &&
        $("#enableTTESuratJalan").is(":checked")
      ) {
        console.log("🔒 Validating TTE Surat Jalan credentials...");

        const tteValidationErrors = validateTTECredentialsSuratJalan();
        if (tteValidationErrors.length > 0) {
          missingFields = missingFields.concat(tteValidationErrors);
        }
      }

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

    // ===== FIX: GUNAKAN PINJAM_ID YANG BENAR UNTUK MASING-MASING TAB =====
    const pinjamIdKDF = $("#pinjamId").val(); // Tab 1 - KDF
    const pinjamIdSuratJalan = $("#pinjamId2").val(); // Tab 2 - Surat Jalan

    console.log("DEBUG: pinjamIdKDF =", pinjamIdKDF);
    console.log("DEBUG: pinjamIdSuratJalan =", pinjamIdSuratJalan);

    // Proses Surat Penanggung Jawab jika dipilih
    if (buatSuratPenanggungJawab) {
      // ===== FIX: GUNAKAN RESOLVED NOMOR SURAT =====
      console.log("🎯 USING RESOLVED NOMOR SURAT:", resolvedNomorSurat);

      const suratPenanggungData = new FormData();

      // ===== FIX: GUNAKAN PINJAM_ID DAN NOMOR_SURAT YANG BENAR =====
      suratPenanggungData.append("pinjam_id", pinjamIdKDF);
      suratPenanggungData.append("nomor_surat", resolvedNomorSurat); // ✅ GUNAKAN RESOLVED VALUE
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

      // ✅ SIMPLIFIED TTE DATA - Biar backend yang handle koordinat default
      if ($("#enableTTEKdf").length > 0 && $("#enableTTEKdf").is(":checked")) {
        console.log(
          "🔒 TTE KDF ENABLED - Adding TTE data to FormData (backend will handle coordinates)"
        );

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

        // Kirim koordinat dari form (untuk custom position) atau biar backend yang tentukan default
        suratPenanggungData.append("tte_x", $("#tte_x_kdf").val() || "");
        suratPenanggungData.append("tte_y", $("#tte_y_kdf").val() || "");
        suratPenanggungData.append(
          "tte_width",
          $("#tte_width_kdf").val() || ""
        );
        suratPenanggungData.append(
          "tte_height",
          $("#tte_height_kdf").val() || ""
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

        console.log(
          "✅ TTE KDF data added - Backend will use default coordinates for visible_bottom"
        );
      }

      // Debug FormData entries
      console.log("=== FINAL FORMDATA ENTRIES (KDF) ===");
      for (let [key, value] of suratPenanggungData.entries()) {
        if (key === "tte_passphrase") {
          console.log(key + ":", "HAS_VALUE");
        } else {
          console.log(key + ":", value);
        }
      }

      const suratPenanggungPromise = $.ajax({
        url: "/AsetKendaraan/generateSuratPenanggungJawabKdf", // ✅ CORRECT URL
        type: "POST",
        data: suratPenanggungData,
        processData: false,
        contentType: false,
      });

      promises.push(suratPenanggungPromise);
    }

    // ===== FIX: PROSES SURAT JALAN DENGAN ENDPOINT DAN DATA YANG BENAR =====
    if (buatSuratJalan) {
      const suratJalanData = new FormData();

      // ===== FIX: GUNAKAN PINJAM_ID YANG BENAR DAN FIELD YANG TEPAT =====
      suratJalanData.append("pinjam_id", pinjamIdSuratJalan);
      suratJalanData.append("tanggal_mulai", $("#tanggal_mulai").val());
      suratJalanData.append("tanggal_selesai", $("#tanggal_selesai").val());
      suratJalanData.append("jam_mulai", $("#jam_mulai").val());
      suratJalanData.append("jam_selesai", $("#jam_selesai").val());
      suratJalanData.append("urusan_kedinasan", $("#urusan_kedinasan").val());
      suratJalanData.append(
        "nama_pemegang_surat",
        $("#nama_pemegang_surat").val()
      );
      suratJalanData.append(
        "nip_pemegang_surat",
        $("#nip_pemegang_surat").val()
      );

      // TTE DATA SUPPORT UNTUK SURAT JALAN
      if (
        $("#enableTTESuratJalan").length > 0 &&
        $("#enableTTESuratJalan").is(":checked")
      ) {
        console.log("🔒 TTE SURAT JALAN ENABLED - Adding TTE data to FormData");

        suratJalanData.append("enable_tte_surat_jalan", "on");
        suratJalanData.append(
          "tte_nik_surat_jalan",
          $("#tte_nik_surat_jalan").val() || ""
        );
        suratJalanData.append(
          "tte_passphrase_surat_jalan",
          $("#tte_passphrase_surat_jalan").val() || ""
        );
        suratJalanData.append(
          "tte_qr_link_surat_jalan",
          $("#tte_qr_link_surat_jalan").val() || "https://s.pu.go.id"
        );
        suratJalanData.append(
          "tte_position_surat_jalan",
          $("#tte_position_surat_jalan").val() || "visible_bottom"
        );
        suratJalanData.append(
          "tte_x_surat_jalan",
          $("#tte_x_surat_jalan").val() || "450"
        );
        suratJalanData.append(
          "tte_y_surat_jalan",
          $("#tte_y_surat_jalan").val() || "250"
        );
        suratJalanData.append(
          "tte_width_surat_jalan",
          $("#tte_width_surat_jalan").val() || "150"
        );
        suratJalanData.append(
          "tte_height_surat_jalan",
          $("#tte_height_surat_jalan").val() || "75"
        );
        suratJalanData.append(
          "tte_reason_surat_jalan",
          $("#tte_reason_surat_jalan").val() ||
            "Surat Jalan telah ditandatangani secara elektronik"
        );
        suratJalanData.append(
          "tte_location_surat_jalan",
          $("#tte_location_surat_jalan").val() || "Jakarta"
        );

        console.log("✅ TTE Surat Jalan data added to FormData");
      }

      // Debug FormData entries
      console.log("=== FINAL FORMDATA ENTRIES (SURAT JALAN) ===");
      for (let [key, value] of suratJalanData.entries()) {
        if (key === "tte_passphrase_surat_jalan") {
          console.log(key + ":", "HAS_VALUE");
        } else {
          console.log(key + ":", value);
        }
      }

      const suratJalanPromise = $.ajax({
        url: "/AsetKendaraan/generateSuratJalan", // ✅ CORRECT URL FOR SURAT JALAN
        type: "POST",
        data: suratJalanData,
        processData: false,
        contentType: false,
      });

      promises.push(suratJalanPromise);
    }

    // Tunggu semua promises selesai dengan ENHANCED RESPONSE HANDLING
    Promise.all(promises)
      .then((responses) => {
        // Cek apakah semua responses success
        let allSuccess = true;
        let errorMessage = "";
        let successDocuments = []; // Track dokumen yang berhasil
        let tteStatuses = []; // NEW: Track TTE status

        for (let i = 0; i < responses.length; i++) {
          const response = responses[i];
          if (!response.success) {
            allSuccess = false;
            errorMessage += (response.error || "Terjadi kesalahan") + "\n";
          } else {
            // NEW: Collect TTE status info
            if (response.tte_applied) {
              const docType =
                i === 0 && buatSuratPenanggungJawab
                  ? "Surat KDF"
                  : "Surat Jalan";
              tteStatuses.push(
                `${docType}: TTE oleh ${response.tte_signer || "Sistem"}`
              );
            }
          }
        }

        // Tentukan jenis dokumen yang berhasil dibuat
        if (allSuccess) {
          if (buatSuratPenanggungJawab) {
            successDocuments.push("Surat Penanggung Jawab");
          }
          if (buatSuratJalan) {
            successDocuments.push("Surat Jalan");
          }
        }

        if (allSuccess) {
          // Buat pesan sukses yang spesifik dengan TTE info
          let successMessage = "";
          if (successDocuments.length === 1) {
            if (successDocuments[0] === "Surat Penanggung Jawab") {
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

          // NEW: Tambahkan info TTE jika ada
          if (tteStatuses.length > 0) {
            successMessage +=
              `<br><br><strong>Status TTE:</strong><br>` +
              tteStatuses.join("<br>");
          }

          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            html: successMessage, // Gunakan HTML untuk support TTE info
            confirmButtonText: "OK",
            confirmButtonColor: "#198754",
          }).then(() => {
            // NEW: Save TTE credentials untuk next time (tanpa passphrase)
            saveTTECredentialsIfNeeded();

            // Tutup modal dulu, baru reload
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
        console.error("Error dalam Promise.all:", error);
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

  // ===== TTE VALIDATION FUNCTIONS =====

  // Validasi TTE credentials untuk KDF
  function validateTTECredentialsKdf() {
    const errors = [];

    const nik = $("#tte_nik_kdf").val().trim();
    if (!nik || nik.length !== 16) {
      $("#tte_nik_kdf").addClass("is-invalid");
      errors.push("NIK KDF harus 16 digit");
    }

    const passphrase = $("#tte_passphrase_kdf").val().trim();
    if (!passphrase || passphrase.length < 6) {
      $("#tte_passphrase_kdf").addClass("is-invalid");
      errors.push("Passphrase KDF minimal 6 karakter");
    }

    const qrLink = $("#tte_qr_link_kdf").val().trim();
    if (!qrLink || !qrLink.startsWith("http")) {
      $("#tte_qr_link_kdf").addClass("is-invalid");
      errors.push("Link QR KDF harus valid (http/https)");
    }

    return errors;
  }

  // Validasi TTE credentials untuk Surat Jalan
  function validateTTECredentialsSuratJalan() {
    const errors = [];

    const nik = $("#tte_nik_surat_jalan").val().trim();
    if (!nik || nik.length !== 16) {
      $("#tte_nik_surat_jalan").addClass("is-invalid");
      errors.push("NIK Surat Jalan harus 16 digit");
    }

    const passphrase = $("#tte_passphrase_surat_jalan").val().trim();
    if (!passphrase || passphrase.length < 6) {
      $("#tte_passphrase_surat_jalan").addClass("is-invalid");
      errors.push("Passphrase Surat Jalan minimal 6 karakter");
    }

    const qrLink = $("#tte_qr_link_surat_jalan").val().trim();
    if (!qrLink || !qrLink.startsWith("http")) {
      $("#tte_qr_link_surat_jalan").addClass("is-invalid");
      errors.push("Link QR Surat Jalan harus valid (http/https)");
    }

    return errors;
  }

  // Save TTE credentials (tanpa passphrase)
  function saveTTECredentialsIfNeeded() {
    try {
      // Save KDF credentials
      if ($("#enableTTEKdf").is(":checked")) {
        const kdfCredentials = {
          nik: $("#tte_nik_kdf").val(),
          qr_link: $("#tte_qr_link_kdf").val(),
        };
        localStorage.setItem(
          "tte_last_credentials_kdf",
          JSON.stringify(kdfCredentials)
        );
      }

      // Save Surat Jalan credentials
      if ($("#enableTTESuratJalan").is(":checked")) {
        const suratJalanCredentials = {
          nik: $("#tte_nik_surat_jalan").val(),
          qr_link: $("#tte_qr_link_surat_jalan").val(),
        };
        localStorage.setItem(
          "tte_last_credentials_surat_jalan",
          JSON.stringify(suratJalanCredentials)
        );
      }
    } catch (e) {
      console.log("Failed to save TTE credentials:", e);
    }
  }

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

  // ===== TTE EVENT HANDLERS - UNTUK SEMUA DOKUMEN =====

  // Toggle TTE options untuk KDF
  $(document).on("change", "#enableTTEKdf", function () {
    const $tteOptions = $("#tteOptionsKdf");
    if ($(this).is(":checked")) {
      $tteOptions.slideDown(300);
      console.log("TTE KDF Options shown");
    } else {
      $tteOptions.slideUp(300);
      console.log("TTE KDF Options hidden");
    }
  });

  // NEW: Toggle TTE options untuk Surat Jalan
  $(document).on("change", "#enableTTESuratJalan", function () {
    const $tteOptions = $("#tteOptionsSuratJalan");
    if ($(this).is(":checked")) {
      $tteOptions.slideDown(300);
      console.log("TTE Surat Jalan Options shown");
    } else {
      $tteOptions.slideUp(300);
      console.log("TTE Surat Jalan Options hidden");
    }
  });

  // Toggle password untuk KDF
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

  // NEW: Toggle password untuk Surat Jalan
  $(document).on("click", "#togglePassphraseSuratJalan", function () {
    const $passInput = $("#tte_passphrase_surat_jalan");
    const $icon = $(this).find("i");

    if ($passInput.attr("type") === "password") {
      $passInput.attr("type", "text");
      $icon.removeClass("bi-eye").addClass("bi-eye-slash");
    } else {
      $passInput.attr("type", "password");
      $icon.removeClass("bi-eye-slash").addClass("bi-eye");
    }
  });

  // NIK Validation untuk KDF
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

  // NEW: NIK Validation untuk Surat Jalan
  $(document).on("input", "#tte_nik_surat_jalan", function () {
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

  // Position Toggle untuk KDF
  // Position Toggle untuk KDF - DIPERBAIKI
  $(document).on("change", "#tte_position_kdf", function () {
    const $customPosition = $("#customPositionKdf");
    if ($(this).val() === "invisible") {
      $customPosition.slideUp(300);
    } else {
      $customPosition.slideDown(300); // Tampil untuk visible_custom
    }
  });

  // NEW: Position Toggle untuk Surat Jalan
  // Position Toggle untuk Surat Jalan - DIPERBAIKI
  $(document).on("change", "#tte_position_surat_jalan", function () {
    const $customPosition = $("#customPositionSuratJalan");
    if ($(this).val() === "invisible") {
      $customPosition.slideUp(300);
    } else {
      $customPosition.slideDown(300); // Tampil untuk visible_custom
    }
  });

  // NEW: Load Last Credentials untuk KDF
  $(document).on("click", "#loadCredentialsKdf", function () {
    const savedCredentials = localStorage.getItem("tte_last_credentials_kdf");
    if (savedCredentials) {
      try {
        const credentials = JSON.parse(savedCredentials);

        if (credentials.nik) {
          $("#tte_nik_kdf").val(credentials.nik);
        }
        if (credentials.qr_link) {
          $("#tte_qr_link_kdf").val(credentials.qr_link);
        }

        Swal.fire({
          icon: "success",
          title: "Credential Dimuat!",
          text: "Credential KDF terakhir berhasil dimuat. Silakan masukkan passphrase.",
          timer: 2000,
          showConfirmButton: false,
        });

        $("#tte_passphrase_kdf").focus();
      } catch (e) {
        console.error("Error loading KDF credentials:", e);
      }
    } else {
      Swal.fire({
        icon: "info",
        title: "Tidak Ada Data",
        text: "Belum ada credential KDF tersimpan sebelumnya",
      });
    }
  });

  // NEW: Load Last Credentials untuk Surat Jalan
  $(document).on("click", "#loadCredentialsSuratJalan", function () {
    const savedCredentials = localStorage.getItem(
      "tte_last_credentials_surat_jalan"
    );
    if (savedCredentials) {
      try {
        const credentials = JSON.parse(savedCredentials);

        if (credentials.nik) {
          $("#tte_nik_surat_jalan").val(credentials.nik);
        }
        if (credentials.qr_link) {
          $("#tte_qr_link_surat_jalan").val(credentials.qr_link);
        }

        Swal.fire({
          icon: "success",
          title: "Credential Dimuat!",
          text: "Credential Surat Jalan terakhir berhasil dimuat. Silakan masukkan passphrase.",
          timer: 2000,
          showConfirmButton: false,
        });

        $("#tte_passphrase_surat_jalan").focus();
      } catch (e) {
        console.error("Error loading Surat Jalan credentials:", e);
      }
    } else {
      Swal.fire({
        icon: "info",
        title: "Tidak Ada Data",
        text: "Belum ada credential Surat Jalan tersimpan sebelumnya",
      });
    }
  });

  console.log(
    "TTE Multi-Document Event handlers loaded - Backend handles default coordinates"
  );
}); // AKHIR $(document).ready

// Function untuk menampilkan modal setuju dengan dual tab dan checkbox
function showSetujuModal(pinjamId) {
  // Reset form
  $(".surat-penanggung-field, .surat-jalan-field").removeClass("is-invalid");
  $("#buatSuratPenanggungJawab, #buatSuratJalan").prop("checked", true);

  // ===== FIX: SET PINJAM_ID YANG BENAR UNTUK KEDUA TAB =====
  $("#pinjamId, #pinjamId2").val(pinjamId);

  // Set tanggal hari ini sebagai default
  const today = new Date().toISOString().split("T")[0];
  $("#tanggal_surat").val(today);
  $("#tanggal_mulai").val(today);
  $("#tanggal_selesai").val(today);

  // Set jam default
  $("#jam_mulai").val("08:00");
  $("#jam_selesai").val("17:00");

  // ===== TRIGGER CHANGE EVENT UNTUK KOORDINAT FORM =====
  // Ini akan memastikan form koordinat tampil berdasarkan nilai default HTML
  $("#tte_position_kdf").trigger("change");
  $("#tte_position_surat_jalan").trigger("change");

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

        // ===== NEW: Set default TTE values untuk SEMUA DOKUMEN =====
        try {
          // Load KDF credentials
          const savedKdfCredentials = localStorage.getItem(
            "tte_last_credentials_kdf"
          );
          if (savedKdfCredentials) {
            const credentials = JSON.parse(savedKdfCredentials);
            if (credentials.nik) {
              $("#tte_nik_kdf").val(credentials.nik);
            }
            if (credentials.qr_link) {
              $("#tte_qr_link_kdf").val(credentials.qr_link);
            }
          }

          // Load Surat Jalan credentials
          const savedSuratJalanCredentials = localStorage.getItem(
            "tte_last_credentials_surat_jalan"
          );
          if (savedSuratJalanCredentials) {
            const credentials = JSON.parse(savedSuratJalanCredentials);
            if (credentials.nik) {
              $("#tte_nik_surat_jalan").val(credentials.nik);
            }
            if (credentials.qr_link) {
              $("#tte_qr_link_surat_jalan").val(credentials.qr_link);
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

$('button[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
  const targetTab = $(e.target).attr("data-bs-target");

  console.log("🧭 Tab aktif:", targetTab);

  if (targetTab === "#suratJalanTab") {
    initSuratJalanTab();
  }

  if (targetTab === "#suratPenanggungTab") {
    initKdfTab();
  }
});
