{{--
  HALAMAN LOGIN: form email + password.
  - Menampilkan pesan sukses (mis. "Pendaftaran berhasil, tunggu ACC").
  - Menampilkan error (password salah / akun belum di-ACC / ditolak).
--}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk — Kedai Rasa</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
  <div class="auth-wrap">
    <div class="auth-card">
      <div class="auth-title">Selamat datang kembali</div>
      <div class="auth-sub">Masuk ke akun Kedai Rasa kamu</div>

      {{-- Pesan sukses dari proses register --}}
      @if (session('success'))
        <div class="auth-success">{{ session('success') }}</div>
      @endif

      {{-- Pesan error pertama (validasi / status akun) --}}
      @if ($errors->any())
        <div class="auth-error">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-row">
          <label>Email</label>
          <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="form-row">
          <label>Password</label>
          <input type="password" name="password" required>
        </div>
        <div class="form-row" style="display:flex; align-items:center; gap:8px;">
          <input type="checkbox" name="remember" id="remember" style="width:auto;">
          <label for="remember" style="margin:0; font-weight:500;">Ingat saya</label>
        </div>
        <button type="submit" class="submit-btn" style="width:100%;">Masuk</button>
      </form>

      <div class="auth-foot">
        Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
      </div>
    </div>
  </div>
</body>
</html>
