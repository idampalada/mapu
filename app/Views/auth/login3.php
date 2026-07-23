<?= $this->extend('home/layouts/index') ?>

<?= $this->section('content') ?>

<title>Login — Sistem Manajemen Aset</title>

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --navy: #374773;
  --gold: #FDB813;
}

html, body {
  height: 100%;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  color: var(--navy);
  background: #f8fafc !important;
  background-image: none !important;
}

/* ── Main wrapper ── */
.siman-main {
  min-height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 120px 16px 64px;
  position: relative;
  overflow: hidden;
  background-color: #f8fafc;
  z-index: 0;
}

/* Fix: beri background solid ke navbar fixed-top agar dot pattern tidak tembus */
#mainNav {
  background-color: #ffffff !important;
  box-shadow: 0 1px 12px rgba(55,71,115,.08);
}

/* Dot grid layer — covers the entire section, all sides */
.siman-main::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: radial-gradient(rgba(55,71,115,.28) 1.4px, transparent 1.4px);
  background-size: 22px 22px;
  pointer-events: none;
  z-index: 1;
}

/* Soft highlighted glow behind the card so dots there read a bit stronger */
.siman-main::after {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(
    ellipse 620px 520px at center,
    rgba(55,71,115,.06) 0%,
    transparent 70%
  );
  pointer-events: none;
  z-index: 1;
}

/* Layer titik biru yang menyala mengikuti kursor (spotlight) */
.dot-spotlight {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(#2563eb 1.8px, transparent 1.8px);
  background-size: 22px 22px;
  -webkit-mask-image: radial-gradient(circle 140px at var(--mx, 50%) var(--my, 50%), black 0%, transparent 100%);
  mask-image: radial-gradient(circle 140px at var(--mx, 50%) var(--my, 50%), black 0%, transparent 100%);
  pointer-events: none;
  z-index: 1;
  opacity: .85;
}

/* Decorative background */
.bg-watermark {
  position: absolute;
  bottom: -80px; right: -40px;
  font-size: 280px;
  font-weight: 900;
  color: var(--navy);
  opacity: .04;
  line-height: 1;
  letter-spacing: -.05em;
  user-select: none;
  pointer-events: none;
  z-index: 2;
}
.ring {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  z-index: 2;
}
.ring-1 { top:30px; left:50px; width:200px; height:200px; border: 1.5px solid rgba(55,71,115,.09); }
.ring-2 { top:75px; left:95px; width:110px; height:110px; border: 1.5px solid rgba(55,71,115,.07); }
.dots-cluster {
  position: absolute;
  top: 48px; right: 80px;
  display: grid;
  grid-template-columns: repeat(5, 8px);
  gap: 6px;
  pointer-events: none;
  z-index: 2;
}
.dots-cluster span {
  display: block;
  width: 3px; height: 3px;
  border-radius: 50%;
  background: var(--gold);
  opacity: .35;
}

/* ── Card ── */
.siman-card {
  position: relative;
  z-index: 3;
  width: 100%;
  max-width: 420px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 18px;
  box-shadow: 0 8px 40px rgba(55,71,115,.10);
  overflow: visible;
  transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
}
.siman-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 24px 60px rgba(55,71,115,.22);
  border-color: rgba(55,71,115,.25);
}
.siman-card-header {
  border-radius: 18px 18px 0 0;
  overflow: hidden;
}
.siman-card-body {
  border-radius: 0 0 18px 18px;
  overflow: hidden;
}

.siman-card-header {
  background: var(--navy);
  padding: 20px 36px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.siman-card-header-label {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: rgba(253,184,19,.8);
}
.siman-card-header-title {
  color: #fff;
  font-size: 18px;
  font-weight: 700;
  letter-spacing: -.03em;
  margin-top: 2px;
}
.siman-pip-row { display: flex; gap: 4px; align-items: center; }
.siman-pip { height: 5px; border-radius: 999px; }
.siman-pip-active { width: 20px; background: var(--gold); }
.siman-pip-inactive { width: 5px; background: rgba(255,255,255,.25); }

.siman-card-body { padding: 32px 36px; }

.siman-card-body h2 {
  font-size: 20px;
  font-weight: 700;
  letter-spacing: -.03em;
  color: var(--navy);
  margin-bottom: 4px;
}
.siman-card-body .sub {
  font-size: 12px;
  color: #94a3b8;
  margin-bottom: 28px;
}

/* Fields */
.siman-field { margin-bottom: 16px; }
.siman-field label {
  display: block;
  font-size: 11px;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 8px;
}
.siman-input-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 14px;
  background: #f8fafc;
  border: 1.5px solid #e2e8f0;
  border-radius: 8px;
  transition: border-color .15s;
}
.siman-input-wrap:focus-within { border-color: var(--navy); }
.siman-input-wrap.is-invalid { border-color: #f87171; background: #fff5f5; }
.siman-input-wrap svg { color: #94a3b8; flex-shrink: 0; transition: color .15s; }
.siman-input-wrap:focus-within svg { color: var(--navy); }
.siman-input-wrap input {
  flex: 1;
  border: none;
  background: transparent;
  outline: none;
  font-size: 13px;
  color: var(--navy);
  font-family: inherit;
}
.siman-input-wrap input::placeholder { color: #cbd5e1; }

.siman-toggle-pw {
  background: none;
  border: none;
  cursor: pointer;
  color: #94a3b8;
  display: flex;
  align-items: center;
  padding: 0;
  transition: color .15s;
}
.siman-toggle-pw:hover { color: var(--navy); }

.siman-err {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: #ef4444;
  margin-top: 6px;
}

/* Meta row: remember + forgot */
.siman-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}
.siman-remember {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: #64748b;
  cursor: pointer;
}
.siman-chk { width: 15px; height: 15px; accent-color: var(--navy); cursor: pointer; }
.siman-forgot {
  font-size: 12px;
  font-weight: 600;
  color: var(--navy);
  text-decoration: none;
}
.siman-forgot:hover { text-decoration: underline; }

/* Submit */
.siman-btn-submit {
  width: 100%;
  padding: 12px;
  background: var(--navy);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: background .15s;
  font-family: inherit;
}
.siman-btn-submit:hover { background: #2d3d63; }

/* Card footer */
.siman-card-footer {
  margin-top: 24px;
  padding-top: 20px;
  border-top: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.siman-card-footer p { font-size: 12px; color: #94a3b8; }
.siman-card-footer a {
  font-size: 12px;
  font-weight: 600;
  color: var(--gold);
  text-decoration: none;
  transition: color .15s;
}
.siman-card-footer a:hover { color: #d4970f; }

@media (max-width: 480px) {
  .siman-main { padding: 40px 16px; }
  .bg-watermark { font-size: 160px; }
  .dots-cluster { display: none; }
  .siman-card-body { padding: 24px 22px; }
  .siman-card-header { padding: 18px 22px; }
}
</style>

<main class="siman-main" id="siman-main-spotlight">
  <div class="dot-spotlight" aria-hidden="true"></div>
  <div class="bg-watermark" aria-hidden="true">SIMAN</div>
  <div class="ring ring-1" aria-hidden="true"></div>
  <div class="ring ring-2" aria-hidden="true"></div>
  <div class="dots-cluster" aria-hidden="true">
    <?php for ($i = 0; $i < 20; $i++): ?>
      <span></span>
    <?php endfor; ?>
  </div>

  <div class="siman-card">
    <div class="siman-card-header">
      <div>
        <div class="siman-card-header-label">Portal Masuk</div>
        <div class="siman-card-header-title">SIMAN</div>
      </div>
      <div class="siman-pip-row">
        <div class="siman-pip siman-pip-active"></div>
        <div class="siman-pip siman-pip-inactive"></div>
        <div class="siman-pip siman-pip-inactive"></div>
        <div class="siman-pip siman-pip-inactive"></div>
        <div class="siman-pip siman-pip-inactive"></div>
      </div>
    </div>

    <div class="siman-card-body">
      <h2>Masuk ke akun Anda</h2>
      <p class="sub">Gunakan email atau username terdaftar untuk mengakses sistem.</p>

      <?= view('Myth\Auth\Views\_message_block') ?>

      <form action="<?= route_to('login') ?>" method="post" class="users">
        <?= csrf_field() ?>

        <div class="siman-field">
          <label>Email atau Username</label>
          <div class="siman-input-wrap <?php if (session('errors.login')): ?>is-invalid<?php endif ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            <?php if ($config->validFields === ['email']): ?>
              <input type="email"
                     name="login"
                     placeholder="contoh@pu.go.id"
                     value="<?= old('login') ?>">
            <?php else: ?>
              <input type="text"
                     name="login"
                     placeholder="Email atau Username"
                     value="<?= old('login') ?>">
            <?php endif; ?>
          </div>
          <?php if (session('errors.login')): ?>
            <span class="siman-err"><?= session('errors.login') ?></span>
          <?php endif; ?>
        </div>

        <div class="siman-field" style="margin-bottom: 24px;">
          <label>Kata Sandi</label>
          <div class="siman-input-wrap <?php if (session('errors.password')): ?>is-invalid<?php endif ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            <input type="password"
                   name="password"
                   id="lp-pw"
                   placeholder="Masukkan kata sandi">
            <button class="siman-toggle-pw" type="button" aria-label="Tampilkan kata sandi"
              onclick="var p=document.getElementById('lp-pw');p.type=p.type==='password'?'text':'password';document.getElementById('icon-eye').style.display=p.type==='password'?'block':'none';document.getElementById('icon-eye-open').style.display=p.type==='password'?'none':'block';">
              <svg id="icon-eye" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
              <svg id="icon-eye-open" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
          <?php if (session('errors.password')): ?>
            <span class="siman-err"><?= session('errors.password') ?></span>
          <?php endif; ?>
        </div>

        <div class="siman-meta">
          <?php if ($config->allowRemembering): ?>
            <label class="siman-remember">
              <input type="checkbox" name="remember" class="siman-chk"
                     <?php if (old('remember')): ?> checked <?php endif ?>>
              Ingat saya
            </label>
          <?php else: ?>
            <span></span>
          <?php endif; ?>

          <?php if ($config->activeResetter): ?>
            <a class="siman-forgot" href="<?= route_to('forgot') ?>">Lupa Kata Sandi?</a>
          <?php endif; ?>
        </div>

        <button type="submit" class="siman-btn-submit">
          Masuk
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M5 12h14M12 5l7 7-7 7"/>
          </svg>
        </button>
      </form>

      <div class="siman-card-footer">
        <p>Ada masalah akses?</p>
        <a href="mailto:admin@pu.go.id">Hubungi Admin &rarr;</a>
      </div>
    </div>
  </div>
</main>

<script>
  (function () {
    var el = document.getElementById('siman-main-spotlight');
    if (!el) return;
    el.addEventListener('mousemove', function (e) {
      var rect = el.getBoundingClientRect();
      var x = e.clientX - rect.left;
      var y = e.clientY - rect.top;
      el.style.setProperty('--mx', x + 'px');
      el.style.setProperty('--my', y + 'px');
    });
    el.addEventListener('mouseleave', function () {
      el.style.setProperty('--mx', '50%');
      el.style.setProperty('--my', '50%');
    });
  })();
</script>

<?= $this->endSection() ?>