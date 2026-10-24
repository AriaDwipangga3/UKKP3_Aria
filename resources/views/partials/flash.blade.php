{{--
  FLASH MESSAGE: kotak pesan sementara.
  - session('success') diisi controller setelah aksi berhasil (hijau).
  - $errors->any() menampilkan error validasi pertama (merah).
  Kotak dengan atribut data-flash otomatis menghilang lewat public/js/dashboard.js.
--}}
@if (session('success'))
  <div class="flash flash-success" data-flash>{{ session('success') }}</div>
@endif

@if ($errors->any())
  <div class="flash flash-error">{{ $errors->first() }}</div>
@endif
