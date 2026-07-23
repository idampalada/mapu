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

<script>
    const BASE_URL = '<?= base_url() ?>';
</script>
<?= $this->endSection() ?>="fw-bold text-dark" value="KDJ">Kendaraan Dinamis Jalan (KDJ)</option>
                                    <option class="text-muted" disabled selected>Sedan, Hatchback, dan SUV</option>
                                    <option class="fw-bold text-dark" value="KDO">Kendaraan Dinamis Off-road (KDO)</option>
                                    <option class="text-muted" disabled selected>Bus, Truk, dan Kendaraan Box</option>
                                    <option class