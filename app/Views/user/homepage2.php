
<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <!-- Improved Header Section -->
    <div class="d-flex justify-content-between align-items-center dashboard-header">
        <h2 class="fw-bold">Manajemen Kendaraan</h2>
        <nav aria-label="breadcrumb" class="breadcrumb-container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#"><i class="bi bi-house-door"></i> Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kendaraan</li>
            </ol>
        </nav>
    </div>

    <!-- Filter Section (New) -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="filterStatus" class="form-label">Status Kendaraan</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Tersedia">Tersedia</option>
                        <option value="Dalam Verifikasi">Dalam Verifikasi</option>
                        <option value="Dipinjam">Dipinjam</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="filterKondisi" class="form-label">Kondisi</label>
                    <select id="filterKondisi" class="form-select">
                        <option value="">Semua Kondisi</option>
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="filterKategori" class="form-label">Kategori</label>
                    <select id="filterKategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        <option value="KDJ">Kendaraan Dinamis Jalan</option>
                        <option value="KDO">Kendaraan Dinamis Off-road</option>
                        <option value="KDF">Kendaraan Dinamis Fasilitas</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button id="resetFilter" class="btn btn-outline-secondary w-100" style="margin-top: 2rem;">
                        <i class="bi bi-x-circle"></i> Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Card Grid -->
    <section class="section">
        <div class="vehicle-grid">
            <?php foreach ($aset as $item): ?>
                <div class="vehicle-card">
                    <div class="vehicle-image">
                        <?php 
                        $images = json_decode($item['gambar_mobil'], true);
                        $images = is_array($images) ? $images : [$item['gambar_mobil']];
                        $mainImage = !empty($images) ? $images[0] : null;
                        ?>
                        <?php if (!empty($mainImage) && file_exists(ROOTPATH . 'public/uploads/images/' . $mainImage)): ?>
                            <img src="<?= base_url('/uploads/images/' . $mainImage) ?>"
                                class="image-preview-trigger" 
                                data-images='<?= htmlspecialchars(json_encode($images)) ?>'
                                alt="<?= $item['merk'] ?>"
                                style="cursor: pointer;">
                        <?php else: ?>
                            <img src="<?= base_url('/assets/images/vehicle-placeholder.jpg') ?>" 
                                alt="<?= $item['merk'] ?>">
                        <?php endif; ?>
                        
                        <?php
                        $statusClass = '';
                        $statusIcon = '';
                        switch(strtolower($item['status_pinjam'])) {
                            case 'tersedia':
                                $statusClass = 'bg-success';
                                $statusIcon = 'bi-check-circle';
                                break;
                            case 'pending':
                            case 'dalam verifikasi':
                                $statusClass = 'bg-warning';
                                $statusIcon = 'bi-clock';
                                break;
                            default:
                                $statusClass = 'bg-info';
                                $statusIcon = 'bi-car-front';
                        }
                        ?>
                        <span class="status-badge <?= $statusClass ?>">
                            <i class="bi <?= $statusIcon ?>"></i> <?= $item['status_pinjam'] ?>
                        </span>
                    </div>

                    <div class="vehicle-info">
                        <h5 class="vehicle-title">
                            <?= $item['merk'] ?>
                        </h5>
                        
                        <div class="property-grid">
                            <div class="property-card primary">
                                <span class="property-label">No. Polisi</span>
                                <p class="property-value"><?= $item['no_polisi'] ?></p>
                            </div>
                            <div class="property-card info">
                                <span class="property-label">Tahun</span>
                                <p class="property-value"><?= $item['tahun_pembuatan'] ?></p>
                            </div>
                            <div class="property-card success">
                                <span class="property-label">Kapasitas</span>
                                <p class="property-value"><?= $item['kapasitas'] ?> Orang</p>
                            </div>
                            <div class="property-card warning">
                                <span class="property-label">Tipe</span>
                                <p class="property-value"><?= $item['kategori_id'] ?></p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 small d-flex align-items-center">
                                <span class="text-muted me-2"><i class="bi bi-upc"></i> Kode:</span>
                                <span class="fw-medium"><?= $item['kode_barang'] ?></span>
                            </p>
                            
                            <?php if (!empty($item['tanggal_kembali'])): ?>
                                <p class="mb-1 small d-flex align-items-center">
                                    <span class="text-muted me-2"><i class="bi bi-calendar-check"></i> Kembali:</span>
                                    <span class="fw-medium"><?= date('d/m/Y', strtotime($item['tanggal_kembali'])) ?></span>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-2 mb-3">
                            <?php
                            $kondisiClass = '';
                            $kondisiIcon = '';
                            switch($item['kondisi']) {
                                case 'Baik':
                                    $kondisiClass = 'bg-success';
                                    $kondisiIcon = 'bi-check-circle';
                                    break;
                                case 'Rusak Ringan':
                                    $kondisiClass = 'bg-warning';
                                    $kondisiIcon = 'bi-exclamation-triangle';
                                    break;
                                default:
                                    $kondisiClass = 'bg-danger';
                                    $kondisiIcon = 'bi-x-circle';
                            }
                            ?>
                            <span class="badge <?= $kondisiClass ?>">
                                <i class="bi <?= $kondisiIcon ?>"></i> <?= $item['kondisi'] ?>
                            </span>

                            <?php if (!empty($item['keterangan'])): ?>
                                <span class="badge bg-danger" data-bs-toggle="tooltip" title="<?= $item['keterangan'] ?>">
                                    <i class="bi bi-info-circle"></i> Info
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex flex-column gap-2">
                            <?php if ($item['status_pinjam'] === 'Tersedia' || $item['status_pinjam'] === 'Dalam Verifikasi'): ?>
                                <?php if ($item['status_pinjam'] === 'Dalam Verifikasi'): ?>
                                    <button type="button" class="btn btn-secondary w-100" disabled>
                                        <i class="bi bi-clock"></i> Menunggu Verifikasi
                                    </button>
                                <?php else: ?>
                                    <?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
                                        <!-- Admin buttons here if needed -->
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-primary w-100" onclick="openPeminjamanModal('<?= $item['id'] ?>')">
                                        <i class="bi bi-plus-circle"></i> Pinjam Kendaraan
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <button type="button" class="btn btn-info w-100" onclick="openPengembalianModal('<?= $item['id'] ?>')">
                                    <i class="bi bi-box-arrow-in-down"></i> Kembalikan Kendaraan
                                </button>

                                <?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
                                    <div class="mt-3">
                                        <h6 class="fw-semibold mb-2">Dokumen Peminjaman:</h6>
                                        <?php if (!empty($item['surat_permohonan']) && file_exists(ROOTPATH . 'public/uploads/documents/' . $item['surat_permohonan'])): ?>
                                            <a href="<?= base_url('/uploads/documents/' . $item['surat_permohonan']) ?>"
                                                target="_blank" class="document-link">
                                                <i class="bi bi-file-earmark-pdf"></i> Surat Permohonan
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($item['surat_jalan_admin']) && file_exists(ROOTPATH . 'public/uploads/documents/' . $item['surat_jalan_admin'])): ?>
                                            <a href="<?= base_url('/uploads/documents/' . $item['surat_jalan_admin']) ?>"
                                                target="_blank" class="document-link">
                                                <i class="bi bi-file-earmark-pdf"></i> Surat Jalan
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
                                <div class="d-flex gap-2 mt-2">
                                    <button type="button" class="btn btn-warning flex-grow-1" onclick="openEditModal('<?= $item['id'] ?>')">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-danger flex-grow-1" onclick="deleteAset('<?= $item['id'] ?>')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
                <!-- Add New Vehicle Card -->
                <div class="vehicle-card" style="background-color: #f8f9fe; border: 2px dashed var(--gray-400);">
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 p-4">
                        <div class="mb-3 text-center">
                            <i class="bi bi-plus-circle" style="font-size: 3rem; color: var(--primary);"></i>
                            <h5 class="mt-3 fw-bold">Tambah Kendaraan Baru</h5>
                            <p class="text-muted">Klik untuk menambahkan kendaraan baru ke sistem</p>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="openAddModal()">
                            <i class="bi bi-plus-lg"></i> Tambah Kendaraan
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<!-- Improved Modal Styling - Pengembalian Modal -->
<div class="modal fade" id="modalPengembalian" tabindex="-1" aria-labelledby="modalPengembalianLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPengembalianLabel">
                    <i class="bi bi-box-arrow-in-down me-2"></i>Form Pengembalian Kendaraan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formPengembalian" action="<?= base_url('/AsetKendaraan/kembali'); ?>" method="post"
                class="kembali" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <input type="hidden" id="kendaraan_id_hidden" name="kendaraan_id" value="">

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nama_penanggung_jawab">Nama Penanggung Jawab</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="nama_penanggung_jawab"
                                        name="nama_penanggung_jawab" required readonly>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="nip_nrp">NIP / NRP</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                    <input type="text" class="form-control" id="nip_nrp" name="nip_nrp" required readonly>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pangkat_golongan">Pangkat / Golongan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-award"></i></span>
                                    <input type="text" class="form-control" id="pangkat_golongan" name="pangkat_golongan"
                                        required readonly>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="jabatan">Jabatan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                                    <input type="text" class="form-control" id="jabatan" name="jabatan" required readonly>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="unit_organisasi">Unit Organisasi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="text" class="form-control" id="unit_organisasi" name="unit_organisasi"
                                        required readonly>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="surat_pengembalian">Surat Pengembalian (PDF)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-file-earmark-pdf"></i></span>
                                    <input type="file" class="form-control" id="surat_pengembalian"
                                        name="surat_pengembalian" accept="application/pdf" required>
                                </div>
                                <small class="text-muted mt-1">Format PDF, maksimal 2MB</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="kendaraan_id">Kendaraan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-car-front"></i></span>
                                    <select class="form-control" id="kendaraan_id_kembali" name="kendaraan_id" required>
                                        <option value="" disabled selected>Pilih Kendaraan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pengemudi">Nama Pengemudi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                    <input type="text" class="form-control" id="pengemudi" name="pengemudi" required readonly>
                                </div>
                            </div>

     

                            <div class="form-group mb-3">
                                <label for="no_hp">Nomor HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" required readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="tanggal_pinjam">Tanggal Pinjam</label>
                                <input type="date" class="form-control" id="tanggal_pinjam" name="tanggal_pinjam"
                                    readonly required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="tanggal_kembali">Tanggal Kembali</label>
                                <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali"
                                    required min="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="form-group mb-3">
                                <label for="berita_acara_pengembalian">Berita Acara Pengembalian (PDF)</label>
                                <input type="file" class="form-control" id="berita_acara_pengembalian"
                                    name="berita_acara_pengembalian" accept="application/pdf" required>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Konfirmasi Pengembalian</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="modalPeminjaman" tabindex="-1" aria-labelledby="modalPeminjamanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPeminjamanLabel">Form Peminjaman Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formPeminjaman" action="<?= base_url('/AsetKendaraan/pinjam'); ?>" method="post" class="pinjam"
                enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nama_penanggung_jawab">Nama Penanggung Jawab</label>
                                <input type="text" class="form-control" id="nama_penanggung_jawab"
                                    name="nama_penanggung_jawab" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="nip_nrp">NIP / NRP</label>
                                <input type="text" class="form-control" id="nip_nrp" name="nip_nrp" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="jabatan">Unit Organisasi</label>
                                <select
                                    class="form-control <?php if (session('errors.unit_organisasi')): ?>is-invalid<?php endif ?>"
                                    name="unit_organisasi">
                                    <option value="" class="text-muted" disabled selected>Pilih</option>
                                    <option value="Setjen">Sekretariat Jenderal</option>
                                    <option value="Itjen">Inspektorat Jenderal</option>
                                    <option value="Ditjen Sumber Daya Air">Direktorat Jenderal Sumber Daya Air</option>
                                    <option value="Ditjen Bina Marga">Direktorat Jenderal Bina Marga</option>
                                    <option value="Ditjen Cipta Karya">Direktorat Jenderal Cipta Karya</option>
                                    <option value="Ditjen Perumahan">Direktorat Jenderal Perumahan</option>
                                    <option value="Ditjen Bina Konstruksi">Direktorat Jenderal Bina Konstruksi</option>
                                    <option value="Ditjen Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan">
                                        Direktorat
                                        Jenderal Pembiayaan Infrastruktur Pekerjaan Umum dan
                                        Perumahan</option>
                                    <option value="BPIW">Badan Pengembangan Infrastruktur Wilayah</option>
                                    <option value="BPSDM">Badan Pengembangan Sumber Daya Manusia</option>
                                    <option value="BPJT">Badan Pengatur Jalan Tol</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="jabatan">Jabatan</label>
                                <select
                                    class="form-control <?php if (session('errors.jabatan')): ?>is-invalid<?php endif ?>"
                                    name="jabatan">
                                    <option value="" class="text-muted" disabled selected>Pilih</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pangkat_golongan"> Pangkat / Golongan</label>
                                <select class="form-control" name="pangkat_golongan">
                                    <option value="" class="text-muted" disabled selected>Pilih</option>
                                    <option value="IV A">IV A - Pembina</option>
                                    <option value="IV B">IV B - Pembina Tingkat 1</option>
                                    <option value="IV C">IV C - Pembina Tingkat Muda</option>
                                    <option value="IV D">IV D - Pembina Tingkat Madya</option>
                                    <option value="IV E">IV E - Pembina Utama</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="kendaraan_id">Pilih Kendaraan</label>
                                <select class="form-control" id="kendaraan_id_pinjam" name="kendaraan_id" required>
                                    <option value="" disabled selected>Pilih Kendaraan</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pengemudi">Nama Pengemudi</label>
                                <input type="text" class="form-control -lg" id="pengemudi" name="pengemudi" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="no_hp">Nomor HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="tanggal_pinjam">Tanggal Pinjam</label>
                                <input type="date" class="form-control" id="tanggal_pinjam" name="tanggal_pinjam" 
                                    required min="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="form-group mb-3">
                                <label for="tanggal_kembali">Tanggal Kembali</label>
                                <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali"
                                    required min="<?= date('Y-m-d') ?>">
                            </div>

                            <!-- <div class="form-group mb-3">
                                <label for="surat_pemakaian">Surat Pemakaian (PDF)</label>
                                <input type="file" class="form-control" id="surat_pemakaian" name="surat_pemakaian"
                                    accept="application/pdf" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="berita_acara_penyerahan">Berita Acara Penyerahan (PDF)</label>
                                <input type="file" class="form-control" id="berita_acara_penyerahan"
                                    name="berita_acara_penyerahan" accept="application/pdf" required>
                            </div> -->
                        </div>


                        <div class="form-group mb-3 mt-auto order-last">
                            <label for="urusan_kedinasan">Urusan Kedinasan</label>
                            <textarea class="form-control" id="urusan_kedinasan" name="urusan_kedinasan" rows="3"
                                required></textarea>
                        </div>

                        <div class="form-group mb-3 mt-auto order-last">
                            <label for="surat_permohonan">Surat Permohonan (PDF)</label>
                            <input type="file" class="form-control" id="surat_permohonan" name="surat_permohonan"
                                accept="application/pdf" required>
                            <small class="text-muted">Max 2MB</small>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="modalEditAset" tabindex="-1" aria-labelledby="modalEditAsetLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditAsetLabel">Form Edit Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditAset" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_kategori_id">Kategori</label>
                                <select class="form-control" id="kategori_id" name="kategori_id" required>
                                    <option value="" class="text-muted" disabled selected> Pilih Kategori Aset</option>
                                    <option class="fw-bold text-dark" value="KDJ">Kendaraan Dinamis Jalan (KDJ)</option>
                                    <option class="text-muted" disabled selected>Sedan, Hatchback, dan SUV</option>
                                    <option class="fw-bold text-dark" value="KDO">Kendaraan Dinamis Off-road (KDO)</option>
                                    <option class="text-muted" disabled selected>Bus, Truk, dan Kendaraan Box</option>
                                    <option class="fw-bold text-dark" value="KDF">Kendaraan Dinamis Fasilitas (KDF)</option>
                                    <option class="text-muted" disabled selected>Ambulance, Mobil Derek, dan Mobil Crane</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_no_sk_psp">No SK PSP</label>
                                <input type="text" class="form-control" id="edit_no_sk_psp" name="no_sk_psp" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_kode_barang">Kode Barang</label>
                                <input type="text" class="form-control" id="edit_kode_barang" name="kode_barang"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="edit_merk">Merk</label>
                                <input type="text" class="form-control" id="edit_merk" name="merk" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_tahun_pembuatan">Tahun Pembuatan</label>
                                <input type="number" class="form-control" id="edit_tahun_pembuatan"
                                    name="tahun_pembuatan">
                            </div>
                            <div class="form-group">
                                <label for="edit_kapasitas">Kapasitas</label>
                                <input type="number" class="form-control" id="edit_kapasitas" name="kapasitas">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_no_polisi">Nomor Polisi</label>
                                <input type="text" class="form-control" id="edit_no_polisi" name="no_polisi">
                            </div>
                            <div class="form-group">
                                <label for="edit_no_bpkb">No BPKB</label>
                                <input type="number" class="form-control" id="edit_no_bpkb" name="no_bpkb">
                            </div>
                            <div class="form-group">
                                <label for="edit_no_stnk">No STNK</label>
                                <input type="number" class="form-control" id="edit_no_stnk" name="no_stnk">
                            </div>
                            <div class="form-group">
                                <label for="edit_no_rangka">No Rangka</label>
                                <input type="number" class="form-control" id="edit_no_rangka" name="no_rangka">
                            </div>
                            <div class="form-group">
                                <label for="edit_kondisi">Kondisi</label>
                                <select class="form-control" id="edit_kondisi" name="kondisi">
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_gambar_mobil">Gambar Mobil (JPG/PNG)</label>
                                <input type="file" class="form-control" id="edit_gambar_mobil" name="gambar_mobil"
                                    accept="image/jpeg,image/png">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                    </div>
                </div>
                <div class="thumbnail-container d-flex justify-content-center mt-3">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="trackingMapModal" tabindex="-1" aria-labelledby="trackingMapLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="trackingMapLabel">Peta Lokasi Kendaraan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="height: 500px;">
        <div id="trackingMap" style="height: 100%;"></div>
      </div>
    </div>
  </div>
</div>

<style>
    /* Modern Color Scheme */
    :root {
        --primary: #4361ee;
        --primary-light: #4895ef;
        --secondary: #3f37c9;
        --success: #4cc9f0;
        --success-dark: #4895ef;
        --danger: #f72585;
        --warning: #f8961e;
        --info: #90e0ef;
        --light: #f8f9fa;
        --dark: #212529;
        --gray-100: #f8f9fa;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        --gray-400: #ced4da;
        --gray-800: #343a40;
        --body-bg: #f5f7fa;
    }

    body {
        background-color: var(--body-bg);
    }

    /* Dashboard Header */
    .dashboard-header {
        padding: 1.5rem 0;
        border-bottom: 1px solid var(--gray-300);
        margin-bottom: 1.5rem;
    }

    .dashboard-header h2 {
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 0;
        position: relative;
    }
    
    .dashboard-header h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 60px;
        height: 4px;
        background: var(--primary);
        border-radius: 2px;
    }

    /* Enhanced Card Design */
    .vehicle-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: none;
        margin-bottom: 1.5rem;
        height: 100%;
    }
    
    .vehicle-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.12), 0 6px 6px rgba(0,0,0,0.1);
    }
    
    .vehicle-image {
        height: 200px;
        position: relative;
        overflow: hidden;
    }
    
    .vehicle-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .vehicle-image img:hover {
        transform: scale(1.05);
    }
    
    .status-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 6px 12px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    }
    
    .vehicle-info {
        padding: 1.5rem;
    }
    
    .vehicle-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--dark);
    }
    
    /* Property Cards */
    .property-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 1rem;
    }
    
    .property-card {
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: all 0.2s ease;
    }
    
    .property-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    }
    
    .property-card.primary {
        border-left: 4px solid var(--primary);
    }
    
    .property-card.info {
        border-left: 4px solid var(--info);
    }
    
    .property-card.success {
        border-left: 4px solid var(--success);
    }
    
    .property-card.warning {
        border-left: 4px solid var(--warning);
    }
    
    .property-label {
        font-size: 0.7rem;
        color: var(--gray-800);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 3px;
    }
    
    .property-value {
        font-size: 0.9rem;
        font-weight: 600;
        margin: 0;
    }
    
    /* Button Styles */
    .btn {
        border-radius: 50px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .btn i {
        font-size: 1rem;
    }
    
    .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    
    .btn-primary:hover {
        background-color: var(--secondary);
        border-color: var(--secondary);
        box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3);
    }
    
    .btn-info {
        background-color: var(--info);
        border-color: var(--info);
        color: var(--dark);
    }
    
    .btn-info:hover {
        background-color: var(--success-dark);
        border-color: var(--success-dark);
        color: white;
        box-shadow: 0 4px 8px rgba(76, 201, 240, 0.3);
    }
    
    .btn-warning {
        background-color: var(--warning);
        border-color: var(--warning);
    }
    
    .btn-warning:hover {
        background-color: #f3722c;
        border-color: #f3722c;
        box-shadow: 0 4px 8px rgba(248, 150, 30, 0.3);
    }
    
    .btn-danger {
        background-color: var(--danger);
        border-color: var(--danger);
    }
    
    .btn-danger:hover {
        background-color: #b5179e;
        border-color: #b5179e;
        box-shadow: 0 4px 8px rgba(247, 37, 133, 0.3);
    }
    
    /* Card Footer */
    .card-footer {
        background-color: rgba(255, 255, 255, 0.8);
        border-top: 1px solid var(--gray-200);
        padding: 1rem 1.5rem;
    }
    
    /* Responsive Card Grid */
    .vehicle-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }
    
    /* Breadcrumb improvements */
    .breadcrumb-container {
        margin-bottom: 1rem;
    }
    
    .breadcrumb {
        background-color: transparent;
        padding: 0.5rem 0;
    }
    
    .breadcrumb-item a {
        color: var(--primary);
        font-weight: 500;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    
    .breadcrumb-item a:hover {
        color: var(--secondary);
    }
    
    .breadcrumb-item.active {
        color: var(--gray-800);
        font-weight: 600;
    }
    
    /* Modal Enhancements */
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .modal-header {
        border-bottom: 1px solid var(--gray-200);
        padding: 1.25rem 1.5rem;
        background-color: var(--gray-100);
    }
    
    .modal-header .modal-title {
        font-weight: 700;
        color: var(--primary);
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        border-top: 1px solid var(--gray-200);
        padding: 1.25rem 1.5rem;
    }
    
    /* Form Elements */
    .form-control {
        border-radius: 8px;
        border: 1px solid var(--gray-300);
        padding: 0.6rem 1rem;
        transition: all 0.2s ease;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        border-color: var(--primary);
    }
    
    .form-group label {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        color: var(--gray-800);
    }
    
    /* Document links */
    .document-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        background-color: var(--gray-100);
        text-decoration: none;
        color: var(--primary);
        font-weight: 500;
        transition: all 0.2s ease;
        margin-bottom: 0.5rem;
    }
    
    .document-link:hover {
        background-color: var(--gray-200);
        color: var(--secondary);
    }
    
    /* Carousel Improvements */
    #imageCarousel .carousel-item img {
        border-radius: 8px;
        max-height: 500px;
        object-fit: contain;
    }
    
    .thumbnail-container {
        display: flex;
        overflow-x: auto;
        gap: 10px;
        padding: 10px 0;
        scroll-behavior: smooth;
    }
    
    .thumbnail-container img {
        height: 60px;
        width: 80px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }
    
    .thumbnail-container img:hover,
    .thumbnail-container img.active {
        border-color: var(--primary);
        transform: scale(1.05);
    }

    /* Additional responsive adjustments */
    @media (max-width: 991.98px) {
        .property-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 767.98px) {
        .dashboard-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .breadcrumb-container {
            margin-top: 1rem;
        }
        
        .vehicle-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    const BASE_URL = '<?= base_url() ?>';
</script>

<?= $this->endSection() ?>