<nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand-img" href="#"><img src="../assets/images/logoPU.png" style="width: 13rem; height: 3rem" alt="PUPR Logo"></a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
            aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            Menu
            <i class="bi bi-list"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav text-uppercase ms-auto py-4 py-lg-0 ">
                <li class="nav-item"><a class="nav-link fw-bold text-dark"
                        href="<?= base_url('#beranda'); ?>">Beranda</a></li>
                <li class="nav-item"><a class="nav-link fw-bold" href="<?= base_url('#services'); ?>">Layanan
                        Kami</a></li>
                <li class="nav-item"><a class="nav-link fw-bold" href="<?= base_url('#faq'); ?>">FAQ</a></li>
                <li class="nav-item btn btn-primary"><a class="nav-link text-white fw-bold"
                href="<?= base_url('register'); ?>">Daftar</a>
            </li>

                <?php if (service('authentication')->check()): ?>
                    <a class="btn fw-bold px-5 py-3 ms-2"
       style="background-color: #ffffff; color: #133E87; border-radius: 2rem; border: 2px solid #133E87; font-size: 1.1rem;"
       href="<?= base_url('logout'); ?>">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item btn btn-primary"><a class="nav-link text-white fw-bold"
                            href="<?= base_url('login'); ?>">Masuk</a></li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>