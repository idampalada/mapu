/* JavaScript untuk Modal TTE - Tambahkan ke file JS utama atau homepage.js */

$(document).ready(function () {
  // ================================================
  // TTE MODAL FUNCTIONS
  // ================================================

  /**
   * Toggle TTE Options berdasarkan checkbox
   */
  $("#enableTTE").change(function () {
    const isChecked = $(this).is(":checked");
    const $tteOptions = $("#tteOptions");
    const $btnText = $("#btnSubmitText");

    if (isChecked) {
      $tteOptions.slideDown(300);
      $btnText.text("Simpan Surat & TTE");
      $(this).next("label").removeClass("text-muted").addClass("text-success");
    } else {
      $tteOptions.slideUp(300);
      $btnText.text("Simpan Surat Saja");
      $(this).next("label").removeClass("text-success").addClass("text-muted");
    }
  });

  /**
   * Toggle Custom Position fields
   */
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
   * Form Submit Handler with TTE
   */
  $("#formEditSurat").on("submit", function (e) {
    e.preventDefault();

    // Validasi form
    if (!validateTTEForm()) {
      return false;
    }

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
        handleTTESuccess(response, $modal);
      },
      error: function (xhr, status, error) {
        handleTTEError(xhr, status, error);
      },
      complete: function () {
        hideLoadingState($submitBtn, originalBtnText);
        hideModalOverlay($modal);
      },
    });
  });

  /**
   * Validasi form TTE
   */
  function validateTTEForm() {
    let isValid = true;

    // Validasi nomor surat
    const nomorSurat = $("#nomor_surat").val().trim();
    if (!nomorSurat) {
      showFieldError("#nomor_surat", "Nomor surat harus diisi");
      isValid = false;
    }

    // Validasi nama kepala satuan kerja
    const namaKepala = $("#nama_kepala_satuan_kerja").val().trim();
    if (!namaKepala) {
      showFieldError(
        "#nama_kepala_satuan_kerja",
        "Nama kepala satuan kerja harus diisi"
      );
      isValid = false;
    }

    // Validasi NIP
    const nipKepala = $("#nip_kepala_satuan_kerja").val().trim();
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
   * Show field error
   */
  function showFieldError(fieldSelector, message) {
    const $field = $(fieldSelector);
    $field.addClass("is-invalid");

    // Remove existing error message
    $field.siblings(".invalid-feedback").remove();

    // Add error message
    $field.after(`<div class="invalid-feedback">${message}</div>`);

    // Focus on first error field
    $field.focus();
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
      const isEnable = response.tte_applied;
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
      handleTTEError(null, "error", response.error);
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
   * Function untuk membuka modal edit (dipanggil dari button edit)
   */
  window.openEditSuratModal = function (pinjamId) {
    // Clear form dan errors
    $("#formEditSurat")[0].reset();
    clearFieldErrors();

    // Set pinjam ID
    $("#pinjam_id_surat").val(pinjamId);

    // Set default tanggal
    $("#tanggal_surat").val(getCurrentDate());

    // Reset TTE options
    $("#enableTTE").prop("checked", true).trigger("change");
    $("#tte_position").val("visible_bottom").trigger("change");

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
  const toastClass = type === "success" ? "bg-success" : "bg-danger";
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
