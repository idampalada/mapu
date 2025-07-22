<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<title>Daftar Pengguna</title>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <h5 class="fw-bold mb-0">Daftar Pengguna</h5>
        <button class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddUser">
            <i class="bi bi-plus-circle"></i> Tambah Pengguna
        </button>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="searchFullname" class="form-label fw-bold">Cari berdasarkan Fullname:</label>
            <input type="text" id="searchFullname" class="form-control" placeholder="Ketik nama...">
        </div>
        <div class="col-md-6">
            <label for="sortOrder" class="form-label fw-bold">Urutkan berdasarkan:</label>
            <select id="sortOrder" class="form-select">
                <option value=""> Default </option>
                <option value="asc">Fullname A-Z</option>
                <option value="desc">Fullname Z-A</option>
            </select>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Fullname</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Unit Organisasi</th>
                            <th>Unit Kerja</th>
                            <th>Status</th>
                            <th>Role</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($users as $user): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $user->fullname ?></td>
                                <td><?= $user->email ?></td>
                                <td><?= $user->username ?></td>
                                <td><?= $user->unit_organisasi ?></td>
                                <td><?= $user->unit_kerja ?></td>
                                <td>
                                    <?php if ($user->active): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>

                                <input type="hidden" name="role" value="">

                                <td>
                                    <select class="form-select form-select-sm role-select" data-user-id="<?= $user->id ?>" data-original-role="<?= $user->role ?>">
                                        <option value="user" <?= $user->role === 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="admin" <?= $user->role === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="admin_gedungutama" <?= $user->role === 'admin_gedungutama' ? 'selected' : '' ?>>Admin Gedung Utama</option>
                                        <option value="admin_pusdatin" <?= $user->role === 'admin_pusdatin' ? 'selected' : '' ?>>Admin Pusdatin</option>
                                        <option value="admin_binamarga" <?= $user->role === 'admin_binamarga' ? 'selected' : '' ?>>Admin Bina Marga</option>
                                        <option value="admin_ciptakarya" <?= $user->role === 'admin_ciptakarya' ? 'selected' : '' ?>>Admin Cipta Karya</option>
                                        <option value="admin_sda" <?= $user->role === 'admin_sda' ? 'selected' : '' ?>>Admin SDA</option>
                                        <option value="admin_gedungg" <?= $user->role === 'admin_gedungg' ? 'selected' : '' ?>>Admin Gedung G</option>
                                        <option value="admin_heritage" <?= $user->role === 'admin_heritage' ? 'selected' : '' ?>>Admin Heritage</option>
                                        <option value="admin_auditorium" <?= $user->role === 'admin_auditorium' ? 'selected' : '' ?>>Admin Auditorium</option>
                                    </select>
                                </td>
                                
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm text-white" 
                                            style="background-color: #435EBE"
                                            onclick="showUserActivity(<?= $user->id ?>)">
                                            Aktivitas
                                        </button>
                                        <button class="btn btn-sm text-white"
                                            style="background-color: #AE445A"
                                            onclick="confirmUserDeletion(<?= $user->id ?>)">
                                            Hapus
                                        </button>
                                        <button class="btn btn-sm text-white"
                                            style="background-color:rgb(0, 255, 64)"
                                            onclick="editUser(<?= $user->id ?>, '<?= htmlspecialchars($user->fullname, ENT_QUOTES) ?>', '<?= htmlspecialchars($user->email, ENT_QUOTES) ?>', '<?= htmlspecialchars($user->unit_organisasi, ENT_QUOTES) ?>', '<?= htmlspecialchars($user->unit_kerja, ENT_QUOTES) ?>', '<?= $user->active ?>')">
                                            Edit
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal User Activity -->
<div class="modal fade" id="modalUserActivity" tabindex="-1" aria-labelledby="modalUserActivityLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUserActivityLabel">Detail Aktivitas Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title mb-3">Informasi Pengguna</h6>
                                <div id="userInfo"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <ul class="nav nav-tabs d-flex justify-content-between mb-4 border-bottom-0" id="activityTabs" role="tablist">
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link active w-100 rounded-0 border-bottom-0" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">
                                    Riwayat Login
                                </button>
                            </li>
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link w-100 rounded-0 border-bottom-0" id="peminjaman-tab" data-bs-toggle="tab" data-bs-target="#peminjaman" type="button" role="tab">
                                    Riwayat Peminjaman
                                </button>
                            </li>
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link w-100 rounded-0 border-bottom-0" id="pengembalian-tab" data-bs-toggle="tab" data-bs-target="#pengembalian" type="button" role="tab">
                                    Riwayat Pengembalian
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="activityTabContent">
                            <div class="tab-pane fade show active" id="login" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Waktu</th>
                                                <th>IP Address</th>
                                                <th>User Agent</th>
                                            </tr>
                                        </thead>
                                        <tbody id="loginHistory"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="peminjaman" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jenis</th>
                                                <th>Nama</th>
                                                <th>Tgl Pinjam</th>
                                                <th>Tgl Kembali</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="peminjamanHistory"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="pengembalian" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jenis</th>
                                                <th>Nama</th>
                                                <th>Tgl Pinjam</th>
                                                <th>Tgl Kembali</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pengembalianHistory"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add User -->
<div class="modal fade" id="modalAddUser" tabindex="-1" aria-labelledby="modalAddUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddUserLabel">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= route_to('register') ?>" method="post" class="users" id="registerForm">
                    <?= csrf_field() ?>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="fullname" placeholder="Fullname" required>
                        <label>Nama Lengkap</label>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                        <label>Username</label>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                        <label>Email</label>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-control" name="unit_organisasi" id="addUnitOrganisasi" required>
                            <option value="" disabled selected></option>
                            <option value="Setjen">Sekretariat Jenderal</option>
                            <option value="Itjen">Inspektorat Jenderal</option>
                            <option value="Ditjen Sumber Daya Air">Direktorat Jenderal Sumber Daya Air</option>
                            <option value="Ditjen Bina Marga">Direktorat Jenderal Bina Marga</option>
                            <option value="Ditjen Cipta Karya">Direktorat Jenderal Cipta Karya</option>
                            <option value="Ditjen Perumahan">Direktorat Jenderal Perumahan</option>
                            <option value="Ditjen Bina Konstruksi">Direktorat Jenderal Bina Konstruksi</option>
                            <option value="Ditjen Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan">Direktorat Jenderal Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan</option>
                            <option value="BPIW">Badan Pengembangan Infrastruktur Wilayah</option>
                            <option value="BPSDM">Badan Pengembangan Sumber Daya Manusia</option>
                            <option value="BPJT">Badan Pengatur Jalan Tol</option>
                        </select>
                        <label class="text-muted">Pilih Unit Organisasi</label>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-control" name="unit_kerja" id="addUnitKerja" required>
                            <option value="" disabled selected>Pilih Unit Kerja</option>
                        </select>
                        <label class="text-muted">Pilih Unit Kerja</label>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-control" name="role" required>
                            <option value="" disabled selected></option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="admin_gedungutama">Admin Gedung Utama</option>
                            <option value="admin_pusdatin">Admin Pusdatin</option>
                            <option value="admin_binamarga">Admin Bina Marga</option>
                            <option value="admin_ciptakarya">Admin Cipta Karya</option>
                            <option value="admin_sda">Admin SDA</option>
                            <option value="admin_gedungg">Admin Gedung G</option>
                            <option value="admin_heritage">Admin Heritage</option>
                            <option value="admin_auditorium">Admin Auditorium</option>
                        </select>
                        <label class="text-muted">Pilih Role</label>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" name="password" placeholder="Password" required>
                                <label class="text-muted">Password</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" name="pass_confirm" placeholder="Konfirmasi Password" required>
                                <label class="text-muted">Konfirmasi Password</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete User -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Anda yakin ingin menghapus User ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="deleteUser()">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-labelledby="modalEditUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditUserLabel">Edit Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" id="editUserId" name="id">

                    <div class="mb-3">
                        <label>Fullname</label>
                        <input type="text" class="form-control" id="editFullname" name="fullname" required>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                        <div class="invalid-feedback">Email harus menggunakan domain @pu.go.id</div>
                    </div>

                    <div class="mb-3">
                        <label>Unit Organisasi</label>
                        <select class="form-control" id="editUnitOrganisasi" name="unit_organisasi" required>
                            <option value="" disabled selected></option>
                            <option value="Setjen">Sekretariat Jenderal</option>
                            <option value="Itjen">Inspektorat Jenderal</option>
                            <option value="Ditjen Sumber Daya Air">Direktorat Jenderal Sumber Daya Air</option>
                            <option value="Ditjen Bina Marga">Direktorat Jenderal Bina Marga</option>
                            <option value="Ditjen Cipta Karya">Direktorat Jenderal Cipta Karya</option>
                            <option value="Ditjen Perumahan">Direktorat Jenderal Perumahan</option>
                            <option value="Ditjen Bina Konstruksi">Direktorat Jenderal Bina Konstruksi</option>
                            <option value="Ditjen Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan">Direktorat Jenderal Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan</option>
                            <option value="BPIW">Badan Pengembangan Infrastruktur Wilayah</option>
                            <option value="BPSDM">Badan Pengembangan Sumber Daya Manusia</option>
                            <option value="BPJT">Badan Pengatur Jalan Tol</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Unit Kerja</label>
                        <select class="form-control" id="editUnitKerja" name="unit_kerja" required>
                            <option value="" disabled selected>Pilih Unit Kerja</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select class="form-control" id="editStatus" name="active">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let currentUserId = null;

// Unit kerja mapping
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

// Populate unit kerja dropdown
function populateUnitKerja(unitOrganisasi, targetSelectId) {
    const unitKerjaSelect = document.getElementById(targetSelectId);
    if (!unitKerjaSelect) return;
    
    unitKerjaSelect.innerHTML = '<option value="" disabled selected>Pilih Unit Kerja</option>';
    
    if (unitOrganisasi in unitKerjaOptions) {
        unitKerjaOptions[unitOrganisasi].forEach(function(unitKerja) {
            const option = document.createElement('option');
            option.value = unitKerja;
            option.textContent = unitKerja;
            unitKerjaSelect.appendChild(option);
        });
    }
}

// Edit user function
function editUser(id, fullname, email, unit_organisasi, unit_kerja, active) {
    document.getElementById("editUserId").value = id;
    document.getElementById("editFullname").value = fullname;
    document.getElementById("editEmail").value = email;
    document.getElementById("editStatus").value = active;
    
    const editUnitOrganisasi = document.getElementById("editUnitOrganisasi");
    editUnitOrganisasi.value = unit_organisasi;
    
    populateUnitKerja(unit_organisasi, 'editUnitKerja');
    
    setTimeout(function() {
        const editUnitKerja = document.getElementById("editUnitKerja");
        editUnitKerja.value = unit_kerja;
        
        if (editUnitKerja.value !== unit_kerja && unit_kerja) {
            const newOption = document.createElement('option');
            newOption.value = unit_kerja;
            newOption.textContent = unit_kerja;
            newOption.selected = true;
            editUnitKerja.appendChild(newOption);
        }
    }, 100);
    
    const modal = new bootstrap.Modal(document.getElementById("modalEditUser"));
    modal.show();
}

// Show user activity (placeholder)
function showUserActivity(userId) {
    alert('Fitur aktivitas user belum diimplementasi');
}

// Confirm user deletion
function confirmUserDeletion(userId) {
    currentUserId = userId;
    const modal = new bootstrap.Modal(document.getElementById("deleteUserModal"));
    modal.show();
}

// Delete user
function deleteUser() {
    if (!currentUserId) return;
    alert('Fitur delete user belum diimplementasi');
    const modal = bootstrap.Modal.getInstance(document.getElementById("deleteUserModal"));
    if (modal) modal.hide();
}

// DOM ready
document.addEventListener("DOMContentLoaded", function() {
    // Search and sort functionality
    const searchInput = document.getElementById("searchFullname");
    const sortSelect = document.getElementById("sortOrder");
    const tableBody = document.querySelector("#usersTable tbody");
    
    if (tableBody) {
        let originalRows = Array.from(tableBody.querySelectorAll("tr"));
        
        function filterAndSort() {
            let filteredRows = [...originalRows];
            const searchText = searchInput ? searchInput.value.toLowerCase() : '';
            const sortOrder = sortSelect ? sortSelect.value : '';

            // Filter
            if (searchText !== "") {
                filteredRows = filteredRows.filter(function(row) {
                    const fullname = row.cells[1].textContent.trim().toLowerCase();
                    return fullname.includes(searchText);
                });
            }

            // Sort
            if (sortOrder === "asc" || sortOrder === "desc") {
                filteredRows.sort(function(a, b) {
                    const nameA = a.cells[1].textContent.trim().toLowerCase();
                    const nameB = b.cells[1].textContent.trim().toLowerCase();
                    return (sortOrder === "asc" ? nameA.localeCompare(nameB) : nameB.localeCompare(nameA));
                });
            }

            // Re-render
            tableBody.innerHTML = "";
            filteredRows.forEach(function(row, index) {
                row.cells[0].textContent = index + 1;
                tableBody.appendChild(row);
            });
        }

        if (searchInput) searchInput.addEventListener("input", filterAndSort);
        if (sortSelect) sortSelect.addEventListener("change", filterAndSort);
    }
    
    // Unit organisasi change handlers
    const addUnitOrgSelect = document.getElementById('addUnitOrganisasi');
    if (addUnitOrgSelect) {
        addUnitOrgSelect.addEventListener('change', function() {
            populateUnitKerja(this.value, 'addUnitKerja');
        });
    }
    
    const editUnitOrgSelect = document.getElementById('editUnitOrganisasi');
    if (editUnitOrgSelect) {
        editUnitOrgSelect.addEventListener('change', function() {
            populateUnitKerja(this.value, 'editUnitKerja');
        });
    }
    
    // Email validation
    const editEmailInput = document.getElementById("editEmail");
    if (editEmailInput) {
        editEmailInput.addEventListener("input", function() {
            let emailPattern = /^[a-zA-Z0-9._%+-]+@pu\.go\.id$/;
            let errorMessage = this.nextElementSibling;

            if (!emailPattern.test(this.value)) {
                this.classList.add("is-invalid");
                if (errorMessage) errorMessage.style.display = "block";
            } else {
                this.classList.remove("is-invalid");
                if (errorMessage) errorMessage.style.display = "none";
            }
        });
    }

    // Form submit
    const editUserForm = document.getElementById("editUserForm");
    if (editUserForm) {
        editUserForm.addEventListener("submit", function(event) {
            event.preventDefault();
            
            let emailInput = document.getElementById("editEmail");
            let emailPattern = /^[a-zA-Z0-9._%+-]+@pu\.go\.id$/;
            if (emailInput && !emailPattern.test(emailInput.value)) {
                alert("Email harus menggunakan domain @pu.go.id");
                return;
            }

            const userId = document.getElementById("editUserId").value;
            const formData = new FormData(this);
            
            fetch("/admin/users/update/" + userId, {
                method: "POST",
                headers: { "X-Requested-With": "XMLHttpRequest" },
                body: formData,
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    alert("User berhasil diperbarui!");
                    window.location.reload();
                } else {
                    alert("Gagal memperbarui user: " + (data.message || "Unknown error"));
                }
            })
            .catch(function(error) {
                alert("Terjadi kesalahan saat memperbarui user.");
            })
            .finally(function() {
                const modal = bootstrap.Modal.getInstance(document.getElementById("modalEditUser"));
                if (modal) modal.hide();
            });
        });
    }
});
</script>

<?= $this->endSection() ?>