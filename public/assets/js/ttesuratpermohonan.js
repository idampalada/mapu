/* JavaScript untuk Modal TTE dengan Credential Input - FIXED VERSION */

$(document).ready(function () {
  // ================================================
  // TTE MODAL FUNCTIONS WITH CREDENTIAL INPUT
  // ================================================

  /**
   * Toggle password visibility untuk surat permohonan
   */
  $("#togglePassphrasePermohonan").click(function () {
    const passInput = $("#tte_passphrase_permohonan");
    const icon = $(this).find("i");

    if (passInput.attr("type") === "password") {
      passInput.attr("type", "text");
      icon.removeClass("bi-eye").addClass("bi-eye-slash");
    } else {
      passInput.attr("type", "password");
      icon.removeClass("bi-eye-slash").addClass("bi-eye");
    }
  });

  // FALLBACK untuk ID lama jika masih ada
  $("#togglePassphrase").click(function () {
    const passInput = $("#tte_passphrase");
    const icon = $(this).find("i");

    if (passInput.attr("type") === "password") {
      passInput.attr("type", "text");
      icon.removeClass("bi-eye").addClass("bi-eye-slash");
    } else {
      passInput.attr("type", "password");
      icon.removeClass("bi-eye-slash").addClass("bi-eye");
    }
  });

  /**
   * Validasi NIK (harus 16 digit) untuk surat permohonan
   */
  $("#tte_nik_permohonan").on("input", function () {
    let value = $(this).val().replace(/\D/g, ""); // Hanya angka

    if (value.length > 16) {
      value = value.substring(0, 16);
    }

    $(this).val(value);

    // Visual feedback
    if (value.length === 16) {
      $(this).removeClass("is-invalid").addClass("is-valid");
    } else {
      $(this).removeClass("is-valid").addClass("is-invalid");
    }
  });

  // FALLBACK untuk ID lama
  $("#tte_nik").on("input", function () {
    let value = $(this).val().replace(/\D/g, "");

    if (value.length > 16) {
      value = value.substring(0, 16);
    }

    $(this).val(value);

    if (value.length === 16) {
      $(this).removeClass("is-invalid").addClass("is-valid");
    } else {
      $(this).removeClass("is-valid").addClass("is-invalid");
    }
  });

  /**
   * Validasi URL QR Link untuk surat permohonan
   */
  $("#tte_qr_link_permohonan").on("blur", function () {
    const url = $(this).val();
    const urlPattern =
      /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;

    if (url && !urlPattern.test(url)) {
      $(this).addClass("is-invalid");
      $(this)
        .siblings(".form-text")
        .text("Format URL tidak valid")
        .addClass("text-danger");
    } else {
      $(this).removeClass("is-invalid");
      $(this)
        .siblings(".form-text")
        .text("Link yang akan ditampilkan pada QR code")
        .removeClass("text-danger");
    }
  });

  // FALLBACK untuk ID lama
  $("#tte_qr_link").on("blur", function () {
    const url = $(this).val();
    const urlPattern =
      /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;

    if (url && !urlPattern.test(url)) {
      $(this).addClass("is-invalid");
      $(this)
        .siblings(".form-text")
        .text("Format URL tidak valid")
        .addClass("text-danger");
    } else {
      $(this).removeClass("is-invalid");
      $(this)
        .siblings(".form-text")
        .text("Link yang akan ditampilkan pada QR code")
        .removeClass("text-danger");
    }
  });

  /**
   * Toggle TTE Options berdasarkan checkbox
   */
  $("#enableTTE").change(function () {
    const isChecked = $(this).is(":checked");
    const $tteOptions = $("#tteOptions");
    const $btnText = $("#btnSubmitText");

    if (isChecked) {
      $tteOptions.slideDown(300);
      if ($btnText.length > 0) {
        $btnText.text("Simpan Surat & TTE");
      }
      $(this).next("label").removeClass("text-muted").addClass("text-success");
    } else {
      $tteOptions.slideUp(300);
      if ($btnText.length > 0) {
        $btnText.text("Simpan Surat Saja");
      }
      $(this).next("label").removeClass("text-success").addClass("text-muted");
    }
  });

  /**
   * Toggle Custom Position fields untuk surat permohonan
   */
  $("#tte_position_permohonan").change(function () {
    const selectedValue = $(this).val();
    const $customPosition = $("#customPositionPermohonan");

    if (selectedValue === "invisible") {
      $customPosition.slideUp(300);
    } else {
      $customPosition.slideDown(300);
    }
  });
  // FALLBACK untuk ID lama
  $("#tte_position").change(function () {
    const selectedValue = $(this).val();
    const $customPosition = $("#customPosition");

    if (selectedValue === "visible_custom") {
      $customPosition.slideDown(300);
    } else {
      $customPosition.slideUp(300);
    }
  });

  /**
   * Enhanced form validation untuk TTE dengan credential - FIXED VERSION
   */
  function validateTTECredentials() {
    let isValid = true;

    // SMART FIELD DETECTION untuk NIK
    let nikField = $("#tte_nik_permohonan");
    if (nikField.length === 0) {
      nikField = $("#tte_nik"); // Fallback ke ID lama
    }

    // SMART FIELD DETECTION untuk Passphrase
    let passphraseField = $("#tte_passphrase_permohonan");
    if (passphraseField.length === 0) {
      passphraseField = $("#tte_passphrase"); // Fallback ke ID lama
    }

    // SMART FIELD DETECTION untuk QR Link
    let qrLinkField = $("#tte_qr_link_permohonan");
    if (qrLinkField.length === 0) {
      qrLinkField = $("#tte_qr_link"); // Fallback ke ID lama
    }

    // Validasi NIK
    if (nikField.length > 0) {
      const nik = nikField.val() ? nikField.val().trim() : "";
      if (!nik || nik.length !== 16) {
        showFieldError(nikField, "NIK harus 16 digit");
        isValid = false;
      }
    }

    // Validasi Passphrase
    if (passphraseField.length > 0) {
      const passphrase = passphraseField.val()
        ? passphraseField.val().trim()
        : "";
      if (!passphrase || passphrase.length < 6) {
        showFieldError(passphraseField, "Passphrase minimal 6 karakter");
        isValid = false;
      }
    }

    // Validasi QR Link
    if (qrLinkField.length > 0) {
      const qrLink = qrLinkField.val() ? qrLinkField.val().trim() : "";
      if (!qrLink || !qrLink.startsWith("http")) {
        showFieldError(qrLinkField, "Link QR harus valid (http/https)");
        isValid = false;
      }
    }

    console.log("TTE Validation Result:", isValid);
    return isValid;
  }

  /**
   * Validasi form umum
   */
  function validateTTEForm() {
    let isValid = true;

    // Validasi nomor surat
    const nomorSurat = $("#nomor_surat").val()
      ? $("#nomor_surat").val().trim()
      : "";
    if (!nomorSurat) {
      showFieldError("#nomor_surat", "Nomor surat harus diisi");
      isValid = false;
    }

    // Validasi nama kepala satuan kerja
    const namaKepala = $("#nama_kepala_satuan_kerja").val()
      ? $("#nama_kepala_satuan_kerja").val().trim()
      : "";
    if (!namaKepala) {
      showFieldError(
        "#nama_kepala_satuan_kerja",
        "Nama kepala satuan kerja harus diisi"
      );
      isValid = false;
    }

    // Validasi NIP
    const nipKepala = $("#nip_kepala_satuan_kerja").val()
      ? $("#nip_kepala_satuan_kerja").val().trim()
      : "";
    if (!nipKepala) {
      showFieldError(
        "#nip_kepala_satuan_kerja",
        "NIP kepala satuan kerja harus diisi"
      );
      isValid = false;
    }

    return isValid;
  }

  /**
   * Form Submit Handler with TTE Credentials - FIXED VERSION
   */
  $("#formEditSurat").on("submit", function (e) {
    e.preventDefault();

    console.log("Form submit started");

    // Clear previous errors
    clearFieldErrors();

    // Validasi form umum
    if (!validateTTEForm()) {
      console.log("Basic form validation failed");
      return false;
    }

    // Validasi TTE credentials jika diaktifkan
    const isTTEEnabled = $("#enableTTE").is(":checked");
    if (isTTEEnabled && !validateTTECredentials()) {
      console.log("TTE validation failed");
      return false;
    }

    console.log("All validations passed, submitting form");

    const formData = new FormData(this);
    const $submitBtn = $("#btnSubmitSurat");
    const $modal = $("#modalEditSurat");
    const originalBtnText = $submitBtn.html();

    // Show loading state
    showLoadingState($submitBtn);

    // Show modal overlay
    showModalOverlay($modal);

    $.ajax({
      url: $(this).attr("action"),
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      timeout: 90000, // 90 seconds for TTE process
      success: function (response) {
        console.log("AJAX Success:", response);
        handleTTESuccess(response, $modal);
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", xhr, status, error);
        handleTTEError(xhr, status, error);
      },
      complete: function () {
        hideLoadingState($submitBtn, originalBtnText);
        hideModalOverlay($modal);
      },
    });
  });

  /**
   * Show field error - FIXED VERSION
   */
  function showFieldError(fieldSelector, message) {
    let $field;

    if (typeof fieldSelector === "string") {
      $field = $(fieldSelector);
    } else {
      $field = fieldSelector; // Already a jQuery object
    }

    if ($field.length > 0) {
      $field.addClass("is-invalid");

      // Remove existing error message
      $field.siblings(".invalid-feedback").remove();

      // Add error message
      $field.after(`<div class="invalid-feedback">${message}</div>`);

      // Focus on first error field
      $field.focus();
    }
  }

  /**
   * Clear field errors
   */
  function clearFieldErrors() {
    $(".form-control").removeClass("is-invalid is-valid");
    $(".invalid-feedback").remove();
  }

  /**
   * Show loading state
   */
  function showLoadingState($btn) {
    $btn.prop("disabled", true).html(`
            <span class="spinner-border spinner-border-sm" role="status"></span>
            Processing TTE...
        `);
  }

  /**
   * Hide loading state
   */
  function hideLoadingState($btn, originalText) {
    $btn.prop("disabled", false).html(originalText);
  }

  /**
   * Show modal overlay
   */
  function showModalOverlay($modal) {
    const overlay = `
            <div class="modal-overlay">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">Memproses tanda tangan elektronik...</small>
                    </div>
                </div>
            </div>
        `;
    $modal.find(".modal-content").append(overlay);
  }

  /**
   * Hide modal overlay
   */
  function hideModalOverlay($modal) {
    $modal.find(".modal-overlay").remove();
  }

  /**
   * Handle TTE Success Response
   */
  function handleTTESuccess(response, $modal) {
    if (response.success) {
      const isEnable = response.tte_applied || false;
      const icon = isEnable ? "success" : "info";
      const title = isEnable ? "Berhasil!" : "Surat Disimpan!";

      let htmlMessage = `<p class="mb-2">${response.message}</p>`;

      if (isEnable) {
        htmlMessage += `
                    <div class="alert alert-success py-2 mt-2">
                        <small>
                            <i class="bi bi-shield-check"></i>
                            <strong>TTE Status:</strong> Dokumen telah ditandatangani elektronik
                            <br><strong>Penandatangan:</strong> ${
                              response.tte_signer || "Sistem"
                            }
                        </small>
                    </div>
                `;
      }

      // Save credentials untuk next time (tanpa passphrase)
      if (isEnable) {
        try {
          const nikValue =
            $("#tte_nik_permohonan").val() || $("#tte_nik").val() || "";
          const qrValue =
            $("#tte_qr_link_permohonan").val() || $("#tte_qr_link").val() || "";

          if (nikValue || qrValue) {
            const credentials = {
              nik: nikValue,
              qr_link: qrValue,
            };
            localStorage.setItem(
              "tte_last_credentials",
              JSON.stringify(credentials)
            );
          }
        } catch (e) {
          console.log("Could not save credentials:", e);
        }
      }

      Swal.fire({
        icon: icon,
        title: title,
        html: htmlMessage,
        showConfirmButton: true,
        confirmButtonText: "OK",
        timer: isEnable ? 5000 : 3000,
        customClass: {
          confirmButton: "btn btn-primary",
        },
      }).then((result) => {
        $modal.modal("hide");
        location.reload(); // Reload untuk update data
      });
    } else {
      handleTTEError(
        null,
        "error",
        response.error || response.message || "Unknown error"
      );
    }
  }

  /**
   * Handle TTE Error
   */
  function handleTTEError(xhr, status, error) {
    let errorMessage = "Terjadi kesalahan sistem.";

    if (xhr && xhr.responseJSON) {
      errorMessage =
        xhr.responseJSON.error || xhr.responseJSON.message || errorMessage;
    } else if (status === "timeout") {
      errorMessage = "Proses TTE timeout. Silakan coba lagi.";
    } else if (error) {
      errorMessage = error;
    }

    Swal.fire({
      icon: "error",
      title: "Gagal!",
      html: `
                <p>${errorMessage}</p>
                <small class="text-muted">
                    <i class="bi bi-info-circle"></i>
                    Jika masalah berlanjut, hubungi administrator sistem.
                </small>
            `,
      confirmButtonText: "OK",
      customClass: {
        confirmButton: "btn btn-primary",
      },
    });

    console.error("TTE Error:", { xhr, status, error });
  }

  /**
   * Function untuk membuka modal edit dengan auto-populate credentials
   */
  window.openEditSuratModal = function (pinjamId) {
    console.log("Opening edit surat modal for pinjam ID:", pinjamId);

    // Clear form dan errors
    $("#formEditSurat")[0].reset();
    clearFieldErrors();

    // Set pinjam ID
    $("#pinjam_id_surat").val(pinjamId);

    // Set default tanggal
    $("#tanggal_surat").val(getCurrentDate());

    // Reset TTE options
    $("#enableTTE").prop("checked", true).trigger("change");

    // Reset position dropdown dengan fallback
    const $positionSelect =
      $("#tte_position_permohonan").length > 0
        ? $("#tte_position_permohonan")
        : $("#tte_position");
    $positionSelect.val("visible_bottom").trigger("change");

    // Auto-populate dari data sebelumnya jika ada (localStorage)
    try {
      const savedCredentials = localStorage.getItem("tte_last_credentials");
      if (savedCredentials) {
        const data = JSON.parse(savedCredentials);
        if (confirm("Gunakan credential TTE terakhir yang digunakan?")) {
          // Try new IDs first, fallback to old IDs
          const $nikField =
            $("#tte_nik_permohonan").length > 0
              ? $("#tte_nik_permohonan")
              : $("#tte_nik");
          const $qrField =
            $("#tte_qr_link_permohonan").length > 0
              ? $("#tte_qr_link_permohonan")
              : $("#tte_qr_link");

          if (data.nik && $nikField.length > 0) {
            $nikField.val(data.nik).trigger("input");
          }
          if (data.qr_link && $qrField.length > 0) {
            $qrField.val(data.qr_link);
          }

          showToast(
            "info",
            "Credential TTE berhasil dimuat dari sesi terakhir"
          );
        }
      }
    } catch (e) {
      console.log("Could not load saved credentials:", e);
    }

    // Show modal
    $("#modalEditSurat").modal("show");
  };

  /**
   * Get current date in YYYY-MM-DD format
   */
  function getCurrentDate() {
    const today = new Date();
    return today.toISOString().split("T")[0];
  }

  /**
   * Auto-resize textarea if any
   */
  $("textarea").on("input", function () {
    this.style.height = "auto";
    this.style.height = this.scrollHeight + "px";
  });

  /**
   * Format NIP input (18 digits only)
   */
  $("#nip_kepala_satuan_kerja").on("input", function () {
    let value = $(this).val().replace(/\D/g, ""); // Remove non-digits
    if (value.length > 18) {
      value = value.substring(0, 18);
    }
    $(this).val(value);
  });

  /**
   * Clear errors on input
   */
  $(".form-control").on("input", function () {
    $(this).removeClass("is-invalid");
    $(this).siblings(".invalid-feedback").remove();
  });

  console.log("TTE Surat Permohonan JS loaded successfully");
});

// ================================================
// UTILITY FUNCTIONS
// ================================================

/**
 * Show TTE status badge in list
 */
function getTTEStatusBadge(isTTESigned) {
  if (isTTESigned) {
    return `
            <span class="badge tte-status-badge tte-signed">
                <i class="bi bi-shield-check"></i> TTE Signed
            </span>
        `;
  } else {
    return `
            <span class="badge tte-status-badge tte-not-signed">
                <i class="bi bi-file-text"></i> No TTE
            </span>
        `;
  }
}

/**
 * Format timestamp for display
 */
function formatTimestamp(timestamp) {
  if (!timestamp) return "-";

  const date = new Date(timestamp);
  return date.toLocaleString("id-ID", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

/**
 * Copy text to clipboard
 */
function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(
    function () {
      showToast("success", "Teks berhasil disalin!");
    },
    function (err) {
      showToast("error", "Gagal menyalin teks");
    }
  );
}

/**
 * Show toast notification
 */
function showToast(type, message) {
  const toastClass =
    type === "success"
      ? "bg-success"
      : type === "error"
      ? "bg-danger"
      : "bg-info";
  const toast = `
        <div class="toast ${toastClass} text-white position-fixed top-0 end-0 m-3" style="z-index: 1060">
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

  $("body").append(toast);
  const $toast = $(".toast:last");
  $toast.toast({ delay: 3000 }).toast("show");

  // Remove toast after hide
  $toast.on("hidden.bs.toast", function () {
    $(this).remove();
  });
}

/**
 * Validate NIK format (additional utility)
 */
function validateNIKFormat(nik) {
  return /^[0-9]{16}$/.test(nik);
}

/**
 * Mask NIK for display
 */
function maskNIK(nik) {
  if (!nik || nik.length < 4) return nik;
  return nik.substring(0, 4) + "*".repeat(nik.length - 4);
}

/**
 * Clear TTE credentials from localStorage
 */
function clearTTECredentials() {
  localStorage.removeItem("tte_last_credentials");
  showToast("info", "Credential TTE telah dibersihkan");
}
