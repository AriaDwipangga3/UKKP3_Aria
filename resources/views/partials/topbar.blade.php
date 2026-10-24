{{--
  TOPBAR: judul halaman (kiri) dan tanggal hari ini (kanan).
  Judul & subjudul diisi tiap halaman lewat @section('pageTitle') dan @section('pageSubtitle').
--}}
<div class="topbar">
  <div>
    <div class="greeting">@yield('pageTitle', 'Dashboard')</div>
    <div class="date">@yield('pageSubtitle', '')</div>
  </div>
  <div class="today-pill">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
</div>
