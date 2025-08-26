<!DOCTYPE html>
<html lang="en">

<head>
        <!-- Style Css -->
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Time Picker CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/time-picker.css') ?>">

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?= base_url(); ?>assets/pupr.ico" />
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <link rel="shortcut icon" href="<?= base_url(); ?>/assets/images/favicon.svg" type="image/x-icon">

    <link rel="stylesheet" href="<?= base_url(); ?>/assets/css/bootstrap.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url(); ?>/assets/css/app.css">

    <link rel="stylesheet" href="<?= base_url(); ?>/assets/vendors/iconly/bold.css">
    <link rel="stylesheet" href="<?= base_url(); ?>/assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <?= $this->renderSection('styles') ?>
</head>

<body>
    <div id="app">
        <?= $this->include('admin/layouts/navbar') ?>

        <div id="main" class="layout-navbar">
            <div class="content-wrapper container">
                <?= $this->renderSection('content') ?>
            </div>

            <?= $this->include('admin/layouts/footer') ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="<?= base_url(); ?>/assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="<?= base_url(); ?>/assets/js/main.js"></script>
    <script src="<?= base_url(); ?>/assets/js/homepage.js"></script>
    <script src="<?= base_url(); ?>/assets/js/laporan.js"></script>
    <script src="<?= base_url(); ?>/assets/js/dashboard.js"></script>
    <script src="<?= base_url(); ?>/assets/js/auth.js"></script>
    <script src="<?= base_url(); ?>/assets/js/pinjam-ruangan.js"></script>
    <script src="<?= base_url(); ?>/assets/js/image-preview.js"></script>
    <script src="<?= base_url(); ?>/assets/js/mainpage.js"></script>
    <script src="<?= base_url('assets/js/pinjam-barang.js') ?>"></script>



    <?= $this->renderSection('javascript') ?>
    

    <script>
        const csrfToken = {
            name: '<?= csrf_token() ?>',
            hash: '<?= csrf_hash() ?>'
        };
    </script>
    <!-- Chart.js Datalabels Plugin -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

</body>

</html>