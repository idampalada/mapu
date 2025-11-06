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

                        <!-- Warna -->
            <div class="col-6">
                <div class="d-flex align-items-center">
                    <div class="me-2 text-danger fs-5"><i class="bi bi-palette"></i></div>
                    <div>
                        <small class="text-muted">Warna</small>
                        <div class="fw-medium"><?= $item['warna'] ?? 'Tidak Diketahui' ?></div>
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
                <button type="button" class="btn btn-secondary btn-sm rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-1"
                    onclick="showTimelineModal('<?= $item['id'] ?>')">
                    <i class="bi bi-clock-history"></i> Timeline Peminjaman
                </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-info btn-sm rounded-pill action-button"
                                            onclick="openPengembalianModal('<?= $item['id'] ?>')">
                                            <i class="bi bi-box-arrow-in-down"></i> Kembalikan Kendaraan
                                        </button>

                <button type="button" class="btn btn-secondary btn-sm rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-1 mt-2"
                    onclick="showTimelineModal('<?= $item['id'] ?>')">
                    <i class="bi bi-clock-history"></i> Timeline Peminjaman
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

                        <!-- Tab navigation for halaman 1 dan 2 -->
                        <ul class="nav nav-tabs mb-3" id="pengembalianTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pihak-kesatu-tab" data-bs-toggle="tab" 
                                    data-bs-target="#pihak-kesatu" type="button" role="tab" 
                                    aria-controls="pihak-kesatu" aria-selected="true">Pihak Kesatu</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="detail-kendaraan-tab" data-bs-toggle="tab" 
                                    data-bs-target="#detail-kendaraan" type="button" role="tab" 
                                    aria-controls="detail-kendaraan" aria-selected="false">Detail Kendaraan</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="pengembalianTabContent">
                            <!-- Tab 1: Pihak Kesatu -->
                            <div class="tab-pane fade show active" id="pihak-kesatu" role="tabpanel" 
                                aria-labelledby="pihak-kesatu-tab">
                                <div class="row">
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
                                            <label for="alamat_rumah" class="form-label">Alamat Rumah</label>
                                            <input type="text" class="form-control" id="alamat_rumah" name="alamat_rumah" readonly>
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label for="no_ktp" class="form-label">No. KTP</label>
                                            <input type="text" class="form-control" id="no_ktp" name="no_ktp" readonly>
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
                                            <label for="kondisi_kembali" class="form-label">Kondisi Kendaraan Saat Pengembalian</label>
                                            <select class="form-control" id="kondisi_kembali" name="kondisi_kembali" required>
                                                <option value="" disabled selected>Pilih Kondisi</option>
                                                <option value="Baik">Baik</option>
                                                <option value="Rusak Ringan">Rusak Ringan</option>
                                                <option value="Rusak Berat">Rusak Berat</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label">Ambil Foto</label>
                                    <div>
                                        <button type="button" class="btn btn-secondary" id="btn-camera-capture">
                                            <i class="bi bi-camera"></i> Ambil Foto
                                        </button>
                                    </div>
                                    
                                    <!-- Container untuk kamera dan preview -->
                                    <div id="camera-container" class="mt-2" style="display:none;">
                                        <video id="camera-feed" autoplay style="width:100%; max-width:640px;"></video>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-primary" id="btn-take-photo">Ambil Foto</button>
                                            <button type="button" class="btn btn-danger" id="btn-cancel-camera">Batal</button>
                                        </div>
                                    </div>
                                    <div id="photo-preview" class="mt-2" style="display:none;">
                                        <img id="captured-photo" class="img-fluid" style="max-width:640px;">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-warning" id="btn-retake-photo">Ambil Ulang</button>
                                        </div>
                                    </div>
                                    <canvas id="photo-canvas" style="display:none;"></canvas>
                                    <input type="hidden" id="photo-data" name="photo_data">
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary" id="btn-next-tab">Selanjutnya</button>
                                </div>
                            </div>
                            
<!-- Tab 2: Detail Kendaraan dengan penambahan field yang dapat diedit untuk Pihak Kedua -->
<div class="tab-pane fade" id="detail-kendaraan" role="tabpanel" 
    aria-labelledby="detail-kendaraan-tab">
    <div class="card p-3 mb-3">
        <h6 class="card-title fw-bold">Pihak Kedua <small class="text-muted">(Dapat diedit)</small></h6>
        <div class="row mb-2">
            <div class="col-md-3">Nama</div>
            <div class="col-md-9">
                <input type="text" class="form-control" id="pihak_kedua_nama" name="pihak_kedua_nama" value="Pak Udin">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-3">NIP</div>
            <div class="col-md-9">
                <input type="text" class="form-control" id="pihak_kedua_nip" name="pihak_kedua_nip" value="12345678">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-3">Jabatan</div>
            <div class="col-md-9">
                <input type="text" class="form-control" id="pihak_kedua_jabatan" name="pihak_kedua_jabatan" value="Kepala Satuan Kerja Selaku Kuasa Pengguna Barang">
            </div>
        </div>
    </div>
    
    <div class="form-group mb-3">
        <label for="nomor_sip" class="form-label">Nomor SIP / Surat Penanggung Jawab</label>
        <input type="text" class="form-control" id="nomor_sip" name="nomor_sip" required>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="kategori_id" class="form-label">Jenis Kendaraan</label>
                <input type="text" class="form-control" id="kategori_id" name="kategori_id" readonly>
            </div>
            
            <div class="form-group mb-3">
                <label for="no_polisi_detail" class="form-label">Nomor Polisi</label>
                <input type="text" class="form-control" id="no_polisi_detail" name="no_polisi_detail" readonly>
            </div>
            
            <div class="form-group mb-3">
                <label for="kode_barang_detail" class="form-label">Kode Barang</label>
                <input type="text" class="form-control" id="kode_barang_detail" name="kode_barang_detail" readonly>
            </div>
            
            <div class="form-group mb-3">
                <label for="nup_detail" class="form-label">NUP</label>
                <input type="text" class="form-control" id="nup_detail" name="nup_detail" readonly>
            </div>
            
            <div class="form-group mb-3">
                <label for="tahun_pembuatan" class="form-label">Tahun Pembuatan</label>
                <input type="text" class="form-control" id="tahun_pembuatan" name="tahun_pembuatan" readonly>
            </div>
            
            <!-- Tambahkan field STNK -->
            <div class="form-group mb-3">
                <label for="nomor_stnk" class="form-label">Nomor STNK</label>
                <input type="text" class="form-control" id="nomor_stnk" name="nomor_stnk" readonly>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="merk_detail" class="form-label">Merk</label>
                <input type="text" class="form-control" id="merk_detail" name="merk_detail" readonly>
            </div>
            
            <div class="form-group mb-3">
                <label for="warna" class="form-label">Warna</label>
                <input type="text" class="form-control" id="warna" name="warna" readonly>
            </div>
            
            <div class="form-group mb-3">
                <label for="nomor_mesin" class="form-label">Nomor Mesin</label>
                <input type="text" class="form-control" id="nomor_mesin" name="nomor_mesin" readonly>
            </div>
            
            <div class="form-group mb-3">
                <label for="nomor_rangka" class="form-label">Nomor Rangka</label>
                <input type="text" class="form-control" id="nomor_rangka" name="nomor_rangka" readonly>
            </div>
            
            <!-- Tambahkan field BPKB -->
            <div class="form-group mb-3">
                <label for="nomor_bpkb" class="form-label">Nomor BPKB</label>
                <input type="text" class="form-control" id="nomor_bpkb" name="nomor_bpkb" readonly>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-secondary" id="btn-prev-tab">Kembali</button>
        <button type="submit" class="btn btn-primary rounded-pill">Konfirmasi Pengembalian</button>
    </div>
</div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer d-none">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Tutup</button>
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
                
                <!-- Halaman 1: Data Peminjam -->
                <div id="page1" class="modal-body p-4">
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
                                <label for="no_ktp" class="form-label">No. KTP</label>
                                <input type="text" class="form-control" id="no_ktp" name="no_ktp" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="alamat_rumah" class="form-label">Alamat Rumah</label>
                                <textarea class="form-control" id="alamat_rumah" name="alamat_rumah" rows="2"
                                    required></textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label for="jabatan" class="form-label">Unit Organisasi</label>
                                <select
                                    class="form-control <?php if (session('errors.unit_organisasi')): ?>is-invalid<?php endif ?>"
                                    name="unit_organisasi" id="unit_organisasi" required>
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
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <select
                                    class="form-control <?php if (session('errors.jabatan')): ?>is-invalid<?php endif ?>"
                                    name="jabatan" id="jabatan" required>
                                    <option value="" class="text-muted" disabled selected>Pilih</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pangkat_golongan" class="form-label">Pangkat / Golongan</label>
                                <select class="form-control" name="pangkat_golongan" id="pangkat_golongan" required>
                                    <option value="" class="text-muted" disabled selected>Pilih</option>
                                    <option value="IV A">IV A - Pembina</option>
                                    <option value="IV B">IV B - Pembina Tingkat 1</option>
                                    <option value="IV C">IV C - Pembina Tingkat Muda</option>
                                    <option value="IV D">IV D - Pembina Tingkat Madya</option>
                                    <option value="IV E">IV E - Pembina Utama</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="kendaraan_id" class="form-label">Pilih Kendaraan</label>
                                <select class="form-control" id="kendaraan_id_pinjam" name="kendaraan_id" required>
                                    <option value="" disabled selected>Pilih Kendaraan</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pengemudi" class="form-label">Nama Pengemudi</label>
                                <input type="text" class="form-control" id="pengemudi" name="pengemudi" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="no_hp" class="form-label">Nomor HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                                <input type="date" class="form-control" id="tanggal_pinjam" name="tanggal_pinjam" 
                                    required min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                                <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali"
                                    required min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="urusan_kedinasan" class="form-label">Urusan Kedinasan</label>
                            <textarea class="form-control" id="urusan_kedinasan" name="urusan_kedinasan" rows="3"
                                required></textarea>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" id="nextBtn" class="btn btn-primary rounded-pill">Selanjutnya &raquo;</button>
                    </div>
                </div>

                <!-- Halaman 2: Data Kendaraan -->
<div id="page2" class="modal-body p-4" style="display: none;">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="detail_jenis_kendaraan" class="form-label">Jenis Kendaraan</label>
                <input type="text" class="form-control" id="detail_jenis_kendaraan" readonly>
            </div>
            
            <div class="form-group mb-3">
                <label for="detail_nopol" class="form-label">Nomor Polisi</label>
                <input type="text" class="form-control" id="detail_nopol" readonly>
            </div>
            
            <div class="form-group mb-3">
                <label for="detail_merk" class="form-label">Merk/Type</label>
                <input type="text" class="form-control" id="detail_merk" readonly>
            </div>
            
            <div class="form-group mb-3">
                <label for="detail_warna" class="form-label">Warna</label>
                <input type="text" class="form-control" id="detail_warna" readonly>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="detail_nomor_mesin" class="form-label">Nomor Mesin</label>
                <input type="text" class="form-control" id="detail_nomor_mesin" readonly>
            </div>
            
            <div class="form-group mb-3">
                <label for="detail_no_rangka" class="form-label">Nomor Rangka</label>
                <input type="text" class="form-control" id="detail_no_rangka" readonly>
            </div>
            
            <div class="form-group mb-3">
                <label for="detail_kode_barang" class="form-label">Kode Barang</label>
                <input type="text" class="form-control" id="detail_kode_barang" readonly>
            </div>
            
            <div class="form-group mb-3">
                <label for="detail_nup" class="form-label">NUP</label>
                <input type="text" class="form-control" id="detail_nup" readonly>
            </div>
            
            <div class="form-group mb-3">
                <label for="detail_tahun_pembuatan" class="form-label">Tahun Pembuatan</label>
                <input type="text" class="form-control" id="detail_tahun_pembuatan" readonly>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-between mt-3">
        <button type="button" id="prevBtn" class="btn btn-light rounded-pill">&laquo; Kembali</button>
        <button type="submit" class="btn btn-primary rounded-pill">Ajukan Peminjaman</button>
    </div>
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
                                <select class="form-control" id="edit_kategori_id" name="kategori_id" required>
                                    <option value="" class="text-muted" disabled selected>Pilih Kategori Aset</option>
                                    <option class="fw-bold text-dark" value="KDJ">Kendaraan Dinamis Jalan (KDJ)</option>
                                    <option class="text-muted" disabled>Sedan, Hatchback, dan SUV</option>
                                    <option class="fw-bold text-dark" value="KDO">Kendaraan Dinamis Off-road (KDO)</option>
                                    <option class="text-muted" disabled>Bus, Truk, dan Kendaraan Box</option>
                                    <option class="fw-bold text-dark" value="KDF">Kendaraan Dinamis Fasilitas (KDF)</option>
                                    <option class="text-muted" disabled>Ambulance, Mobil Derek, dan Mobil Crane</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_kode_barang" class="form-label">Kode Barang</label>
                                <input type="text" class="form-control" id="edit_kode_barang" name="kode_barang" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_nup" class="form-label">NUP</label>
                                <input type="text" class="form-control" id="edit_nup" name="nup">
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_merk" class="form-label">Merk</label>
                                <input type="text" class="form-control" id="edit_merk" name="merk" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_warna" class="form-label">Warna</label>
                                <input type="text" class="form-control" id="edit_warna" name="warna">
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_tahun_pembuatan" class="form-label">Tahun Pembuatan</label>
                                <input type="number" class="form-control" id="edit_tahun_pembuatan" name="tahun_pembuatan">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_kapasitas" class="form-label">Kapasitas</label>
                                <input type="number" class="form-control" id="edit_kapasitas" name="kapasitas">
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_no_polisi" class="form-label">Nomor Polisi</label>
                                <input type="text" class="form-control" id="edit_no_polisi" name="no_polisi">
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_nomor_mesin" class="form-label">Nomor Mesin</label>
                                <input type="text" class="form-control" id="edit_nomor_mesin" name="nomor_mesin">
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_no_rangka" class="form-label">No Rangka</label>
                                <input type="text" class="form-control" id="edit_no_rangka" name="no_rangka">
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_no_stnk" class="form-label">Nomor STNK</label>
                                <input type="text" class="form-control" id="edit_no_stnk" name="no_stnk" placeholder="Masukkan nomor STNK">
                            </div>
                            <div class="form-group mb-3">
                                <label for="edit_no_bpkb" class="form-label">Nomor BPKB</label>
                                <input type="text" class="form-control" id="edit_no_bpkb" name="no_bpkb" placeholder="Masukkan nomor BPKB">
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
                                <input type="file" class="form-control" id="edit_gambar_mobil" name="gambar_mobil" accept="image/jpeg,image/png">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
                                <div class="mt-2">
                                    <img id="current_image_preview" class="img-fluid rounded" style="max-height: 150px; display: none;" alt="Preview Gambar">
                                </div>
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

<!-- Modal Timeline Peminjaman -->
<!-- Modal Timeline Peminjaman dengan Tab Section -->
<!-- Modal Timeline Peminjaman dengan Tab dan Table Section -->
<!-- Modal Timeline Peminjaman dengan Tab dan Table Section -->
<!-- Modal Timeline Peminjaman dengan Tab dan Table Section -->
<div class="modal fade" id="modalTimeline" tabindex="-1" aria-labelledby="modalTimelineLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTimelineLabel">Verifikasi Peminjaman & Pengembalian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs border-0" id="timelineTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="peminjaman-tab" data-bs-toggle="tab" data-bs-target="#peminjaman" type="button" role="tab" aria-controls="peminjaman" aria-selected="true">
                            Peminjaman Pending <span id="peminjamanPendingCount" class="badge rounded-pill bg-danger ms-1">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pengembalian-tab" data-bs-toggle="tab" data-bs-target="#pengembalian" type="button" role="tab" aria-controls="pengembalian" aria-selected="false">
                            Pengembalian Pending <span id="pengembalianPendingCount" class="badge rounded-pill bg-danger ms-1">0</span>
                        </button>
                    </li>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content p-3" id="timelineTabContent">
                    <!-- Peminjaman Pending -->
                    <div class="tab-pane fade show active" id="peminjaman" role="tabpanel" aria-labelledby="peminjaman-tab">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Penanggung Jawab</th>
                                        <th>Kendaraan</th>
                                        <th>Urusan Kedinasan</th>
                                        <th>Status</th>
                                        <th>Dokumen</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tanggal Kembali</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="peminjamanPendingTable">
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-3">Memuat data peminjaman...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Pengembalian Pending -->
                    <div class="tab-pane fade" id="pengembalian" role="tabpanel" aria-labelledby="pengembalian-tab">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Penanggung Jawab</th>
                                        <th>Kendaraan</th>
                                        <th>Urusan Kedinasan</th>
                                        <th>Status</th>
                                        <th>Dokumen</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tanggal Kembali</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="pengembalianPendingTable">
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-3">Memuat data pengembalian...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
    // JavaScript untuk fungsi kamera
// JavaScript untuk fungsi kamera dan tab navigation
$(document).ready(function() {
    let stream;
    
    // Tombol navigasi tab
    $('#btn-next-tab').click(function() {
        $('#detail-kendaraan-tab').tab('show');
    });
    
    $('#btn-prev-tab').click(function() {
        $('#pihak-kesatu-tab').tab('show');
    });
    
    // Tombol untuk membuka kamera
    $('#btn-camera-capture').click(function() {
        startCamera();
    });
    
    // Tombol untuk mengambil foto
    $('#btn-take-photo').click(function() {
        takePhoto();
    });
    
    // Tombol untuk membatalkan kamera
    $('#btn-cancel-camera').click(function() {
        stopCamera();
    });
    
    // Tombol untuk mengambil ulang foto
    $('#btn-retake-photo').click(function() {
        $('#photo-preview').hide();
        startCamera();
    });
    
    // Fungsi untuk memulai kamera
    function startCamera() {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(mediaStream) {
                    stream = mediaStream;
                    const video = document.getElementById('camera-feed');
                    video.srcObject = mediaStream;
                    video.play();
                    $('#camera-container').show();
                })
                .catch(function(error) {
                    console.error("Tidak dapat mengakses kamera:", error);
                    alert("Tidak dapat mengakses kamera. Pastikan kamera tersedia dan izin diberikan.");
                });
        } else {
            alert("Browser Anda tidak mendukung akses kamera");
        }
    }
    
    // Fungsi untuk mengambil foto
    function takePhoto() {
        const video = document.getElementById('camera-feed');
        const canvas = document.getElementById('photo-canvas');
        const context = canvas.getContext('2d');
        
        // Set ukuran canvas sama dengan video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Gambar video ke canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Tambahkan timestamp pada foto
        context.fillStyle = "#ffffff";
        context.fillRect(0, canvas.height - 30, canvas.width, 30);
        context.fillStyle = "#000000";
        context.font = "14px Arial";
        const timestamp = new Date().toLocaleString();
        context.fillText("Timestamp: " + timestamp, 10, canvas.height - 10);
        
        // Dapatkan data image
        const photoData = canvas.toDataURL('image/jpeg');
        
        // Simpan data foto ke input hidden
        $('#photo-data').val(photoData);
        
        // Tampilkan preview
        $('#captured-photo').attr('src', photoData);
        $('#photo-preview').show();
        
        // Sembunyikan kamera
        $('#camera-container').hide();
        
        // Stop kamera
        stopCamera();
    }
    
    // Fungsi untuk menghentikan kamera
    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => {
                track.stop();
            });
            stream = null;
        }
        $('#camera-container').hide();
    }
    
    // Mengisi form pengembalian saat kendaraan dipilih
    $('#kendaraan_id_kembali').on('change', function() {
        const kendaraanId = $(this).val();
        if (kendaraanId) {
            $('#kendaraan_id_hidden').val(kendaraanId);
            $.ajax({
                url: '/AsetKendaraan/getPeminjamanInfo',
                type: 'POST',
                data: { kendaraan_id: kendaraanId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Isi data pihak kesatu
                        $('#nama_penanggung_jawab').val(response.data.nama_penanggung_jawab);
                        $('#nip_nrp').val(response.data.nip_nrp);
                        $('#pangkat_golongan').val(response.data.pangkat_golongan);
                        $('#jabatan').val(response.data.jabatan);
                        $('#unit_organisasi').val(response.data.unit_organisasi);
                        $('#alamat_rumah').val(response.data.alamat_rumah || '');
                        $('#no_ktp').val(response.data.no_ktp || '');
                        $('#pengemudi').val(response.data.pengemudi);
                        $('#no_hp').val(response.data.no_hp);
                        $('#tanggal_pinjam').val(response.data.tanggal_pinjam);
                        $('#tanggal_kembali').val(response.data.tanggal_kembali);
                        
                        // Isi data kendaraan
                        $('#kategori_id').val(response.asset.kategori_id);
                        $('#no_polisi_detail').val(response.asset.no_polisi);
                        $('#kode_barang_detail').val(response.asset.kode_barang);
                        $('#nup_detail').val(response.asset.nup || '-');
                        $('#tahun_pembuatan').val(response.asset.tahun_pembuatan || '-');
                        $('#merk_detail').val(response.asset.merk);
                        $('#warna').val(response.asset.warna || '-');
                        $('#nomor_mesin').val(response.asset.nomor_mesin || '-');
                        $('#nomor_rangka').val(response.asset.no_rangka || '-');
                        
                    } else {
                        alert('Gagal mendapatkan data peminjaman');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengambil data');
                }
            });
        }
    });
    
    // Load kendaraan yang dipinjam
    function loadKendaraanDipinjam() {
        $.ajax({
            url: '/AsetKendaraan/getKendaraanDipinjam',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                const select = $('#kendaraan_id_kembali');
                select.find('option:not(:first)').remove();
                
                if (response.length > 0) {
                    $.each(response, function(i, item) {
                        select.append($('<option>', {
                            value: item.id,
                            text: item.merk + ' - ' + item.no_polisi
                        }));
                    });
                } else {
                    select.append($('<option>', {
                        disabled: true,
                        text: 'Tidak ada kendaraan yang dipinjam'
                    }));
                }
            },
            error: function() {
                alert('Gagal memuat daftar kendaraan');
            }
        });
    }
    
    // Inisialisasi
    loadKendaraanDipinjam();
    
    // Form submit
$('#formPengembalian').on('submit', function(e) {
    e.preventDefault();
    
    // Validasi foto
    if (!$('#photo-data').val()) {
        Swal.fire({
            icon: 'error',
            title: 'Perhatian',
            text: 'Silahkan ambil foto kendaraan terlebih dahulu',
            confirmButtonColor: '#dc3545'
        });
        $('#pihak-kesatu-tab').tab('show');
        return false;
    }
    
    // Validasi nomor SIP
    if (!$('#nomor_sip').val()) {
        Swal.fire({
            icon: 'error',
            title: 'Perhatian',
            text: 'Nomor SIP / Surat Penanggung Jawab harus diisi',
            confirmButtonColor: '#dc3545'
        });
        return false;
    }
    
    const formData = new FormData(this);
    
    // Tambahkan hidden field untuk berita_acara_pengembalian dari foto
    if (!formData.has('berita_acara_pengembalian') || !formData.get('berita_acara_pengembalian').size) {
        // Gunakan foto dari kamera sebagai berita acara jika tidak ada file yang diupload
        formData.append('berita_acara_pengembalian', 'auto_generated_' + new Date().getTime() + '.jpg');
    }
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
        },
        success: function(response) {
            $('button[type="submit"]').prop('disabled', false).html('Konfirmasi Pengembalian');
            
            if (response.success) {
                $('#modalPengembalian').modal('hide');
                
                // Tampilkan modal sukses dengan ikon ceklis
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Pengajuan pengembalian berhasil dikirim',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#198754'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.error || 'Gagal melakukan pengembalian',
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function() {
            $('button[type="submit"]').prop('disabled', false).html('Konfirmasi Pengembalian');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memproses pengembalian',
                confirmButtonColor: '#dc3545'
            });
        }
    });
});
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const page1 = document.getElementById('page1');
    const page2 = document.getElementById('page2');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const kendaraanSelect = document.getElementById('kendaraan_id_pinjam');
    
    // Navigasi antar halaman
    nextBtn.addEventListener('click', function() {
        // Validasi form halaman 1
        const requiredFields = page1.querySelectorAll('[required]');
        let valid = true;
        
        requiredFields.forEach(field => {
            if (!field.value) {
                valid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!valid) {
            alert('Mohon lengkapi semua field yang diperlukan');
            return;
        }
        
        // Jika valid, load data kendaraan dan pindah ke halaman 2
        const selectedKendaraanId = kendaraanSelect.value;
        if (selectedKendaraanId) {
            loadKendaraanDetails(selectedKendaraanId);
            page1.style.display = 'none';
            page2.style.display = 'block';
        } else {
            alert('Silahkan pilih kendaraan terlebih dahulu');
        }
    });
    
    prevBtn.addEventListener('click', function() {
        page2.style.display = 'none';
        page1.style.display = 'block';
    });
    
    // Load detail kendaraan
    function loadKendaraanDetails(kendaraanId) {
    fetch(`<?= base_url('AsetKendaraan/getAsetById/') ?>${kendaraanId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const kendaraan = data.data;
                
                // Konversi kategori_id ke jenis kendaraan
                let jenisKendaraan = "Tidak Diketahui";
                switch(kendaraan.kategori_id) {
                    case "KDJ":
                        jenisKendaraan = "Kendaraan Dinamis Jalan (KDJ)";
                        break;
                    case "KDO":
                        jenisKendaraan = "Kendaraan Dinamis Off-road (KDO)";
                        break;
                    case "KDF":
                        jenisKendaraan = "Kendaraan Dinamis Fasilitas (KDF)";
                        break;
                    default:
                        jenisKendaraan = kendaraan.kategori_id || "Tidak Diketahui";
                }
                
                // Isi form dengan data kendaraan
                document.getElementById('detail_jenis_kendaraan').value = jenisKendaraan;
                document.getElementById('detail_nopol').value = kendaraan.no_polisi || '-';
                document.getElementById('detail_merk').value = kendaraan.merk || '-';
                document.getElementById('detail_warna').value = kendaraan.warna || '-';
                document.getElementById('detail_nomor_mesin').value = kendaraan.nomor_mesin || '-';
                document.getElementById('detail_no_rangka').value = kendaraan.no_rangka || '-';
                document.getElementById('detail_kode_barang').value = kendaraan.kode_barang || '-';
                document.getElementById('detail_nup').value = kendaraan.nup || '-';
                document.getElementById('detail_tahun_pembuatan').value = kendaraan.tahun_pembuatan || '-';
            } else {
                alert('Gagal memuat detail kendaraan: ' + (data.error || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat detail kendaraan');
        });
}
    
    // Load daftar kendaraan tersedia saat form dibuka
    function loadAvailableKendaraan() {
        fetch('<?= base_url('AsetKendaraan/getKendaraan') ?>')
            .then(response => response.json())
            .then(data => {
                kendaraanSelect.innerHTML = '<option value="" disabled selected>Pilih Kendaraan</option>';
                
                data.forEach(kendaraan => {
                    if (kendaraan.status_pinjam === 'Tersedia') {
                        const option = document.createElement('option');
                        option.value = kendaraan.id;
                        option.textContent = `${kendaraan.merk} - ${kendaraan.no_polisi}`;
                        kendaraanSelect.appendChild(option);
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat daftar kendaraan');
            });
    }
    
    // Initialize
    loadAvailableKendaraan();
    
    // Dynamic jabatan options based on unit organisasi selection
    const unitOrganisasiSelect = document.getElementById('unit_organisasi');
    const jabatanSelect = document.getElementById('jabatan');
    
    unitOrganisasiSelect.addEventListener('change', function() {
        // Reset jabatan options
        jabatanSelect.innerHTML = '<option value="" disabled selected>Pilih</option>';
        
        // Add jabatan options based on selected unit
        const unit = this.value;
        let jabatanOptions = [];
        
        switch(unit) {
            case 'Setjen':
                jabatanOptions = ['Sekretaris Jenderal', 'Kepala Biro', 'Kepala Bagian', 'Kepala Subbagian', 'Staff'];
                break;
            case 'Itjen':
                jabatanOptions = ['Inspektur Jenderal', 'Sekretaris Inspektorat Jenderal', 'Inspektur', 'Staff'];
                break;
            // Add more cases as needed
            default:
                jabatanOptions = ['Direktur Jenderal', 'Sekretaris Direktorat Jenderal', 'Direktur', 'Staff'];
        }
        
        jabatanOptions.forEach(jabatan => {
            const option = document.createElement('option');
            option.value = jabatan;
            option.textContent = jabatan;
            jabatanSelect.appendChild(option);
        });
    });
});
</script>
<style>
    #modalPengembalian .modal-dialog {
  max-width: 800px; /* Sesuaikan dengan kebutuhan */
  height: 90vh;
}

#modalPengembalian .modal-content {
  height: 100%;
}

#modalPengembalian .modal-body {
  max-height: calc(90vh - 120px); /* Tinggi viewport dikurangi header dan footer modal */
  overflow-y: auto;
  padding: 20px;
}
</style>
<?= $this->endSection() ?>="fw-bold text-dark" value="KDJ">Kendaraan Dinamis Jalan (KDJ)</option>
                                    <option class="text-muted" disabled selected>Sedan, Hatchback, dan SUV</option>
                                    <option class="fw-bold text-dark" value="KDO">Kendaraan Dinamis Off-road (KDO)</option>
                                    <option class="text-muted" disabled selected>Bus, Truk, dan Kendaraan Box</option>
                                    <option class