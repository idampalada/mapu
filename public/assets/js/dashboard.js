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
  if (modalPilihVerifikasi) {
    modalPilihVerifikasi.hide();
  }

  // Cek apakah modalVerifikasi sudah dimuat dalam DOM
  const modalVerifikasiEl = document.getElementById("modalVerifikasi");
  console.log("Modal Verifikasi Element:", modalVerifikasiEl); // Debugging

  if (!modalVerifikasiEl) {
    // Jika modal belum ada, load secara AJAX
    $.ajax({
      url: "/AsetKendaraan/loadModalVerifikasi",
      type: "GET",
      success: function (response) {
        // Tambahkan modal ke body
        $("body").append(response);
        // Ambil referensi modal yang baru ditambahkan
        const newModalEl = document.getElementById("modalVerifikasi");
        // Buat instance modal baru dan tampilkan
        const newModal = new bootstrap.Modal(newModalEl);
        newModal.show();
      },
      error: function () {
        alert("Gagal memuat modal verifikasi. Silakan coba lagi.");
      },
    });
  } else {
    // Jika modal sudah ada, tampilkan
    let modalVerifikasi = bootstrap.Modal.getInstance(modalVerifikasiEl);
    if (!modalVerifikasi) {
      modalVerifikasi = new bootstrap.Modal(modalVerifikasiEl);
    }
    modalVerifikasi.show();
  }
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
  const tipe = document.getElementById("tolakTipe").value;
  const jenis = document.getElementById("jenisVerifikasi").value;
  const alasan = document.getElementById("alasanPenolakan").value;
  const dokumenInput = document.getElementById("dokumen_tambahan");

  console.log("submitPenolakan called with:", {
    id: id,
    tipe: tipe,
    jenis: jenis,
    alasan: alasan,
  });

  if (!alasan.trim()) {
    Swal.fire({
      icon: "error",
      title: "Error!",
      text: "Alasan penolakan harus diisi!",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  // TAMBAHKAN VALIDASI FILE
  if (!dokumenInput || !dokumenInput.files || dokumenInput.files.length === 0) {
    Swal.fire({
      icon: "error",
      title: "Error!",
      text: "Dokumen tambahan harus diupload!",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  // Tambahkan validasi tipe dan ukuran file
  const file = dokumenInput.files[0];
  const validTypes = [
    "application/pdf",
    "image/jpeg",
    "image/jpg",
    "image/png",
  ];
  const maxSize = 2 * 1024 * 1024; // 2MB

  if (!validTypes.includes(file.type)) {
    Swal.fire({
      icon: "error",
      title: "Format File Tidak Valid",
      text: "File harus berformat PDF, JPG, atau PNG",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  if (file.size > maxSize) {
    Swal.fire({
      icon: "error",
      title: "Ukuran File Terlalu Besar",
      text: "Ukuran maksimum file adalah 2MB",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  const formData = new FormData();
  const baseUrl = window.location.origin;
  let endpoint = "";
  let paramName = "";

  // PERBAIKAN: Menentukan endpoint berdasarkan tipe kendaraan atau pengembalian
  if (tipe === "kendaraan" || tipe === "pengembalian") {
    if (jenis === "peminjaman") {
      // Jika tipe pengembalian tapi jenis peminjaman, ini untuk pengembalian kendaraan
      if (tipe === "pengembalian") {
        endpoint = baseUrl + "/admin/AsetKendaraan/verifikasiPengembalian";
        paramName = "kembali_id";
      } else {
        endpoint = baseUrl + "/AsetKendaraan/verifikasiPeminjaman";
        paramName = "pinjam_id";
      }
    } else {
      endpoint = baseUrl + "/admin/AsetKendaraan/verifikasiPengembalian";
      paramName = "kembali_id";
    }
  } else if (tipe === "ruangan") {
    endpoint = baseUrl + "/admin/verifikasi-ruangan/verifikasiPeminjaman";
    paramName = "pinjam_id";
  } else if (tipe === "barang") {
    if (jenis === "peminjaman") {
      endpoint = baseUrl + "/admin/User/Barang/verifikasiPeminjaman";
      paramName = "pinjam_id";
    } else {
      endpoint = baseUrl + "/admin/User/Barang/verifikasiPengembalian";
      paramName = "id";
    }
  }

  // Tambahkan prefix /admin/ jika belum ada
  if (
    endpoint &&
    !endpoint.includes("/admin/") &&
    (tipe === "pengembalian" || jenis === "pengembalian")
  ) {
    // Jika endpoint tidak memiliki /admin/ dan terkait pengembalian
    endpoint = endpoint.replace(baseUrl, baseUrl + "/admin");
  }

  // Jika masih tidak ada endpoint
  if (!endpoint) {
    console.error(
      "Endpoint tidak ditemukan untuk tipe:",
      tipe,
      "dan jenis:",
      jenis
    );
    Swal.fire({
      icon: "error",
      title: "Error Konfigurasi",
      text: "Tidak dapat menentukan endpoint. Silakan hubungi administrator.",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  // Parameter
  formData.append(paramName, id);
  formData.append("status", "ditolak");
  formData.append("keterangan", alasan);
  formData.append("dokumen_tambahan", file);

  console.log("Final endpoint:", endpoint);
  console.log("Form data:", Array.from(formData.entries()));

  Swal.fire({
    title: "Memproses...",
    allowOutsideClick: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  fetch(endpoint, {
    method: "POST",
    body: formData,
    headers: {
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((res) => {
      console.log("Response status:", res.status);
      console.log("Response headers:", res.headers);

      if (!res.ok) {
        throw new Error(`HTTP ${res.status}: ${res.statusText}`);
      }
      return res.json();
    })
    .then((data) => {
      console.log("Response data:", data);

      if (data.error) {
        throw new Error(data.error);
      }

      Swal.fire({
        icon: data.success ? "success" : "error",
        title: data.success ? "Berhasil!" : "Gagal!",
        text: data.message || data.error,
      }).then(() => {
        if (data.success) {
          // Close modal tolak
          const modalTolak = bootstrap.Modal.getInstance(
            document.getElementById("modalTolak")
          );
          if (modalTolak) {
            modalTolak.hide();
          }
          location.reload();
        }
      });
    })
    .catch((err) => {
      console.error("Fetch error:", err);
      Swal.fire({
        icon: "error",
        title: "Error!",
        text: "Gagal memproses penolakan: " + err.message,
        confirmButtonColor: "#dc3545",
      });
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

// Global variable untuk menyimpan file
let selectedFiles = [];

function handleImagePreview(e) {
  const newFiles = Array.from(e.target.files);

  console.log(
    "handleImagePreview dipanggil dengan",
    newFiles.length,
    "file baru"
  );
  console.log("selectedFiles sebelum:", selectedFiles.length);

  // Validasi maksimal 5 foto total
  if (selectedFiles.length + newFiles.length > 5) {
    Swal.fire({
      icon: "warning",
      title: "Maksimal 5 foto!",
      text: `Anda sudah memiliki ${selectedFiles.length} foto. Maksimal total 5 foto.`,
      confirmButtonColor: "#dc3545",
    });
    e.target.value = "";
    return;
  }

  let isValid = true;
  const validNewFiles = [];

  // Validasi setiap file baru
  newFiles.forEach((file) => {
    console.log(
      "Memvalidasi file:",
      file.name,
      "Size:",
      file.size,
      "Type:",
      file.type
    );

    // Validasi ukuran
    if (file.size > 5 * 1024 * 1024) {
      Swal.fire({
        icon: "error",
        title: "File terlalu besar!",
        text: `${file.name} melebihi 5MB`,
        confirmButtonColor: "#dc3545",
      });
      isValid = false;
      return;
    }

    // Validasi format
    if (!["image/jpeg", "image/jpg", "image/png"].includes(file.type)) {
      Swal.fire({
        icon: "error",
        title: "Format tidak didukung!",
        text: `${file.name} harus berformat JPG atau PNG`,
        confirmButtonColor: "#dc3545",
      });
      isValid = false;
      return;
    }

    // Cek duplikasi nama file
    const isDuplicate = selectedFiles.some(
      (existingFile) => existingFile.name === file.name
    );
    if (!isDuplicate) {
      validNewFiles.push(file);
    } else {
      console.log("File duplikat diabaikan:", file.name);
    }
  });

  if (!isValid) {
    e.target.value = "";
    return;
  }

  // TAMBAHKAN file baru ke array
  selectedFiles = [...selectedFiles, ...validNewFiles];
  console.log("selectedFiles setelah:", selectedFiles.length);

  // Update file input dengan semua file - JANGAN RESET!
  updateFileInput();

  // Update preview
  updatePreview();

  // JANGAN RESET INPUT - ini yang menyebabkan validasi gagal
  // e.target.value = "";  // HAPUS BARIS INI
}

function updateFileInput() {
  const input = document.getElementById("gambar_mobil");
  if (!input) {
    console.error("Input gambar_mobil tidak ditemukan!");
    return;
  }

  const dt = new DataTransfer();
  selectedFiles.forEach((file) => {
    dt.items.add(file);
  });

  input.files = dt.files;

  // PENTING: Hapus atribut required karena kita handle validasi manual
  input.removeAttribute("required");

  console.log("File input diupdate dengan", dt.files.length, "file");
}

function updatePreview() {
  const previewRow = document.getElementById("previewRow");

  if (!previewRow) {
    console.error("Element previewRow tidak ditemukan!");
    return;
  }

  console.log("updatePreview dipanggil untuk", selectedFiles.length, "file");

  // Clear preview sebelumnya
  previewRow.innerHTML = "";

  if (selectedFiles.length === 0) {
    console.log("Tidak ada file untuk preview");
    return;
  }

  // Buat preview untuk setiap file
  selectedFiles.forEach((file, index) => {
    console.log("Membuat preview untuk file", index + 1, ":", file.name);

    const reader = new FileReader();
    reader.onload = function (e) {
      console.log("FileReader berhasil untuk file:", file.name);

      const previewCol = document.createElement("div");
      previewCol.className = "col-md-4 mb-3";
      previewCol.innerHTML = `
                <div class="card">
                    <img src="${e.target.result}" 
                         class="card-img-top" 
                         style="height: 150px; object-fit: cover; cursor: pointer;"
                         onclick="showImagePreview('${e.target.result}')"
                         alt="${file.name}">
                    <div class="card-body p-2">
                        <p class="card-text small text-muted mb-1">${file.name}</p>
                        <button type="button" 
                                class="btn btn-danger btn-sm w-100" 
                                onclick="removeImage(${index})">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </div>
                </div>
            `;
      previewRow.appendChild(previewCol);
      console.log("Preview ditambahkan ke DOM untuk file:", file.name);
    };

    reader.onerror = function (e) {
      console.error("Error reading file:", file.name, e);
    };

    reader.readAsDataURL(file);
  });
}

function removeImage(index) {
  console.log("Menghapus file pada index:", index);

  // Hapus file dari array
  selectedFiles.splice(index, 1);

  // Update file input dan preview
  updateFileInput();
  updatePreview();

  console.log(
    `File pada index ${index} dihapus. Sisa: ${selectedFiles.length} file`
  );
}

function showImagePreview(src) {
  Swal.fire({
    imageUrl: src,
    imageAlt: "Preview Image",
    showConfirmButton: false,
    showCloseButton: true,
    width: "80%",
    padding: "1em",
    background: "#fff",
  });
}

function handleTambahAsetSubmit(e) {
  e.preventDefault();

  const form = e.target;

  // VALIDASI MANUAL UNTUK FILE
  if (selectedFiles.length === 0) {
    Swal.fire({
      icon: "error",
      title: "Error!",
      text: "Minimal 1 foto harus diunggah",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  if (selectedFiles.length > 5) {
    Swal.fire({
      icon: "error",
      title: "Error!",
      text: "Maksimal 5 foto yang dapat diunggah",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  // Pastikan form input lain valid (kecuali file)
  const otherInputs = form.querySelectorAll(
    "input:not(#gambar_mobil), select, textarea"
  );
  let isFormValid = true;

  otherInputs.forEach((input) => {
    if (input.hasAttribute("required") && !input.value.trim()) {
      input.reportValidity();
      isFormValid = false;
    }
  });

  if (!isFormValid) {
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

  // Buat FormData dengan file yang benar
  const formData = new FormData();

  // Tambahkan semua input form kecuali file
  const formInputs = new FormData(form);
  for (let [key, value] of formInputs.entries()) {
    if (key !== "gambar_mobil[]") {
      formData.append(key, value);
    }
  }

  // Tambahkan file dari selectedFiles
  selectedFiles.forEach((file) => {
    formData.append("gambar_mobil[]", file);
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
          // Reset semua
          form.reset();
          selectedFiles = [];
          updatePreview();

          const modal = bootstrap.Modal.getInstance(
            document.getElementById("modalTambahAset")
          );
          if (modal) modal.hide();
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

// Initialize saat DOM ready
document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM loaded, menginisialisasi event listeners");

  // Reset saat modal dibuka
  const modal = document.getElementById("modalTambahAset");
  if (modal) {
    modal.addEventListener("show.bs.modal", function () {
      console.log("Modal dibuka, reset selectedFiles");
      selectedFiles = [];
      updatePreview();

      // Reset required attribute untuk input file
      const fileInput = document.getElementById("gambar_mobil");
      if (fileInput) {
        fileInput.removeAttribute("required");
      }
    });
  }

  // Event listener untuk input file
  const fileInput = document.getElementById("gambar_mobil");
  if (fileInput) {
    console.log("Event listener untuk file input ditambahkan");
    fileInput.addEventListener("change", handleImagePreview);

    // Hapus required attribute karena kita handle manual
    fileInput.removeAttribute("required");
  } else {
    console.error("Input file gambar_mobil tidak ditemukan!");
  }

  // Event listener untuk form submit
  const formTambahAset = document.getElementById("formTambahAset");
  if (formTambahAset) {
    formTambahAset.addEventListener("submit", handleTambahAsetSubmit);
  }
});
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
// ===== COMPLETE JAVASCRIPT FOR TTE RATING MODAL =====
// Replace your existing JS with this complete version

// Enhanced showRatingModal function with TTE support
function showRatingModal(kembaliId) {
  // Reset form
  $("#formRatingAdmin")[0].reset();
  $("#kembali_id_rating").val(kembaliId);

  // Reset validation
  $(".form-control").removeClass("is-invalid is-valid");
  $(".invalid-feedback").remove();

  // Reset TTE toggle to off by default
  $("#enableTTEPengembalian").prop("checked", false);
  $("#tteOptionsPengembalian").hide();

  // Reset rating display
  $(".rating-text-admin").text("0/5");

  // Load saved credentials if available
  loadSavedCredentialsPengembalianRating();

  // Show modal
  const modal = new bootstrap.Modal(
    document.getElementById("modalRatingAdmin")
  );
  modal.show();

  console.log("Rating modal opened for kembali ID:", kembaliId);
}

// Ganti fungsi verifikasiPengembalian untuk menggunakan modal rating
function verifikasiPengembalian(id, status) {
  // Jika status disetujui, tampilkan modal rating
  if (status === "disetujui") {
    showRatingModal(id);
    return;
  }

  // Jika status ditolak, gunakan fungsi showTolakModal
  if (status === "ditolak") {
    showTolakModal("pengembalian", id);
    return;
  }
}

// ===== HELPER FUNCTIONS YANG LENGKAP =====
function validateRatingForm() {
  console.log("Validating rating form...");

  // Clear previous errors
  clearRatingValidationErrors();

  let isValid = true;

  // Validasi rating admin wajib
  const rating = $('input[name="rating_admin"]:checked').val();
  if (!rating) {
    showRatingFieldError(
      'input[name="rating_admin"]',
      "Rating kondisi kendaraan harus dipilih"
    );
    isValid = false;
  }

  // Cek TTE enabled
  const isTteEnabled = $("#enableTTEPengembalian").is(":checked");
  if (isTteEnabled) {
    console.log("TTE enabled, validating credentials...");

    // Validasi NIK
    const nik = $("#tte_nik_pengembalian").val().trim();
    if (!nik || nik.length !== 16) {
      showRatingFieldError("#tte_nik_pengembalian", "NIK harus 16 digit");
      isValid = false;
    }

    // Validasi Passphrase
    const passphrase = $("#tte_passphrase_pengembalian").val();
    if (!passphrase || passphrase.length < 6) {
      showRatingFieldError(
        "#tte_passphrase_pengembalian",
        "Passphrase minimal 6 karakter"
      );
      isValid = false;
    }

    // Validasi QR Link
    const qrLink = $("#tte_qr_link_pengembalian").val().trim();
    if (!qrLink || !isValidUrl(qrLink)) {
      showRatingFieldError(
        "#tte_qr_link_pengembalian",
        "Link QR harus berupa URL yang valid"
      );
      isValid = false;
    }
  }

  console.log("Form validation result:", isValid);
  return isValid;
}

function showRatingFieldError(fieldSelector, message) {
  const $field = $(fieldSelector);
  $field.addClass("is-invalid");

  // Remove existing error message
  $field.siblings(".invalid-feedback").remove();

  // Add error message
  $field.after(`<div class="invalid-feedback">${message}</div>`);
}

function clearRatingValidationErrors() {
  $(".form-control").removeClass("is-invalid is-valid");
  $(".invalid-feedback").remove();
}

function showRatingLoadingState($btn) {
  $btn.prop("disabled", true);
  $btn.html(
    '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...'
  );
}

function hideRatingLoadingState($btn, originalText = "Submit") {
  $btn.prop("disabled", false);
  $btn.html(originalText);
}

function loadSavedCredentialsPengembalianRating() {
  try {
    const saved = localStorage.getItem("tte_last_credentials_pengembalian");
    if (saved) {
      const credentials = JSON.parse(saved);
      $("#tte_nik_pengembalian").val(credentials.nik || "");
      $("#tte_qr_link_pengembalian").val(credentials.qr_link || "");
      // TIDAK load passphrase untuk keamanan
      console.log("Credentials loaded for pengembalian rating");
    }
  } catch (e) {
    console.warn("Error loading saved credentials:", e);
  }
}

function saveRatingCredentialsPengembalian() {
  try {
    const credentials = {
      nik: $("#tte_nik_pengembalian").val().trim(),
      qr_link: $("#tte_qr_link_pengembalian").val().trim(),
      // TIDAK simpan passphrase untuk keamanan
    };

    if (credentials.nik || credentials.qr_link) {
      localStorage.setItem(
        "tte_last_credentials_pengembalian",
        JSON.stringify(credentials)
      );
      console.log("Credentials saved for pengembalian");
    }
  } catch (e) {
    console.warn("Error saving credentials:", e);
  }
}

function isValidUrl(string) {
  try {
    new URL(string);
    return true;
  } catch (_) {
    return false;
  }
}

// ===== ENHANCED SUBMIT HANDLER WITH TTE SUPPORT =====
document.addEventListener("DOMContentLoaded", function () {
  // Inisialisasi rating bintang admin
  const ratingInputsAdmin = document.querySelectorAll(
    'input[name="rating_admin"]'
  );
  const ratingTextAdmin = document.querySelector(".rating-text-admin");

  if (ratingInputsAdmin.length > 0 && ratingTextAdmin) {
    ratingInputsAdmin.forEach((input) => {
      input.addEventListener("change", function () {
        const value = this.value;
        ratingTextAdmin.textContent = `${value}/5`;
      });
    });
  }

  // Enhanced submit handler with TTE support
  const btnSubmitRating = document.getElementById("btnSubmitRating");
  if (btnSubmitRating) {
    btnSubmitRating.addEventListener("click", function (e) {
      e.preventDefault();

      console.log("Submit rating clicked");

      // Validasi form
      if (!validateRatingForm()) {
        console.log("Form validation failed");
        return;
      }

      const form = document.getElementById("formRatingAdmin");
      const kembaliId = document.getElementById("kembali_id_rating").value;
      const rating = form.querySelector(
        'input[name="rating_admin"]:checked'
      ).value;
      const keterangan = document.getElementById("keterangan").value;
      const isTteEnabled = $("#enableTTEPengembalian").is(":checked");

      console.log("Form data:", {
        kembaliId,
        rating,
        isTteEnabled,
      });

      // Show loading state
      const $submitBtn = $(this);
      showRatingLoadingState($submitBtn);

      // Prepare FormData
      const formData = new FormData();
      formData.append("kembali_id", kembaliId);
      formData.append("status", "disetujui");
      formData.append("rating_admin", rating);
      formData.append("keterangan", keterangan);

      // Auto-detect TTE dan set parameter
      if (isTteEnabled) {
        console.log("TTE enabled, adding TTE parameters");

        formData.append("enable_tte_pengembalian", "on");
        formData.append("tte_nik", $("#tte_nik_pengembalian").val().trim());
        formData.append(
          "tte_passphrase",
          $("#tte_passphrase_pengembalian").val()
        );
        formData.append(
          "tte_qr_link",
          $("#tte_qr_link_pengembalian").val().trim()
        );
        formData.append(
          "tte_position",
          $("#tte_position").val() || "visible_bottom"
        );
        formData.append("tte_x", $("#tte_x").val() || "400");
        formData.append("tte_y", $("#tte_y").val() || "700");
        formData.append("tte_width", $("#tte_width").val() || "150");
        formData.append("tte_height", $("#tte_height").val() || "75");
        formData.append(
          "tte_reason",
          $("#tte_reason").val() ||
            "Berita Acara Pengembalian telah disetujui dan ditandatangani secara elektronik"
        );
        formData.append("tte_location", $("#tte_location").val() || "Jakarta");

        // Save credentials before submit
        saveRatingCredentialsPengembalian();
      }

      // Smart endpoint routing (auto-detection di backend)
      const endpoint = "/AsetKendaraan/verifikasiPengembalian";

      console.log("Submitting to:", endpoint);

      // Submit form
      fetch(endpoint, {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => response.json())
        .then((data) => {
          hideRatingLoadingState($submitBtn, "Submit");

          console.log("Response:", data);

          if (data.success) {
            // Success dengan info TTE
            let message = data.message || "Pengembalian berhasil disetujui";

            if (data.tte_applied) {
              message += ` (TTE oleh ${data.tte_signer})`;
            }

            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: message,
              confirmButtonText: "OK",
              confirmButtonColor: "#198754",
            }).then(() => {
              // Close modal
              const modal = bootstrap.Modal.getInstance(
                document.getElementById("modalRatingAdmin")
              );
              if (modal) modal.hide();

              // Reload page
              window.location.reload();
            });
          } else {
            throw new Error(
              data.error || "Terjadi kesalahan saat memproses persetujuan"
            );
          }
        })
        .catch((error) => {
          hideRatingLoadingState($submitBtn, "Submit");
          console.error("Submit error:", error);

          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text:
              error.message || "Terjadi kesalahan saat memproses persetujuan",
            confirmButtonText: "Tutup",
            confirmButtonColor: "#dc3545",
          });
        });
    });
  }
});

// ===== TTE EVENT HANDLERS DENGAN JQUERY =====
$(document).ready(function () {
  console.log("Initializing TTE event handlers for rating modal");

  // TTE Toggle Handler
  $("#enableTTEPengembalian").on("change", function () {
    const isChecked = $(this).is(":checked");
    console.log("TTE toggle changed:", isChecked);

    if (isChecked) {
      $("#tteOptionsPengembalian").slideDown(300);
      // Load saved credentials when TTE is enabled
      loadSavedCredentialsPengembalianRating();
    } else {
      $("#tteOptionsPengembalian").slideUp(300);
    }
  });

  // Password visibility toggle
  $("#togglePassphrasePengembalian").on("click", function (e) {
    e.preventDefault();
    const $passphraseInput = $("#tte_passphrase_pengembalian");
    const $icon = $(this).find("i");

    if ($passphraseInput.attr("type") === "password") {
      $passphraseInput.attr("type", "text");
      $icon.removeClass("bi-eye").addClass("bi-eye-slash");
    } else {
      $passphraseInput.attr("type", "password");
      $icon.removeClass("bi-eye-slash").addClass("bi-eye");
    }
  });

  // NIK validation (16 digits only)
  $("#tte_nik_pengembalian").on("input", function () {
    let value = $(this).val().replace(/\D/g, ""); // Remove non-digits
    if (value.length > 16) {
      value = value.substring(0, 16);
    }
    $(this).val(value);

    // Visual validation
    if (value.length === 16) {
      $(this).removeClass("is-invalid").addClass("is-valid");
    } else if (value.length > 0) {
      $(this).removeClass("is-valid").addClass("is-invalid");
    } else {
      $(this).removeClass("is-valid is-invalid");
    }
  });

  // Load saved credentials button
  $("#loadCredentialsPengembalian").on("click", function (e) {
    e.preventDefault();
    loadSavedCredentialsPengembalianRating();

    // Show feedback
    $(this).html('<i class="bi bi-check"></i> Loaded').addClass("btn-success");
    setTimeout(() => {
      $(this)
        .html('<i class="bi bi-arrow-clockwise"></i> Load Saved')
        .removeClass("btn-success");
    }, 2000);
  });

  console.log("TTE event handlers initialized");
});

console.log("Rating modal with TTE support loaded");
