<?= $this->extend('admin/layouts/app') ?>
<?php helper('auth'); ?>
<?= $this->section('content') ?>

<div class="content-container">
    <div class="page-heading">
        <div class="page-title">
            <div class="row mb-4">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Kategori: <?= esc($kategoriLabel) ?></h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url('user/barang') ?>">Barang</a></li>
                            <li class="breadcrumb-item active"><?= esc($kategoriLabel) ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($barang)): ?>
        <div class="alert text-center text-white" style="background-color: #074799; border-radius: 1rem;">
            Belum ada barang yang ditambahkan dalam kategori ini.
        </div>
    <?php else: ?>
        <section class="section">
            <div class="row">
                <?php $modals = []; ?>
                <?php foreach ($barang as $item): ?>
                    <?php if ($item['status'] === 'Dipinjam'): ?>
    <!-- <div class="small text-muted">
        Debug: Status: <?= $item['status'] ?>, 
        Pinjam ID: <?= !empty($item['pinjam_id']) ? $item['pinjam_id'] : 'Kosong' ?>,
        Peminjaman Status: <?= !empty($item['pinjam_status']) ? $item['pinjam_status'] : 'Unknown' ?>
    </div> -->
<?php endif; ?>
<div class="col-md-4 mb-4 d-flex">
    <div class="card h-100 w-100 shadow-sm">
        <img src="<?= base_url('uploads/barang/' . $item['gambar']) ?>"
             class="card-img-top barang-image image-preview-trigger"
              data-fotos='<?= json_encode([$item['gambar']]) ?>'
             alt="<?= esc($item['nama_barang']) ?>">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title fw-bold mb-2 text-capitalize"><?= esc($item['nama_barang']) ?></h5>

            <div class="mb-1 d-flex">
                <small class="text-muted me-2" style="min-width: 90px;">Kondisi:</small>
                <span><?= esc($item['kondisi']) ?></span>
            </div>
            <div class="mb-1 d-flex">
                <small class="text-muted me-2" style="min-width: 90px;">Lokasi:</small>
                <span><?= esc($item['lokasi']) ?></span>
            </div>
            <div class="mb-1 d-flex">
                <small class="text-muted me-2" style="min-width: 90px;">Kode Barang:</small>
                <span><?= esc($item['kode_barang']) ?></span>
            </div>
            <div class="mb-1 d-flex">
                <small class="text-muted me-2" style="min-width: 90px;">Status:</small>
                <span><?= esc($item['status']) ?></span>
            </div>
        </div>
        <div class="card-footer bg-white border-0">
            <div class="d-grid">
                <?php if ($item['status'] === 'Tersedia') : ?>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill"
                            data-bs-toggle="modal" data-bs-target="#modalPinjam<?= $item['id'] ?>">
                        <i class="bi bi-calendar-plus"></i> Pinjam
                    </button>
                <?php elseif ($item['status'] === 'Dipinjam') : ?>
                    <button type="button" class="btn btn-info btn-sm rounded-pill"
                            onclick="kembalikanBarangById(<?= $item['id'] ?>)">
                        <i class="bi bi-box-arrow-in-down"></i> Kembalikan
                    </button>
                    <?php endif; ?>

                    <!-- <?php if ($item['status'] !== 'Tersedia') : ?>
  <button type="button" class="btn btn-danger btn-sm rounded-pill mt-2"
          onclick="ajukanPerbaikan(<?= $item['id'] ?>, '<?= esc($item['nama_barang']) ?>')">
    <i class="bi bi-tools"></i> Ajukan Perbaikan
  </button>
                <?php endif; ?> -->
            </div>
        </div>
    </div>
</div>
<!-- Modal Galeri -->
<div class="modal fade" id="modalGaleriBarang" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div id="carouselGaleriBarang" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner" id="carouselGaleriBarangInner">
            <!-- Slides injected via JS -->
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselGaleriBarang" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselGaleriBarang" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>


                    <?php ob_start(); ?>
                    <!-- Modal Form Pinjam -->
                    <div class="modal fade" id="modalPinjam<?= $item['id'] ?>" tabindex="-1" aria-labelledby="modalPinjamLabel<?= $item['id'] ?>" aria-hidden="true">
                      <div class="modal-dialog">
                      <form class="formPinjamBarang" data-barang-id="<?= $item['id'] ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="barang_id" value="<?= $item['id'] ?>">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalPinjamLabel<?= $item['id'] ?>">Form Peminjaman Barang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                </div>
                                <div class="modal-body">
                                <div class="mb-3">
  <label for="nama_peminjam<?= $item['id'] ?>" class="form-label">Nama Peminjam</label>
  <input type="text" name="nama_peminjam" class="form-control" id="nama_peminjam<?= $item['id'] ?>" required>
</div>
                                    <div class="mb-3">
    <label for="tanggal<?= $item['id'] ?>" class="form-label">Tanggal</label>
    <input type="date" name="tanggal" class="form-control" id="tanggal<?= $item['id'] ?>" required>
</div>
<div class="mb-3">
    <label for="waktu_mulai<?= $item['id'] ?>" class="form-label">Waktu Mulai</label>
    <input type="time" name="waktu_mulai" class="form-control" id="waktu_mulai<?= $item['id'] ?>" required>
</div>
<div class="mb-3">
    <label for="waktu_selesai<?= $item['id'] ?>" class="form-label">Waktu Selesai</label>
    <input type="time" name="waktu_selesai" class="form-control" id="waktu_selesai<?= $item['id'] ?>" required>
</div>
                                    <div class="mb-3">
                                        <label for="keperluan<?= $item['id'] ?>" class="form-label">Keperluan</label>
                                        <textarea name="keperluan" class="form-control" id="keperluan<?= $item['id'] ?>" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Ajukan Peminjaman</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                </div>
                            </div>
                        </form>
                      </div>
                    </div>
                    <?php $modals[] = ob_get_clean(); ?>
                <?php endforeach ?>
            </div>
        </section>

        <!-- Render Semua Modal di Sini -->
        <?php foreach ($modals as $modal) {
            echo $modal;
        } ?>
    <?php endif; ?>
</div>
<script>
  const URL_PINJAM_BARANG = "<?= base_url('user/barang/pinjam') ?>";

  function kembalikanBarangById(barangId) {
    Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin mengembalikan barang ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Kembalikan",
        cancelButtonText: "Batal",
        confirmButtonColor: "#435ebe",
        cancelButtonColor: "#dc3545",
    }).then((result) => {
        if (result.isConfirmed) {
            // Tambahkan CSRF token
            const csrfName = '<?= csrf_token() ?>';
            const csrfHash = '<?= csrf_hash() ?>';
            
            fetch("/user/barang/kembalikanById", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": csrfHash  // Tambahkan header CSRF
                },
                body: JSON.stringify({ 
                    barang_id: barangId,
                    [csrfName]: csrfHash  // Tambahkan CSRF token ke body
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire("Berhasil!", data.message, "success").then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire("Gagal!", data.error, "error");
                }
            })
            .catch(error => {
                Swal.fire("Error!", "Terjadi kesalahan server.", "error");
                console.error(error);
            });
        }
    });
}
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.image-preview-trigger').forEach(img => {
    img.addEventListener('click', function () {
      const fotos = JSON.parse(this.getAttribute('data-fotos') || '[]');
      const inner = document.getElementById('carouselGaleriBarangInner');
      inner.innerHTML = '';

      if (fotos.length === 0) return;

      fotos.forEach((foto, index) => {
        const activeClass = index === 0 ? 'active' : '';
        inner.innerHTML += `
          <div class="carousel-item ${activeClass}">
            <img src="<?= base_url('uploads/barang/') ?>${foto}" 
                 class="d-block w-100" 
                 style="max-height: 50vh; object-fit: contain;" 
                 alt="Barang ${index + 1}">
          </div>`;
      });

      new bootstrap.Modal(document.getElementById('modalGaleriBarang')).show();
    });
  });
});

</script>
<script>
const USER_ID = <?= user()->id ?>;
</script>

<?= $this->endSection() ?>

