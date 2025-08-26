<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<title>Profil Saya</title>

<div class="content-container">
    <div class="page-heading">
        <div class="page-title">
            <div class="row mb-4">
                <div class="col-12">
                    <h3>Profil Saya</h3>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>

<div class="tab-content">
    <div class="tab-pane fade show active" id="profile">
    <div class="card shadow-sm border border-primary p-4">
        <form id="editUserForm" action="<?= base_url('user/profile/update') ?>" method="post">
            <input type="hidden" id="editUserId" name="id" value="<?= esc($user->id) ?>">
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" value="<?= esc($user->username) ?>">
                        </div>
                <div class="mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" name="fullname" class="form-control" value="<?= esc($user->fullname) ?>">
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= esc($user->email) ?>">
                </div>
            <div class="mb-3">
        <label>Unit Organisasi</label>
        <select class="form-control" id="unitOrganisasi" name="unit_organisasi" readonly> 
            <option value="" disabled>Pilih Unit Organisasi</option>
            <?php
                $unitOrganisasiList = [
                    "Setjen" => "Sekretariat Jenderal",
                    "Itjen" => "Inspektorat Jenderal",
                    "Ditjen Sumber Daya Air" => "Direktorat Jenderal Sumber Daya Air",
                    "Ditjen Bina Marga" => "Direktorat Jenderal Bina Marga",
                    "Ditjen Cipta Karya" => "Direktorat Jenderal Cipta Karya",
                    "Ditjen Perumahan" => "Direktorat Jenderal Perumahan",
                    "Ditjen Bina Konstruksi" => "Direktorat Jenderal Bina Konstruksi",
                    "Ditjen Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan" => "Direktorat Jenderal Pembiayaan Infrastruktur PUPR",
                    "BPIW" => "Badan Pengembangan Infrastruktur Wilayah",
                    "BPSDM" => "Badan Pengembangan SDM",
                    "BPJT" => "Badan Pengatur Jalan Tol"
                ];
                foreach ($unitOrganisasiList as $key => $label):
            ?>
                <option value="<?= $key ?>" <?= ($user->unit_organisasi === $key ? 'selected' : '') ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Unit Kerja</label>
        <select class="form-control" id="unitKerja" name="unit_kerja" readonly>
            <option value="<?= esc($user->unit_kerja) ?>" selected><?= esc($user->unit_kerja) ?></option>
        </select>
    </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
        <script>
document.addEventListener("DOMContentLoaded", function () {
    // Daftar unit kerja berdasarkan unit organisasi
    const unitKerjaOptions = {
        "Setjen": ["Biro Perencanaan", "Biro Kepegawaian", "Biro Keuangan", "Biro Hukum", "Biro Umum", "Pusdatin"],
        "Itjen": ["Sekretariat Itjen", "Inspektorat I", "Inspektorat II", "Inspektorat III", "Inspektorat IV"],
        "Ditjen Sumber Daya Air": ["Sekretariat Ditjen SDA", "Dit. Bina Operasi dan Pemeliharaan", "Dit. Sungai dan Pantai", "Dit. Irigasi"],
        "Ditjen Bina Marga": ["Sekretariat Ditjen Bina Marga", "Dit. Jalan Bebas Hambatan", "Dit. Jalan Nasional"],
        "Ditjen Cipta Karya": ["Sekretariat Ditjen Cipta Karya", "Dit. Pengembangan Kawasan Permukiman", "Dit. Air Minum"],
        "Ditjen Perumahan": ["Sekretariat Ditjen Perumahan", "Dit. Rumah Umum", "Dit. Rumah Susun"],
        "Ditjen Bina Konstruksi": ["Sekretariat Ditjen Bina Konstruksi", "Dit. Kompetensi dan Produktivitas Konstruksi", "Dit. Pengembangan Jasa Konstruksi"],
        "Ditjen Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan": ["Sekretariat DJPI", "Dit. Pembiayaan Perumahan", "Dit. Pembiayaan Infrastruktur"],
        "BPIW": ["Sekretariat BPIW", "Pusat Pengembangan Kawasan Strategis", "Pusat Pengembangan Kawasan Perkotaan"],
        "BPSDM": ["Sekretariat BPSDM", "Pusat Pendidikan dan Pelatihan", "Pusat Pembinaan Kompetensi"],
        "BPJT": ["Sekretariat BPJT", "Divisi Pengembangan", "Divisi Operasi"]
    };

    // Fungsi untuk mengisi dropdown unit kerja
    function populateUnitKerja(unitOrganisasi, targetSelectId) {
        const unitKerjaSelect = document.getElementById(targetSelectId);
        
        // Kosongkan dropdown terlebih dahulu
        unitKerjaSelect.innerHTML = '<option value="" disabled selected>Pilih Unit Kerja</option>';
        
        // Isi dropdown dengan opsi unit kerja yang sesuai
        if (unitOrganisasi in unitKerjaOptions) {
            unitKerjaOptions[unitOrganisasi].forEach(unitKerja => {
                const option = document.createElement('option');
                option.value = unitKerja;
                option.textContent = unitKerja;
                unitKerjaSelect.appendChild(option);
            });
        }
    }
    
    // Tambahkan event listener untuk perubahan pada unit organisasi di form edit
    const editUnitOrgSelect = document.getElementById('editUnitOrganisasi');
    if (editUnitOrgSelect) {
        editUnitOrgSelect.addEventListener('change', function() {
            populateUnitKerja(this.value, 'editUnitKerja');
        });
    }
    
    // Ubah fungsi editUser untuk mengatur nilai dropdown dengan benar
    window.editUser = function (id, fullname, email, unit_organisasi, unit_kerja, active) {
        // Set nilai input biasa
        document.getElementById("editUserId").value = id;
        document.getElementById("editFullname").value = fullname;
        document.getElementById("editEmail").value = email;
        document.getElementById("editStatus").value = active;
        
        // Set nilai dropdown unit organisasi
        const unitOrgSelect = document.getElementById("editUnitOrganisasi");
        unitOrgSelect.value = unit_organisasi;
        
        // Isi dropdown unit kerja berdasarkan unit organisasi
        populateUnitKerja(unit_organisasi, 'editUnitKerja');
        
        // Set nilai unit kerja setelah dropdown terisi (gunakan timeout kecil)
        setTimeout(() => {
            const unitKerjaSelect = document.getElementById("editUnitKerja");
            unitKerjaSelect.value = unit_kerja;
            
            // Jika nilai tidak ditemukan dalam dropdown, tambahkan sebagai opsi baru
            if (unitKerjaSelect.value !== unit_kerja && unit_kerja) {
                const newOption = document.createElement('option');
                newOption.value = unit_kerja;
                newOption.textContent = unit_kerja;
                unitKerjaSelect.appendChild(newOption);
                unitKerjaSelect.value = unit_kerja;
            }
        }, 100);
        
        // Tampilkan modal
        const modal = new bootstrap.Modal(document.getElementById("modalEditUser"));
        modal.show();
    };
    
    // Validasi email
    document.getElementById("editEmail").addEventListener("input", function () {
        let emailInput = this;
        let emailPattern = /^[a-zA-Z0-9._%+-]+@pu\.go\.id$/;
        let errorMessage = emailInput.nextElementSibling;

        if (!emailPattern.test(emailInput.value)) {
            emailInput.classList.add("is-invalid");
            errorMessage.style.display = "block";
        } else {
            emailInput.classList.remove("is-invalid");
            errorMessage.style.display = "none";
        }
    });

    // Form submit handler
    document.getElementById("editUserForm").addEventListener("submit", function (event) {
        event.preventDefault();
        
        // Validasi email
        let emailInput = document.getElementById("editEmail");
        let emailPattern = /^[a-zA-Z0-9._%+-]+@pu\.go\.id$/;
        if (!emailPattern.test(emailInput.value)) {
            alert("Email harus menggunakan domain @pu.go.id");
            return;
        }

        // Ambil semua data form
        const userId = document.getElementById("editUserId").value;
        const formData = new FormData(this);

        // Kirim data ke server
        fetch(`/admin/users/update/${userId}`, {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("User berhasil diperbarui!");
                window.location.reload();
            } else {
                console.error("Server response:", data);
                alert("Gagal memperbarui user: " + (data.message || "Unknown error"));
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
            alert("Terjadi kesalahan saat memperbarui user.");
        })
        .finally(() => {
            const modal = bootstrap.Modal.getInstance(document.getElementById("modalEditUser"));
            if (modal) modal.hide();
        });
    });
});
</script>
</div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
