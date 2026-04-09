<?= $this->extend('auth/layouts/index') ?>

<?= $this->section('content') ?>

<title>Login</title>
<div class="asset-grid"></div>

<div class="login-form">
    <h1 class="auth-title text-center">Masuk</h1>
    <p class="auth-subtitle mb-5">Silahkan masukkan email atau username dan kata sandi Anda</p>

    <?= view('Myth\Auth\Views\_message_block') ?>

    <form action="<?= route_to('login') ?>" method="post" class="users">
        <?= csrf_field() ?>

        <?php if ($config->validFields === ['email']): ?>
            <div class="form-floating mb-3">
                <input type="email"
                       class="form-control <?php if (session('errors.login')): ?>is-invalid<?php endif ?>"
                       name="login"
                       placeholder="Email atau Username">
                <label for="floatingInput">Email atau Username</label>
                <div class="invalid-feedback">
                    <?= session('errors.login') ?>
                </div>
            </div>
        <?php else: ?>
            <div class="form-floating mb-3">
                <input type="text"
                       class="form-control <?php if (session('errors.login')): ?>is-invalid<?php endif ?>"
                       name="login"
                       placeholder="Email atau Username">
                <label for="floatingInput">Email atau Username</label>
                <div class="invalid-feedback">
                    <?= session('errors.login') ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-floating mb-3">
            <input class="form-control <?php if (session('errors.password')): ?>is-invalid<?php endif ?>"
                   type="password"
                   name="password"
                   placeholder="Kata Sandi">
            <label for="floatingInput">Kata Sandi</label>
            <div class="invalid-feedback">
                <?= session('errors.password') ?>
            </div>
        </div>

        <?php if ($config->allowRemembering): ?>
            <div class="form-check mb-3">
                <input type="checkbox" name="remember" class="form-check-input"
                       <?php if (old('remember')): ?> checked <?php endif ?>>
                <label class="form-check-label"><?= lang('Auth.rememberMe') ?></label>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary w-100">Masuk</button>

        <?php if ($config->activeResetter): ?>
            <div class="text-center mt-4">
                <a class="katasandi" href="<?= route_to('forgot') ?>">Lupa Kata Sandi?</a>
            </div>
        <?php endif; ?>
    </form>
</div>

<style>
    body {
        background: url('https://your-background-image-url-here.jpg') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Poppins', sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
    }

    .login-form {
        background: rgba(255, 255, 255, 0.9); /* putih lembut tanpa blur */
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        border-radius: 20px;
        padding: 40px 45px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        width: 420px; /* ukuran awal */
        text-align: center;
    }

    .auth-title {
        font-weight: 700;
        font-size: 1.8rem;
        margin-bottom: 10px;
        color: #222;
    }

    .auth-subtitle {
        font-size: 0.95rem;
        color: #555;
        margin-bottom: 30px;
    }

    .form-floating {
        text-align: left;
    }

    .form-control {
        border-radius: 10px;
        border: 1px solid #ccc;
        padding: 12px;
        font-size: 1rem;
        background-color: #fff;
    }

    .form-control:focus {
        border-color: #2a6fd8;
        box-shadow: 0 0 0 3px rgba(42, 111, 216, 0.2);
    }

    .btn-primary {
        background-color: #2a3da3;
        border: none;
        padding: 12px;
        border-radius: 10px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-primary:hover {
        background-color: #1d2d7a;
    }

    .katasandi {
        color: #1a3bb4;
        font-weight: 500;
        text-decoration: none;
    }

    .katasandi:hover {
        text-decoration: underline;
    }

    @media (max-width: 500px) {
        .login-form {
            width: 90%;
            padding: 30px 25px;
        }
    }
</style>

<?= $this->endSection() ?>
