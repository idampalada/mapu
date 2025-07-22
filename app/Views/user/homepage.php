<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <!-- Modern Header with Gradient Background -->
    <div class="header-section py-4 px-3 mb-4 rounded-lg" style="background: linear-gradient(135deg, #2D3748 0%, #1E293B 100%);">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="text-white mb-0 display-6 fw-bold">Kendaraan</h2>
                <p class="text-light mb-0 opacity-75">Kelola dan pantau aset kendaraan dengan mudah</p>
            </div>
            <div class="col-md-6">
                <nav aria-label="breadcrumb" class="d-flex justify-content-md-end">
                    <ol class="breadcrumb mb-0 bg-transparent py-2 px-3 rounded" style="background-color: rgba(255,255,255,0.1);">
                        <li class="breadcrumb-item"><a href="#" class="text-light">Beranda</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Kendaraan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="mb-4 p-3 bg-white rounded-lg shadow-sm">
        <div class="row align-items-center g-3">
            <div class="col-md-3">
                <select class="form-select form-select-sm border-0 bg-light" id="filterKategori">
                    <option value="">Semua Kategori</option>
                    <option value="KDJ">Kendaraan Dinamis Jalan</option>
                    <option value="KDO">Kendaraan Dinamis Off-road</option>
                    <option value="KDF">Kendaraan Dinamis Fasilitas</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm border-0 bg-light" id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="Tersedia">Tersedia</option>
                    <option value="Dipinjam">Dipinjam</option>
                    <option value="Verifikasi">Dalam Verifikasi</option>
                </select>
            </div>
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-0 bg-light" placeholder="Cari kendaraan..." id="searchKendaraan">
                </div>
            </div>
            <div class="col-md-2 text-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary active"><i class="bi bi-grid-3x3-gap"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-primary"><i class="bi bi-list"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle Cards Section -->
    <div class="vehicles-container">
        <div class="row g-4">
            <?php foreach ($aset as $item): ?>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 vehicle-card">
                    <div class="card border-0 h-100 vehicle-item" style="border-radius: 16px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                        <!-- Card Header with Image and Badge -->
                        <div class="position-relative vehicle-image-container">
                            <?php 
                            $images = json_decode($item['gambar_mobil'], true);
                            $images = is_array($images) ? $images : [$item['gambar_mobil']];
                            $mainImage = !empty($images) ? $images[0] : null;
                            ?>
                            <div class="vehicle-image-wrapper" style="height: 180px; overflow: hidden;">
                                <?php if (!empty($mainImage) && file_exists(ROOTPATH . 'public/uploads/images/' . $mainImage)): ?>
                                    <img src="<?= base_url('/uploads/images/' . $mainImage) ?>"
                                        class="w-100 h-100 object-fit-cover image-preview-trigger" 
                                        data-images='<?= htmlspecialchars(json_encode($images)) ?>'
                                        alt="<?= $item['merk'] ?>"
                                        style="cursor: pointer; transition: transform 0.5s;">
                                <?php else: ?>
                                    <img src="<?= base_url('/assets/images/faces/1.jpg') ?>" 
                                        class="w-100 h-100 object-fit-cover"
                                        alt="<?= $item['merk'] ?>"
                                        style="transition: transform 0.5s;">
                                <?php endif; ?>
                            </div>
                            
                            <!-- Status Badge & Condition Indicator -->
                            <div class="position-absolute top-0 end-0 p-3">
                                <?php 
                                $statusClass = '';
                                switch($item['status_pinjam']) {
                                    case 'Tersedia':
                                        $statusClass = 'bg-success';
                                        $statusIcon = 'bi-check-circle-fill';
                                        break;
                                    case 'Pending':
                                    case 'Dalam Verifikasi':
                                        $statusClass = 'bg-warning';
                                        $statusIcon = 'bi-clock-fill';
                                        break;
                                    default:
                                        $statusClass = 'bg-info';
                                        $statusIcon = 'bi-arrow-repeat';
                                }
                                ?>
                                <span class="badge <?= $statusClass ?> pill-badge">
                                    <i class="bi <?= $statusIcon ?> me-1"></i>
                                    <?= $item['status_pinjam'] ?>
                                </span>
                            </div>
                            
                            <!-- Condition Indicator (at bottom of image) -->
                            <div class="position-absolute bottom-0 start-0 p-3 w-100" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                                <h5 class="text-white mb-0 text-shadow fw-bold"><?= $item['merk'] ?></h5>
                                <div class="d-flex mt-1">
                                    <span class="badge <?= $item['kondisi'] === 'Baik' ? 'bg-success' :
                                        ($item['kondisi'] === 'Rusak Ringan' ? 'bg-warning' : 'bg-danger') ?> me-2">
                                        <?= $item['kondisi'] ?>
                                    </span>
                                    
                                    <?php if (!empty($item['keterangan'])): ?>
                                        <span class="badge bg-danger" data-bs-toggle="tooltip" title="<?= $item['keterangan'] ?>">
                                            <i class="bi bi-info-circle"></i> Info
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                       <div class="card-body">
    <!-- Primary Details -->
    <div class="vehicle-details mb-3">
        <div class="row g-3">
            <!-- No Polisi -->
            <div class="col-6">
                <div class="d-flex align-items-center">
                    <div class="me-2 text-primary fs-5"><i class="bi bi-car-front"></i></div>
                    <div>
                        <small class="text-muted">No. Polisi</small>
                        <div class="fw-medium"><?= $item['no_polisi'] ?></div>
                    </div>
                </div>
            </div>

            <!-- Tahun -->
            <div class="col-6">
                <div class="d-flex align-items-center">
                    <div class="me-2 text-info fs-5"><i class="bi bi-calendar3"></i></div>
                    <div>
                        <small class="text-muted">Tahun</small>
                        <div class="fw-medium"><?= $item['tahun_pembuatan'] ?></div>
                    </div>
                </div>
            </div>

            <!-- Kapasitas -->
            <div class="col-6">
                <div class="d-flex align-items-center">
                    <div class="me-2 text-success fs-5"><i class="bi bi-people"></i></div>
                    <div>
                        <small class="text-muted">Kapasitas</small>
                        <div class="fw-medium"><?= $item['kapasitas'] ?> Orang</div>
                    </div>
                </div>
            </div>

            <!-- Kode Barang -->
            <div class="col-6">
                <div class="d-flex align-items-center">
                    <div class="me-2 text-primary fs-5"><i class="bi bi-upc-scan"></i></div>
                    <div>
                        <small class="text-muted">Kode Barang</small>
                        <div class="fw-medium"><?= $item['kode_barang'] ?></div>
                    </div>
                </div>
            </div>

            <!-- Tipe -->
            <div class="col-6">
                <div class="d-flex align-items-center">
                    <div class="me-2 text-warning fs-5"><i class="bi bi-tag"></i></div>
                    <div>
                        <small class="text-muted">Tipe</small>
                        <div class="fw-medium"><?= $item['kategori_id'] ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tanggal Kembali -->
        <?php if (!empty($item['tanggal_kembali'])): ?>
        <div class="mt-3 p-2 rounded text-center" style="background-color: #f0f8ff; border-left: 3px solid #0d6efd;">
            <small class="text-muted">Tanggal Kembali</small>
            <div class="fw-bold"><?= date('d/m/Y', strtotime($item['tanggal_kembali'])) ?></div>
        </div>
        <?php endif; ?>
    </div>
</div>

                        
                        <!-- Card Footer with Action Buttons -->
                        <div class="card-footer bg-white border-0 pt-0">
                            <div class="d-grid">
                                <div class="d-flex flex-column gap-2">
                                    <?php if ($item['status_pinjam'] === 'Tersedia' || $item['status_pinjam'] === 'Dalam Verifikasi'): ?>
                                        <?php if ($item['status_pinjam'] === 'Dalam Verifikasi'): ?>
                                            <button type="button" class="btn btn-light btn-sm rounded-pill" disabled>
                                                <i class="bi bi-clock"></i> Menunggu Verifikasi
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-primary btn-sm rounded-pill action-button"
                                            onclick="openPeminjamanModal('<?= $item['id'] ?>')">
                                                <i class="bi bi-plus-circle"></i> Pinjam Kendaraan
                                            </button>
                                        <?php endif; ?>
                                                                            <button type="button" class="btn btn-success btn-sm rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-1"
                                        onclick="trackKendaraan('<?= $item['no_polisi'] ?>')">
                                        <i class="bi bi-geo-alt"></i> Status
                                    </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-info btn-sm rounded-pill action-button"
                                            onclick="openPengembalianModal('<?= $item['id'] ?>')">
                                            <i class="bi bi-box-arrow-in-down"></i> Kembalikan Kendaraan
                                        </button>

                                        <?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
                                            <div class="mt-2 document-section">
                                                <div class="document-header px-2 py-1 rounded bg-light">
                                                    <small class="fw-medium"><i class="bi bi-file-earmark"></i> Dokumen</small>
                                                </div>
                                                <div class="document-links mt-1">
                                                    <?php if (!empty($item['surat_permohonan']) && file_exists(ROOTPATH . 'public/uploads/documents/' . $item['surat_permohonan'])): ?>
                                                        <a href="<?= base_url('/uploads/documents/' . $item['surat_permohonan']) ?>"
                                                            target="_blank" class="btn btn-sm btn-outline-primary mb-1 w-100 rounded-pill btn-document">
                                                            <i class="bi bi-file-earmark-pdf"></i> Surat Permohonan
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if (!empty($item['surat_jalan_admin']) && file_exists(ROOTPATH . 'public/uploads/documents/' . $item['surat_jalan_admin'])): ?>
                                                        <a href="<?= base_url('/uploads/documents/' . $item['surat_jalan_admin']) ?>"
                                                            target="_blank" class="btn btn-sm btn-outline-primary mb-1 w-100 rounded-pill btn-document">
                                                            <i class="bi bi-file-earmark-pdf"></i> Surat Jalan
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
                                        <div class="d-flex gap-2 mt-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm flex-grow-1 rounded-pill"
                                                onclick="openEditModal('<?= $item['id'] ?>')">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm flex-grow-1 rounded-pill" 
                                                onclick="deleteAset('<?= $item['id'] ?>')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modals - keeping the same functionality -->
<div class="modal fade" id="modalPengembalian" tabindex="-1" aria-labelledby="modalPengembalianLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="modalPengembalianLabel">Form Pengembalian Kendaraan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formPengembalian" action="<?= base_url('/AsetKendaraan/kembali'); ?>" method="post"
                class="kembali" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row">
                        <input type="hidden" id="kendaraan_id_hidden" name="kendaraan_id" value="">

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nama_penanggung_jawab" class="form-label">Nama Penanggung Jawab</label>
                                <input type="text" class="form-control" id="nama_penanggung_jawab"
                                    name="nama_penanggung_jawab" required readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="nip_nrp" class="form-label">NIP / NRP</label>
                                <input type="text" class="form-control" id="nip_nrp" name="nip_nrp" required readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pangkat_golongan" class="form-label">Pangkat / Golongan</label>
                                <input type="text" class="form-control" id="pangkat_golongan" name="pangkat_golongan"
                                    required readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <input type="text" class="form-control" id="jabatan" name="jabatan" required readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="unit_organisasi" class="form-label">Unit Organisasi</label>
                                <input type="text" class="form-control" id="unit_organisasi" name="unit_organisasi"
                                    required readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="surat_pengembalian" class="form-label">Surat Pengembalian (PDF)</label>
                                <input type="file" class="form-control" id="surat_pengembalian"
                                    name="surat_pengembalian" accept="application/pdf" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="kendaraan_id" class="form-label">Kendaraan</label>
                                <select class="form-control" id="kendaraan_id_kembali" name="kendaraan_id" required>
                                    <option value="" disabled selected>Kendaraan</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pengemudi" class="form-label">Nama Pengemudi</label>
                                <input type="text" class="form-control" id="pengemudi" name="pengemudi" required readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="no_hp" class="form-label">Nomor HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" required readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                                <input type="date" class="form-control" id="tanggal_pinjam" name="tanggal_pinjam"
                                    readonly required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                                <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali"
                                    required min="<?= date('Y-m-d') ?>">
                            </div>

<div class="form-group mb-3">
    <label for="berita_acara_pengembalian" class="form-label">Berita Acara Pengembalian (PDF, PNG, JPEG)</label>
    <input type="file" class="form-control" id="berita_acara_pengembalian"
        name="berita_acara_pengembalian" accept="application/pdf,image/png,image/jpeg" required>
</div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Konfirmasi Pengembalian</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="modalPeminjaman" tabindex="-1" aria-labelledby="modalPeminjamanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="modalPeminjamanLabel">Form Peminjaman Kendaraan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formPeminjaman" action="<?= base_url('/AsetKendaraan/pinjam'); ?>" method="post" class="pinjam"
                enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nama_penanggung_jawab" class="form-label">Nama Penanggung Jawab</label>
                                <input type="text" class="form-control" id="nama_penanggung_jawab"
                                    name="nama_penanggung_jawab" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="nip_nrp" class="form-label">NIP / NRP</label>
                                <input type="text" class="form-control" id="nip_nrp" name="nip_nrp" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="jabatan" class="form-label">Unit Organisasi</label>
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
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <select
                                    class="form-control <?php if (session('errors.jabatan')): ?>is-invalid<?php endif ?>"
                                    name="jabatan">
                                    <option value="" class="text-muted" disabled selected>Pilih</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pangkat_golongan" class="form-label">Pangkat / Golongan</label>
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
                                <label for="kendaraan_id" class="form-label">Pilih Kendaraan</label>
                                <select class="form-control" id="kendaraan_id_pinjam" name="kendaraan_id" required>
                                    <option value="" disabled selected>Pilih Kendaraan</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pengemudi" class="form-label">Nama Pengemudi</label>
                                <input type="text" class="form-control -lg" id="pengemudi" name="pengemudi" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="no_hp" class="form-label">Nomor HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                                <input type="date" class="form-control" id="tanggal_pinjam" name="tanggal_pinjam" 
                                    required min="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="form-group mb-3">
                                <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                                <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali"
                                    required min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>


                        <div class="form-group mb-3 mt-auto order-last">
                            <label for="urusan_kedinasan" class="form-label">Urusan Kedinasan</label>
                            <textarea class="form-control" id="urusan_kedinasan" name="urusan_kedinasan" rows="3"
                                required></textarea>
                        </div>

                        <div class="form-group mb-3 mt-auto order-last">
                            <label for="surat_permohonan" class="form-label">Surat Permohonan (PDF)</label>
                            <input type="file" class="form-control" id="surat_permohonan" name="surat_permohonan"
                                accept="application/pdf" required>
                            <small class="text-muted">Max 2MB</small>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Ajukan Peminjaman</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="modalEditAset" tabindex="-1" aria-labelledby="modalEditAsetLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="modalEditAsetLabel">Form Edit Kendaraan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditAset" method="post" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <input type="hidden" id="edit_id" name="id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_kategori_id" class="form-label">Kategori</label>
                                <select class="form-control" id="kategori_id" name="kategori_id" required>
                                    <option value="" class="text-muted" disabled selected> Pilih Kategori Aset</option>
                                    <option class="fw-bold text-dark" value="KDF">Kendaraan Dinamis Fasilitas (KDF)</option>
                                    <option class="text-muted" disabled selected>Ambulance, Mobil Derek, dan Mobil Crane</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_no_sk_psp" class="form-label">No SK PSP</label>
                                <input type="text" class="form-control" id="edit_no_sk_psp" name="no_sk_psp" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_kode_barang" class="form-label">Kode Barang</label>
                                <input type="text" class="form-control" id="edit_kode_barang" name="kode_barang"
                                    required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_merk" class="form-label">Merk</label>
                                <input type="text" class="form-control" id="edit_merk" name="merk" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_tahun_pembuatan" class="form-label">Tahun Pembuatan</label>
                                <input type="number" class="form-control" id="edit_tahun_pembuatan"
                                    name="tahun_pembuatan">
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_kapasitas" class="form-label">Kapasitas</label>
                                <input type="number" class="form-control" id="edit_kapasitas" name="kapasitas">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_no_polisi" class="form-label">Nomor Polisi</label>
                                <input type="text" class="form-control" id="edit_no_polisi" name="no_polisi">
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_no_bpkb" class="form-label">No BPKB</label>
                                <input type="number" class="form-control" id="edit_no_bpkb" name="no_bpkb">
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_no_stnk" class="form-label">No STNK</label>
                                <input type="number" class="form-control" id="edit_no_stnk" name="no_stnk">
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_no_rangka" class="form-label">No Rangka</label>
                                <input type="number" class="form-control" id="edit_no_rangka" name="no_rangka">
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_kondisi" class="form-label">Kondisi</label>
                                <select class="form-control" id="edit_kondisi" name="kondisi">
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_gambar_mobil" class="form-label">Gambar Mobil (JPG/PNG)</label>
                                <input type="file" class="form-control" id="edit_gambar_mobil" name="gambar_mobil"
                                    accept="image/jpeg,image/png">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Foto Kendaraan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
                <div class="thumbnail-container d-flex justify-content-center py-3 bg-light">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="trackingMapModal" tabindex="-1" aria-labelledby="trackingMapLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="trackingMapLabel">Peta Lokasi Kendaraan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0" style="height: 500px;">
        <div id="trackingMap" style="height: 100%;"></div>
      </div>
    </div>
  </div>
</div>

<!-- Enhanced CSS for modern look -->
<style>
    /* Modern Card Styles */
    .vehicle-card .card {
        transition: all 0.3s ease;
        border-radius: 16px;
        overflow: hidden;
    }
    
    .vehicle-card .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
    }
    
    /* Image hover effect */
    .vehicle-image-wrapper img:hover {
        transform: scale(1.05);
    }
    
    /* Detail items styling */
    .detail-item {
        display: flex;
        align-items: center;
        padding: 8px 10px;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 8px;
        transition: all 0.2s;
    }
    
    .detail-item:hover {
        background-color: #e9ecef;
    }
    
    .detail-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: rgba(13, 110, 253, 0.1);
        margin-right: 10px;
        font-size: 14px;
    }
    
    /* Status badge styling */
    .pill-badge {
        padding: 0.5em 1em;
        border-radius: 50px;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    /* Text shadow for better readability on image overlays */
    .text-shadow {
        text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
    }
    
    /* Action buttons styling */
    .action-button {
        transition: all 0.2s;
        border: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .action-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    /* Document button styling */
    .btn-document {
        transition: all 0.2s;
        font-size: 0.8rem;
    }
    
    .btn-document:hover {
        background-color: #e7f1ff;
        color: #0d6efd;
        border-color: #0d6efd;
    }
    
    /* Modal styling */
    .modal-content {
        border-radius: 16px;
        overflow: hidden;
    }
    
    /* Header gradient */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    }
    
    /* Form controls styling */
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.6rem 1rem;
    }
    
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #adb5bd;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #6c757d;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .header-section {
            text-align: center;
        }
        
        .header-section nav {
            justify-content: center !important;
            margin-top: 1rem;
        }
    }
</style>

<script>
    const BASE_URL = '<?= base_url() ?>';
    
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Image hover zoom effect
        const vehicleImages = document.querySelectorAll('.vehicle-image-wrapper img');
        vehicleImages.forEach(img => {
            img.addEventListener('mouseover', () => {
                img.style.transform = 'scale(1.05)';
            });
            
            img.addEventListener('mouseout', () => {
                img.style.transform = 'scale(1)';
            });
        });
        
        // Filter functionality
        const searchInput = document.getElementById('searchKendaraan');
        if(searchInput) {
            searchInput.addEventListener('input', filterVehicles);
        }
        
        const filterKategori = document.getElementById('filterKategori');
        if(filterKategori) {
            filterKategori.addEventListener('change', filterVehicles);
        }
        
        const filterStatus = document.getElementById('filterStatus');
        if(filterStatus) {
            filterStatus.addEventListener('change', filterVehicles);
        }
        
        function filterVehicles() {
            const searchValue = searchInput ? searchInput.value.toLowerCase() : '';
            const kategoriValue = filterKategori ? filterKategori.value.toLowerCase() : '';
            const statusValue = filterStatus ? filterStatus.value.toLowerCase() : '';
            
            const vehicles = document.querySelectorAll('.vehicle-card');
            
            vehicles.forEach(vehicle => {
                const vehicleText = vehicle.textContent.toLowerCase();
                const kategoriText = vehicle.querySelector('.detail-content:nth-child(4) span') ? 
                    vehicle.querySelector('.detail-content:nth-child(4) span').textContent.toLowerCase() : '';
                const statusText = vehicle.querySelector('.badge') ? 
                    vehicle.querySelector('.badge').textContent.toLowerCase() : '';
                
                const matchesSearch = searchValue === '' || vehicleText.includes(searchValue);
                const matchesKategori = kategoriValue === '' || kategoriText.includes(kategoriValue);
                const matchesStatus = statusValue === '' || statusText.includes(statusValue);
                
                if (matchesSearch && matchesKategori && matchesStatus) {
                    vehicle.style.display = '';
                } else {
                    vehicle.style.display = 'none';
                }
            });
        }
    });
</script>

<?= $this->endSection() ?>="fw-bold text-dark" value="KDJ">Kendaraan Dinamis Jalan (KDJ)</option>
                                    <option class="text-muted" disabled selected>Sedan, Hatchback, dan SUV</option>
                                    <option class="fw-bold text-dark" value="KDO">Kendaraan Dinamis Off-road (KDO)</option>
                                    <option class="text-muted" disabled selected>Bus, Truk, dan Kendaraan Box</option>
                                    <option class