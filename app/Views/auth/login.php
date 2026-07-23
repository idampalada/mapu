<!DOCTYPE html>
<html class="light" lang="id">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>SIMANSET — Masuk</title>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">try{
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-surface-variant": "#45464f",
                        "primary-container": "#223468",
                        "primary-fixed-dim": "#b4c5ff",
                        "tertiary-fixed-dim": "#c1c7cf",
                        "primary": "#081e52",
                        "inverse-primary": "#b4c5ff",
                        "on-secondary": "#ffffff",
                        "on-primary-container": "#8d9ed9",
                        "surface": "#f8f9ff",
                        "surface-bright": "#f8f9ff",
                        "surface-variant": "#d3e4fe",
                        "inverse-surface": "#213145",
                        "background": "#f8f9ff",
                        "on-error-container": "#93000a",
                        "on-secondary-fixed": "#271900",
                        "on-secondary-fixed-variant": "#5e4200",
                        "secondary-fixed-dim": "#ffba22",
                        "error": "#ba1a1a",
                        "primary-fixed": "#dbe1ff",
                        "outline": "#757680",
                        "tertiary-container": "#31383e",
                        "tertiary-fixed": "#dde3eb",
                        "inverse-on-surface": "#eaf1ff",
                        "surface-container-highest": "#d3e4fe",
                        "surface-dim": "#cbdbf5",
                        "on-primary-fixed": "#01174b",
                        "on-tertiary-container": "#9ba1a8",
                        "secondary-container": "#fdb718",
                        "tertiary": "#1c2328",
                        "on-tertiary": "#ffffff",
                        "on-error": "#ffffff",
                        "on-primary-fixed-variant": "#334479",
                        "on-background": "#0b1c30",
                        "outline-variant": "#c5c6d1",
                        "secondary-fixed": "#ffdea8",
                        "surface-container-high": "#dce9ff",
                        "surface-container-lowest": "#ffffff",
                        "on-secondary-container": "#6b4b00",
                        "on-tertiary-fixed-variant": "#41474e",
                        "surface-tint": "#4b5c92",
                        "surface-container": "#e5eeff",
                        "error-container": "#ffdad6",
                        "surface-container-low": "#eff4ff",
                        "on-tertiary-fixed": "#161c22",
                        "on-primary": "#ffffff",
                        "on-surface": "#0b1c30",
                        "secondary": "#7c5800",
                        "brand-gold": "#FCB717",
                        "brand-navy": "#223468"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    "spacing": {
                        "stack-gap": "20px",
                        "input-height": "48px",
                        "container-max-width": "440px",
                        "gutter": "24px",
                        "margin-mobile": "16px",
                        "space-unit": "4px"
                    },
                    "fontFamily": {
                        "body-md": ["Manrope"],
                        "label-sm": ["Manrope"],
                        "body-lg": ["Manrope"],
                        "headline-lg": ["Manrope"],
                        "label-caps": ["Manrope"],
                        "headline-lg-mobile": ["Manrope"],
                        "headline-md": ["Manrope"]
                    },
                    "fontSize": {
                        "body-md": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}],
                        "body-lg": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.1em", "fontWeight": "700"}],
                        "headline-lg-mobile": ["26px", {"lineHeight": "32px", "fontWeight": "700"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600"}]
                    }
                },
            },
        }
    }catch(_e){}</script>
</head>
<body class="flex min-h-screen bg-white overflow-x-hidden">

<!-- Left Side: Navy Welcome Section (approx 40%) -->
<aside class="hidden lg:flex w-[40%] bg-brand-navy flex-col justify-center items-center p-12 text-white relative">
  <!-- Top-left PU badge -->
  <div class="absolute top-8 left-8 z-20">
    <img alt="Sigap Membangun Negeri Untuk Rakyat" class="h-10 w-auto" src="<?= base_url('assets/images/logo/siapmembangun.png') ?>">
  </div>
  <div class="z-10 text-center">
    <img alt="SIMANSET Logo" class="w-80 h-auto mb-16 mx-auto filter brightness-0 invert" src="<?= base_url('assets/images/logo/mapuu.png') ?>">
    <div class="max-w-md">
      <h2 class="text-[40px] leading-tight font-bold mb-6">Selamat Datang</h2>
      <p class="text-body-lg opacity-80">Sistem Informasi Manajemen Aset Kementerian Pekerjaan Umum secara terpadu dan efisien.</p>
    </div>
  </div>
  <!-- Decorative abstract element -->
  <div class="absolute inset-0 overflow-hidden opacity-10 pointer-events-none">
    <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0px); background-size: 24px 24px;"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -mr-48 -mt-48"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full -ml-32 -mb-32"></div>
  </div>
  <footer class="absolute bottom-8 left-12">
    <p class="text-label-sm uppercase tracking-widest opacity-60">© 2026 KEMENTERIAN PEKERJAAN UMUM</p>
  </footer>
</aside>

<!-- Right Side: Login Form (approx 60% or 100% on mobile) -->
<main class="flex-grow flex items-center justify-center p-margin-mobile md:p-12 lg:w-[60%] bg-surface-container-lowest py-20">
  <div class="w-full max-w-[440px]">

    <!-- Logo -->
    <div class="flex flex-col items-center mb-8">
      <img alt="SIMANSET Logo" class="w-auto" style="height:160px" src="<?= base_url('assets/images/logo/mapuu.png') ?>">
    </div>

    <div class="mb-10 lg:text-left text-center">
      <p class="font-label-caps text-label-caps text-outline uppercase tracking-widest mb-2">AUTENTIKASI</p>
      <h1 class="font-headline-lg text-primary text-[36px]">Masuk ke akun</h1>
    </div>

    <!-- Global message block (success / error) -->
    <div class="mb-6">
      <?= view('Myth\Auth\Views\_message_block') ?>
    </div>

    <!-- Login Form -->
    <form action="<?= route_to('login') ?>" method="POST" class="w-full flex flex-col gap-8">
      <?= csrf_field() ?>

      <!-- Email / Username Field -->
      <div class="flex flex-col gap-2">
        <label class="font-label-caps text-label-caps text-on-surface-variant text-[13px]" for="login">
          <?= ($config->validFields === ['email']) ? 'EMAIL' : 'EMAIL ATAU USERNAME' ?>
        </label>
        <input
          class="h-input-height w-full px-4 border <?= session('errors.login') ? 'border-error focus:ring-error/20 focus:border-error' : 'border-outline-variant focus:ring-brand-gold/20 focus:border-brand-gold' ?> bg-white rounded focus:ring-2 text-body-md transition-all placeholder:text-outline/50 outline-none"
          id="login"
          name="login"
          type="<?= ($config->validFields === ['email']) ? 'email' : 'text' ?>"
          placeholder="<?= ($config->validFields === ['email']) ? 'nama@pu.go.id' : 'Masukkan email atau username' ?>"
          value="<?= old('login') ?>"
          autocomplete="email"
          required>
        <?php if (session('errors.login')): ?>
          <span class="text-error text-label-sm"><?= session('errors.login') ?></span>
        <?php endif; ?>
      </div>

      <!-- Password Field -->
      <div class="flex flex-col gap-2 relative">
        <label class="font-label-caps text-label-caps text-on-surface-variant text-[13px]" for="password">KATA SANDI</label>
        <div class="relative">
          <input
            class="h-input-height w-full px-4 pr-12 border <?= session('errors.password') ? 'border-error focus:ring-error/20 focus:border-error' : 'border-outline-variant focus:ring-brand-gold/20 focus:border-brand-gold' ?> bg-white rounded focus:ring-2 text-body-md transition-all placeholder:text-outline/50 outline-none"
            id="password"
            name="password"
            type="password"
            placeholder="••••••••"
            autocomplete="current-password"
            required>
          <button class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors" type="button" id="togglePassBtn">
            <span class="material-symbols-outlined text-[20px]">visibility</span>
          </button>
        </div>
        <?php if (session('errors.password')): ?>
          <span class="text-error text-label-sm"><?= session('errors.password') ?></span>
        <?php endif; ?>
      </div>

      <!-- Remember me + Forgot password -->
      <div class="flex items-center justify-between -mt-4">
        <?php if ($config->allowRemembering): ?>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="remember" id="remember" class="rounded border-outline-variant text-brand-navy focus:ring-brand-gold/40" <?php if (old('remember')): ?> checked <?php endif ?>>
            <span class="text-body-md text-on-surface-variant"><?= lang('Auth.rememberMe') ?></span>
          </label>
        <?php else: ?>
          <span></span>
        <?php endif; ?>

        <?php if ($config->activeResetter): ?>
          <a class="font-label-sm text-label-sm font-bold text-secondary-container hover:text-secondary transition-colors" href="<?= route_to('forgot') ?>">Lupa kata sandi?</a>
        <?php endif; ?>
      </div>

      <!-- Submit Button -->
      <button class="h-input-height w-full bg-brand-navy text-white font-label-caps text-label-caps tracking-[0.1em] rounded-lg shadow-sm hover:opacity-95 active:scale-[0.98] transition-all flex items-center justify-center mt-4" type="submit">
        MASUK
      </button>
    </form>

    <!-- Divider -->
    <div class="w-full flex items-center gap-4 my-8">
      <div class="flex-grow h-[1px] bg-outline-variant"></div>
      <span class="font-label-caps text-label-caps text-outline">ATAU</span>
      <div class="flex-grow h-[1px] bg-outline-variant"></div>
    </div>

    <!-- Footer Links -->
    <div class="flex flex-col gap-4 text-center">
      <p class="font-body-md text-body-md text-on-surface-variant">
        Belum punya akun?
        <a class="text-primary font-bold hover:underline" href="<?= route_to('register') ?>">Register now</a>
      </p>
    </div>

    <!-- Mobile Only Footer Disclaimer -->
    <footer class="lg:hidden mt-12 text-center">
      <p class="font-label-sm text-label-sm text-outline uppercase tracking-tighter">© 2026 KEMENTERIAN PEKERJAAN UMUM</p>
    </footer>

  </div>
</main>

<script>
  // Password visibility toggle
  const toggleBtn = document.getElementById('togglePassBtn');
  const passInput = document.getElementById('password');
  const visibilityIcon = toggleBtn?.querySelector('span');

  if (toggleBtn && passInput) {
    toggleBtn.addEventListener('click', () => {
      const isPassword = passInput.type === 'password';
      passInput.type = isPassword ? 'text' : 'password';
      visibilityIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
    });
  }
</script>
</body>
</html>