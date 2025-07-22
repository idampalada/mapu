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
                    <a class="navbar-brand-img" href="#"><img src="<?= base_url('assets/images/logoPU.png') ?>" style="width: 13rem; height: 3rem" alt="PUPR Logo"></a>
                </h3>
            </div>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
            aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
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
                        <i class="bi bi-door-open"></i> Room
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
