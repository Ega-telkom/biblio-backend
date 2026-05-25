<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Biblio</title>
    <link rel="stylesheet" href="/css/login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
</head>
<body>
<h2 class="sr-only">Halaman login admin: form di kiri, logo Biblio di kanan.</h2>

<div class="login-wrapper">

    <!-- Kiri: form login -->
    <div class="left-panel">
        <div class="brand">
            <span class="brand-name">Biblio<span>Store</span></span>
        </div>

        <h1 class="form-title">Selamat datang<br>kembali</h1>
        <p class="form-subtitle">Masuk untuk mengontrol perpustakaan digital</p>

        <div class="field-group">
            <label class="field-label" for="emailInput">Email</label>
            <div class="field-wrap">
                <i class="ti ti-mail" aria-hidden="true"></i>
                <input type="email" id="emailInput" name="email" placeholder="admin@biblio.com" autocomplete="email" />
            </div>
        </div>

        <div class="field-group">
            <label class="field-label" for="pwInput">Password</label>
            <div class="field-wrap">
                <i class="ti ti-lock" aria-hidden="true"></i>
                <input type="password" id="pwInput" name="password" placeholder="Masukkan password" autocomplete="current-password" />
                <button type="button" class="eye-toggle" aria-label="Tampilkan password" onclick="togglePw()">
                    <i class="ti ti-eye" id="eyeIcon" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <div class="forgot-link">
            <a href="#">Lupa password?</a>
        </div>

        <button type="button" class="btn-login" onclick="handleLogin()">
            <i class="ti ti-login" aria-hidden="true"></i>
            Masuk Sekarang
        </button>
    </div>

    <div class="divider" aria-hidden="true"></div>

    <!-- Kanan: logo Biblio -->
    <div class="right-panel">
        <div class="right-panel-content">
            <img
                src="/Icons/Biblio-ico.png"
                alt="Logo Biblio"
                class="biblio-logo"
                width="280"
                height="280"
            />
            <p class="right-panel-tagline">Perpustakaan Digital Biblio</p>
            <p class="right-panel-desc">Kelola buku, genre, dan koleksi</p>
        </div>
    </div>

</div>

<script>
  function togglePw() {
    const pw = document.getElementById('pwInput');
    const icon = document.getElementById('eyeIcon');
    if (pw.type === 'password') {
      pw.type = 'text';
      icon.className = 'ti ti-eye-off';
    } else {
      pw.type = 'password';
      icon.className = 'ti ti-eye';
    }
  }

  function handleLogin() {
    const email = document.getElementById('emailInput').value;
    const pw = document.getElementById('pwInput').value;
    if (!email || !pw) {
      alert('Harap isi email dan password.');
      return;
    }
    // Sambungkan ke API /auth/login atau redirect Swagger sesuai kebutuhan
    console.log('Login:', email);
  }
</script>
</body>
</html>
