document.addEventListener("DOMContentLoaded", function () {
  const formSetuju = document.getElementById("formSetuju");
  if (formSetuju) {
    formSetuju.addEventListener("submit", function (e) {
      e.preventDefault();

      Swal.fire({
        title: "Mohon Tunggu",
        text: "Sedang memproses verifikasi...",
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      let formData = new FormData(this);
      formData.append("status", "disetujui");

      fetch(`${BASE_URL}/AsetKendaraan/verifikasiPeminjaman`, {
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
              $("#modalSetuju").modal("hide");
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
            text: error.message || "Terjadi kesalahan saat verifikasi",
            confirmButtonText: "Tutup",
            confirmButtonColor: "#dc3545",
          });
        });
    });
  }

  initializeHistoryTables();

  const detailModal = document.getElementById("detailModal");
  if (detailModal) {
    detailModal.addEventListener("hidden.bs.modal", function () {
      document.getElementById("detailContent").innerHTML = "";
    });
  }

  const modalPeminjaman = document.getElementById("modalPeminjaman");
  if (modalPeminjaman) {
    modalPeminjaman.addEventListener("shown.bs.modal", function () {
      initializeJabatanDropdown();
    });
  }

  const formPeminjaman = document.getElementById("formPeminjaman");
  if (formPeminjaman) {
    formPeminjaman.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const requiredFields = [
        "nama_penanggung_jawab",
        "nip_nrp",
        "no_ktp", // Tambahkan field baru
        "alamat_rumah", // Tambahkan field baru
        "pangkat_golongan",
        "jabatan",
        "unit_organisasi",
        "kendaraan_id",
        "pengemudi",
        "no_hp",
        "tanggal_pinjam",
        "tanggal_kembali",
        "urusan_kedinasan",
      ];

      for (const field of requiredFields) {
        const value = formData.get(field);
        if (!value) {
          Swal.fire({
            icon: "error",
            title: "Error!",
            text: `${field.replace(/_/g, " ")} harus diisi`,
            confirmButtonText: "Tutup",
            confirmButtonColor: "#dc3545",
          });
          return;
        }
      }

      Swal.fire({
        title: "Mohon Tunggu",
        text: "Sedang memproses data dan memeriksa keamanan file...",
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      fetch(this.action, {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            if (response.status === 413) {
              throw new Error("Ukuran file terlalu besar. Maksimal 2MB");
            }
            return response.json().then((errData) => {
              throw new Error(
                errData.error || "Terjadi kesalahan saat mengirim data"
              );
            });
          }
          return response.json();
        })
        .then((data) => {
          if (data.error) {
            if (data.error.includes("terdeteksi tidak aman")) {
              showFileUnsafeModal(data.error);
            } else if (data.error.includes("Ukuran file")) {
              throw new Error(data.error);
            } else {
              throw new Error(data.error);
            }
          } else if (data.success) {
            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: "Pengajuan peminjaman berhasil dikirim. Mohon tunggu verifikasi.",
              confirmButtonText: "OK",
              confirmButtonColor: "#198754",
            }).then((result) => {
              if (result.isConfirmed) {
                this.reset();
                const modal = bootstrap.Modal.getInstance(
                  document.getElementById("modalPeminjaman")
                );
                if (modal) modal.hide();
                window.location.reload();
              }
            });
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: error.message || "Terjadi kesalahan saat mengirim data",
            confirmButtonText: "Tutup",
            confirmButtonColor: "#dc3545",
          });
        });
    });
  }

  const modalPengembalian = document.getElementById("modalPengembalian");
  if (modalPengembalian) {
  }

  const formEditAset = document.getElementById("formEditAset");
  if (formEditAset) {
    formEditAset.addEventListener("submit", handleEditAsetSubmit);
  }
});

const jabatanMapping = {
  Setjen: [
    "Kepala Biro Perencanaan Anggaran dan Kerja Sama Luar Negeri",
    "Kepala Biro Kepegawaian, Organisasi, dan Tata Laksana",
    "Kepala Biro Keuangan",
    "Kepala Biro Umum",
    "Kepala Biro Hukum",
    "Kepala Biro Pengelolaan Barang Milik Negara",
    "Kepala Biro Komunikasi Publik",
    "Kepala Pusat Analisis Pelaksanaan Kebijakan",
    "Kepala Pusat Data dan Teknologi Informasi",
    "Kepala Pusat Fasilitasi Infrastruktur Daerah",
  ],

  Itjen: [
    "Sekretaris Inspektorat Jenderal",
    "Inspektur 1",
    "Inspektur 2",
    "Inspektur 3",
    "Inspektur 4",
    "Inspektur 5",
    "Inspektur 6",
  ],

  "Ditjen Sumber Daya Air": [
    "Sekretaris Direktorat Jenderal",
    "Direktur Sistem dan Strategi Pengelolaan Sumber Daya Air",
    "Direktur Sungai dan Pantai",
    "Direktur Irigasi dan Rawa",
    "Direktur Bendungan dan Danau",
    "Direktur Air Tanah dan Air Baku",
    "Direktur Bina Operasi dan Pemeliharaan",
    "Direktur Bina Teknik Sumber Daya Air",
    "Direktur Kepatuhan Intern",
    "Kepala Pusat Pengendalian Lumpur Sidoarjo",
  ],

  "Ditjen Bina Marga": [
    "Sekretaris Direktorat Jenderal",
    "Direktur Sistem dan Strategi Pengelenggaraan Jalan dan Jembatan",
    "Direktur Pembangunan Jalan",
    "Direktur Pembangunan Jembatan",
    "Direktur Preservasi Jalan dan Jembatan Wilayah I",
    "Direktur Preservasi Jalan dan Jembatan Wilayah II",
    "Direktur Jalan Bebas Hambatan",
    "Direktur Bina Teknik Jalan dan Jembatan",
    "Direktur Kepatuhan Intern",
  ],

  "Ditjen Cipta Karya": [
    "Sekretaris Direktorat Jenderal",
    "Direktur Sistem dan Strategi Pengelenggaraan Infrastruktur Permukiman",
    "Direktur Bina Penataan Bangunan",
    "Direktur Air Minum",
    "Direktur Pengembangan Kawasan Permukiman",
    "Direktur Sanitasi",
    "Direktur Prasarana Strategis",
    "Direktur Bina Teknik Permukiman dan Perumahan",
    "Direktur Kepatuhan Intern",
  ],

  "Ditjen Bina Konstruksi": [
    "Sekretaris Direktorat Jenderal",
    "Direktur Pengembangan Jasa Konstruksi",
    "Direktur Kelembagaan dan Sumber Daya Konstruksi",
    "Direktur Kompentensi dan Produktivitas Konstruksi",
    "Direktur Pengadaan Jasa Konstruksi",
    "Direktur Keberlanjutan Konstruksi",
  ],

  "Ditjen Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan": [
    "Sekretaris Direktorat Jenderal",
    "Direktur Pengembangan Sistem dan Strategi Penyelenggaraan Pembiayaan",
    "Direktur Pelaksanaan Pembiayaan Infrastruktur Sumber Daya Air",
    "Direktur Pelaksanaan Pembiayaan Infrastruktur Jalan dan Jembatan",
    "Direktur Pelaksanaan Pembiayaan Infrastruktur Permukiman",
    "Direktur Pelaksanaan Pembiayaan Perumahan",
  ],

  BPIW: [
    "Sekretaris Badan",
    "Kepala Pusat Pengembangan Infrastruktur Wilayah Nasional",
    "Kepala Pusat Pengembangan Infrastruktur Pekerjaan Umum dan Perumahan Rakyat Wilayah I",
    "Kepala Pusat Pengembangan Infrastruktur Pekerjaan Umum dan Perumahan Rakyat Wilayah II",
    "Kepala Pusat Pengembangan Infrastruktur Pekerjaan Umum dan Perumahan Rakyat Wilayah III",
  ],

  BPSDM: [
    "Sekretaris Badan",
    "Kepala Pusat Pengembangan Talenta",
    "Kepala Pusat Pengembangan Kompetensi Sumber Daya Air dan Permukaan",
    "Kepala Pusat Pengembangan Kompetensi Jalan, Perumahan, dan Pengembangan Infrastruktur Wilayah",
    "Kepala Pusat Pengembangan Kompetensi Manajemen",
  ],

  BPJT: ["Sekretaris Badan"],
};

function initializeUnitCards() {
  document.querySelectorAll(".unit-card").forEach((card) => {
    card.addEventListener("click", function () {
      const unitName = this.querySelector(".card-title").textContent;
      console.log(`Unit ${unitName} diklik`);
    });
  });
}

function initializeJabatanDropdown() {
  const unitOrganisasiSelect = document.querySelector(
    'select[name="unit_organisasi"]'
  );
  const jabatanSelect = document.querySelector('select[name="jabatan"]');

  if (!unitOrganisasiSelect || !jabatanSelect) {
    console.error("Elemen dropdown tidak ditemukan");
    return;
  }

  function updateJabatanOptions() {
    const selectedUnit = unitOrganisasiSelect.value;

    jabatanSelect.innerHTML =
      '<option value="" class="text-muted" disabled selected>Pilih Jabatan</option>';

    if (jabatanMapping[selectedUnit]) {
      jabatanMapping[selectedUnit].forEach((jabatan) => {
        const option = document.createElement("option");
        option.value = jabatan;
        option.textContent = jabatan;
        jabatanSelect.appendChild(option);
      });
      jabatanSelect.disabled = false;
    } else {
      jabatanSelect.disabled = true;
    }
  }

  unitOrganisasiSelect.addEventListener("change", updateJabatanOptions);

  if (unitOrganisasiSelect.value) {
    updateJabatanOptions();
  }
}

function handlePengembalianSubmit(e) {
  e.preventDefault();

  const kendaraanId = document.getElementById("kendaraan_id_hidden")?.value;
  if (!kendaraanId) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Data kendaraan tidak valid",
      confirmButtonText: "Tutup",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  // Validasi foto kamera
  const photoData = document.getElementById("photo-data")?.value;
  if (!photoData) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Foto kendaraan diperlukan untuk pengembalian",
      confirmButtonText: "Tutup",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  // Ubah daftar field yang wajib diisi - HAPUS berita_acara_pengembalian dari daftar
  const requiredFields = [
    "nama_penanggung_jawab",
    "nip_nrp",
    "pangkat_golongan",
    "jabatan",
    "unit_organisasi",
    "tanggal_kembali",
    // "berita_acara_pengembalian", // Hapus validasi ini
    "nomor_sip", // Tambahkan nomor_sip yang diperlukan
  ];

  for (const field of requiredFields) {
    const input = e.target.querySelector(`[name="${field}"]`);
    if (!input?.value) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: `Field ${field.replace(/_/g, " ")} harus diisi`,
        confirmButtonText: "Tutup",
        confirmButtonColor: "#dc3545",
      });
      return;
    }
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

  const formData = new FormData(e.target);

  // Log form data for debugging
  console.log("Submitting form data:", {
    kendaraanId: kendaraanId,
    photoData: photoData ? "Photo data exists" : "No photo data",
    formValues: Object.fromEntries(formData),
  });

  fetch(e.target.action, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      if (data.error) {
        if (data.error.includes("terdeteksi tidak aman")) {
          showFileUnsafeModal(data.error);
        } else {
          throw new Error(data.error);
        }
      }

      if (data.success) {
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: "Pengajuan pengembalian berhasil dikirim",
          confirmButtonText: "OK",
          confirmButtonColor: "#198754",
        }).then(() => {
          e.target.reset();
          const modal = bootstrap.Modal.getInstance(
            document.getElementById("modalPengembalian")
          );
          if (modal) modal.hide();
          window.location.reload();
        });
      } else {
        throw new Error("Terjadi kesalahan saat memproses data");
      }
    })
    .catch((error) => {
      console.error("Error in form submission:", error);
      Swal.fire({
        icon: "error",
        title: "Gagal!",
        text: error.message || "Terjadi kesalahan saat mengirim data",
        confirmButtonText: "Tutup",
        confirmButtonColor: "#dc3545",
      });
    });
}

function showSetujuModal(pinjamId) {
  $("#pinjamId").val(pinjamId);
  $("#modalSetuju").modal("show");
}

function openEditModal(id) {
  if (!id) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "ID aset tidak valid",
      confirmButtonText: "Tutup",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  Swal.fire({
    title: "Mohon Tunggu",
    text: "Sedang mengambil data...",
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  const url = `${window.location.origin}/AsetKendaraan/getAsetById/${id}`;

  fetch(url)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        const aset = data.data;

        // Update daftar field yang digunakan, tanpa field yang sudah dihapus
        const fields = [
          "id",
          "kategori_id",
          "kode_barang",
          "merk",
          "tahun_pembuatan",
          "kapasitas",
          "no_polisi",
          "no_rangka",
          "warna", // Field baru
          "nomor_mesin", // Field baru
          "nup", // Field baru
          "kondisi",
        ];

        // Isi field yang ada saja, jangan periksa field yang tidak ada
        fields.forEach((field) => {
          const element = document.getElementById(`edit_${field}`);
          if (element) {
            // Gunakan nilai default string kosong jika field tidak ada
            element.value = aset[field] || "";
          }
        });

        const currentImagePreview = document.getElementById(
          "current_image_preview"
        );
        if (currentImagePreview && aset.gambar_mobil) {
          currentImagePreview.src = `${window.location.origin}/uploads/images/${aset.gambar_mobil}`;
          currentImagePreview.style.display = "block";
          document.getElementById("edit_gambar_mobil").value = "";
        }

        Swal.close();
        const modal = new bootstrap.Modal(
          document.getElementById("modalEditAset")
        );
        modal.show();
      } else {
        throw new Error(data.error || "Gagal mengambil data aset");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Gagal!",
        text: error.message || "Gagal mengambil data aset",
        confirmButtonText: "Tutup",
        confirmButtonColor: "#dc3545",
      });
    });
}

function handleEditAsetSubmit(e) {
  e.preventDefault();

  const id = document.getElementById("edit_id").value;

  Swal.fire({
    title: "Mohon Tunggu",
    text: "Sedang memproses perubahan data...",
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  const formData = new FormData(this);

  fetch(`${window.location.origin}/AsetKendaraan/edit/${id}`, {
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
        }).then((result) => {
          if (result.isConfirmed) {
            const modal = bootstrap.Modal.getInstance(
              document.getElementById("modalEditAset")
            );
            modal.hide();
            window.location.reload();
          }
        });
      } else {
        throw new Error(
          data.error || "Terjadi kesalahan saat memperbarui data"
        );
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Gagal!",
        text: error.message || "Terjadi kesalahan saat memperbarui data",
        confirmButtonText: "Tutup",
        confirmButtonColor: "#dc3545",
      });
    });
}

function showDetail(type, id) {
  Swal.fire({
    title: "Mohon Tunggu",
    text: "Sedang mengambil data...",
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  fetch(`${window.location.origin}/user/riwayat/detail/${type}/${id}`)
    .then((response) => response.json())
    .then((data) => {
      Swal.close();

      if (data.success) {
        const modal = new bootstrap.Modal(
          document.getElementById("detailModal")
        );
        document.getElementById("detailContent").innerHTML = data.html;
        modal.show();
      } else {
        Swal.fire({
          icon: "error",
          title: "Gagal",
          text: data.message || "Gagal mengambil detail data",
          confirmButtonText: "Tutup",
          confirmButtonColor: "#dc3545",
        });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Terjadi kesalahan saat mengambil data",
        confirmButtonText: "Tutup",
        confirmButtonColor: "#dc3545",
      });
    });
}

function initializeHistoryTables() {
  const tabelRiwayatPeminjaman = document.getElementById(
    "tabelRiwayatPeminjaman"
  );
  const tabelRiwayatPengembalian = document.getElementById(
    "tabelRiwayatPengembalian"
  );

  if (tabelRiwayatPeminjaman) {
    $(tabelRiwayatPeminjaman).DataTable({
      order: [[0, "desc"]],
      pageLength: 10,
      language: {
        url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json",
      },
    });
  }

  if (tabelRiwayatPengembalian) {
    $(tabelRiwayatPengembalian).DataTable({
      order: [[0, "desc"]],
      pageLength: 10,
      language: {
        url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json",
      },
    });
  }
}

function showTolakModal(type, id) {
  document.getElementById("formTolak").reset();
  document.getElementById("tolakId").value = id;
  document.getElementById("tolakTipe").value = type;

  const modalTolak = new bootstrap.Modal(document.getElementById("modalTolak"));
  modalTolak.show();

  document
    .getElementById("modalTolak")
    .addEventListener("shown.bs.modal", function () {
      document.getElementById("alasanPenolakan").focus();
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

  if (dokumenInput && dokumenInput.files.length > 0) {
    formData.append("dokumen_tambahan", dokumenInput.files[0]);
  }

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

// Fungsi tambahan untuk debugging HTML modal
function debugModal() {
  console.log("==== MODAL DEBUG ====");
  console.log("Modal element:", document.getElementById("modalTolak"));
  const formElements = document
    .getElementById("modalTolak")
    ?.querySelectorAll("input, select, textarea");
  if (formElements) {
    formElements.forEach((el) => {
      console.log(
        `Element ${el.id || el.name}: type=${el.type}, value="${el.value}"`
      );
    });
  }
  console.log("====================");
}

// Panggil debugging modal ketika modal dibuka
if (typeof bootstrap !== "undefined") {
  document.addEventListener("DOMContentLoaded", function () {
    const modalElement = document.getElementById("modalTolak");
    if (modalElement) {
      modalElement.addEventListener("shown.bs.modal", function () {
        debugModal();
      });
    }
  });
}

function verifikasiPeminjaman(id, status, keterangan = "") {
  const formData = new FormData();
  formData.append("pinjam_id", id);
  formData.append("status", status);
  formData.append("keterangan", keterangan);

  if (status === "disetujui") {
    const suratJalanInput = document.querySelector("#surat_jalan_admin");
    if (suratJalanInput && suratJalanInput.files[0]) {
      formData.append("surat_jalan_admin", suratJalanInput.files[0]);
    }
  }

  fetch(`${window.location.origin}/AsetKendaraan/verifikasiPeminjaman`, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: "Verifikasi berhasil dilakukan",
          confirmButtonText: "OK",
          confirmButtonColor: "#198754",
        }).then(() => {
          window.location.reload();
        });
      } else {
        throw new Error(data.error || "Terjadi kesalahan saat verifikasi");
      }
    })
    .catch((error) => {
      Swal.fire({
        icon: "error",
        title: "Gagal!",
        text: error.message || "Terjadi kesalahan saat verifikasi",
        confirmButtonText: "Tutup",
        confirmButtonColor: "#dc3545",
      });
    });
}

function verifikasiPengembalian(id, status, keterangan = "") {
  Swal.fire({
    title: "Mohon Tunggu",
    text: "Sedang memproses verifikasi pengembalian...",
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  const headers = {
    "Content-Type": "application/x-www-form-urlencoded",
    "X-Requested-With": "XMLHttpRequest",
  };

  const formData = new URLSearchParams({
    kembali_id: id,
    status: status,
    keterangan: keterangan,
  });

  const timeout = 30000;
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), timeout);

  fetch(`${window.location.origin}/AsetKendaraan/verifikasiPengembalian`, {
    method: "POST",
    headers: headers,
    body: formData,
    signal: controller.signal,
  })
    .then((response) => {
      clearTimeout(timeoutId);
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: "Verifikasi pengembalian berhasil dilakukan",
          confirmButtonText: "OK",
          confirmButtonColor: "#198754",
        }).then(() => {
          window.location.reload();
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
        text:
          error.message === "The user aborted a request."
            ? "Waktu permintaan habis. Silakan coba lagi."
            : error.message || "Terjadi kesalahan saat verifikasi",
        confirmButtonText: "Tutup",
        confirmButtonColor: "#dc3545",
      });
    });
}

function deleteAset(id) {
  Swal.fire({
    title: "Apakah anda yakin?",
    text: "Data yang dihapus tidak dapat dikembalikan!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, hapus!",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`${window.location.origin}/AsetKendaraan/delete/${id}`, {
        method: "DELETE",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Accept: "application/json",
        },
        credentials: "same-origin",
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: data.message,
              confirmButtonText: "OK",
              confirmButtonColor: "#198754",
            }).then(() => {
              window.location.reload();
            });
          } else {
            throw new Error(
              data.error || "Terjadi kesalahan saat menghapus data"
            );
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: error.message || "Terjadi kesalahan saat menghapus data",
            confirmButtonText: "Tutup",
            confirmButtonColor: "#dc3545",
          });
        });
    }
  });
}

function openPeminjamanModal(id) {
  const modal = new bootstrap.Modal(document.getElementById("modalPeminjaman"));

  document.getElementById("modalPeminjaman").addEventListener(
    "shown.bs.modal",
    function () {
      const kendaraanSelect = document.getElementById("kendaraan_id_pinjam");

      fetch("/AsetKendaraan/getKendaraan")
        .then((response) => response.json())
        .then((data) => {
          kendaraanSelect.innerHTML =
            '<option value="" disabled>Pilih Kendaraan</option>';

          data.forEach((kendaraan) => {
            if (kendaraan.status_pinjam === "Tersedia" || kendaraan.id === id) {
              const option = document.createElement("option");
              option.value = kendaraan.id;
              option.textContent = `${kendaraan.merk} - ${kendaraan.no_polisi}`;
              kendaraanSelect.appendChild(option);
            }
          });

          if (id) {
            kendaraanSelect.value = id;
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: "Gagal memuat data kendaraan",
            confirmButtonText: "Tutup",
            confirmButtonColor: "#dc3545",
          });
        });
    },
    { once: true }
  );

  modal.show();
}

function loadKendaraanPinjam() {
  console.log("Kendaraan loading handled by openPeminjamanModal");
}

function openPengembalianModal(kendaraanId) {
  const form = document.getElementById("formPengembalian");
  if (form) form.reset();

  console.log("Loading data for kendaraan:", kendaraanId);

  // Panggil endpoint untuk mendapatkan data
  fetch(`/AsetKendaraan/getPeminjamanForKembali/${kendaraanId}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      console.log("Data received:", data);

      if (data.error) {
        throw new Error(data.error);
      }

      // Set kendaraan ID
      document.getElementById("kendaraan_id_hidden").value = kendaraanId;

      // Tab Pihak Kesatu - Isi data
      document.getElementById("nama_penanggung_jawab").value =
        data.nama_penanggung_jawab || "";
      document.getElementById("nip_nrp").value = data.nip_nrp || "";
      document.getElementById("pangkat_golongan").value =
        data.pangkat_golongan || "";
      document.getElementById("jabatan").value = data.jabatan || "";
      document.getElementById("unit_organisasi").value =
        data.unit_organisasi || "";
      document.getElementById("alamat_rumah").value = data.alamat_rumah || "";
      document.getElementById("no_ktp").value = data.no_ktp || "";
      document.getElementById("no_hp").value = data.no_hp || "";
      document.getElementById("pengemudi").value = data.pengemudi || "";

      if (data.tanggal_pinjam) {
        const tanggalPinjam = data.tanggal_pinjam.split("T")[0];
        document.getElementById("tanggal_pinjam").value = tanggalPinjam;
      }

      if (data.tanggal_kembali) {
        const tanggalKembali = data.tanggal_kembali.split("T")[0];
        document.getElementById("tanggal_kembali").value = tanggalKembali;
      }

      // Isi dropdown kendaraan
      const kendaraanSelect = document.getElementById("kendaraan_id_kembali");
      if (kendaraanSelect) {
        kendaraanSelect.innerHTML = "";
        const option = new Option(
          `${data.merk} - ${data.no_polisi}`,
          kendaraanId,
          true,
          true
        );
        kendaraanSelect.appendChild(option);
      }

      // Tab Detail Kendaraan - Isi data
      document.getElementById("kategori_id").value = data.kategori_id || "";
      document.getElementById("no_polisi_detail").value = data.no_polisi || "";
      document.getElementById("kode_barang_detail").value =
        data.kode_barang || "";
      document.getElementById("nup_detail").value = data.nup || "-";
      document.getElementById("tahun_pembuatan").value =
        data.tahun_pembuatan || "-";
      document.getElementById("merk_detail").value = data.merk || "";
      document.getElementById("warna").value = data.warna || "-";
      document.getElementById("nomor_mesin").value = data.nomor_mesin || "-";
      document.getElementById("nomor_rangka").value = data.nomor_rangka || "-";

      // Tampilkan modal
      const modal = new bootstrap.Modal(
        document.getElementById("modalPengembalian")
      );
      modal.show();

      // Aktifkan tab pertama
      const firstTab = document.getElementById("pihak-kesatu-tab");
      if (firstTab) {
        const tab = new bootstrap.Tab(firstTab);
        tab.show();
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: error.message || "Gagal memuat data kendaraan",
        confirmButtonText: "Tutup",
        confirmButtonColor: "#dc3545",
      });
    });
}

function loadKendaraanData() {
  const kendaraanSelect = document.getElementById("kendaraan_id");

  kendaraanSelect.disabled = true;

  fetch("/AsetKendaraan/getKendaraan")
    .then((response) => response.json())
    .then((data) => {
      kendaraanSelect.innerHTML =
        '<option value="" disabled selected>Pilih Kendaraan</option>';

      data.forEach((kendaraan) => {
        const option = document.createElement("option");
        option.value = kendaraan.id;
        option.textContent = `${kendaraan.merk} - ${kendaraan.no_polisi}`;
        kendaraanSelect.appendChild(option);
      });
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Gagal!",
        text: "Gagal memuat data kendaraan",
        confirmButtonText: "Tutup",
        confirmButtonColor: "#dc3545",
      });
    })
    .finally(() => {
      kendaraanSelect.disabled = false;
    });
}
function showEditSuratModal(pinjamId) {
  // Isi ID peminjaman
  $("#pinjam_id_surat").val(pinjamId);

  // Set tanggal hari ini sebagai default
  const today = new Date().toISOString().split("T")[0];
  $("#tanggal_surat").val(today);

  // Nilai default untuk kepala satuan kerja
  $("#nama_kepala_satuan_kerja").val(""); // Kosong untuk diisi user
  $("#nip_kepala_satuan_kerja").val("");

  // Tampilkan modal
  const modal = new bootstrap.Modal(document.getElementById("modalEditSurat"));
  modal.show();
}

function showFileUnsafeModal(message) {
  Swal.fire({
    icon: "error",
    title: "File Tidak Aman",
    text: message,
    showConfirmButton: true,
    confirmButtonText: "Tutup",
    confirmButtonColor: "#dc3545",
    allowOutsideClick: false,
  }).then((result) => {
    if (result.isConfirmed) {
      const forms = document.querySelectorAll("form");
      forms.forEach((form) => {
        const fileInputs = form.querySelectorAll('input[type="file"]');
        fileInputs.forEach((input) => (input.value = ""));
      });
    }
  });
}
// Di file homepage.js
$(document).ready(function () {
  // Tangani klik tombol setujui
  $(".btn-setujui").on("click", function () {
    const pinjamId = $(this).data("id");
    showSetujuModal(pinjamId);
  });

  // Form buat otomatis
  $("#formBuatOtomatis").on("submit", function (e) {
    e.preventDefault();

    // Show loading
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html(
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...'
    );
    submitBtn.prop("disabled", true);

    // Ambil data form
    const formData = new FormData(this);

    // Kirim request AJAX
    $.ajax({
      url: "/admin/AsetKendaraan/generateSuratJalan",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        // Reset button
        submitBtn.html(originalText);
        submitBtn.prop("disabled", false);

        if (response.success) {
          // Tutup modal
          $("#modalSetuju").modal("hide");

          // Tampilkan modal sukses
          $("#modalSuccess").modal("show");

          // Refresh halaman setelah 2 detik
          setTimeout(function () {
            location.reload();
          }, 2000);
        } else {
          alert(
            response.error || "Terjadi kesalahan saat memproses peminjaman"
          );
        }
      },
      error: function (xhr, status, error) {
        // Reset button
        submitBtn.html(originalText);
        submitBtn.prop("disabled", false);

        console.error(xhr.responseText);
        alert("Terjadi kesalahan: " + error);
      },
    });
  });
});

// Function untuk menampilkan modal setuju
// Function untuk menampilkan modal setuju dengan dual tab dan checkbox
function showSetujuModal(pinjamId) {
  // Reset form
  $(".surat-penanggung-field, .surat-jalan-field").removeClass("is-invalid");
  $("#buatSuratPenanggungJawab, #buatSuratJalan").prop("checked", true);

  // Set ID peminjaman
  $("#pinjamId, #pinjamId2").val(pinjamId);

  // Reset field data penandatangan (biarkan kosong)
  $("#nama_penanggung_jawab_kendaraan, #nip_penanggung_jawab_kendaraan").val(
    ""
  );
  $("#nama_kepala_satuan_kerja, #nip_kepala_satuan_kerja").val("");

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

// Event handler untuk tombol submit utama
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

    // Validasi field yang diperlukan untuk surat yang dipilih
    let isValid = true;

    if (buatSuratPenanggungJawab) {
      // Reset validasi terlebih dahulu
      $(".surat-penanggung-field").removeClass("is-invalid");

      // Cek field-field penting
      const fields = [
        { id: "nomor_surat", name: "Nomor Surat" },
        { id: "tanggal_surat", name: "Tanggal Surat" },
        { id: "tempat_surat", name: "Tempat Surat" },
        {
          id: "nama_penanggung_jawab_kendaraan",
          name: "Nama Penanggung Jawab Kendaraan",
        },
        {
          id: "nip_penanggung_jawab_kendaraan",
          name: "NIP Penanggung Jawab Kendaraan",
        },
        { id: "nama_kepala_satuan_kerja", name: "Nama Kepala Satuan Kerja" },
        { id: "nip_kepala_satuan_kerja", name: "NIP Kepala Satuan Kerja" },
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
          text: "Lengkapi semua field untuk Surat Penanggung Jawab KDF",
          confirmButtonText: "OK",
          confirmButtonColor: "#dc3545",
        });
        isValid = false;
        return;
      }
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

    // Proses Surat Penanggung Jawab jika dipilih
    if (buatSuratPenanggungJawab) {
      const suratPenanggungData = new FormData();
      suratPenanggungData.append("pinjam_id", pinjamId);
      suratPenanggungData.append("nomor_surat", $("#nomor_surat").val());
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

      const suratPenanggungPromise = $.ajax({
        url: "/AsetKendaraan/generateSuratPenanggungJawabKdf",
        type: "POST",
        data: suratPenanggungData,
        processData: false,
        contentType: false,
      });

      promises.push(suratPenanggungPromise);
    }

    // Proses Surat Jalan jika dipilih
    if (buatSuratJalan) {
      const suratJalanData = new FormData();
      suratJalanData.append("pinjam_id", pinjamId);
      suratJalanData.append("tanggal_mulai", $("#tanggal_mulai").val());
      suratJalanData.append("tanggal_selesai", $("#tanggal_selesai").val());
      suratJalanData.append("jam_mulai", $("#jam_mulai").val());
      suratJalanData.append("jam_selesai", $("#jam_selesai").val());
      suratJalanData.append("urusan_kedinasan", $("#urusan_kedinasan").val());

      const suratJalanPromise = $.ajax({
        url: "/AsetKendaraan/generateSuratJalan",
        type: "POST",
        data: suratJalanData,
        processData: false,
        contentType: false,
      });

      promises.push(suratJalanPromise);
    }

    // Tunggu semua promises selesai
    Promise.all(promises)
      .then((responses) => {
        // Cek apakah semua responses success
        let allSuccess = true;
        let errorMessage = "";

        for (const response of responses) {
          if (!response.success) {
            allSuccess = false;
            errorMessage += (response.error || "Terjadi kesalahan") + "\n";
          }
        }

        if (allSuccess) {
          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: "Dokumen berhasil dibuat",
            confirmButtonText: "OK",
            confirmButtonColor: "#198754",
          }).then(() => {
            location.reload();
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
        Swal.fire({
          icon: "error",
          title: "Gagal!",
          text: "Terjadi kesalahan saat memproses data",
          confirmButtonText: "Tutup",
          confirmButtonColor: "#dc3545",
        });
      });
  });

  // Toggle required attribute berdasarkan checkbox
  $("#buatSuratPenanggungJawab").on("change", function () {
    const isChecked = $(this).is(":checked");
    $(".surat-penanggung-field").prop("required", isChecked);
  });

  $("#buatSuratJalan").on("change", function () {
    const isChecked = $(this).is(":checked");
    $(".surat-jalan-field").prop("required", isChecked);
  });
});
