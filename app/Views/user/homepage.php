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


    <!-- Filter & Search -->
    <div class="filter-bar mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <select class="form-select form-select-modern" id="filterKategori">
                    <option value="">Semua Kategori</option>
                    <option value="KDJ">Kendaraan Dinamis Jalan</option>
                    <option value="KDO">Kendaraan Dinamis Off-road</option>
                    <option value="KDF">Kendaraan Dinamis Fasilitas</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-modern" id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="Tersedia">Tersedia</option>
                    <option value="Dipinjam">Dipinjam</option>
                    <option value="Verifikasi">Dalam Verifikasi</option>
                </select>
            </div>
            <div class="col-md-4">
                <div class="search-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control form-control-modern" placeholder="Cari kendaraan..." id="searchKendaraan">
                </div>
            </div>
            <div class="col-md-2 text-end">
                <div class="btn-group view-toggle" role="group">
                    <button type="button" class="btn btn-view active"><i class="bi bi-grid-3x3-gap"></i></button>
                    <button type="button" class="btn btn-view"><i class="bi bi-list"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle Cards -->
    <div class="vehicles-container">
        <div class="vehicle-grid">
            <?php foreach ($aset as $item): ?>
                <div class="vehicle-card">
                    <div class="vc-card">
                        <!-- Image -->
                        <div class="vc-image-wrap">
                            <?php
                            $images = json_decode($item['gambar_mobil'], true);
                            $images = is_array($images) ? $images : [$item['gambar_mobil']];
                            $mainImage = !empty($images) ? $images[0] : null;
                            ?>
                            <?php if (!empty($mainImage) && file_exists(ROOTPATH . 'public/uploads/images/' . $mainImage)): ?>
                                <img src="<?= base_url('/uploads/images/' . $mainImage) ?>"
                                    class="vc-image image-preview-trigger"
                                    data-images='<?= htmlspecialchars(json_encode($images)) ?>'
                                    alt="<?= $item['merk'] ?>">
                            <?php else: ?>
                                <img src="<?= base_url('/assets/images/faces/1.jpg') ?>"
                                    class="vc-image"
                                    alt="<?= $item['merk'] ?>">
                            <?php endif; ?>

                            <?php
                            $statusClass = '';
                            switch ($item['status_pinjam']) {
                                case 'Tersedia':
                                    $statusClass = 'status-available';
                                    $statusIcon = 'bi-check-circle-fill';
                                    break;
                                case 'Pending':
                                case 'Dalam Verifikasi':
                                    $statusClass = 'status-pending';
                                    $statusIcon = 'bi-clock-fill';
                                    break;
                                default:
                                    $statusClass = 'status-borrowed';
                                    $statusIcon = 'bi-arrow-repeat';
                            }
                            ?>
                            <span class="vc-status-badge <?= $statusClass ?>">
                                <i class="bi <?= $statusIcon ?>"></i> <?= $item['status_pinjam'] ?>
                            </span>

                            <?php if (!empty($item['keterangan'])): ?>
                                <span class="vc-info-badge" data-bs-toggle="tooltip" title="<?= $item['keterangan'] ?>">
                                    <i class="bi bi-info-circle"></i>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Body -->
                        <div class="vc-body">
                            <div class="vc-title-row">
                                <h3 class="vc-title"><?= $item['merk'] ?></h3>
                                <span class="vc-condition-badge cond-<?= $item['kondisi'] === 'Baik' ? 'good' : ($item['kondisi'] === 'Rusak Ringan' ? 'warn' : 'bad') ?>">
                                    <?= $item['kondisi'] ?>
                                </span>
                            </div>

                            <div class="vc-meta-grid">
                                <div class="vc-meta">
                                    <i class="bi bi-car-front"></i>
                                    <div>
                                        <span class="vc-meta-label">No. Polisi</span>
                                        <span class="vc-meta-value"><?= $item['no_polisi'] ?></span>
                                    </div>
                                </div>
                                <div class="vc-meta">
                                    <i class="bi bi-calendar3"></i>
                                    <div>
                                        <span class="vc-meta-label">Tahun</span>
                                        <span class="vc-meta-value"><?= $item['tahun_pembuatan'] ?></span>
                                    </div>
                                </div>
                                <div class="vc-meta">
                                    <i class="bi bi-people"></i>
                                    <div>
                                        <span class="vc-meta-label">Kapasitas</span>
                                        <span class="vc-meta-value"><?= $item['kapasitas'] ?> Orang</span>
                                    </div>
                                </div>
                                <div class="vc-meta">
                                    <i class="bi bi-upc-scan"></i>
                                    <div>
                                        <span class="vc-meta-label">Kode Barang</span>
                                        <span class="vc-meta-value"><?= $item['kode_barang'] ?></span>
                                    </div>
                                </div>
                                <div class="vc-meta">
                                    <i class="bi bi-tag"></i>
                                    <div>
                                        <span class="vc-meta-label">Tipe</span>
                                        <span class="vc-meta-value"><?= $item['kategori_id'] ?></span>
                                    </div>
                                </div>
                                <div class="vc-meta">
                                    <i class="bi bi-palette"></i>
                                    <div>
                                        <span class="vc-meta-label">Warna</span>
                                        <span class="vc-meta-value"><?= $item['warna'] ?? 'Tidak Diketahui' ?></span>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($item['tanggal_kembali'])): ?>
                                <div class="vc-return-date">
                                    <span class="vc-meta-label">Tanggal Kembali</span>
                                    <span class="vc-return-value"><?= date('d/m/Y', strtotime($item['tanggal_kembali'])) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Footer / Actions -->
                        <div class="vc-footer">
                            <div class="d-flex flex-column gap-2">
                                <?php if ($item['status_pinjam'] === 'Tersedia' || $item['status_pinjam'] === 'Dalam Verifikasi'): ?>
                                    <?php if ($item['status_pinjam'] === 'Dalam Verifikasi'): ?>
                                        <button type="button" class="vc-btn vc-btn-muted" disabled>
                                            <i class="bi bi-clock"></i> Menunggu Verifikasi
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="vc-btn vc-btn-primary"
                                            onclick="openPeminjamanModal('<?= $item['id'] ?>')">
                                            <i class="bi bi-plus-circle"></i> Pinjam Kendaraan
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" class="vc-btn vc-btn-success"
                                        onclick="trackKendaraan('<?= $item['no_polisi'] ?>')">
                                        <i class="bi bi-geo-alt"></i> Status
                                    </button>
                                    <button type="button" class="vc-btn vc-btn-secondary"
                                        onclick="showTimelineModal('<?= $item['id'] ?>')">
                                        <i class="bi bi-clock-history"></i> Timeline Peminjaman
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="vc-btn vc-btn-info"
                                        onclick="openPengembalianModal('<?= $item['id'] ?>')">
                                        <i class="bi bi-box-arrow-in-down"></i> Kembalikan Kendaraan
                                    </button>
                                    <button type="button" class="vc-btn vc-btn-secondary"
                                        onclick="showTimelineModal('<?= $item['id'] ?>')">
                                        <i class="bi bi-clock-history"></i> Timeline Peminjaman
                                    </button>

                                    <?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
                                        <div class="vc-documents">
                                            <div class="vc-documents-header">
                                                <i class="bi bi-file-earmark"></i> Dokumen
                                            </div>
                                            <?php if (!empty($item['surat_permohonan']) && file_exists(ROOTPATH . 'public/uploads/documents/' . $item['surat_permohonan'])): ?>
                                                <a href="<?= base_url('/uploads/documents/' . $item['surat_permohonan']) ?>"
                                                    target="_blank" class="vc-doc-link">
                                                    <i class="bi bi-file-earmark-pdf"></i> Surat Permohonan
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($item['surat_jalan_admin']) && file_exists(ROOTPATH . 'public/uploads/documents/' . $item['surat_jalan_admin'])): ?>
                                                <a href="<?= base_url('/uploads/documents/' . $item['surat_jalan_admin']) ?>"
                                                    target="_blank" class="vc-doc-link">
                                                    <i class="bi bi-file-earmark-pdf"></i> Surat Jalan
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="vc-btn vc-btn-outline flex-grow-1"
                                            onclick="openEditModal('<?= $item['id'] ?>')">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <button type="button" class="vc-btn vc-btn-outline-danger flex-grow-1"
                                            onclick="deleteAset('<?= $item['id'] ?>')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- Semua modal di bawah ini SAMA PERSIS strukturnya dengan versi -->
<!-- sebelumnya (id, name, action, JS hook tidak diubah) — hanya   -->
<!-- kelas Bootstrap dasarnya dipertahankan agar semua script lama -->
<!-- (validasi, kamera, tab, dsb) tetap berjalan tanpa perubahan.  -->
<!-- ============================================================ -->

<!-- Modal Pengembalian -->
<div class="modal fade" id="modalPengembalian" tabindex="-1" aria-labelledby="modalPengembalianLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow modal-modern">
            <div class="modal-header modal-header-modern">
                <h5 class="modal-title" id="modalPengembalianLabel">Form Pengembalian Kendaraan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formPengembalian" action="<?= base_url('/AsetKendaraan/kembali'); ?>" method="post"
                class="kembali needs-validation" enctype="multipart/form-data" novalidate>
                <div class="modal-body p-4">
                    <div class="row">
                        <input type="hidden" id="kendaraan_id_hidden" name="kendaraan_id" value="">
                        <input type="hidden" id="is_late_return" name="is_late_return" value="false">
                        <input type="hidden" id="days_late" name="days_late" value="0">

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
                            <div class="tab-pane fade show active" id="pihak-kesatu" role="tabpanel"
                                aria-labelledby="pihak-kesatu-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="nama_penanggung_jawab" class="form-label">Nama Penanggung Jawab</label>
                                            <input type="text" class="form-control" id="nama_penanggung_jawab"
                                                name="nama_penanggung_jawab" required readonly>
                                            <div class="invalid-feedback">Nama penanggung jawab harus diisi.</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="nip_nrp" class="form-label">NIP / NRP</label>
                                            <input type="text" class="form-control" id="nip_nrp" name="nip_nrp" required readonly>
                                            <div class="invalid-feedback">NIP/NRP harus diisi.</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="pangkat_golongan" class="form-label">Pangkat / Golongan</label>
                                            <input type="text" class="form-control" id="pangkat_golongan" name="pangkat_golongan"
                                                required readonly>
                                            <div class="invalid-feedback">Pangkat/Golongan harus diisi.</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="jabatan" class="form-label">Jabatan</label>
                                            <input type="text" class="form-control" id="jabatan" name="jabatan" required readonly>
                                            <div class="invalid-feedback">Jabatan harus diisi.</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="unit_organisasi" class="form-label">Unit Organisasi</label>
                                            <input type="text" class="form-control" id="unit_organisasi" name="unit_organisasi"
                                                required readonly>
                                            <div class="invalid-feedback">Unit organisasi harus diisi.</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="alamat_rumah" class="form-label">Alamat Rumah</label>
                                            <input type="text" class="form-control" id="alamat_rumah" name="alamat_rumah" required readonly>
                                            <div class="invalid-feedback">Alamat rumah harus diisi.</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="no_ktp" class="form-label">No. KTP</label>
                                            <input type="text" class="form-control" id="no_ktp" name="no_ktp" required readonly>
                                            <div class="invalid-feedback">No. KTP harus diisi.</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="rating_pengguna" class="form-label">Rating Penggunaan Kendaraan <span class="text-danger"> *</span></label>
                                            <div class="rating-container">
                                                <div class="star-rating d-flex align-items-center">
                                                    <div class="rating-stars">
                                                        <input type="radio" id="star5" name="rating_pengguna" value="5" required />
                                                        <label for="star5" title="Sangat Baik"><i class="bi bi-star-fill"></i></label>

                                                        <input type="radio" id="star4" name="rating_pengguna" value="4" />
                                                        <label for="star4" title="Baik"><i class="bi bi-star-fill"></i></label>

                                                        <input type="radio" id="star3" name="rating_pengguna" value="3" />
                                                        <label for="star3" title="Cukup"><i class="bi bi-star-fill"></i></label>

                                                        <input type="radio" id="star2" name="rating_pengguna" value="2" />
                                                        <label for="star2" title="Kurang"><i class="bi bi-star-fill"></i></label>

                                                        <input type="radio" id="star1" name="rating_pengguna" value="1" />
                                                        <label for="star1" title="Sangat Kurang"><i class="bi bi-star-fill"></i></label>
                                                    </div>
                                                    <span class="ms-3 rating-text">0/5</span>
                                                </div>
                                                <div class="form-text">
                                                    <small><i class="bi bi-info-circle"></i> Berikan rating penggunaan kendaraan (1-5)</small>
                                                </div>
                                                <div class="invalid-feedback d-block" style="display:none!important" id="rating-error">
                                                    Rating penggunaan kendaraan harus dipilih.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="kendaraan_id" class="form-label">Kendaraan</label>
                                            <select class="form-control" id="kendaraan_id_kembali" name="kendaraan_id" required>
                                                <option value="" disabled selected>Kendaraan</option>
                                            </select>
                                            <div class="invalid-feedback">Kendaraan harus dipilih.</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="pengemudi" class="form-label">Nama Pengemudi</label>
                                            <input type="text" class="form-control" id="pengemudi" name="pengemudi" required readonly>
                                            <div class="invalid-feedback">Nama pengemudi harus diisi.</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="no_hp" class="form-label">Nomor HP</label>
                                            <input type="text" class="form-control" id="no_hp" name="no_hp" required readonly>
                                            <div class="invalid-feedback">Nomor HP harus diisi.</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                                            <input type="date" class="form-control" id="tanggal_pinjam" name="tanggal_pinjam"
                                                readonly required>
                                            <div class="invalid-feedback">Tanggal pinjam harus diisi.</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                                            <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali"
                                                required min="<?= date('Y-m-d') ?>">
                                            <small class="form-text text-muted">
                                                Tanggal kembali yang telah ditetapkan pada saat peminjaman.
                                            </small>
                                            <div class="invalid-feedback">Tanggal kembali harus diisi.</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="kondisi_kembali" class="form-label">Kondisi Kendaraan Saat Pengembalian <span class="text-danger"> *</span></label>
                                            <select class="form-control" id="kondisi_kembali" name="kondisi_kembali" required>
                                                <option value="" disabled selected>Pilih Kondisi</option>
                                                <option value="Baik">Baik</option>
                                                <option value="Rusak Ringan">Rusak Ringan</option>
                                                <option value="Rusak Berat">Rusak Berat</option>
                                            </select>
                                            <div class="invalid-feedback">Kondisi kendaraan harus dipilih.</div>
                                        </div>
                                    </div>
                                </div>

                                <div id="late_return_section" class="col-12 mb-3 d-none">
                                    <div class="card border-danger">
                                        <div class="card-header bg-danger text-white">
                                            <h5 class="mb-0">
                                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                Keterlambatan Pengembalian
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-warning" role="alert">
                                                <p><strong>Perhatian:</strong> Anda terlambat mengembalikan kendaraan selama <span id="late_days_display" class="fw-bold">0</span> hari.</p>
                                                <p class="mb-0">Mohon berikan alasan keterlambatan untuk melanjutkan proses pengembalian.</p>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="alasan_keterlambatan" class="form-label">Alasan Keterlambatan <span class="text-danger"> *</span></label>
                                                <textarea class="form-control" id="alasan_keterlambatan" name="alasan_keterlambatan" rows="3"
                                                    placeholder="Jelaskan alasan keterlambatan pengembalian kendaraan"></textarea>
                                                <div class="invalid-feedback">Alasan keterlambatan wajib diisi untuk pengembalian yang terlambat.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Ambil Foto Kendaraan <span class="text-danger"> *</span></label>
                                    <div>
                                        <button type="button" class="btn btn-secondary" id="btn-camera-capture">
                                            <i class="bi bi-camera"></i> Ambil Foto
                                        </button>
                                    </div>

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
                                    <input type="hidden" id="photo-data" name="photo_data" required>
                                    <div class="invalid-feedback d-block" style="display:none!important" id="photo-error">
                                        Foto kendaraan harus diambil.
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary" id="btn-next-tab">Selanjutnya</button>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="detail-kendaraan" role="tabpanel"
                                aria-labelledby="detail-kendaraan-tab">
                                <div class="card p-3 mb-3">
                                    <h6 class="card-title fw-bold">Pihak Kedua <small class="text-muted">(Dapat diedit)</small></h6>

                                    <div class="row mb-2">
                                        <div class="col-md-3">Nama <span class="text-danger"> *</span></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="pihak_kedua_nama" name="pihak_kedua_nama"
                                                value="Pak Udin" required>
                                            <div class="invalid-feedback">Nama pihak kedua harus diisi.</div>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-md-3">NIP <span class="text-danger"> *</span></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="pihak_kedua_nip" name="pihak_kedua_nip"
                                                value="12345678" required>
                                            <div class="invalid-feedback">NIP pihak kedua harus diisi.</div>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-md-3">Jabatan <span class="text-danger"> *</span></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="pihak_kedua_jabatan" name="pihak_kedua_jabatan"
                                                value="Kepala Satuan Kerja Selaku Kuasa Pengguna Barang" required>
                                            <div class="invalid-feedback">Jabatan pihak kedua harus diisi.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="nomor_sip" class="form-label">Nomor SIP / Surat Penanggung Jawab <span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" id="nomor_sip" name="nomor_sip" required>
                                    <div class="invalid-feedback">Nomor SIP/Surat Penanggung Jawab harus diisi.</div>
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

<!-- Modal Peminjaman -->
<div class="modal fade" id="modalPeminjaman" tabindex="-1" aria-labelledby="modalPeminjamanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow modal-modern">
            <div class="modal-header modal-header-modern">
                <h5 class="modal-title" id="modalPeminjamanLabel">Form Peminjaman Kendaraan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formPeminjaman" action="<?= base_url('/AsetKendaraan/pinjam'); ?>" method="post" class="pinjam"
                enctype="multipart/form-data">

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

<!-- Modal Edit Aset -->
<div class="modal fade" id="modalEditAset" tabindex="-1" aria-labelledby="modalEditAsetLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow modal-modern">
            <div class="modal-header modal-header-modern">
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

<!-- Modal Image Preview -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow modal-modern">
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

<!-- Modal Tracking Map -->
<div class="modal fade" id="trackingMapModal" tabindex="-1" aria-labelledby="trackingMapLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow modal-modern">
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
<div class="modal fade" id="modalTimeline" tabindex="-1" aria-labelledby="modalTimelineLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow modal-modern">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTimelineLabel">Verifikasi Peminjaman & Pengembalian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
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

                <div class="tab-content p-3" id="timelineTabContent">
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

                    <div class="tab-pane fade" id="pengembalian" role="tabpanel" aria-labelledby="pengembalian-tab">
                        <div class="table-responsive">
                            <h5 class="mb-3">Pengembalian Pending</h5>
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

                            <h5 class="mt-4 mb-3">Histori Penolakan Pengembalian</h5>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Penanggung Jawab</th>
                                        <th>Kendaraan</th>
                                        <th>Status</th>
                                        <th>Alasan Penolakan</th>
                                        <th>Dokumen</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tanggal Kembali</th>
                                    </tr>
                                </thead>
                                <tbody id="penolakanHistoryTable">
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-3">Memuat histori penolakan...</p>
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

<!-- ================= MODERN STYLESHEET ================= -->
<style>
:root{
    --kv-bg:#f6f7fb;
    --kv-surface:#ffffff;
    --kv-border:#e7e9f0;
    --kv-text:#1c2333;
    --kv-text-muted:#6b7280;
    --kv-primary:#3950A2;
    --kv-primary-dark:#2c3d80;
    --kv-radius:16px;
}

.kendaraan-page{ color:var(--kv-text); }

/* Header */
.page-header .page-title{
    font-size:28px; font-weight:700; margin:0 0 2px; color:var(--kv-text);
}
.page-header .page-subtitle{ color:var(--kv-text-muted); margin:0; font-size:14px; }
.page-header .breadcrumb{ background:transparent; padding:0; }
.page-header .breadcrumb-item a{ color:var(--kv-text-muted); text-decoration:none; }
.page-header .breadcrumb-item.active{ color:var(--kv-text); }

/* Filter bar */
.filter-bar{
    background:var(--kv-surface);
    border:1px solid var(--kv-border);
    border-radius:14px;
    padding:14px 16px;
}
.form-select-modern, .form-control-modern{
    border:1px solid var(--kv-border);
    background:#fafbff;
    border-radius:10px;
    font-size:14px;
    padding:8px 12px;
}
.form-select-modern:focus, .form-control-modern:focus{
    border-color:var(--kv-primary);
    box-shadow:0 0 0 3px rgba(79,93,246,0.12);
}
.search-wrapper{ position:relative; }
.search-wrapper i{
    position:absolute; left:12px; top:50%; transform:translateY(-50%);
    color:var(--kv-text-muted); font-size:14px;
}
.search-wrapper .form-control-modern{ padding-left:34px; }
.view-toggle .btn-view{
    border:1px solid var(--kv-border); background:#fafbff; color:var(--kv-text-muted);
}
.view-toggle .btn-view.active{ background:var(--kv-primary); color:#fff; border-color:var(--kv-primary); }

/* Grid */
.vehicle-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));
    gap:20px;
}

/* Card */
.vc-card{
    background:var(--kv-surface);
    border:1px solid var(--kv-border);
    border-radius:var(--kv-radius);
    overflow:hidden;
    display:flex;
    flex-direction:column;
    height:100%;
    transition:transform .25s ease, box-shadow .25s ease;
}
.vc-card:hover{
    transform:translateY(-4px);
    box-shadow:0 14px 30px rgba(28,35,51,0.08);
}

.vc-image-wrap{ position:relative; height:170px; background:#eef0f6; }
.vc-image{ width:100%; height:100%; object-fit:cover; transition:transform .5s ease; }
.vc-card:hover .vc-image{ transform:scale(1.05); }

.vc-status-badge{
    position:absolute; top:12px; right:12px;
    font-size:12px; font-weight:600; padding:5px 12px; border-radius:20px;
    display:inline-flex; align-items:center; gap:5px;
    backdrop-filter:blur(2px);
}
.status-available{ background:rgba(34,197,94,0.14); color:#16803d; }
.status-pending{ background:rgba(245,158,11,0.16); color:#92650a; }
.status-borrowed{ background:rgba(59,130,246,0.16); color:#1d4ed8; }

.vc-info-badge{
    position:absolute; top:12px; left:12px;
    width:26px; height:26px; border-radius:50%;
    background:rgba(220,53,69,0.9); color:#fff;
    display:flex; align-items:center; justify-content:center; font-size:13px;
    cursor:pointer;
}

.vc-body{ padding:16px; flex:1; }
.vc-title-row{ display:flex; justify-content:space-between; align-items:flex-start; gap:8px; margin-bottom:12px; }
.vc-title{ font-size:16px; font-weight:700; margin:0; color:var(--kv-text); }
.vc-condition-badge{
    font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px; white-space:nowrap;
}
.cond-good{ background:rgba(34,197,94,0.14); color:#16803d; }
.cond-warn{ background:rgba(245,158,11,0.16); color:#92650a; }
.cond-bad{ background:rgba(220,53,69,0.14); color:#b91c1c; }

.vc-meta-grid{
    display:grid; grid-template-columns:1fr 1fr; gap:10px;
}
.vc-meta{ display:flex; align-items:flex-start; gap:8px; }
.vc-meta i{ color:var(--kv-primary); font-size:15px; margin-top:2px; }
.vc-meta-label{ display:block; font-size:11px; color:var(--kv-text-muted); }
.vc-meta-value{ display:block; font-size:13px; font-weight:600; color:var(--kv-text); }

.vc-return-date{
    margin-top:14px; padding:8px 12px; border-radius:10px;
    background:#eef2ff; border-left:3px solid var(--kv-primary);
    display:flex; justify-content:space-between; align-items:center;
}
.vc-return-value{ font-weight:700; font-size:13px; color:var(--kv-text); }

.vc-footer{ padding:0 16px 16px; }

.vc-btn{
    border:none; border-radius:999px; padding:11px 16px; font-size:14px; font-weight:600;
    display:flex; align-items:center; justify-content:center; gap:8px;
    transition:opacity .2s ease, transform .2s ease, box-shadow .2s ease;
}
.vc-btn:hover{ opacity:.92; }
.vc-btn:active{ transform:scale(0.98); }
.vc-btn-primary{ background:#3950A2; color:#fff; box-shadow:0 4px 10px rgba(57,80,162,0.25); }
.vc-btn-success{ background:#157347; color:#fff; box-shadow:0 4px 10px rgba(21,115,71,0.22); }
.vc-btn-info{ background:#3950A2; color:#fff; box-shadow:0 4px 10px rgba(57,80,162,0.22); }
.vc-btn-secondary{ background:#5C636A; color:#fff; box-shadow:0 4px 10px rgba(92,99,106,0.2); }
.vc-btn-muted{ background:#e5e7eb; color:#9ca3af; cursor:not-allowed; box-shadow:none; }
.vc-btn-outline{ background:#fff; border:1.5px solid #3950A2; color:#3950A2; }
.vc-btn-outline-danger{ background:#fff; border:1.5px solid #dc3545; color:#dc3545; }

.vc-documents{ margin-top:4px; }
.vc-documents-header{ font-size:12px; font-weight:600; color:var(--kv-text-muted); margin-bottom:6px; }
.vc-doc-link{
    display:flex; align-items:center; gap:6px; font-size:12.5px; font-weight:600;
    color:var(--kv-primary); border:1px solid var(--kv-border); border-radius:10px;
    padding:7px 10px; margin-bottom:6px; text-decoration:none;
}
.vc-doc-link:hover{ background:#f4f5ff; }

/* Modals */
.modal-content.modal-modern{ border-radius:16px; overflow:hidden; }
.modal-header-modern{ background:linear-gradient(135deg,var(--kv-primary) 0%, var(--kv-primary-dark) 100%); color:#fff; }

/* Rating stars kept from original */
.rating-stars{ direction:rtl; display:inline-block; }
.rating-stars input[type="radio"]{ display:none; }
.rating-stars label{ color:#bbb; font-size:1.5rem; padding:0; cursor:pointer; margin:0 2px; }
.rating-stars label:hover, .rating-stars label:hover ~ label, .rating-stars input[type="radio"]:checked ~ label{ color:#ffb700; }
.rating-text{ font-size:1rem; align-self:center; }
.rating-container{ margin-bottom:10px; }

#kendaraan_id_kembali[readonly]{
    background-color:#e9ecef !important; pointer-events:none; -webkit-appearance:none; -moz-appearance:none; appearance:none;
}
#kendaraan_id_kembali{
    background-color:#e9ecef !important; color:#6c757d !important; pointer-events:none !important;
    cursor:not-allowed !important; border-color:#ced4da !important;
}
.form-control[readonly]{ background-color:#e9ecef; color:#6c757d; opacity:1; }

@media (max-width:768px){
    .page-header{ text-align:center; }
    .page-header nav{ justify-content:center !important; margin-top:8px; }
    .vehicle-grid{ grid-template-columns:1fr; }
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