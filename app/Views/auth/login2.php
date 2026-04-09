<?= $this->extend('auth/layouts/index') ?>

<?= $this->section('content') ?>

<title>Login — Sistem Manajemen Aset</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html, body {
  height: 100%;
  font-family: 'Plus Jakarta Sans', sans-serif;
  background: #0b1423;
  overflow: hidden;
}

/* ─── FULL SCREEN WRAPPER ─── */
.lp-wrap {
  display: flex;
  height: 100vh;
  width: 100vw;
}

/* ─── LEFT: HERO ─── */
.lp-hero {
  flex: 1;
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 44px 48px;
}

.lp-hero-img {
  position: absolute;
  inset: 0;
  background-image: url('https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?w=1400&auto=format&fit=crop&q=80');
  background-size: cover;
  background-position: center;
  filter: brightness(0.42) saturate(0.75);
}

.lp-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg,
    rgba(10, 20, 60, 0.85) 0%,
    rgba(26, 47, 110, 0.5) 60%,
    rgba(0,0,0,0.25) 100%
  );
}

.lp-hero-top {
  position: relative;
  display: flex;
  align-items: center;
  gap: 16px;
}

.lp-logo-sq {
    width: 52px; height: 52px;
    background: #fff;          /* putih agar logo terlihat jelas */
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    padding: 8px;
}

.lp-logo-info { color: #fff; }
.lp-logo-info p:first-child {
  font-size: 10px; font-weight: 500;
  color: rgba(255,255,255,0.5);
  letter-spacing: 0.1em; text-transform: uppercase;
}
.lp-logo-info p:last-child {
  font-size: 16px; font-weight: 700;
}

.lp-hero-mid { position: relative; }

.lp-eyebrow {
  display: inline-flex; align-items: center; gap: 8px;
  border: 1px solid rgba(255,193,0,0.45);
  background: rgba(255,193,0,0.1);
  border-radius: 100px;
  padding: 5px 14px;
  margin-bottom: 22px;
}
.lp-eyebrow-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: #ffc100;
  animation: blink 2s infinite;
}
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
.lp-eyebrow span {
  font-size: 11px; font-weight: 600; letter-spacing: 0.08em;
  text-transform: uppercase; color: #ffc100;
}

.lp-big-title {
  font-size: clamp(28px, 3.5vw, 52px);
  font-weight: 800;
  color: #fff;
  line-height: 1.15;
  margin-bottom: 18px;
  letter-spacing: -0.025em;
}
.lp-big-title em { font-style: normal; color: #ffc100; }

.lp-tagline {
  font-size: 14px;
  color: rgba(255,255,255,0.48);
  line-height: 1.75;
  max-width: 400px;
  margin-bottom: 40px;
}

.lp-pills { display: flex; flex-wrap: wrap; gap: 10px; }

.lp-pill {
  display: flex; align-items: center;
  background: rgba(255,255,255,0.07);
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 12px;
  padding: 12px 20px;
  backdrop-filter: blur(8px);
}
.lp-pill-num {
  font-size: 20px; font-weight: 700; color: #ffc100;
  margin-bottom: 2px;
}
.lp-pill-label {
  font-size: 11px; color: rgba(255,255,255,0.4);
}

.lp-hero-bot {
  position: relative;
  display: flex; align-items: center; justify-content: space-between;
}
.lp-hero-foot {
  font-size: 11px; color: rgba(255,255,255,0.2);
  letter-spacing: 0.05em;
}
.lp-ver-badge {
  background: rgba(255,255,255,0.07);
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 100px;
  padding: 4px 12px;
  font-size: 10px; font-weight: 600;
  color: rgba(255,255,255,0.3);
  letter-spacing: 0.06em; text-transform: uppercase;
}

/* ─── RIGHT: FORM PANEL ─── */
.lp-panel {
  width: 460px;
  flex-shrink: 0;
  background: #fff;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 56px 48px;
  overflow-y: auto;
  position: relative;
}

.lp-panel::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 4px;
  background: linear-gradient(90deg, #1a2f6e 0%, #ffc100 100%);
}

.lp-panel-head { margin-bottom: 38px; }

.lp-welcome-label {
  font-size: 11px; font-weight: 700;
  letter-spacing: 0.1em; text-transform: uppercase;
  color: #1a2f6e;
  margin-bottom: 12px;
  display: flex; align-items: center; gap: 10px;
}
.lp-welcome-label::after {
  content: '';
  width: 36px; height: 3px;
  background: #ffc100; border-radius: 2px;
}

.lp-panel-title {
  font-size: 32px; font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.025em;
  line-height: 1.18;
  margin-bottom: 10px;
}

.lp-panel-sub {
  font-size: 13px; color: #94a3b8; line-height: 1.65;
}

.lp-field { margin-bottom: 20px; }

.lp-lbl {
  display: block;
  font-size: 11px; font-weight: 700;
  color: #334155;
  letter-spacing: 0.07em; text-transform: uppercase;
  margin-bottom: 8px;
}

.lp-input-wrap { position: relative; }

.lp-ico {
  position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
  font-size: 15px; opacity: 0.35; pointer-events: none;
}

.lp-inp {
  width: 100%;
  background: #f8fafc;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  padding: 15px 16px 15px 48px;
  font-size: 14px; color: #0f172a;
  font-family: 'Plus Jakarta Sans', sans-serif;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
}
.lp-inp::placeholder { color: #cbd5e1; }
.lp-inp:focus {
  border-color: #1a2f6e;
  background: #fff;
  box-shadow: 0 0 0 4px rgba(26,47,110,0.08);
}
.lp-inp.is-invalid { border-color: #f87171; background: #fff5f5; }

.lp-eye {
  position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer;
  font-size: 15px; color: #94a3b8; padding: 0;
  transition: color 0.2s;
}
.lp-eye:hover { color: #1a2f6e; }

.lp-err {
  font-size: 12px; color: #ef4444; margin-top: 6px; display: block;
}

.lp-meta {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 30px;
}

.lp-remember {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; color: #64748b; cursor: pointer;
}
.lp-chk { width: 16px; height: 16px; accent-color: #1a2f6e; cursor: pointer; }

.lp-forgot {
  font-size: 13px; font-weight: 600;
  color: #1a2f6e; text-decoration: none;
}
.lp-forgot:hover { text-decoration: underline; }

.lp-cta {
  width: 100%;
  background: #1a2f6e;
  color: #fff;
  border: none; border-radius: 12px;
  padding: 16px 24px;
  font-size: 15px; font-weight: 700;
  font-family: 'Plus Jakarta Sans', sans-serif;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 10px;
  transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
  margin-bottom: 22px;
  box-shadow: 0 4px 22px rgba(26,47,110,0.28);
}
.lp-cta:hover { background: #0f1f52; box-shadow: 0 6px 30px rgba(26,47,110,0.38); }
.lp-cta:active { transform: scale(0.98); }

.lp-cta-icon {
  width: 28px; height: 28px;
  background: #ffc100;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; color: #1a2f6e; font-weight: 900;
}

.lp-div {
  display: flex; align-items: center; gap: 12px; margin-bottom: 18px;
}
.lp-div::before, .lp-div::after { content:''; flex:1; height:1px; background:#f1f5f9; }
.lp-div span { font-size: 11px; color: #cbd5e1; font-weight: 500; }

.lp-sso {
  width: 100%;
  background: #fff;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  padding: 14px 20px;
  font-size: 13px; font-weight: 600;
  color: #334155;
  font-family: 'Plus Jakarta Sans', sans-serif;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 10px;
  transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
}
.lp-sso:hover {
  border-color: #1a2f6e;
  background: #f8faff;
  box-shadow: 0 0 0 4px rgba(26,47,110,0.05);
}

.lp-sso-badge {
  width: 26px; height: 26px;
  background: #1a2f6e;
  border-radius: 7px;
  display: flex; align-items: center; justify-content: center;
  font-size: 9px; font-weight: 800; color: #ffc100;
}

.lp-help {
  text-align: center;
  font-size: 12px; color: #94a3b8; margin-top: 26px;
}
.lp-help a { color: #1a2f6e; font-weight: 600; text-decoration: none; }
.lp-help a:hover { text-decoration: underline; }

/* ─── RESPONSIVE ─── */
@media (max-width: 800px) {
  html, body { overflow: auto; }
  .lp-wrap { flex-direction: column; height: auto; min-height: 100vh; }
  .lp-hero { min-height: 280px; padding: 32px 28px; }
  .lp-big-title { font-size: 26px; }
  .lp-pills { display: none; }
  .lp-panel { width: 100%; padding: 40px 28px; }
}
</style>

<div class="lp-wrap">

  <!-- ══════ LEFT HERO ══════ -->
  <div class="lp-hero">
    <div class="lp-hero-img"></div>
    <div class="lp-hero-overlay"></div>

    <div class="lp-hero-top">
    <div class="lp-logo-sq">
        <img src="<?= base_url('favicon.ico') ?>" 
             alt="Logo PU" 
             style="width: 32px; height: 32px; object-fit: contain;">
    </div>
    <div class="lp-logo-info">
        <p>Kementerian</p>
        <p>Pekerjaan Umum</p>
    </div>
</div>

    <div class="lp-hero-mid">
      <div class="lp-eyebrow">
        <span class="lp-eyebrow-dot"></span>
        <span>Sistem Manajemen Aset</span>
      </div>
      <h1 class="lp-big-title">
        Kelola Aset<br>
        Infrastruktur<br>
        <em>Lebih Cerdas.</em>
      </h1>
      <p class="lp-tagline">
        Platform digital terpadu Kementerian Pekerjaan Umum untuk
        pencatatan, pemantauan, dan pelaporan aset infrastruktur
        nasional secara real-time.
      </p>
      <div class="lp-pills">
        <div class="lp-pill">
          <div>
            <div class="lp-pill-num">250</div>
            <div class="lp-pill-label">Aset Kendaraan</div>
          </div>
        </div>
        <div class="lp-pill">
          <div>
            <div class="lp-pill-num">150</div>
            <div class="lp-pill-label">Aset Ruangan</div>
          </div>
        </div>
        <div class="lp-pill">
          <div>
            <div class="lp-pill-num">5.000</div>
            <div class="lp-pill-label">Aset Barang</div>
          </div>
        </div>
      </div>
    </div>

    <div class="lp-hero-bot">
      <span class="lp-hero-foot">SIMANSET · © 2025 Kementerian PU</span>
      <span class="lp-ver-badge">PUSDATIN</span>
    </div>
  </div>

  <!-- ══════ RIGHT FORM ══════ -->
  <div class="lp-panel">

    <div class="lp-panel-head">
      <div class="lp-welcome-label">Portal Masuk</div>
      <h2 class="lp-panel-title">Masuk ke<br>Akun Anda</h2>
      <p class="lp-panel-sub">Gunakan email atau username terdaftar untuk mengakses sistem.</p>
    </div>

    <?= view('Myth\Auth\Views\_message_block') ?>

    <form action="<?= route_to('login') ?>" method="post" class="users">
      <?= csrf_field() ?>

      <div class="lp-field">
        <label class="lp-lbl">Email atau Username</label>
        <div class="lp-input-wrap">
          <span class="lp-ico">✉️</span>
          <?php if ($config->validFields === ['email']): ?>
            <input type="email"
                   class="lp-inp <?php if (session('errors.login')): ?>is-invalid<?php endif ?>"
                   name="login"
                   placeholder="contoh@pu.go.id"
                   value="<?= old('login') ?>">
          <?php else: ?>
            <input type="text"
                   class="lp-inp <?php if (session('errors.login')): ?>is-invalid<?php endif ?>"
                   name="login"
                   placeholder="Email atau Username"
                   value="<?= old('login') ?>">
          <?php endif; ?>
          <?php if (session('errors.login')): ?>
            <span class="lp-err"><?= session('errors.login') ?></span>
          <?php endif; ?>
        </div>
      </div>

      <div class="lp-field">
        <label class="lp-lbl">Kata Sandi</label>
        <div class="lp-input-wrap">
          <span class="lp-ico">🔒</span>
          <input class="lp-inp <?php if (session('errors.password')): ?>is-invalid<?php endif ?>"
                 type="password"
                 name="password"
                 id="lp-pw"
                 placeholder="Masukkan kata sandi">
          <button class="lp-eye" type="button"
            onclick="var p=document.getElementById('lp-pw');p.type=p.type==='password'?'text':'password';this.textContent=p.type==='password'?'👁':'🙈'">👁</button>
          <?php if (session('errors.password')): ?>
            <span class="lp-err"><?= session('errors.password') ?></span>
          <?php endif; ?>
        </div>
      </div>

      <div class="lp-meta">
        <?php if ($config->allowRemembering): ?>
          <label class="lp-remember">
            <input type="checkbox" name="remember" class="lp-chk"
                   <?php if (old('remember')): ?> checked <?php endif ?>>
            Ingat saya
          </label>
        <?php else: ?>
          <span></span>
        <?php endif; ?>

        <?php if ($config->activeResetter): ?>
          <a class="lp-forgot" href="<?= route_to('forgot') ?>">Lupa Kata Sandi?</a>
        <?php endif; ?>
      </div>

      <button type="submit" class="lp-cta">
        <span class="lp-cta-icon">→</span>
        Masuk ke Sistem
      </button>

    </form>


    <p class="lp-help">
      Ada masalah akses? <a href="mailto:admin@pu.go.id">Hubungi Admin</a>
    </p>

  </div>
</div>

<?= $this->endSection() ?>