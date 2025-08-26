document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".formPinjamBarang").forEach(function (form) {
    form.addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      formData.append("<?= csrf_token() ?>", "<?= csrf_hash() ?>");

      const barangId = this.getAttribute("data-barang-id");

      try {
        const response = await fetch(URL_PINJAM_BARANG, {
          method: "POST",
          body: formData,
        });

        const result = await response.json();
        console.log("Result:", result);

        if (result.success) {
          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: result.message,
            confirmButtonColor: "#198754",
          }).then(() => {
            const modal = bootstrap.Modal.getInstance(
              document.getElementById(`modalPinjam${barangId}`)
            );
            if (modal) modal.hide();
            setTimeout(() => window.location.reload(), 1000);
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Gagal",
            text: result.error || "Terjadi kesalahan saat menyimpan data.",
            confirmButtonColor: "#dc3545",
          });
        }
      } catch (err) {
        console.error(err);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Terjadi kesalahan sistem. Silakan coba lagi.",
        });
      }
    });
  });
});
