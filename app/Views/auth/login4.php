<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manajemen Aset</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Halant:wght@400;700&display=swap');
        
        :root {
            --zoom-level: 0.90; 
            --cursor-x: -1000px; 
            --cursor-y: -1000px;
        }

        body { 
            background-color: #F4F9FC; 
            font-family: 'Halant', serif;
            margin: 0;
            overflow-x: hidden;
            zoom: var(--zoom-level);
            
            background-image: radial-gradient(#C6D6E6 2.5px, transparent 2.5px);
            background-size: 40px 40px; 
            background-position: 0 0;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            pointer-events: none; 
            z-index: 0;
            
            background-image: radial-gradient(#6B8EAC 4px, transparent 4px);
            background-size: 40px 40px;
            background-position: 0 0;

            -webkit-mask: radial-gradient(circle 200px at var(--cursor-x) var(--cursor-y), black 0%, transparent 100%);
            mask: radial-gradient(circle 200px at var(--cursor-x) var(--cursor-y), black 0%, transparent 100%);
        }

        .input-figma {
            border-style: solid;
            border-color: #2B377B;
            border-width: 2px 2px 5px 2px;
        }

        /* Border merah untuk input yang error (Validasi gagal) */
        .input-error {
            border-color: #DC2626 !important; 
        }

        .bg-blob-shadow {
            box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25), inset 0px 4px 4px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center relative pb-20">

    <!-- Logo PU (Pojok Kiri Atas) -->
    <div class="absolute top-10 left-10 z-50">
        <img src="<?= base_url('assets/images/logopu.png') ?>" alt="Logo PU" class="h-20 w-auto">
    </div>

    <!-- Navbar -->
    <nav class="w-full max-w-[1147px] h-[73px] bg-white/67 border-[5px] border-[#E7EDED] rounded-[39px] flex items-center justify-center gap-12 mt-[51px] mb-[50px] z-20 relative">
        <a href="#" class="text-[32px] text-[#2B377B] leading-[50px] hover:opacity-80">Beranda</a>
        <a href="#" class="text-[32px] text-[#2B377B] leading-[50px] hover:opacity-80 mx-8">Layanan Kami</a>
        <a href="#" class="text-[32px] text-[#2B377B] leading-[50px] hover:opacity-80 mr-8">FAQ</a>
        <a href="<?= route_to('register') ?>" class="text-[32px] text-[#2B377B] leading-[50px] hover:opacity-80">Register</a>
        <a href="#" class="text-[32px] text-[#2B377B] font-bold leading-[50px] hover:opacity-80">Login</a>
    </nav>

    <!-- Kontainer Utama -->
    <main class="w-full max-w-[1600px] flex flex-wrap lg:flex-nowrap items-center justify-center gap-16 xl:gap-[100px] relative z-10 px-4">
        
        <!-- Kolom Kiri (Background Blur & Gambar Robot Besar) -->
        <div class="relative w-[828px] h-[798px] flex items-center justify-center shrink-0 scale-75 xl:scale-100 origin-center">
            <div class="absolute inset-0 bg-[#E9EFEF] border-[5px] border-[#E9EFEF] rounded-[112px] blur-[2px] bg-blob-shadow"></div>
            <img src="<?= base_url('assets/images/3d-robot-icon-design-cute-robot-with-laptop-3d-artificial-intelligence-photo.png') ?>" 
                 alt="AI Robot" class="relative z-10 w-[647px] h-[647px] object-contain">
        </div>

        <!-- Kolom Kanan (Form Login Terintegrasi Myth:Auth) -->
        <div class="flex flex-col items-center xl:items-start w-full max-w-[582px] shrink-0 mt-10 lg:mt-0">
            
            <div class="text-center w-full mb-6 flex flex-col items-center relative z-10">
                <h2 class="text-[32px] font-bold text-[#2B377B] leading-[50px]">Hello !</h2>
                <h1 class="text-[36px] font-bold text-[#2B377B] leading-[57px]">Welcome Back!</h1>
                <p class="text-[36px] text-[#2B377B] leading-[57px] mt-2">Let’s Login to Your Account</p>
            </div>

            <!-- Menampilkan Pesan Error Global Myth:Auth -->
            <div class="w-full text-center mb-4 relative z-10">
                <?= view('Myth\Auth\Views\_message_block') ?>
            </div>

            <!-- Form Action ke route 'login' Myth:Auth -->
            <form action="<?= route_to('login') ?>" method="POST" class="w-full flex flex-col items-center relative z-10">
                <!-- CSRF Token (Wajib di CI4) -->
                <?= csrf_field() ?>

                <!-- Logika Username / Email Myth:Auth -->
                <div class="w-[582px] mb-6">
                    <label class="block text-[24px] text-[#2B377B] mb-1 ml-6">
                        <?= ($config->validFields === ['email']) ? 'Email' : 'Email atau Username' ?>
                    </label>
                    <input type="<?= ($config->validFields === ['email']) ? 'email' : 'text' ?>" 
                           name="login" 
                           class="w-full h-[56px] bg-white input-figma rounded-[39px] px-6 text-[24px] outline-none <?php if (session('errors.login')): ?>input-error<?php endif ?>" 
                           value="<?= old('login') ?>"
                           required />
                    <!-- Pesan Error Username -->
                    <div class="text-red-500 text-lg ml-6 mt-1 font-sans">
                        <?= session('errors.login') ?>
                    </div>
                </div>
                
                <!-- Password -->
                <div class="w-[582px] mb-4">
                    <label class="block text-[24px] text-[#2B377B] mb-1 ml-6">Kata Sandi</label>
                    <input type="password" name="password" 
                           class="w-full h-[56px] bg-white input-figma rounded-[39px] px-6 text-[24px] outline-none <?php if (session('errors.password')): ?>input-error<?php endif ?>" 
                           required />
                    <!-- Pesan Error Password -->
                    <div class="text-red-500 text-lg ml-6 mt-1 font-sans">
                        <?= session('errors.password') ?>
                    </div>
                </div>

                <!-- Fitur Remember Me (Jika diaktifkan di config) -->
                <?php if ($config->allowRemembering): ?>
                    <div class="w-[582px] flex items-center ml-12 mb-4">
                        <input type="checkbox" name="remember" class="w-5 h-5 cursor-pointer" <?php if (old('remember')): ?> checked <?php endif ?>>
                        <label class="ml-3 text-[20px] text-[#2B377B]"><?= lang('Auth.rememberMe') ?></label>
                    </div>
                <?php endif; ?>

                <div class="w-[582px] flex justify-center mt-6">
                    <button type="submit" class="w-[188px] h-[53px] bg-[#30408E] text-white text-[32px] leading-[50px] rounded-[20px] border border-[#2B377B] hover:opacity-90 transition cursor-pointer shadow-md">
                        Masuk
                    </button>
                </div>
            </form>

            <div class="w-full flex flex-col items-center mt-10 relative z-10">
                <!-- Fitur Register -->
                <div class="text-[24px] text-[#2B377B] leading-[38px]">
                    Don’t have an account? 
                    <a href="<?= route_to('register') ?>" class="underline ml-2 hover:opacity-80 font-bold">Register now</a>
                </div>
                
                <!-- Fitur Reset Password Myth:Auth -->
                <?php if ($config->activeResetter): ?>
                    <a href="<?= route_to('forgot') ?>" class="text-[24px] text-red-600 font-semibold leading-[38px] mt-4 hover:underline">
                        Lupa Kata Sandi?
                    </a>
                <?php endif; ?>
                
                <a href="#" class="text-[20px] text-[#2B377B] leading-[38px] mt-6 hover:underline">
                    Privacy Policy
                </a>
            </div>

        </div>
    </main>

    <!-- Robot Kecil -->
    <div class="absolute bottom-[120px] right-10 xl:right-[5%] hidden md:block z-20">
        <img src="<?= base_url('assets/images/E118QZ79.png') ?>" alt="Small Robot" class="w-[300px] h-auto object-contain">
    </div>

    <!-- Script Interaktif untuk Efek Senter di Kursor -->
    <script>
        document.addEventListener('mousemove', function(e) {
            const zoomLevel = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--zoom-level')) || 0.90;
            const x = e.clientX / zoomLevel;
            const y = e.clientY / zoomLevel;
            document.documentElement.style.setProperty('--cursor-x', `${x}px`);
            document.documentElement.style.setProperty('--cursor-y', `${y}px`);
        });
    </script>
</body>
</html>