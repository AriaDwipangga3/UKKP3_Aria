{{--
  HALAMAN REGISTER: form pendaftaran customer.
  Akun yang dibuat berstatus "pending" dan baru bisa dipakai login setelah di-ACC admin.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar — Kedai Rasa</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
  <div class="auth-wrap">
    <div class="auth-card">
      <div class="auth-title">Buat akun baru</div>
      <div class="auth-sub">Daftar sebagai customer. Akun aktif setelah di-ACC admin.</div>

      {{-- Error validasi pertama (email sudah dipakai, password tidak cocok, dll.) --}}
      @if ($errors->any())
        <div class="auth-error">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-row">
          <label>Nama lengkap</label>
          <input type="text" name="name" value="{{ old('name') }}" required autofocus>
        </div>
        <div class="form-row">
          <label>Email</label>
          <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="form-row">
          <label>Password (min. 8 karakter)</label>
          <input type="password" name="password" required>
        </div>
        <div class="form-row">
          <label>Konfirmasi password</label>
          <input type="password" name="password_confirmation" required>
        </div>
        <button type="submit" class="submit-btn" style="width:100%;">Daftar</button>
      </form>

      <div class="auth-foot">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
      </div>
    </div>
  </div>
</body>
</html>
