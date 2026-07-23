<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIMANSET — Masuk</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: #fff;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .page-wrapper {
      display: flex;
      min-width: 1200px;
      min-height: 100vh;
      background-color: #fff;
    }

    /* ── LEFT PANEL ── */
    .panel-left {
      display: flex;
      flex-direction: column;
      position: relative;
      width: 52%;
      background-color: #223468;
      overflow: hidden;
      flex-shrink: 0;
    }

    .panel-left .blob-tr {
      position: absolute;
      top: -96px; right: -96px;
      width: 320px; height: 320px;
      opacity: .18;
      pointer-events: none;
    }
    .panel-left .blob-bl {
      position: absolute;
      bottom: -80px; left: -80px;
      width: 380px; height: 380px;
      opacity: .14;
      pointer-events: none;
    }
    .panel-left .deco-lines {
      position: absolute;
      inset: 0; width: 100%; height: 100%;
      pointer-events: none;
    }

    .panel-left .logo-pu {
      position: relative;
      z-index: 2;
      padding: 40px 48px;
    }
    .panel-left .logo-pu img {
      height: 56px; /* was 38px */
      object-fit: contain;
    }

    .panel-left .center-content {
      position: relative;
      z-index: 2;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 0 48px 64px;
    }
    .panel-left .accent-bar {
      width: 44px; height: 3px; /* was 32px / 2px */
      border-radius: 99px;
      background: #FCB717;
      margin-bottom: 20px;
    }
    .panel-left h2 {
      font-size: 52px; /* was 36px */
      font-weight: 200;
      color: #fff;
      line-height: 1.3;
      letter-spacing: -.5px;
      margin-bottom: 18px;
    }
    .panel-left h2 span { color: #FCB717; }
    .panel-left p {
      font-size: 17px; /* was 13px */
      font-weight: 300;
      color: rgba(255,255,255,.42);
      line-height: 1.7;
      max-width: 360px; /* was 280px */
    }

    .panel-left footer {
      position: relative;
      z-index: 2;
      padding: 20px 48px;
      border-top: 1px solid rgba(255,255,255,.07);
      font-size: 12px; /* was 10px */
      letter-spacing: .3em;
      text-transform: uppercase;
      color: rgba(255,255,255,.22);
    }

    /* ── RIGHT PANEL ── */
    .panel-right {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: #fff;
      padding: 64px 32px;
      position: relative;
      overflow: hidden;
      min-width: 0;
    }

    .panel-right .deco-tr,
    .panel-right .deco-bl {
      position: absolute;
      pointer-events: none;
    }
    .panel-right .deco-tr { top: 0; right: 0; width: 240px; height: 240px; }
    .panel-right .deco-bl { bottom: 0; left: 0; width: 220px; height: 220px; }

    /* form */
    .form-wrap {
      position: relative;
      z-index: 2;
      width: 100%;
      max-width: 480px; /* was 360px */
    }

    .form-wrap .top-form-logo {
      width: 160px; /* was 110px */
      height: auto;
      object-fit: contain;
      margin: 0 170px 32px auto;
      display: block;
    }

    .form-wrap .eyebrow {
      font-size: 13px; /* was 10px */
      letter-spacing: .3em;
      text-transform: uppercase;
      color: rgba(34,52,104,.35);
      margin-bottom: 10px;
    }
    .form-wrap h1 {
      font-size: 38px; /* was 27px */
      font-weight: 300;
      color: #223468;
      letter-spacing: -.3px;
      margin-bottom: 40px;
    }

    .field { margin-bottom: 30px; }
    .field label {
      display: block;
      font-size: 12px; /* was 10px */
      letter-spacing: .25em;
      text-transform: uppercase;
      font-weight: 500;
      color: rgba(34,52,104,.38);
      margin-bottom: 10px;
    }
    .field .input-wrap { position: relative; }
    .field input {
      width: 100%;
      background: transparent;
      border: none;
      border-bottom: 2px solid rgba(34,52,104,.13); /* was 1.5px */
      padding: 14px 36px 14px 0; /* was 10px 32px 10px 0 */
      font-size: 18px; /* was 14px */
      font-weight: 300;
      color: #223468;
      outline: none;
      transition: border-color .25s;
    }
    .field input:focus { border-bottom-color: #FCB717; }
    .field input::placeholder { color: rgba(34,52,104,.18); }

    .input-error { border-bottom-color: #dc3545 !important; }
    .error-text { color: #dc3545; font-size: 13px; margin-top: 8px; display: block; }

    .toggle-pass {
      position: absolute;
      right: 0; top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: rgba(34,52,104,.3);
      padding: 4px;
      line-height: 0;
      transition: color .2s;
    }
    .toggle-pass svg { width: 20px; height: 20px; } /* was 15px */
    .toggle-pass:hover { color: rgba(34,52,104,.6); }

    .checkbox-wrap {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: -8px 0 24px;
    }
    .checkbox-wrap input { width: 18px; height: 18px; margin: 0; cursor: pointer; }
    .checkbox-wrap label {
      margin: 0;
      font-size: 14px; /* was 11px */
      font-weight: 300;
      letter-spacing: normal;
      text-transform: none;
      color: rgba(34,52,104,.7);
      cursor: pointer;
    }

    .forgot {
      display: flex;
      justify-content: flex-end;
      margin: -8px 0 24px;
    }
    .forgot a {
      font-size: 14px; /* was 11px */
      font-weight: 300;
      color: #FCB717;
      text-decoration: none;
      transition: opacity .2s;
    }
    .forgot a:hover { opacity: .7; }

    .btn-submit {
      width: 100%;
      padding: 18px; /* was 14px */
      background: #223468;
      color: #fff;
      border: none;
      font-size: 13px; /* was 11px */
      letter-spacing: .3em;
      text-transform: uppercase;
      font-weight: 600;
      cursor: pointer;
      transition: background .25s, color .25s;
      margin-bottom: 32px;
    }
    .btn-submit:hover { background: #FCB717; color: #223468; }

    .divider {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 28px;
    }
    .divider span {
      flex: 1; height: 1px;
      background: rgba(34,52,104,.07);
    }
    .divider em {
      font-size: 12px; /* was 10px */
      letter-spacing: .3em;
      text-transform: uppercase;
      font-style: normal;
      color: rgba(34,52,104,.22);
    }

    .register {
      text-align: center;
      font-size: 14px; /* was 11px */
      font-weight: 300;
      color: rgba(34,52,104,.35);
    }
    .register a {
      font-weight: 600;
      color: #223468;
      text-decoration: none;
      transition: color .2s;
    }
    .register a:hover { color: #FCB717; }
  </style>
</head>
<body>

  <div class="page-wrapper">
    <!-- ══ LEFT PANEL ══ -->
    <div class="panel-left">
      <svg class="blob-tr" viewBox="0 0 320 320">
        <path d="M160,20 C230,10 310,70 300,150 C290,230 210,310 140,300 C70,290 10,220 20,150 C30,80 90,30 160,20Z" fill="#FCB717"/>
      </svg>

      <svg class="blob-bl" viewBox="0 0 380 380">
        <path d="M190,30 C270,20 360,90 355,175 C350,260 270,355 185,350 C100,345 20,265 25,180 C30,95 110,40 190,30Z" fill="#FCB717"/>
      </svg>

      <svg class="deco-lines" viewBox="0 0 500 800" preserveAspectRatio="xMidYMid slice">
        <path d="M -20 200 C 80 160,140 280,220 240 S 360 140,520 200" fill="none" stroke="rgba(255,255,255,.07)" stroke-width="1.5"/>
        <path d="M -20 420 C 60 380,160 460,260 420 S 420 320,520 380" fill="none" stroke="rgba(255,255,255,.05)" stroke-width="1"/>
        <path d="M 80 610 C 160 570,240 650,340 600 S 480 510,540 570" fill="none" stroke="rgba(252,183,23,.1)" stroke-width="1.5"/>
        <circle cx="60"  cy="120" r="2.5" fill="rgba(252,183,23,.28)"/>
        <circle cx="380" cy="175" r="1.5" fill="rgba(255,255,255,.18)"/>
        <circle cx="285" cy="345" r="3"   fill="rgba(252,183,23,.18)"/>
        <circle cx="120" cy="505" r="2"   fill="rgba(255,255,255,.13)"/>
        <circle cx="440" cy="625" r="2"   fill="rgba(252,183,23,.22)"/>
        <line x1="310" y1="272" x2="322" y2="284" stroke="rgba(252,183,23,.3)" stroke-width="1.5"/>
        <line x1="322" y1="272" x2="310" y2="284" stroke="rgba(252,183,23,.3)" stroke-width="1.5"/>
        <line x1="140" y1="442" x2="150" y2="452" stroke="rgba(255,255,255,.18)" stroke-width="1"/>
        <line x1="150" y1="442" x2="140" y2="452" stroke="rgba(255,255,255,.18)" stroke-width="1"/>
      </svg>

      <div class="logo-pu">
        <img src="<?= base_url('assets/images/logoPU.png') ?>" alt="Kementerian Pekerjaan Umum">
      </div>

      <div class="center-content">
        <div class="accent-bar"></div>
        <h2>Sistem Informasi<br><span>Manajemen</span><br>Aset</h2>
        <p>Platform pengelolaan aset Kementerian Pekerjaan Umum secara terpadu dan efisien.</p>
      </div>

      <footer>© 2026 Kementerian Pekerjaan Umum</footer>
    </div>

    <!-- ══ RIGHT PANEL ══ -->
    <div class="panel-right">
      <svg class="deco-tr" viewBox="0 0 240 240">
        <path d="M 240 0 Q 160 60 185 145 Q 210 225 240 240" fill="none" stroke="#FCB717" stroke-width="1.5" stroke-opacity=".28"/>
        <path d="M 240 0 Q 185 85 215 185" fill="none" stroke="#223468" stroke-width="1" stroke-opacity=".08"/>
        <circle cx="200" cy="38" r="3"   fill="#FCB717" fill-opacity=".35"/>
        <circle cx="232" cy="98" r="1.8" fill="#223468" fill-opacity=".12"/>
      </svg>

      <svg class="deco-bl" viewBox="0 0 220 220">
        <path d="M 0 220 Q 60 155 42 75 Q 22 18 0 0" fill="none" stroke="#223468" stroke-width="1" stroke-opacity=".07"/>
        <path d="M 0 220 Q 78 175 68 95" fill="none" stroke="#FCB717" stroke-width="1.5" stroke-opacity=".2"/>
        <circle cx="48" cy="178" r="2.5" fill="#FCB717" fill-opacity=".28"/>
        <line x1="28" y1="138" x2="38" y2="148" stroke="#223468" stroke-width="1" stroke-opacity=".18"/>
        <line x1="38" y1="138" x2="28" y2="148" stroke="#223468" stroke-width="1" stroke-opacity=".18"/>
      </svg>

      <div class="form-wrap">
        <img src="<?= base_url('assets/images/logo/mapuu.png') ?>" alt="SIMANSET" class="top-form-logo">

        <p class="eyebrow">Autentikasi</p>
        <h1>Masuk ke akun</h1>

        <div style="margin-bottom: 20px;">
          <?= view('Myth\Auth\Views\_message_block') ?>
        </div>

        <form action="<?= route_to('login') ?>" method="POST">
          <?= csrf_field() ?>

          <div class="field">
            <label for="login"><?= ($config->validFields === ['email']) ? 'Email' : 'Email atau Username' ?></label>
            <div class="input-wrap">
              <input
                type="<?= ($config->validFields === ['email']) ? 'email' : 'text' ?>"
                id="login"
                name="login"
                placeholder="<?= ($config->validFields === ['email']) ? 'nama@pu.go.id' : 'Masukkan email atau username' ?>"
                class="<?php if (session('errors.login')): ?>input-error<?php endif ?>"
                value="<?= old('login') ?>"
                autocomplete="email"
                required>
            </div>
            <?php if (session('errors.login')): ?>
                <span class="error-text"><?= session('errors.login') ?></span>
            <?php endif; ?>
          </div>

          <div class="field">
            <label for="password">Kata Sandi</label>
            <div class="input-wrap">
              <input
                type="password"
                id="password"
                name="password"
                placeholder="••••••••"
                class="<?php if (session('errors.password')): ?>input-error<?php endif ?>"
                autocomplete="current-password"
                required>
              <button type="button" class="toggle-pass" onclick="togglePass()" aria-label="Tampilkan kata sandi">
                <svg id="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg id="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="display:none">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                  <line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
              </button>
            </div>
            <?php if (session('errors.password')): ?>
                <span class="error-text"><?= session('errors.password') ?></span>
            <?php endif; ?>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <?php if ($config->allowRemembering): ?>
              <div class="checkbox-wrap">
                <input type="checkbox" name="remember" id="remember" <?php if (old('remember')): ?> checked <?php endif ?>>
                <label for="remember"><?= lang('Auth.rememberMe') ?></label>
              </div>
            <?php else: ?>
              <div></div>
            <?php endif; ?>

            <?php if ($config->activeResetter): ?>
              <div class="forgot" style="margin: 0;">
                <a href="<?= route_to('forgot') ?>">Lupa kata sandi?</a>
              </div>
            <?php endif; ?>
          </div>

          <button type="submit" class="btn-submit">Masuk</button>
        </form>

        <div class="divider"><span></span><em>atau</em><span></span></div>

        <p class="register">
          Belum punya akun? <a href="<?= route_to('register') ?>">Register now</a>
        </p>
      </div>
    </div>
  </div>

  <script>
    function togglePass() {
      var input   = document.getElementById('password');
      var iconOn  = document.getElementById('icon-eye');
      var iconOff = document.getElementById('icon-eye-off');
      if (input.type === 'password') {
        input.type     = 'text';
        iconOn.style.display  = 'none';
        iconOff.style.display = 'block';
      } else {
        input.type     = 'password';
        iconOn.style.display  = 'block';
        iconOff.style.display = 'none';
      }
    }
  </script>
</body>
</html>