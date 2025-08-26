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

      const suratPermohonan = e.target.querySelector(
        '[name="surat_permohonan"]'
      ).files[0];
      if (suratPermohonan && suratPermohonan.size > 2 * 1024 * 1024) {
        Swal.fire({
          icon: "error",
          title: "Gagal!",
          text: "Ukuran file Surat Permohonan tidak boleh lebih dari 2MB",
          confirmButtonText: "Tutup",
          confirmButtonColor: "#dc3545",
        });
        return;
      }

      const formData = new FormData(this);
      const requiredFields = [
        "nama_penanggung_jawab",
        "nip_nrp",
        "pangkat_golongan",
        "jabatan",
        "unit_organisasi",
        "kendaraan_id",
        "pengemudi",
        "no_hp",
        "tanggal_pinjam",
        "tanggal_kembali",
        "urusan_kedinasan",
        "surat_permohonan",
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

  const formPengembalian = document.getElementById("formPengembalian");
  if (formPengembalian) {
    formPengembalian.removeEventListener("submit", handlePengembalianSubmit);
    formPengembalian.addEventListener("submit", handlePengembalianSubmit);
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

  const suratPengembalian = e.target.querySelector(
    '[name="surat_pengembalian"]'
  )?.files[0];
  const beritaAcara = e.target.querySelector(
    '[name="berita_acara_pengembalian"]'
  )?.files[0];

  const maxSize = 2 * 1024 * 1024;

  if (suratPengembalian && suratPengembalian.size > maxSize) {
    Swal.fire({
      icon: "error",
      title: "Gagal!",
      text: "Ukuran file Surat Pengembalian tidak boleh lebih dari 2MB",
      confirmButtonText: "Tutup",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  if (beritaAcara && beritaAcara.size > maxSize) {
    Swal.fire({
      icon: "error",
      title: "Gagal!",
      text: "Ukuran file Berita Acara tidak boleh lebih dari 2MB",
      confirmButtonText: "Tutup",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

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

  const requiredFields = [
    "nama_penanggung_jawab",
    "nip_nrp",
    "pangkat_golongan",
    "jabatan",
    "unit_organisasi",
    "tanggal_kembali",
    "surat_pengembalian",
    "berita_acara_pengembalian",
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

  console.log("Submitting form data:", {
    kendaraanId: kendaraanId,
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

        const fields = [
          "id",
          "kategori_id",
          "no_sk_psp",
          "kode_barang",
          "merk",
          "tahun_pembuatan",
          "kapasitas",
          "no_polisi",
          "no_bpkb",
          "no_stnk",
          "no_rangka",
          "kondisi",
        ];

        for (const field of fields) {
          if (typeof aset[field] === "undefined") {
            throw new Error(`Data ${field} tidak ditemukan`);
          }
        }

        fields.forEach((field) => {
          const element = document.getElementById(`edit_${field}`);
          if (element) {
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

  // PERBAIKAN: Parameter yang benar untuk setiap tipe
  if (tipe === "ruangan") {
    formData.append("pinjam_id", id);
  } else if (tipe === "kendaraan") {
    if (jenis === "peminjaman") {
      formData.append("pinjam_id", id);
    } else {
      formData.append("kembali_id", id);
    }
  } else if (tipe === "barang") {
    if (jenis === "peminjaman") {
      formData.append("pinjam_id", id);
    } else {
      formData.append("id", id);
    }
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

  // PERBAIKAN: Endpoint yang BENAR untuk setiap tipe
  let endpoint = "";

  if (tipe === "ruangan") {
    // RUANGAN SELALU KE PEMINJAMAN (tidak ada pengembalian ruangan)
    endpoint = "/admin/User/Ruangan/verifikasiPeminjaman";
  } else if (tipe === "kendaraan") {
    if (jenis === "peminjaman") {
      endpoint = "/AsetKendaraan/verifikasiPeminjaman";
    } else {
      endpoint = "/AsetKendaraan/verifikasiPengembalianKendaraan";
    }
  } else if (tipe === "barang") {
    if (jenis === "peminjaman") {
      endpoint = "/admin/User/Barang/verifikasiPeminjaman";
    } else {
      endpoint = "/admin/User/Barang/verifikasiPengembalian";
    }
  }

  console.log("Final endpoint:", endpoint);
  console.log("Form data:", Array.from(formData.entries()));

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

  fetch(`/AsetKendaraan/getPeminjamanData/${kendaraanId}`)
    .then((response) => response.json())
    .then((response) => {
      if (response.error) {
        throw new Error(response.error);
      }

      document.getElementById("kendaraan_id_hidden").value = kendaraanId;

      const fields = [
        "nama_penanggung_jawab",
        "nip_nrp",
        "pangkat_golongan",
        "jabatan",
        "unit_organisasi",
        "pengemudi",
        "no_hp",
      ];

      fields.forEach((field) => {
        const input = document.getElementById(field);
        if (input && response[field]) {
          input.value = response[field];
        }
      });

      if (response.tanggal_pinjam) {
        const tanggalPinjam = response.tanggal_pinjam.split("T")[0];
        document.getElementById("tanggal_pinjam").value = tanggalPinjam;
      }

      const kendaraanSelect = document.getElementById("kendaraan_id_kembali");
      if (kendaraanSelect) {
        kendaraanSelect.innerHTML = "";
        const option = new Option(
          `${response.merk} - ${response.no_polisi}`,
          kendaraanId,
          true,
          true
        );
        kendaraanSelect.appendChild(option);
      }

      const modal = new bootstrap.Modal(
        document.getElementById("modalPengembalian")
      );
      modal.show();
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
