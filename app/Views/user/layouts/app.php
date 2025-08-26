<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Manajemen Aset') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/pupr.ico') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">

    <!-- Optional: Ikon Bootstrap / Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f0f4f9;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: white;
            border-bottom: 1px solid #e0e0e0;
            padding: 8px 0;
        }

        .navbar-brand img {
            height: 44px;
            margin-right: 10px;
        }

        .page-content {
            padding: 30px 0;
        }

        .page-title {
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
        }

        .building-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .building-card {
            width: 100%;
            max-width: 300px;
            height: 200px;
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            cursor: pointer;
            transition: transform 0.3s;
        }

        .building-card:hover {
            transform: translateY(-5px);
        }

        .building-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .building-card .card-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px;
            background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
            color: white;
            font-weight: 500;
            text-align: center;
        }

        /* Form styling */
        .form-label {
            font-weight: 500;
            color: #555;
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #ccc;
            padding: 8px 12px;
        }

        .form-select {
            border-radius: 4px;
            border: 1px solid #ccc;
            padding: 8px 12px;
            background-color: #f8f8f8;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 8px 24px;
            font-weight: 500;
        }

        .nav-link {
            color: #333;
            font-weight: 500;
            padding: 8px 16px;
        }

        .nav-link.active {
            font-weight: 600;
            color: #007bff;
        }

        .nav-link i {
            margin-right: 5px;
        }

        .logout-link {
            color: #dc3545;
        }

        .user-info {
            margin-left: 15px;
            color: #666;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('mainpage') ?>">
                <img src="<?= base_url('assets/images/logoPU.png') ?>" alt="Logo PUPR">
                <span class="d-none d-md-inline">KEMENTERIAN PEKERJAAN UMUM</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <a class="nav-link <?= current_url() == base_url('mainpage') ? 'active' : '' ?>" href="<?= base_url('mainpage') ?>">
                            <i class="bi bi-house-door"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), 'kendaraan') ? 'active' : '' ?>" href="<?= base_url('user/homepage') ?>">
                            <i class="bi bi-car-front"></i> Kendaraan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), 'ruangan') ? 'active' : '' ?>" href="<?= base_url('user/ruangan') ?>">
                            <i class="bi bi-building"></i> Ruangan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), 'profile') ? 'active' : '' ?>" href="<?= base_url('user/profile') ?>">
                            <i class="bi bi-person"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), 'dashboard') ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), 'riwayat') ? 'active' : '' ?>" href="<?= base_url('riwayat') ?>">
                            <i class="bi bi-clock-history"></i> Riwayat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), 'daftar') ? 'active' : '' ?>" href="<?= base_url('daftar') ?>">
                            <i class="bi bi-list-check"></i> Daftar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link logout-link" href="<?= base_url('logout') ?>">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                    <?php if(session()->get('username')): ?>
                    <li class="nav-item">
                        <span class="user-info">| <?= esc(session()->get('username')) ?></span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Konten Halaman -->
    <div class="page-content">
        <div class="container">
            <?php if(isset($pageTitle)): ?>
                <h2 class="page-title"><?= esc($pageTitle) ?></h2>
            <?php endif; ?>
            
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- Bootstrap 5.3.2 JS Bundle (termasuk Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>