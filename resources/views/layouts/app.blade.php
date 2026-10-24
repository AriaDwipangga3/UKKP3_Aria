{{--
  LAYOUT UTAMA: kerangka halaman setelah login (sidebar + topbar + isi + footer).
  Halaman lain memakai @extends('layouts.app') lalu mengisi @section('content').
--}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Kedai Rasa')</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

  {{-- Halaman anak boleh menambah <style> atau <link> lewat section ini --}}
  @yield('head')
</head>
<body>

  <div class="app">

    {{-- Sidebar: menu berbeda tergantung role (admin / customer) --}}
    @include('partials.sidebar')

    <div class="main">

      {{-- Topbar: judul halaman + tanggal hari ini --}}
      @include('partials.topbar')

      <div class="ring-divider"></div>

      {{-- Pesan sukses / error setelah suatu aksi (ACC, tolak, simpan, hapus, dll.) --}}
      @include('partials.flash')

      {{-- Bagian isi yang berbeda-beda tiap halaman --}}
      @yield('content')

      @include('partials.footer')
    </div>

  </div>

  <script src="{{ asset('js/dashboard.js') }}"></script>
  {{-- Script tambahan khusus halaman tertentu (opsional) --}}
  @yield('scripts')

</body>
</html>
