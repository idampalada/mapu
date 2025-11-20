// Update function bukaBookingModal untuk booking langsung
function bukaBookingModal(
  ruanganId,
  ruanganNama,
  ruanganKapasitas,
  ruanganFasilitas
) {
  // Buat modal dinamis untuk booking langsung
  const modalHtml = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #28a745, #20c997); color: white;">
                    <h5 class="modal-title">
                        <i class="bi bi-lightning-fill me-2"></i>
                        Booking Ruangan Langsung
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formBookingLangsung" action="${baseUrl}User/Ruangan/bookingLangsung" method="post">
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Booking Langsung:</strong> Ruangan akan langsung terbooking tanpa perlu persetujuan admin. 
                            Anda dapat langsung menggunakan ruangan sesuai jadwal yang dipilih.
                        </div>
                        
                        <input type="hidden" name="ruangan_id" value="${ruanganId}">
                        
                        <div class="mb-3">
                            <label class="form-label">Ruangan</label>
                            <input type="text" class="form-control" value="${ruanganNama}" readonly>
                            <small class="text-muted">Kapasitas maksimal: ${ruanganKapasitas} orang</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal" required 
                                           min="${
                                             new Date()
                                               .toISOString()
                                               .split("T")[0]
                                           }"
                                           max="${
                                             new Date(
                                               Date.now() +
                                                 30 * 24 * 60 * 60 * 1000
                                             )
                                               .toISOString()
                                               .split("T")[0]
                                           }">
                                    <small class="text-muted">Maksimal 30 hari ke depan</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Peserta <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="jumlah_peserta" required 
                                           max="${ruanganKapasitas}" min="1" 
                                           placeholder="Max ${ruanganKapasitas} orang">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="waktu_mulai" required 
                                           min="07:30" max="16:30" onchange="validateBookingTime()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="waktu_selesai" required 
                                           min="07:30" max="16:30" onchange="validateBookingTime()">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="keperluan" rows="3" required 
                                      placeholder="Jelaskan keperluan booking ruangan..." minlength="5"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Penanggung Jawab <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_penanggung_jawab" required 
                                   placeholder="Nama lengkap penanggung jawab" minlength="3">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Unit/Organisasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="unit_organisasi" required 
                                   placeholder="Nama unit/organisasi" minlength="3">
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Booking bersifat final dan langsung aktif</li>
                                <li>Harap datang tepat waktu sesuai jadwal</li>
                                <li>Pembatalan hanya bisa dilakukan H-1</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-lightning-fill"></i> Booking Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;

  // Hapus modal lama jika ada
  const existingModal = document.getElementById("modalBookingLangsung");
  if (existingModal) {
    existingModal.remove();
  }

  // Buat modal baru
  const modalElement = document.createElement("div");
  modalElement.className = "modal fade";
  modalElement.id = "modalBookingLangsung";
  modalElement.tabIndex = "-1";
  modalElement.innerHTML = modalHtml;
  document.body.appendChild(modalElement);

  // Tampilkan modal
  const modal = new bootstrap.Modal(modalElement);
  modal.show();

  // Handle form submission
  const form = document.getElementById("formBookingLangsung");
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Validate form
    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    // Submit dengan loading
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
    submitBtn.disabled = true;

    // Submit form
    form.submit();
  });
}

// Validation function untuk waktu booking
function validateBookingTime() {
  const waktuMulai = document.querySelector('input[name="waktu_mulai"]');
  const waktuSelesai = document.querySelector('input[name="waktu_selesai"]');

  if (waktuMulai && waktuSelesai && waktuMulai.value && waktuSelesai.value) {
    const mulai = new Date(`2024-01-01 ${waktuMulai.value}`);
    const selesai = new Date(`2024-01-01 ${waktuSelesai.value}`);

    if (selesai <= mulai) {
      waktuSelesai.setCustomValidity("Waktu selesai harus setelah waktu mulai");
    } else if ((selesai - mulai) / (1000 * 60) < 30) {
      waktuSelesai.setCustomValidity("Durasi minimal 30 menit");
    } else if ((selesai - mulai) / (1000 * 60 * 60) > 8) {
      waktuSelesai.setCustomValidity("Durasi maksimal 8 jam");
    } else {
      waktuSelesai.setCustomValidity("");
    }
  }
}
