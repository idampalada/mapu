function updateUserRole(userId, newRole) {
  Swal.fire({
    title: "Konfirmasi",
    text: "Apakah Anda yakin ingin mengubah role pengguna ini?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ya, Ubah",
    cancelButtonText: "Batal",
    confirmButtonColor: "#435ebe",
    cancelButtonColor: "#dc3545",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Memproses...",
        text: "Mohon tunggu sebentar",
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      fetch("/admin/users/changerole", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
          user_id: userId,
          role: newRole,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: data.message,
              showConfirmButton: false,
              timer: 1500,
            }).then(() => {
              window.location.reload();
            });
          } else {
            throw new Error(data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: error.message || "Terjadi kesalahan saat mengubah role",
            confirmButtonText: "Tutup",
            confirmButtonColor: "#dc3545",
          }).then(() => {
            window.location.reload();
          });
        });
    }
  });
}

document.querySelectorAll(".role-select").forEach((select) => {
  select.addEventListener("change", function () {
    const userId = this.dataset.userId;
    const newRole = this.value;
    const originalRole = this.dataset.originalRole;

    Swal.fire({
      title: "Konfirmasi",
      text: "Apakah Anda yakin ingin mengubah role pengguna ini?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, Ubah",
      cancelButtonText: "Batal",
      confirmButtonColor: "#435ebe",
      cancelButtonColor: "#dc3545",
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: "Memproses...",
          text: "Mohon tunggu sebentar",
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading();
          },
        });

        fetch("/admin/users/changerole", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({
            user_id: userId,
            role: newRole,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              Swal.fire({
                icon: "success",
                title: "Berhasil!",
                text: data.message,
                showConfirmButton: false,
                timer: 1500,
              }).then(() => {
                window.location.reload();
              });
            } else {
              throw new Error(data.message);
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            Swal.fire({
              icon: "error",
              title: "Gagal!",
              text: error.message || "Terjadi kesalahan saat mengubah role",
              confirmButtonText: "Tutup",
              confirmButtonColor: "#dc3545",
            }).then(() => {
              this.value = originalRole;
            });
          });
      } else {
        this.value = originalRole;
      }
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  initializePeminjamanChart();
  initializeStatusChart();
  initializePengembalianChart();
  initializeVerifikasiHandlers();

  setInterval(refreshChartData, 300000);

  document.querySelectorAll(".action-card").forEach((card) => {
    card.addEventListener("click", function (e) {
      e.preventDefault();
      const action = this.dataset.action;

      switch (action) {
        case "verifikasi":
          const modalPilihVerifikasi = new bootstrap.Modal(
            document.getElementById("modalPilihVerifikasi")
          );
          modalPilihVerifikasi.show();
          break;
        case "tambah":
          const modalPilihTambah = new bootstrap.Modal(
            document.getElementById("modalPilihTambah")
          );
          modalPilihTambah.show();
          break;
        case "pemeliharaan":
          const modalTambahJadwal = new bootstrap.Modal(
            document.getElementById("modalTambahJadwal")
          );
          modalTambahJadwal.show();
          break;
        case "laporan":
          const modalLaporan = new bootstrap.Modal(
            document.getElementById("modalLaporan")
          );
          modalLaporan.show();

          fetch("/admin/pemeliharaan-rutin/get-kendaraan")
            .then((response) => response.json())
            .then((data) => {
              const select = document.getElementById("kendaraan_laporan");
              select.innerHTML =
                '<option value="" class="text-muted" disabled selected>Pilih Kendaraan</option>';
              data.forEach((kendaraan) => {
                select.innerHTML += `<option value="${kendaraan.id}">${kendaraan.merk} - ${kendaraan.no_polisi}</option>`;
              });
            })
            .catch((error) => console.error("Error:", error));
          break;
      }
    });
  });

  const triggerTabList = [].slice.call(
    document.querySelectorAll("#verificationTabs a")
  );
  triggerTabList.forEach(function (triggerEl) {
    new bootstrap.Tab(triggerEl);
  });

  const buktiInput = document.getElementById("bukti_foto");
  if (buktiInput) {
    buktiInput.addEventListener("change", function (e) {
      if (e.target.files && e.target.files[0]) {
        const file = e.target.files[0];

        if (file.size > 2 * 1024 * 1024) {
          Swal.fire({
            icon: "error",
            title: "Error!",
            text: "Ukuran file terlalu besar. Maksimal 2MB",
          });
          e.target.value = "";
          return;
        }

        const validTypes = ["image/jpeg", "image/png"];
        if (!validTypes.includes(file.type)) {
          Swal.fire({
            icon: "error",
            title: "Error!",
            text: "Format file tidak didukung. Gunakan JPG atau PNG",
          });
          e.target.value = "";
          return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById("previewImage").src = e.target.result;
          const previewModal = new bootstrap.Modal(
            document.getElementById("modalPreviewGambar")
          );
          previewModal.show();
        };
        reader.readAsDataURL(file);
      }
    });
  }

  const formLaporan = document.getElementById("formLaporan");
  if (formLaporan) {
    formLaporan.addEventListener("submit", function (e) {
      e.preventDefault();

      const submitBtn = this.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

      const formData = new FormData(this);

      if (typeof csrfToken !== "undefined") {
        formData.append(csrfToken.name, csrfToken.hash);
      }

      Swal.fire({
        title: "Memproses...",
        text: "Mohon tunggu sebentar",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      fetch(this.action, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: data.message,
              timer: 1500,
              showConfirmButton: false,
            }).then(() => {
              formLaporan.reset();
              const modal = bootstrap.Modal.getInstance(
                document.getElementById("modalLaporan")
              );
              modal.hide();

              window.location.reload();
            });
          } else {
            throw new Error(data.message || "Terjadi kesalahan");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Error!",
            text: error.message || "Terjadi kesalahan sistem",
          });
        })
        .finally(() => {
          submitBtn.disabled = false;
          submitBtn.innerHTML = "Simpan";
        });
    });
  }

  const btnSubmitLaporan = document.querySelector(
    '#formLaporan button[type="submit"]'
  );
  if (btnSubmitLaporan) {
    btnSubmitLaporan.addEventListener("click", function () {
      const form = document.getElementById("formLaporan");
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }
    });
  }

  const modalTambahRuangan = document.getElementById("modalTambahRuangan");
  if (modalTambahRuangan) {
    modalTambahRuangan.addEventListener("shown.bs.modal", function () {
      initializeTambahRuanganForm();
    });
  }

  const formEditRuangan = document.getElementById("formEditRuangan");
  if (formEditRuangan) {
    formEditRuangan.addEventListener("submit", handleEditRuanganSubmit);
  }
});

function showVerifikasiKendaraan() {
  const modalPilihVerifikasi = bootstrap.Modal.getInstance(
    document.getElementById("modalPilihVerifikasi")
  );
  modalPilihVerifikasi?.hide();

  const modalEl = document.getElementById("modalVerifikasi");
  console.log("modalVerifikasi:", modalEl); // <-- Debug log

  if (!modalEl) {
    alert("Elemen modalVerifikasi tidak ditemukan di DOM.");
    return;
  }

  const modalVerifikasi = new bootstrap.Modal(modalEl);
  modalVerifikasi.show();
}

function showVerifikasiKendaraan() {
  const modalPilihVerifikasiEl = document.getElementById(
    "modalPilihVerifikasi"
  );
  if (!modalPilihVerifikasiEl) {
    alert("Modal Pilih Verifikasi tidak ditemukan.");
    return;
  }

  // Pastikan instance-nya ada, atau buat jika belum ada
  let modalPilihVerifikasi = bootstrap.Modal.getInstance(
    modalPilihVerifikasiEl
  );
  if (!modalPilihVerifikasi) {
    modalPilihVerifikasi = new bootstrap.Modal(modalPilihVerifikasiEl);
  }
  modalPilihVerifikasi.hide();

  const modalVerifikasiEl = document.getElementById("modalVerifikasi");
  if (!modalVerifikasiEl) {
    alert("Modal Verifikasi tidak ditemukan.");
    return;
  }

  let modalVerifikasi = bootstrap.Modal.getInstance(modalVerifikasiEl);
  if (!modalVerifikasi) {
    modalVerifikasi = new bootstrap.Modal(modalVerifikasiEl);
  }
  modalVerifikasi.show();
}
function showVerifikasiBarang() {
  const modal = new bootstrap.Modal(
    document.getElementById("modalVerifikasiBarang")
  );
  modal.show();
}

function verifikasiPeminjamanBarang(id, status) {
  if (status === "ditolak") {
    showTolakModal("barang", id); // tipe 'barang' akan diproses di submitPenolakan
    return;
  }

  Swal.fire({
    title: "Konfirmasi",
    text: "Apakah Anda yakin ingin menyetujui peminjaman barang ini?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Ya, Setujui",
    cancelButtonText: "Batal",
    confirmButtonColor: "#198754",
    cancelButtonColor: "#dc3545",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Memproses...",
        text: "Mohon tunggu sebentar",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      const formData = new FormData();
      formData.append("pinjam_id", id);
      formData.append("status", "disetujui");

      fetch("/admin/User/Barang/verifikasiPeminjaman", {
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
              title: "Berhasil!",
              text: data.message || "Peminjaman barang telah disetujui.",
              showConfirmButton: false,
              timer: 1500,
            }).then(() => {
              location.reload();
            });
          } else {
            throw new Error(data.error || "Terjadi kesalahan saat verifikasi");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: error.message,
            confirmButtonText: "Tutup",
          });
        });
    }
  });
}

function initializeVerifikasiHandlers() {
  const formVerifikasi = document.getElementById("formVerifikasiRuangan");
  if (formVerifikasi) {
    formVerifikasi.addEventListener("submit", handleVerifikasiSubmit);
  }
}

function initializeCharts() {
  initializePeminjamanChart();
  initializePengembalianChart();
  initializeStatusChart();
  initializeRoomUsageChart();
}

function getGedungRole(lokasi) {
  const roleMap = {
    "Gedung Utama": "admin_gedungutama",
    "Pusat Data dan Teknologi Informasi": "admin_pusdatin",
    "Bina Marga": "admin_binamarga",
    "Cipta Karya": "admin_ciptakarya",
    "Sumber Daya Air": "admin_sda",
    "Gedung G": "admin_gedungg",
    Heritage: "admin_heritage",
    Auditorium: "admin_auditorium",
  };

  return roleMap[lokasi] || null;
}

function verifikasiPeminjamanRuangan(id, status) {
  if (status === "ditolak") {
    showTolakModal("peminjaman", id);
    return;
  }

  Swal.fire({
    title: "Konfirmasi",
    text: "Apakah Anda yakin ingin menyetujui peminjaman ini?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Ya, Setuju",
    cancelButtonText: "Batal",
    confirmButtonColor: "#198754",
    cancelButtonColor: "#dc3545",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Memproses...",
        text: "Mohon tunggu sebentar",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      const formData = new FormData();
      formData.append("pinjam_id", id);
      formData.append("status", "disetujui");

      fetch("/admin/User/Ruangan/verifikasiPeminjaman", {
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
              title: "Berhasil!",
              text: data.message,
              showConfirmButton: false,
              timer: 1500,
            }).then(() => {
              location.reload();
            });
          } else {
            throw new Error(data.error || "Terjadi kesalahan saat verifikasi");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: error.message,
            confirmButtonText: "Tutup",
          });
        });
    }
  });
}

function submitPenolakan() {
  const id = document.getElementById("tolakId").value;
  const tipe = document.getElementById("tolakTipe").value; // 'ruangan' atau 'kendaraan'
  const jenis = document.getElementById("jenisVerifikasi").value; // 'peminjaman' atau 'pengembalian'
  const alasan = document.getElementById("alasanPenolakan").value;
  const dokumenInput = document.getElementById("dokumen_tambahan");

  if (!alasan.trim()) {
    Swal.fire({
      icon: "error",
      title: "Error!",
      text: "Alasan penolakan harus diisi!",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  const formData = new FormData();
  if (jenis === "peminjaman") {
    formData.append("pinjam_id", id);
  } else {
    formData.append("kembali_id", id);
  }

  formData.append("status", "ditolak");
  formData.append("keterangan", alasan);
  if (dokumenInput && dokumenInput.files.length > 0) {
    formData.append("dokumen_tambahan", dokumenInput.files[0]);
  }

  Swal.fire({
    title: "Memproses...",
    allowOutsideClick: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  let endpoint = "";

  if (tipe === "ruangan") {
    endpoint =
      jenis === "peminjaman"
        ? "/admin/User/Ruangan/verifikasiPeminjaman"
        : "/admin/User/Ruangan/verifikasiPengembalianRuangan";
  } else if (tipe === "kendaraan") {
    endpoint =
      jenis === "peminjaman"
        ? "/AsetKendaraan/verifikasiPeminjaman"
        : "/AsetKendaraan/verifikasiPengembalianKendaraan";
  } else if (tipe === "barang") {
    endpoint =
      jenis === "peminjaman"
        ? "/admin/User/Barang/verifikasiPeminjaman"
        : "/admin/User/Barang/verifikasiPengembalianBarang"; // ini opsional, jika kamu punya pengembalian
  }

  fetch(endpoint, {
    method: "POST",
    body: formData,
    headers: {
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((res) => res.json())
    .then((data) => {
      Swal.fire({
        icon: data.success ? "success" : "error",
        title: data.success ? "Berhasil!" : "Gagal!",
        text: data.message || data.error,
      }).then(() => {
        if (data.success) location.reload();
      });
    })
    .catch((err) => {
      console.error(err);
      Swal.fire("Error", "Gagal memproses penolakan", "error");
    });
}

function initializeRoomUsageChart() {
  const ctx = document.getElementById("roomUsageChart");
  if (!ctx) return;

  const roomUsageChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
      ],
      datasets: [
        {
          label: "Penggunaan Ruangan",
          data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
          backgroundColor: "rgba(75, 192, 192, 0.2)",
          borderColor: "rgba(75, 192, 192, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
          },
        },
      },
      plugins: {
        title: {
          display: true,
          text: "Statistik Penggunaan Ruangan",
        },
      },
    },
  });

  fetchRoomUsageData(roomUsageChart);
}

async function fetchRoomUsageData(chart) {
  try {
    const response = await fetch("/admin/dashboard/getRoomUsageAPI");
    const data = await response.json();

    if (data.data) {
      chart.data.datasets[0].data = data.data;
      chart.update();
    }
  } catch (error) {
    console.error("Error fetching room usage data:", error);
  }
}

function initializeTambahRuanganForm() {
  const imageInput = document.getElementById("foto_ruangan");
  const formTambahRuangan = document.getElementById("formTambahRuangan");

  if (imageInput) {
    imageInput.addEventListener("change", handleRuanganImagePreview);
  }

  if (formTambahRuangan) {
    formTambahRuangan.addEventListener("submit", handleTambahRuanganSubmit);
  }
}

function handleRuanganImagePreview(e) {
  const files = e.target.files;
  const previewRow = document.getElementById("previewRuanganRow");

  if (files.length > 5) {
    Swal.fire({
      icon: "error",
      title: "Error!",
      text: "Maksimal 5 foto yang dapat diunggah",
    });
    e.target.value = "";
    return;
  }

  for (let file of files) {
    if (file.size > 2 * 1024 * 1024) {
      Swal.fire({
        icon: "error",
        title: "Error!",
        text: `File ${file.name} melebihi batas ukuran 2MB`,
      });
      e.target.value = "";
      return;
    }

    if (!["image/jpeg", "image/png"].includes(file.type)) {
      Swal.fire({
        icon: "error",
        title: "Error!",
        text: `File ${file.name} harus berformat JPG atau PNG`,
      });
      e.target.value = "";
      return;
    }
  }

  previewRow.innerHTML = "";
  Array.from(files).forEach((file, index) => {
    const reader = new FileReader();
    reader.onload = function (e) {
      const previewCol = document.createElement("div");
      previewCol.className = "col-md-4 mb-3 preview-wrapper";
      previewCol.innerHTML = `
                <div class="card">
                    <img src="${e.target.result}" 
                         class="card-img-top preview-image" 
                         style="height: 200px; object-fit: cover; cursor: pointer"
                         alt="${file.name}"
                         onclick="showImageDetail('${e.target.result}')">
                    <div class="card-body p-2">
                        <p class="card-text small text-muted mb-0">${file.name}</p>
                    </div>
                    <button type="button" 
                            class="btn btn-danger btn-sm delete-btn" 
                            onclick="removeRuanganPreview(${index})">×</button>
                </div>
            `;
      previewRow.appendChild(previewCol);
    };
    reader.readAsDataURL(file);
  });
}

function handleTambahRuanganSubmit(e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  // Validasi lokasi dipilih
  const lokasi = formData.get("lokasi");
  if (!lokasi) {
    Swal.fire({
      icon: "error",
      title: "Error!",
      text: "Lokasi gedung harus dipilih",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  Swal.fire({
    title: "Mohon Tunggu",
    text: "Sedang memproses data...",
    allowOutsideClick: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  fetch(form.action, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: data.message,
          confirmButtonText: "OK",
        }).then(() => {
          form.reset();
          document.getElementById("previewRuanganRow").innerHTML = "";
          const modal = bootstrap.Modal.getInstance(
            document.getElementById("modalTambahRuangan")
          );
          modal.hide();
          window.location.reload();
        });
      } else {
        // Check jika error terkait permission
        if (data.error && data.error.includes("tidak memiliki akses")) {
          Swal.fire({
            icon: "warning",
            title: "Akses Ditolak!",
            text: data.error,
            confirmButtonText: "Mengerti",
            confirmButtonColor: "#f39c12",
          });
        } else {
          throw new Error(
            data.error || "Terjadi kesalahan saat menyimpan data"
          );
        }
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Gagal!",
        text: error.message,
        confirmButtonText: "Tutup",
      });
    });
}

function removeRuanganPreview(index) {
  const input = document.getElementById("foto_ruangan");
  const dt = new DataTransfer();
  const files = Array.from(input.files);

  files.splice(index, 1);
  files.forEach((file) => dt.items.add(file));
  input.files = dt.files;

  handleRuanganImagePreview({ target: input });
}

function showTambahKendaraan() {
  const modalPilihTambah = bootstrap.Modal.getInstance(
    document.getElementById("modalPilihTambah")
  );
  modalPilihTambah.hide();

  const modalTambahAset = new bootstrap.Modal(
    document.getElementById("modalTambahAset")
  );
  modalTambahAset.show();
  initializeTambahAsetForm();
}

function showTambahRuangan() {
  const modalPilihTambah = bootstrap.Modal.getInstance(
    document.getElementById("modalPilihTambah")
  );
  modalPilihTambah.hide();

  const modalTambahRuangan = new bootstrap.Modal(
    document.getElementById("modalTambahRuangan")
  );
  modalTambahRuangan.show();
}

function initializeTambahAsetForm() {
  const imageInput = document.getElementById("gambar_mobil");
  const formTambahAset = document.getElementById("formTambahAset");

  if (imageInput) {
    imageInput.removeEventListener("change", handleImagePreview);
    imageInput.addEventListener("change", handleImagePreview);
  }

  if (formTambahAset) {
    formTambahAset.removeEventListener("submit", handleTambahAsetSubmit);
    formTambahAset.addEventListener("submit", handleTambahAsetSubmit);
  }
}

function handleImagePreview(e) {
  const newFiles = e.target.files;
  const previewRow = document.getElementById("previewRow");
  const input = document.getElementById("gambar_mobil");

  const dt = new DataTransfer();

  if (newFiles.length > 5) {
    Swal.fire({
      icon: "error",
      title: "Error!",
      text: "Maksimal 5 foto yang dapat diunggah",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  const existingFiles = Array.from(input.files || []);
  existingFiles.forEach((file) => dt.items.add(file));

  let isValid = true;
  Array.from(newFiles).forEach((file) => {
    if (file.size > 5 * 1024 * 1024) {
      Swal.fire({
        icon: "error",
        title: "Error!",
        text: `File ${file.name} melebihi batas ukuran 5MB`,
        confirmButtonColor: "#dc3545",
      });
      isValid = false;
      return;
    }

    if (!["image/jpeg", "image/png"].includes(file.type)) {
      Swal.fire({
        icon: "error",
        title: "Error!",
        text: `File ${file.name} harus berformat JPG atau PNG`,
        confirmButtonColor: "#dc3545",
      });
      isValid = false;
      return;
    }

    const isDuplicate = existingFiles.some(
      (existingFile) => existingFile.name === file.name
    );

    if (!isDuplicate) {
      dt.items.add(file);
    }
  });

  if (!isValid) return;

  input.files = dt.files;

  previewRow.innerHTML = "";

  Array.from(dt.files).forEach((file, index) => {
    const reader = new FileReader();
    reader.onload = function (e) {
      const previewCol = document.createElement("div");
      previewCol.className = "col-md-4 mb-3 preview-wrapper";
      previewCol.innerHTML = `
                <div class="card">
                    <img src="${e.target.result}" 
                         class="card-img-top preview-image" 
                         style="height: 200px; object-fit: cover; cursor: pointer"
                         alt="${file.name}"
                         onclick="showImageDetail('${e.target.result}')">
                    <div class="card-body p-2">
                        <p class="card-text small text-muted mb-0">${file.name}</p>
                    </div>
                    <button type="button" 
                            class="btn btn-danger btn-sm delete-btn" 
                            onclick="removePreview(${index})">×</button>
                </div>
            `;
      previewRow.appendChild(previewCol);
    };
    reader.readAsDataURL(file);
  });

  const fileLabel = input.nextElementSibling;
  const fileCount = dt.files.length;
  if (fileCount > 0) {
    const fileNames = Array.from(dt.files)
      .map((f) => f.name)
      .join(", ");
    fileLabel.textContent = `${fileCount} file${
      fileCount > 1 ? "s" : ""
    } terupload: ${fileNames}`;
  } else {
    fileLabel.textContent = "Tidak ada file dipilih";
  }
}

function updateImagePreviews(files) {
  const previewRow = document.getElementById("previewRow");
  previewRow.innerHTML = "";

  Array.from(files).forEach((file, index) => {
    const reader = new FileReader();
    reader.onload = function (e) {
      const previewCol = document.createElement("div");
      previewCol.className = "col-md-4 mb-3 preview-wrapper";
      previewCol.innerHTML = `
                <div class="card">
                    <img src="${e.target.result}" 
                         class="card-img-top preview-image" 
                         style="height: 200px; object-fit: cover; cursor: pointer"
                         alt="${file.name}"
                         onclick="showImageDetail('${e.target.result}')">
                    <div class="card-body p-2">
                        <p class="card-text small text-muted mb-0">${file.name}</p>
                    </div>
                    <button type="button" 
                            class="btn btn-danger btn-sm delete-btn" 
                            onclick="removePreview(${index})">×</button>
                </div>
            `;
      previewRow.appendChild(previewCol);
    };
    reader.readAsDataURL(file);
  });
}

function updateFileLabel(files) {
  const input = document.getElementById("gambar_mobil");
  const fileLabel = input.nextElementSibling;
  const fileCount = files.length;

  if (fileCount > 0) {
    const fileNames = Array.from(files)
      .map((f) => f.name)
      .join(", ");
    fileLabel.textContent = `${fileCount} file${
      fileCount > 1 ? "s" : ""
    } terupload: ${fileNames}`;
  } else {
    fileLabel.textContent = "Tidak ada file dipilih";
  }
}

function removePreview(index) {
  const input = document.getElementById("gambar_mobil");
  const dt = new DataTransfer();
  const files = Array.from(input.files);

  files.splice(index, 1);

  files.forEach((file) => dt.items.add(file));

  input.files = dt.files;

  const previewRow = document.getElementById("previewRow");
  previewRow.innerHTML = "";

  Array.from(dt.files).forEach((file, idx) => {
    const reader = new FileReader();
    reader.onload = function (e) {
      const previewCol = document.createElement("div");
      previewCol.className = "col-md-4 mb-3 preview-wrapper";
      previewCol.innerHTML = `
                <div class="card">
                    <img src="${e.target.result}" 
                         class="card-img-top preview-image" 
                         style="height: 200px; object-fit: cover; cursor: pointer"
                         alt="${file.name}"
                         onclick="showImageDetail('${e.target.result}')">
                    <div class="card-body p-2">
                        <p class="card-text small text-muted mb-0">${file.name}</p>
                    </div>
                    <button type="button" 
                            class="btn btn-danger btn-sm delete-btn" 
                            onclick="removePreview(${idx})">×</button>
                </div>
            `;
      previewRow.appendChild(previewCol);
    };
    reader.readAsDataURL(file);
  });

  const fileLabel = input.nextElementSibling;
  const fileCount = dt.files.length;
  if (fileCount > 0) {
    const fileNames = Array.from(dt.files)
      .map((f) => f.name)
      .join(", ");
    fileLabel.textContent = `${fileCount} file${
      fileCount > 1 ? "s" : ""
    } terupload: ${fileNames}`;
  } else {
    fileLabel.textContent = "Tidak ada file dipilih";
  }
}

function showImageDetail(src) {
  const detailImage = document.getElementById("detailImage");
  detailImage.src = src;
  const modal = new bootstrap.Modal(
    document.getElementById("imageDetailModal")
  );
  modal.show();
}

function handleTambahAsetSubmit(e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const files = form.querySelector("#gambar_mobil").files;
  if (files.length === 0) {
    Swal.fire({
      icon: "error",
      title: "Error!",
      text: "Minimal 1 foto harus diunggah",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  if (files.length > 5) {
    Swal.fire({
      icon: "error",
      title: "Error!",
      text: "Maksimal 5 foto yang dapat diunggah",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  Swal.fire({
    title: "Mohon Tunggu",
    text: "Sedang memproses data...",
    allowOutsideClick: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  fetch(form.action, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: data.message,
          confirmButtonText: "OK",
          confirmButtonColor: "#198754",
        }).then(() => {
          form.reset();
          document.getElementById("previewRow").innerHTML = "";
          const modal = bootstrap.Modal.getInstance(
            document.getElementById("modalTambahAset")
          );
          modal.hide();
          window.location.reload();
        });
      } else {
        throw new Error(data.error || "Terjadi kesalahan saat menyimpan data");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Gagal!",
        text: error.message,
        confirmButtonText: "Tutup",
        confirmButtonColor: "#dc3545",
      });
    });
}

function formatDate(date) {
  if (!date) return "-";
  const d = new Date(date);
  return d.toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "long",
    year: "numeric",
  });
}

function initializePeminjamanChart() {
  const ctx = document.getElementById("peminjamanChart");
  if (!ctx) return;

  const peminjamanChart = new Chart(ctx, {
    plugins: [ChartDataLabels],
    type: "line",
    data: {
      labels: [],
      datasets: [
        {
          label: "Peminjaman",
          data: [],
          borderColor: "#435ebe",
          backgroundColor: "rgba(67, 94, 190, 0.1)",
          borderWidth: 2,
          tension: 0.4,
          fill: true,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        datalabels: {
          color: "#000",
          anchor: "end",
          align: "top",
          font: {
            weight: "bold",
          },
          formatter: Math.round,
        },
        legend: {
          position: "top",
        },
        title: {
          display: true,
          text: "Statistik Peminjaman Bulanan",
          padding: {
            top: 10,
            bottom: 30,
          },
        },
        tooltip: {
          mode: "index",
          intersect: false,
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 10,
          },
          // max akan di-set nanti berdasarkan data
        },
      },
    },
  });

  fetch("/admin/dashboard/chart/peminjaman")
    .then((res) => res.json())
    .then((data) => {
      const nilai = data.map((item) => item.jumlah);
      const nilaiMaksimum = Math.max(...nilai);
      const yMax = nilaiMaksimum;

      // Update skala y secara dinamis
      peminjamanChart.options.scales.y.max = yMax;

      // Set label dan data
      peminjamanChart.data.labels = data.map((item) => item.bulan);
      peminjamanChart.data.datasets[0].data = nilai;

      peminjamanChart.update();
    })
    .catch((error) => console.error("Error fetching peminjaman data:", error));
}
let chartInstance = null;

let chartPeminjaman;

function loadChartData(mode, param1 = "", param2 = "") {
  const ctx = document.getElementById("chartPeminjamanUnified");
  if (!ctx) return;

  const url = {
    bulanan: "/admin/dashboard/chart/peminjaman-bulanan",
    mingguan: `/admin/dashboard/chart/peminjaman-mingguan?bulan=${param1}&minggu=${param2}`,
    harian: `/admin/dashboard/chart/peminjaman-harian?tanggal=${param1}`,
  }[mode];

  fetch(url)
    .then((res) => res.json())
    .then((data) => {
      const labels = data.map((d) => d.label);
      const jumlah = data.map((d) => d.jumlah);

      if (chartPeminjaman) chartPeminjaman.destroy();

      chartPeminjaman = new Chart(ctx, {
        type: mode === "harian" ? "bar" : "line",
        data: {
          labels: labels,
          datasets: [
            {
              label:
                mode === "bulanan"
                  ? "Peminjaman Bulanan"
                  : mode === "mingguan"
                  ? "Peminjaman Mingguan"
                  : "Peminjaman Harian",
              data: jumlah,
              backgroundColor:
                mode === "harian"
                  ? "rgba(0, 123, 255, 0.6)"
                  : "rgba(67, 94, 190, 0.1)",
              borderColor: mode === "harian" ? "#007bff" : "#435ebe",
              fill: true,
              tension: 0.4,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: "top" },
            title: {
              display: true,
              text:
                mode === "bulanan"
                  ? "Statistik Peminjaman Bulanan"
                  : mode === "mingguan"
                  ? "Statistik Peminjaman Mingguan"
                  : "Statistik Peminjaman Harian",
              padding: { top: 10, bottom: 20 },
            },
            datalabels: {
              anchor: "end",
              align: "top",
              color: "#000",
              font: {
                weight: "bold",
                size: 12,
              },
              formatter: (value) => value,
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { stepSize: 1 },
            },
          },
        },
        plugins: [ChartDataLabels],
      });
    });
}

document.addEventListener("DOMContentLoaded", function () {
  const modeSelector = document.getElementById("modeChartSelector");
  const bulanSelect = document.getElementById("filterBulan");
  const mingguSelect = document.getElementById("filterMinggu");
  const tanggalInput = document.getElementById("filterTanggal");

  function updateInputs() {
    const mode = modeSelector.value;
    document.getElementById("grupFilterBulan").style.display =
      mode === "mingguan" ? "block" : "none";
    document.getElementById("grupFilterMinggu").style.display =
      mode === "mingguan" ? "block" : "none";
    document.getElementById("grupFilterTanggal").style.display =
      mode === "harian" ? "block" : "none";
  }

  function refreshChart() {
    const mode = modeSelector.value;
    if (mode === "bulanan") {
      loadChartData("bulanan");
    } else if (mode === "mingguan") {
      loadChartData("mingguan", bulanSelect.value, mingguSelect.value);
    } else {
      loadChartData("harian", tanggalInput.value);
    }
  }

  updateInputs();
  refreshChart();

  modeSelector.addEventListener("change", () => {
    updateInputs();
    refreshChart();
  });

  bulanSelect.addEventListener("change", refreshChart);
  mingguSelect.addEventListener("change", refreshChart);
  tanggalInput.addEventListener("change", refreshChart);
});
let chartPengembalian;

function loadPengembalianChartData(mode, param1 = "", param2 = "") {
  const ctx = document.getElementById("chartPengembalianUnified");
  if (!ctx) return;

  const url = {
    bulanan: "/admin/dashboard/chart/pengembalian-bulanan",
    mingguan: `/admin/dashboard/chart/pengembalian-mingguan?bulan=${param1}&minggu=${param2}`,
    harian: `/admin/dashboard/chart/pengembalian-harian?tanggal=${param1}`,
  }[mode];

  fetch(url)
    .then((res) => res.json())
    .then((data) => {
      const labels = data.map((d) => d.label);
      const jumlah = data.map((d) => d.jumlah);

      if (chartPengembalian) chartPengembalian.destroy();

      chartPengembalian = new Chart(ctx, {
        type: mode === "harian" ? "bar" : "line",
        data: {
          labels: labels,
          datasets: [
            {
              label:
                mode === "bulanan"
                  ? "Pengembalian Bulanan"
                  : mode === "mingguan"
                  ? "Pengembalian Mingguan"
                  : "Pengembalian Harian",
              data: jumlah,
              backgroundColor:
                mode === "harian"
                  ? "rgba(40, 167, 69, 0.6)"
                  : "rgba(32, 201, 151, 0.1)",
              borderColor: mode === "harian" ? "#28a745" : "#20c997",
              fill: true,
              tension: 0.4,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: "top" },
            title: {
              display: true,
              text:
                mode === "bulanan"
                  ? "Statistik Pengembalian Bulanan"
                  : mode === "mingguan"
                  ? "Statistik Pengembalian Mingguan"
                  : "Statistik Pengembalian Harian",
              padding: { top: 10, bottom: 20 },
            },
            datalabels: {
              anchor: "end",
              align: "top",
              color: "#000",
              font: {
                weight: "bold",
                size: 12,
              },
              formatter: (value) => value,
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { stepSize: 1 },
            },
          },
        },
        plugins: [ChartDataLabels],
      });
    });
}
document.addEventListener("DOMContentLoaded", function () {
  // ...
  const modeSelectorPengembalian = document.getElementById(
    "modeChartSelectorPengembalian"
  );
  const bulanSelectPengembalian = document.getElementById(
    "filterBulanPengembalian"
  );
  const mingguSelectPengembalian = document.getElementById(
    "filterMingguPengembalian"
  );
  const tanggalInputPengembalian = document.getElementById(
    "filterTanggalPengembalian"
  );

  function updateInputsPengembalian() {
    const mode = modeSelectorPengembalian.value;
    document.getElementById("grupFilterBulanPengembalian").style.display =
      mode === "mingguan" ? "block" : "none";
    document.getElementById("grupFilterMingguPengembalian").style.display =
      mode === "mingguan" ? "block" : "none";
    document.getElementById("grupFilterTanggalPengembalian").style.display =
      mode === "harian" ? "block" : "none";
  }

  function refreshChartPengembalian() {
    const mode = modeSelectorPengembalian.value;
    if (mode === "bulanan") {
      loadPengembalianChartData("bulanan");
    } else if (mode === "mingguan") {
      loadPengembalianChartData(
        "mingguan",
        bulanSelectPengembalian.value,
        mingguSelectPengembalian.value
      );
    } else {
      loadPengembalianChartData("harian", tanggalInputPengembalian.value);
    }
  }

  if (modeSelectorPengembalian) {
    updateInputsPengembalian();
    refreshChartPengembalian();

    modeSelectorPengembalian.addEventListener("change", () => {
      updateInputsPengembalian();
      refreshChartPengembalian();
    });

    bulanSelectPengembalian.addEventListener(
      "change",
      refreshChartPengembalian
    );
    mingguSelectPengembalian.addEventListener(
      "change",
      refreshChartPengembalian
    );
    tanggalInputPengembalian.addEventListener(
      "change",
      refreshChartPengembalian
    );
  }
});

function initializeStatusChart() {
  const ctx = document.getElementById("statusChart");
  if (!ctx) return;

  const statusChart = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: ["Tersedia", "Dipinjam", "Maintenance"],
      datasets: [
        {
          data: [12, 8, 4],
          backgroundColor: ["#198754", "#435ebe", "#ffc107"],
          borderWidth: 0,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            padding: 20,
          },
        },
        title: {
          display: true,
          text: "Status Kendaraan",
          padding: {
            top: 10,
            bottom: 30,
          },
        },
      },
      cutout: "65%",
    },
  });
  fetchStatusData(statusChart);
}

function initializePengembalianChart() {
  const canvas = document.getElementById("pengembalianChart");
  if (!canvas || !canvas.getContext) return;

  const ctx = canvas.getContext("2d");

  const pengembalianChart = new Chart(ctx, {
    type: "line",
    data: {
      labels: [],
      datasets: [
        {
          label: "Pengembalian",
          data: [],
          borderColor: "#20c997",
          backgroundColor: "rgba(32, 201, 151, 0.1)",
          borderWidth: 2,
          tension: 0.4,
          fill: true,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "top",
        },
        title: {
          display: true,
          text: "Statistik Pengembalian Bulanan",
          padding: {
            top: 10,
            bottom: 30,
          },
        },
        tooltip: {
          mode: "index",
          intersect: false,
        },
        datalabels: {
          anchor: "end",
          align: "top",
          font: {
            weight: "bold",
          },
          formatter: Math.round,
          color: "#000",
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 200,
          },
        },
      },
    },
    plugins: [ChartDataLabels],
  });

  fetch("/admin/dashboard/chart/pengembalian")
    .then((res) => {
      if (!res.ok) throw new Error("Gagal fetch data pengembalian");
      return res.json();
    })
    .then((data) => {
      if (!Array.isArray(data) || data.length === 0) {
        console.warn("Data pengembalian kosong");
        return;
      }

      pengembalianChart.data.labels = data.map((item) => item.bulan);
      pengembalianChart.data.datasets[0].data = data.map((item) => item.jumlah);
      pengembalianChart.update();
    })
    .catch((error) => {
      console.error("Error fetching pengembalian data:", error);
    });
}

async function fetchPeminjamanData(chart) {
  try {
    const response = await fetch("/admin/dashboard/getStatistikAPI");
    const data = await response.json();

    if (data.data) {
      chart.data.datasets[0].data = data.data;
      chart.update();
    }
  } catch (error) {
    console.error("Error fetching peminjaman data:", error);
  }
}

async function fetchStatusData(chart) {
  try {
    const response = await fetch("/admin/dashboard/getStatusKendaraanAPI");
    const data = await response.json();

    if (data.data) {
      const statusData = [
        data.data.find((item) => item.status_pinjam === "Tersedia")?.total || 0,
        data.data.find((item) => item.status_pinjam === "Dipinjam")?.total || 0,
        data.data.find((item) => item.kondisi !== "Baik")?.total || 0,
      ];

      chart.data.datasets[0].data = statusData;
      chart.update();
    }
  } catch (error) {
    console.error("Error fetching status data:", error);
  }
}

async function fetchPengembalianData(chart) {
  try {
    const response = await fetch("/admin/dashboard/getPengembalianAPI");
    const data = await response.json();

    if (data.data) {
      chart.data.datasets[0].data = data.data;
      chart.update();
    }
  } catch (error) {
    console.error("Error fetching pengembalian data:", error);
  }
}

function refreshChartData() {
  Chart.instances.forEach((chart) => {
    switch (chart.canvas.id) {
      case "peminjamanChart":
        fetchPeminjamanData(chart);
        break;
      case "pengembalianChart":
        fetchPengembalianData(chart);
        break;
      case "statusChart":
        fetchStatusData(chart);
        break;
      case "roomUsageChart":
        fetchRoomUsageData(chart);
        break;
    }
  });
}

let userIdToDelete = null;

function confirmUserDeletion(userId) {
  userIdToDelete = userId;

  Swal.fire({
    title: "Konfirmasi Hapus",
    icon: "warning",
    text: "Apakah Anda yakin ingin menghapus pengguna ini?",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Ya, Hapus",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      deleteUser();
    }
  });
}

function deleteUser() {
  if (!userIdToDelete) return;

  Swal.fire({
    title: "Sedang memproses...",
    text: "Sedang menghapus pengguna",
    allowOutsideClick: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  fetch("/admin/users/deleteUser", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify({
      userId: userIdToDelete,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: "Pengguna berhasil dihapus",
          showConfirmButton: false,
          timer: 1500,
        }).then(() => {
          window.location.reload();
        });
      } else {
        throw new Error(data.message || "Gagal menghapus pengguna");
      }
    })
    .catch((error) => {
      console.error("Error: ", error);

      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: error.message || "Terjadi kesalah saat menghapus pengguna",
        confirmButtonText: "Tutup",
        confirmButtonColor: "#dc3545",
      });
    });
}

function showUserActivity(userId) {
  Swal.fire({
    title: "Memuat...",
    text: "Sedang mengambil data aktivitas",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  fetch(`/admin/users/getActivity/${userId}`)
    .then((response) => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then((data) => {
      Swal.close();

      if (data.success) {
        const userInfo = document.getElementById("userInfo");
        if (userInfo) {
          userInfo.innerHTML = `
            <div class="row">
              <div class="col-md-4">
                <p><strong>Nama:</strong> ${
                  data.user.fullname || data.user.username
                }</p>
                <p><strong>Username:</strong> ${data.user.username}</p>
              </div>
              <div class="col-md-4">
                <p><strong>Email:</strong> ${data.user.email}</p>
                <p><strong>Status:</strong> 
                  <span class="badge bg-${
                    data.user.active ? "success" : "danger"
                  }">
                    ${data.user.active ? "Aktif" : "Tidak Aktif"}
                  </span>
                </p>
              </div>
            </div>`;
        }

        const loginHistory = document.getElementById("loginHistory");
        if (loginHistory) {
          loginHistory.innerHTML =
            (data.logins || []).length > 0
              ? data.logins
                  .map(
                    (login) => `
                <tr>
                  <td>${formatDate(login.date)}</td>
                  <td>${login.ip_address}</td>
                  <td>${login.user_agent || "-"}</td>
                </tr>
              `
                  )
                  .join("")
              : '<tr><td colspan="3" class="text-center">Tidak ada data login</td></tr>';
        }

        const peminjamanHistory = document.getElementById("peminjamanHistory");
        if (peminjamanHistory) {
          const allPeminjaman = [
            ...(data.peminjaman || []).map((item) => ({
              jenis: "Kendaraan",
              nama: `${item.merk} - ${item.no_polisi}`,
              tanggal_pinjam: item.tanggal_pinjam,
              tanggal_kembali: item.tanggal_kembali,
              status: item.status,
              created_at: item.created_at,
            })),
            ...(data.peminjaman_kendaraan || []).map((item) => ({
              jenis: "Kendaraan",
              nama: `${item.merk} - ${item.no_polisi}`,
              tanggal_pinjam: item.tanggal_pinjam,
              tanggal_kembali: item.tanggal_kembali,
              status: item.status,
              created_at: item.created_at,
            })),
            ...(data.peminjaman_barang || []).map((item) => ({
              jenis: "Barang",
              nama: item.nama_barang,
              tanggal_pinjam: item.tanggal_pinjam,
              tanggal_kembali: item.tanggal_kembali,
              status: item.status,
              created_at: item.created_at,
            })),
            ...(data.peminjaman_ruangan || []).map((item) => ({
              jenis: "Ruangan",
              nama: item.nama_ruangan,
              tanggal_pinjam: item.waktu_mulai || item.created_at,
              tanggal_kembali: item.waktu_selesai || "-",
              status: "Dipinjam",
              created_at: item.created_at,
            })),
          ];

          peminjamanHistory.innerHTML =
            allPeminjaman.length > 0
              ? allPeminjaman
                  .map(
                    (p) => `
                <tr>
                  <td>${formatDate(p.created_at)}</td>
                  <td>${p.jenis}</td>
                  <td>${p.nama}</td>
                  <td>${formatDate(p.tanggal_pinjam)}</td>
                  <td>${formatDate(p.tanggal_kembali)}</td>
                  <td><span class="badge bg-${getStatusColor(p.status)}">${
                      p.status
                    }</span></td>
                </tr>`
                  )
                  .join("")
              : '<tr><td colspan="6" class="text-center">Tidak ada data peminjaman</td></tr>';
        }

        const pengembalianHistory = document.getElementById(
          "pengembalianHistory"
        );
        if (pengembalianHistory) {
          const allPengembalian = [
            ...(data.pengembalian || []).map((item) => ({
              jenis: "Kendaraan",
              nama: `${item.merk} - ${item.no_polisi}`,
              tanggal_pinjam: item.tanggal_pinjam,
              tanggal_kembali: item.tanggal_kembali,
              status: item.status,
              created_at: item.created_at,
            })),
            ...(data.pengembalian_kendaraan || []).map((item) => ({
              jenis: "Kendaraan",
              nama: `${item.merk} - ${item.no_polisi}`,
              tanggal_pinjam: item.tanggal_pinjam,
              tanggal_kembali: item.tanggal_kembali,
              status: item.status,
              created_at: item.created_at,
            })),
            ...(data.pengembalian_barang || []).map((item) => ({
              jenis: "Barang",
              nama: item.nama_barang,
              tanggal_pinjam: item.tanggal_pinjam,
              tanggal_kembali: item.tanggal_kembali,
              status: item.status,
              created_at: item.created_at,
            })),
            ...(data.pengembalian_ruangan || []).map((item) => ({
              jenis: "Ruangan",
              nama: item.nama_ruangan,
              tanggal_pinjam: item.waktu_mulai || item.created_at,
              tanggal_kembali: item.waktu_selesai || "-",
              status: "Dikembalikan",
              created_at: item.created_at,
            })),
          ];

          pengembalianHistory.innerHTML =
            allPengembalian.length > 0
              ? allPengembalian
                  .map(
                    (k) => `
                <tr>
                  <td>${formatDate(k.created_at)}</td>
                  <td>${k.jenis}</td>
                  <td>${k.nama}</td>
                  <td>${formatDate(k.tanggal_pinjam)}</td>
                  <td>${formatDate(k.tanggal_kembali)}</td>
                  <td><span class="badge bg-${getStatusColor(k.status)}">${
                      k.status
                    }</span></td>
                </tr>`
                  )
                  .join("")
              : '<tr><td colspan="6" class="text-center">Tidak ada data pengembalian</td></tr>';
        }

        new bootstrap.Modal(
          document.getElementById("modalUserActivity")
        ).show();
      } else {
        throw new Error(
          data.message || "Terjadi kesalahan saat mengambil data"
        );
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Error!",
        text:
          error.message || "Terjadi kesalahan saat mengambil data aktivitas",
      });
    });
}

function showDetailAset(id) {
  Swal.fire({
    title: "Memuat...",
    text: "Sedang mengambil data aset",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  fetch(`/admin/daftar-aset/detail/${id}`)
    .then((response) => response.json())
    .then((data) => {
      Swal.close();

      if (data.success) {
        const aset = data.data;
        const gambarArray = aset.gambar_mobil
          ? JSON.parse(aset.gambar_mobil)
          : [];

        document.getElementById("detailAsetContent").innerHTML = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Merk:</strong> ${aset.merk}</p>
                            <p><strong>No. Polisi:</strong> ${
                              aset.no_polisi
                            }</p>
                            <p><strong>Kode Barang:</strong> ${
                              aset.kode_barang
                            }</p>
                            <p><strong>No. SK PSP:</strong> ${
                              aset.no_sk_psp
                            }</p>
                            <p><strong>Tahun Pembuatan:</strong> ${
                              aset.tahun_pembuatan
                            }</p>
                            <p><strong>Kapasitas:</strong> ${aset.kapasitas}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>No. BPKB:</strong> ${aset.no_bpkb}</p>
                            <p><strong>No. STNK:</strong> ${aset.no_stnk}</p>
                            <p><strong>No. Rangka:</strong> ${
                              aset.no_rangka
                            }</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-${
                                  aset.status_pinjam === "Tersedia"
                                    ? "success"
                                    : aset.status_pinjam === "Dipinjam"
                                    ? "warning"
                                    : aset.status_pinjam === "Dalam Verifikasi"
                                    ? "info"
                                    : "primary"
                                }">
                                    ${aset.status_pinjam}
                                </span>
                            </p>
                            <p><strong>Kondisi:</strong>
                                <span class="badge bg-${
                                  aset.kondisi === "Baik"
                                    ? "success"
                                    : aset.kondisi === "Rusak Ringan"
                                    ? "warning"
                                    : "danger"
                                }">
                                    ${aset.kondisi}
                                </span>
                            </p>
                        </div>
                    </div>
                    ${
                      gambarArray.length > 0
                        ? `
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3">Foto Kendaraan:</h6>
                            </div>
                            ${gambarArray
                              .map(
                                (gambar) => `
                                <div class="col-md-4 mb-3">
                                    <img src="/uploads/images/${gambar}" 
                                         alt="Gambar Mobil" 
                                         class="img-fluid rounded w-100"
                                         style="height: 200px; object-fit: cover; cursor: pointer"
                                         onclick="showImageDetail('/uploads/images/${gambar}')"
                                         onerror="this.src='/assets/images/no-image.jpg'">
                                </div>
                            `
                              )
                              .join("")}
                        </div>
                    `
                        : '<p class="text-center text-muted">Tidak ada gambar tersedia</p>'
                    }
                `;

        const modal = new bootstrap.Modal(
          document.getElementById("modalDetailAset")
        );
        modal.show();
      } else {
        throw new Error(
          data.message || "Terjadi kesalahan saat mengambil detail aset"
        );
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Error!",
        text: error.message || "Terjadi kesalahan saat mengambil detail aset",
      });
    });
}

function getStatusColor(status) {
  switch (status?.toLowerCase()) {
    case "aktif":
    case "selesai":
      return "success";
    case "pending":
      return "warning";
    case "ditolak":
      return "danger";
    default:
      return "secondary";
  }
}

let leafletMap = null; // variabel global untuk cache map

function trackKendaraan(nopol) {
  fetch(`/tracking-api?nopol=${encodeURIComponent(nopol)}`)
    .then((res) => res.json())
    .then((kendaraan) => {
      if (kendaraan.error) {
        Swal.fire("Gagal", kendaraan.error, "error");
        return;
      }

      const info = kendaraan.current;
      const [lat, lng] = info.latlng.split(",").map(parseFloat);

      Swal.fire({
        title: `Tracking: ${kendaraan.nopol}`,
        html: `
          <b>Status:</b> ${kendaraan.isOnline ? "✅ Online" : "❌ Offline"}<br>
          <b>Lokasi:</b> ${info.latlng}<br>
          <b>Odometer:</b> ${info.totalOdometer} meter<br>
          <b>Kecepatan:</b> ${info.vehicleSpeed ?? 0} km/h<br>
          <b>Tegangan Aki:</b> ${info.externalVoltage} mV
        `,
        icon: kendaraan.isOnline ? "success" : "warning",
        showCancelButton: true,
        confirmButtonText: "📍 Tracking",
        cancelButtonText: "Tutup",
        reverseButtons: true,
      }).then((result) => {
        if (result.isConfirmed) {
          const mapModal = new bootstrap.Modal(
            document.getElementById("trackingMapModal")
          );
          mapModal.show();

          // Delay untuk pastikan modal sudah render
          setTimeout(() => {
            // Hapus instance lama jika ada
            if (leafletMap !== null) {
              leafletMap.remove();
            }

            leafletMap = L.map("trackingMap").setView([lat, lng], 16);

            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
              attribution: "&copy; OpenStreetMap contributors",
            }).addTo(leafletMap);

            L.marker([lat, lng])
              .addTo(leafletMap)
              .bindPopup(
                `${kendaraan.nopol}<br>Kecepatan: ${
                  info.vehicleSpeed ?? 0
                } km/h`
              )
              .openPopup();
          }, 300);
        }
      });
    })
    .catch((err) => {
      console.error(err);
      Swal.fire("Error", "Gagal mengakses tracking", "error");
    });
}

window.addEventListener("resize", function () {
  Chart.instances.forEach((chart) => {
    chart.resize();
  });
});

// === dashboard.js versi fix ===

function verifikasiPengembalianBarang(id, status) {
  const title =
    status === "disetujui" ? "Setujui Pengembalian" : "Tolak Pengembalian";
  const text =
    status === "disetujui"
      ? "Apakah Anda yakin ingin menyetujui pengembalian barang ini?"
      : "Apakah Anda yakin ingin menolak pengembalian barang ini?";

  Swal.fire({
    title,
    text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#435ebe",
    cancelButtonColor: "#dc3545",
    confirmButtonText: "Ya",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      const formData = new FormData();
      formData.append("id", id);
      formData.append("status", status);

      fetch(`${BASE_URL}/admin/User/Barang/verifikasiPengembalian`, {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire("Berhasil!", data.message, "success").then(() => {
              location.reload();
            });
          } else {
            Swal.fire("Gagal!", data.error || "Terjadi kesalahan.", "error");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire("Gagal!", "Terjadi kesalahan.", "error");
        });
    }
  });
}

function ajukanPengembalianBarang(id) {
  Swal.fire({
    title: "Konfirmasi",
    text: "Yakin ingin mengajukan pengembalian barang ini?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#435ebe",
    cancelButtonColor: "#dc3545",
    confirmButtonText: "Ya, Ajukan",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      const formData = new FormData();
      formData.append("id", id);
      formData.append("user_id", USER_ID);

      fetch(`${BASE_URL}/user/barang/kembalikan`, {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire("Berhasil!", data.message, "success").then(() => {
              location.reload();
            });
          } else {
            Swal.fire("Gagal!", data.error || "Terjadi kesalahan.", "error");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire("Gagal!", "Terjadi kesalahan.", "error");
        });
    }
  });
}
let chartPeminjamanBarang; // grafik bawah (interaktif)
let chartPeminjamanBarangStatic; // grafik atas (statis)

function loadChartBarang(mode, param1 = "", param2 = "") {
  const ctx = document.getElementById("chartPeminjamanBarangUnified");
  if (!ctx) return;

  const url = {
    bulanan: "/admin/dashboard/chart/peminjaman-barang-bulanan",
    mingguan: `/admin/dashboard/chart/peminjaman-barang-mingguan?bulan=${param1}&minggu=${param2}`,
    harian: `/admin/dashboard/chart/peminjaman-barang-harian?tanggal=${param1}`,
  }[mode];

  fetch(url)
    .then((res) => res.json())
    .then((data) => {
      const labels = data.map((d) => d.label);
      const jumlah = data.map((d) => d.jumlah);

      if (chartPeminjamanBarang) chartPeminjamanBarang.destroy();

      chartPeminjamanBarang = new Chart(ctx, {
        type: mode === "harian" ? "bar" : "line",
        data: {
          labels: labels,
          datasets: [
            {
              label:
                mode === "bulanan"
                  ? "Peminjaman Barang Bulanan"
                  : mode === "mingguan"
                  ? "Peminjaman Barang Mingguan"
                  : "Peminjaman Barang Harian",
              data: jumlah,
              backgroundColor:
                mode === "harian"
                  ? "rgba(255, 99, 132, 0.6)"
                  : "rgba(255, 159, 64, 0.2)",
              borderColor: mode === "harian" ? "#ff6384" : "#ff9f40",
              borderWidth: 2,
              fill: true,
              tension: 0.4,
              pointRadius: 4,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: "top" },
            title: {
              display: true,
              text:
                mode === "bulanan"
                  ? "Statistik Peminjaman Barang Bulanan"
                  : mode === "mingguan"
                  ? "Statistik Peminjaman Barang Mingguan"
                  : "Statistik Peminjaman Barang Harian",
              padding: { top: 10, bottom: 20 },
            },
            datalabels: {
              anchor: "end",
              align: "top",
              color: "#000",
              font: {
                weight: "bold",
                size: 12,
              },
              formatter: (value) => value,
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { stepSize: 1 },
            },
          },
        },
        plugins: [ChartDataLabels],
      });
    });
}

document.addEventListener("DOMContentLoaded", function () {
  // Grafik statis atas (bulanan)
  const ctxBarangStatic = document.getElementById("chartPeminjamanBarang");
  if (ctxBarangStatic) {
    fetch("/admin/dashboard/chart/peminjaman-barang-bulanan")
      .then((res) => res.json())
      .then((data) => {
        const labels = data.map((d) => d.label);
        const jumlah = data.map((d) => d.jumlah);

        chartPeminjamanBarangStatic = new Chart(ctxBarangStatic, {
          type: "line",
          data: {
            labels: labels,
            datasets: [
              {
                label: "Peminjaman Barang Bulanan",
                data: jumlah,
                backgroundColor: "rgba(255, 159, 64, 0.2)",
                borderColor: "#ff9f40",
                borderWidth: 2,
                tension: 0.3,
                fill: true,
                pointRadius: 4,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { position: "top" },
              title: {
                display: true,
                text: "Statistik Peminjaman Barang Bulanan",
              },
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: { stepSize: 1 },
              },
            },
          },
        });
      });
  }

  // Grafik dinamis bawah (dengan filter)
  const modeBarang = document.getElementById("modeChartSelectorBarang");
  const bulanBarang = document.getElementById("filterBulanBarang");
  const mingguBarang = document.getElementById("filterMingguBarang");
  const tanggalBarang = document.getElementById("filterTanggalBarang");

  if (modeBarang) {
    function updateInputBarang() {
      const mode = modeBarang.value;
      document.getElementById("grupFilterBulanBarang").style.display =
        mode === "mingguan" ? "block" : "none";
      document.getElementById("grupFilterMingguBarang").style.display =
        mode === "mingguan" ? "block" : "none";
      document.getElementById("grupFilterTanggalBarang").style.display =
        mode === "harian" ? "block" : "none";
    }

    function refreshChartBarang() {
      const mode = modeBarang.value;
      if (mode === "bulanan") {
        loadChartBarang("bulanan");
      } else if (mode === "mingguan") {
        loadChartBarang("mingguan", bulanBarang.value, mingguBarang.value);
      } else {
        loadChartBarang("harian", tanggalBarang.value);
      }
    }

    updateInputBarang();
    refreshChartBarang();

    modeBarang.addEventListener("change", () => {
      updateInputBarang();
      refreshChartBarang();
    });

    bulanBarang.addEventListener("change", refreshChartBarang);
    mingguBarang.addEventListener("change", refreshChartBarang);
    tanggalBarang.addEventListener("change", refreshChartBarang);
  }
});
