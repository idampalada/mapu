<div class="modal fade" id="modalSuratJalan" tabindex="-1" aria-labelledby="modalSuratJalanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuratJalanLabel">Buat Surat Jalan Kendaraan Dinas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formSuratJalan" action="<?= base_url('SuratJalan/generate') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" id="pinjam_id" name="pinjam_id" value="">
                    
                    <!-- Tampilkan data peminjam yang sudah ada -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nama_penanggung_jawab" class="form-label">Nama Penanggung Jawab</label>
                                <input type="text" class="form-control" id="nama_penanggung_jawab" name="nama_penanggung_jawab" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="nip_nrp" class="form-label">NIP/NRP</label>
                                <input type="text" class="form-control" id="nip_nrp" name="nip_nrp" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="pangkat_golongan" class="form-label">Pangkat/Golongan</label>
                                <input type="text" class="form-control" id="pangkat_golongan" name="pangkat_golongan" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <input type="text" class="form-control" id="jabatan" name="jabatan" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="kode_barang" class="form-label">Kode Barang</label>
                                <input type="text" class="form-control" id="kode_barang" name="kode_barang" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="no_polisi" class="form-label">Nomor Polisi</label>
                                <input type="text" class="form-control" id="no_polisi" name="no_polisi" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form untuk input data tambahan -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="jam_selesai" class="form-label">Jam Selesai</label>
                                <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="urusan_kedinasan" class="form-label">Urusan Kedinasan</label>
                        <textarea class="form-control" id="urusan_kedinasan" name="urusan_kedinasan" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat Surat Jalan</button>
                </div>
            </form>
        </div>
    </div>
</div>