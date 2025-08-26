<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center py-4">
        <h2 class="fw-bold text-primary">Kendaraan</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kendaraan</li>
            </ol>
        </nav>
    </div>

    <section class="section mb-5">
        <div class="row g-4">
            <?php foreach ($aset as $item): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; overflow: hidden; transition: transform 0.3s ease;">
                        <div class="position-relative" style="height: 13rem;">
                            <?php 
                            $images = json_decode($item['gambar_mobil'], true);
                            $images = is_array($images) ? $images : [$item['gambar_mobil']];
                            $mainImage = !empty($images) ? $images[0] : null;
                            ?>
                            <?php if (!empty($mainImage) && file_exists(ROOTPATH . 'public/uploads/images/' . $mainImage)): ?>
                                <img src="<?= base_url('/uploads/images/' . $mainImage) ?>"
                                    class="w-100 h-100 object-fit-cover image-preview-trigger" 
                                    data-images='<?= htmlspecialchars(json_encode($images)) ?>'
                                    alt="<?= $item['merk'] ?>"
                                    style="cursor: pointer; border-top-left-radius: .7rem; border-top-right-radius: .7rem;">
                            <?php else: ?>
                                <img src="<?= base_url('/assets/images/faces/1.jpg') ?>" 
                                    class="w-100 h-100 object-fit-cover"
                                    alt="<?= $item['merk'] ?>"
                                    style="border-top-left-radius: .7rem; border-top-right-radius: .7rem;">
                            <?php endif; ?>
                            <div class="position-absolute top-0 end-0 p-2 d-flex gap-2">
                                <span class="badge <?= $item['status_pinjam'] === 'Tersedia' ? 'bg-success' :
                                    ($item['status_pinjam'] === 'Pending' ? 'bg-warning' : 'bg-info') ?>">
                                    <?= $item['status_pinjam'] ?>
                                </span>
                            </div>
                        </div>

                        <div class="card-body" style="background-color: #f8f9fe;">
                            <h5 class="card-title fw-bold text-primary mb-3">
                                <?= $item['merk'] ?>
                            </h5>
                            <div class="mb-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="bg-white rounded-3 p-2 shadow-sm border-start border-3 border-primary">
                                            <small class="text-muted d-block">No. Polisi:</small>
                                            <span class="fw-medium"><?= $item['no_polisi'] ?></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-white rounded-3 p-2 shadow-sm border-start border-3 border-secondary">
                                            <small class="text-muted d-block">Tahun:</small>
                                            <span class="fw-medium"><?= $item['tahun_pembuatan'] ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-2 bg-white rounded-3 p-2 shadow-sm border-start border-3 border-info">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Kode Barang:</small>
                                            <span class="fw-medium"><?= $item['kode_barang'] ?></span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Tipe:</small>
                                            <span class="fw-medium"><?= $item['kategori_id'] ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-2 bg-white rounded-3 p-2 shadow-sm border-start border-3 border-success">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Kapasitas:</small>
                                            <span class="fw-medium"><?= $item['kapasitas'] ?> Orang</span>
                                        </div>
                                        <?php if (!empty($item['tanggal_kembali'])): ?>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Kembali:</small>
                                            <span class="fw-medium"><?= date('d/m/Y', strtotime($item['tanggal_kembali'])) ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mb-3">
                                <span class="badge <?= $item['kondisi'] === 'Baik' ? 'bg-success' :
                                    ($item['kondisi'] === 'Rusak Ringan' ? 'bg-warning' : 'bg-danger') ?>">
                                    <?= $item['kondisi'] ?>
                                </span>

                                <?php if (!empty($item['keterangan'])): ?>
                                    <span class="badge bg-danger" data-bs-toggle="tooltip" title="<?= $item['keterangan'] ?>">
                                        <i class="bi bi-info-circle"></i>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-0 p-3">
                            <div class="d-grid">
                                <div class="d-flex flex-column gap-2">
                                    <?php if ($item['status_pinjam'] === 'Tersedia' || $item['status_pinjam'] === 'Dalam Verifikasi'): ?>
                                        <?php if ($item['status_pinjam'] === 'Dalam Verifikasi'): ?>
                                            <button type="button" class="btn btn-secondary btn-sm rounded-pill shadow-sm" disabled>
                                                <i class="bi bi-clock"></i> Menunggu Verifikasi
                                            </button>
                                        <?php else: ?>
                                            <?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
                                            <!-- Tombol Tracking remains commented as in original -->
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-primary btn-sm rounded-pill shadow-sm hover-effect d-flex align-items-center justify-content-center gap-1"
                                            style="background-color: #133E87; color: white; border: none; height: 2.2rem;" onclick="openPeminjamanModal('<?= $item['id'] ?>')">
                                                <i class="bi bi-plus-circle"></i> Pinjam
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-info btn-sm rounded-pill shadow-sm"
                                            onclick="openPengembalianModal('<?= $item['id'] ?>')" style="background-color: #536493; color: white; border: none">
                                            <i class="bi bi-box-arrow-in-down"></i> Kembalikan
                                        </button>

                                        <?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
                                            <div class="mt-3">
                                                <h6 class="mb-2">Dokumen Peminjaman:</h6>
                                                <?php if (!empty($item['surat_permohonan']) && file_exists(ROOTPATH . 'public/uploads/documents/' . $item['surat_permohonan'])): ?>
                                                    <a href="<?= base_url('/uploads/documents/' . $item['surat_permohonan']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary mb-1 w-100">
                                                        <i class="bi bi-file-earmark-pdf"></i> Surat Permohonan
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (!empty($item['surat_jalan_admin']) && file_exists(ROOTPATH . 'public/uploads/documents/' . $item['surat_jalan_admin'])): ?>
                                                    <a href="<?= base_url('/uploads/documents/' . $item['surat_jalan_admin']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary mb-1 w-100">
                                                        <i class="bi bi-file-earmark-pdf"></i> Surat Jalan
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (in_groups(['admin', 'admin_gedungutama'])): ?>
                                        <button type="button" class="btn btn-warning btn-sm rounded-pill shadow-sm hover-effect d-flex align-items-center justify-content-center gap-1"
                                        style="background-color: #608BC1; color: white; border: none; height: 2.2rem;"
                                            onclick="openEditModal('<?= $item['id'] ?>')">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-sm rounded-pill shadow-sm hover-effect d-flex align-items-center justify-content-center gap-1" 
                                            style="background-color: #AE445A; color: white; border: none; height: 2.2rem;"
                                            onclick="deleteAset('<?= $item['id'] ?>')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<!-- Modals - unchanged from original -->
<div class="modal fade" id="modalPengembalian" tabindex="-1" aria-labelledby="modalPengembalianLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPengembalianLabel">Form Pengembalian Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formPengembalian" action="<?= base_url('/AsetKendaraan/kembali'); ?>" method="post"
                class="kembali" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="kendaraan_id_hidden" name="kendaraan_id" value="">

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nama_penanggung_jawab">Nama Penanggung Jawab</label>
                                <input type="text" class="form-control" id="nama_penanggung_jawab"
                                    name="nama_penanggung_jawab" required readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="nip_nrp">NIP / NRP</label>
                                <input type="text" class="form-control" id="nip_nrp" name="nip_nrp" required readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pangkat_golongan">Pangkat / Golongan</label>
                                <input type="text" class="form-control" id="pangkat_golongan" name="pangkat_golongan"
                                    required readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="jabatan">Jabatan</label>
                                <input type="text" class="form-control" id="jabatan" name="jabatan" required readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="unit_organisasi">Unit Organisasi</label>
                                <input type="text" class="form-control" id="unit_organisasi" name="unit_organisasi"
                                    required readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="surat_pengembalian">Surat Pengembalian (PDF)</label>
                                <input type="file" class="form-control" id="surat_pengembalian"
                                    name="surat_pengembalian" accept="application/pdf" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="kendaraan_id">Kendaraan</label>
                                <select class="form-control" id="kendaraan_id_kembali" name="kendaraan_id" required>
                                    <option value="" disabled selected>Kendaraan</option>
                                </select>
                                <!-- <input type="hidden" id="kendaraan_id_hidden" name="kendaraan_id"> -->
                            </div>

                            <div class="form-group mb-3">
                                <label for="pengemudi">Nama Pengemudi</label>
                                <input type="text" class="form-control" id="pengemudi" name="pengemudi" required readonly>
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

<!-- Added CSS for consistency with mainpage styling -->
<style>
    .card {
        transition: transform 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
    
    .hover-effect:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }
    
    .pagination .page-item .page-link {
        color: #0d6efd;
        border: none;
        margin: 0 2px;
        border-radius: 4px;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        color: white;
    }
    
    .row.g-4 > [class*="col-"] {
        padding: 10px;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
        border-radius: 6px;
    }
</style>

<script>
    const BASE_URL = '<?= base_url() ?>';
</script>

<?= $this->endSection() ?>