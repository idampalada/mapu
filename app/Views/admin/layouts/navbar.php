<?php
helper('auth');

// Gunakan URI segment pertama dengan nilai default 'mainpage' jika kosong
$uriSegment = service('uri')->getSegment(2) ?? 'mainpage';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid">
        <div class="navbar-brand-wrapper">
            <div class="app-brand">
                <h3 class="app-title">
                    <a class="navbar-brand-img" href="<?= base_url('mainpage') ?>">
    <img src="<?= base_url('assets/images/logoPU.png') ?>" style="width: 13rem; height: 3rem" alt="PUPR Logo">
</a>

                </h3>
            </div>
        </div>

<button class="navbar-toggler d-lg-none"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#mobileMenu"
        aria-controls="mobileMenu"
        aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>


        <div class="collapse navbar-collapse justify-content-end" id="navbarMain">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?= ($uriSegment == 'mainpage') ? 'active' : '' ?>" href="<?= base_url('mainpage') ?>">
                        <i class="bi bi-house-door-fill"></i> Home
                    </a>
                </li>

                <li class="nav-item">
                <a class="nav-link <?= ($uriSegment == 'barang') ? 'active' : '' ?>" href="<?= base_url('user/barang') ?>">
                    <i class="bi bi-box-seam"></i> Barang
                </a>
            </li>

                            <li class="nav-item">
                    <a class="nav-link scan-menu <?= ($uriSegment == 'scan') ? 'active' : '' ?>" href="<?= base_url('user/scan') ?>">
                        <i class="bi bi-qr-code-scan"></i> Scan
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link <?= ($uriSegment == 'homepage') ? 'active' : '' ?>" href="<?= base_url('homepage') ?>">
                        <i class="bi bi-car-front-fill"></i> Kendaraan
                    </a>
                </li>

                <?php if (in_groups('user') ||
                        in_groups('admin') || 
                        in_groups('admin_gedungutama') || 
                        in_groups('admin_pusdatin') || 
                        in_groups('admin_binamarga') || 
                        in_groups('admin_ciptakarya') || 
                        in_groups('admin_sda') || 
                        in_groups('admin_gedungg') ||
                        in_groups('admin_heritage') ||
                        in_groups('admin_auditorium')): ?>
                    <!-- <li class="nav-item">
                        <a class="nav-link <?= ($uriSegment == 'riwayat') ? 'active' : '' ?>" href="<?= base_url('user/riwayat') ?>">
                            <i class="bi bi-sliders"></i> Riwayat
                        </a>
                    </li> -->
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?= ($uriSegment == 'ruangan') ? 'active' : '' ?>" href="<?= base_url('user/ruangan') ?>">
                        <i class="bi bi-door-open"></i> Ruangan
                    </a>
                </li>
                <li>
                <a class="nav-link" href="<?= base_url('user/profile'); ?>">
                    <i class="bi bi-person-circle"></i> Profile
                </a>
            </li>

                <?php if (in_groups('admin') || 
                        in_groups('admin_gedungutama') || 
                        in_groups('admin_pusdatin') || 
                        in_groups('admin_binamarga') || 
                        in_groups('admin_ciptakarya') || 
                        in_groups('admin_sda') || 
                        in_groups('admin_gedungg') ||
                        in_groups('admin_heritage') ||
                        in_groups('admin_auditorium')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($uriSegment == 'dashboard') ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">
                            <i class="bi bi-sliders"></i> Dashboard
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (in_groups('admin') || in_groups('admin_gedungutama')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-card-list"></i> History
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('admin/riwayat') ?>">
                                    <i class="bi bi-arrow-left-right me-2"></i>Peminjaman & Pengembalian
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('admin/laporan/pemeliharaan-rutin') ?>">
                                    <i class="bi bi-tools me-2"></i>Pemeliharaan
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person"></i> Daftar
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('admin/daftar-pengguna') ?>">
                                    <i class="bi bi-person me-2"></i>Daftar Pengguna
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('admin/daftar-aset') ?>">
                                    <i class="bi bi-book me-2"></i>Daftar Aset
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('logout') ?>">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>

                <span>|</span>

                <a class="nav-link" style="color:blue"><?= user()->fullname; ?></a>
            </ul>
        </div>
    </div>
</nav>
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header">
        <h6 class="mb-0">Hello, <?= user()->fullname; ?></h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-0">
        <ul class="list-group list-group-flush mobile-menu">

            <li class="list-group-item">
                <a href="<?= base_url('mainpage') ?>">
                    <i class="bi bi-house-door-fill"></i> Home
                </a>
            </li>

            <li class="list-group-item">
                <a href="<?= base_url('user/barang') ?>">
                    <i class="bi bi-box-seam"></i> Barang
                </a>
            </li>

            <li class="list-group-item">
                <a href="<?= base_url('user/scan') ?>">
                    <i class="bi bi-qr-code-scan"></i> Scan
                </a>
            </li>

            <li class="list-group-item">
                <a href="<?= base_url('homepage') ?>">
                    <i class="bi bi-car-front-fill"></i> Kendaraan
                </a>
            </li>

            <li class="list-group-item">
                <a href="<?= base_url('user/ruangan') ?>">
                    <i class="bi bi-door-open"></i> Room
                </a>
            </li>

            <li class="list-group-item">
                <a href="<?= base_url('user/profile') ?>">
                    <i class="bi bi-person-circle"></i> Profile
                </a>
            </li>

            <li class="list-group-item text-danger">
                <a href="<?= base_url('logout') ?>">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>

        </ul>
    </div>
</div>

<style>
    @media (max-width: 991px) {

    .offcanvas {
        width: 78%;
        max-width: 320px;
    }

    .mobile-menu a {
        display: flex;
        align-items: center;
        gap: .75rem;
        text-decoration: none;
        color: #333;
        font-size: .95rem;
    }

    .mobile-menu .list-group-item {
        padding: .9rem 1.2rem;
        border-color: #f1f1f1;
    }

    .mobile-menu .list-group-item:hover {
        background: #f8f9fa;
    }
}

    </style>
