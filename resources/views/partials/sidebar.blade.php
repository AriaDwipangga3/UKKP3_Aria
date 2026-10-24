{{--
  SIDEBAR: menu navigasi di sisi kiri.
  - Admin    : Dashboard (lencana = customer menunggu ACC), Kelola User, Pengaduan (lencana = pengaduan baru),
               Kategori Pengaduan
  - Customer : Beranda, Buat Pengaduan, Riwayat Pengaduan
  Data user diambil langsung dari auth()->user().
--}}
@php
  $currentUser = auth()->user();
  $initial = strtoupper(substr($currentUser->name, 0, 2));
  // Jumlah pendaftar yang menunggu ACC, hanya dihitung untuk admin.
  $pendingCount = $currentUser->isAdmin() ? \App\Models\User::pending()->count() : 0;
  // Jumlah pengaduan berstatus 'baru' (belum dibuka admin), hanya dihitung untuk admin.
  $pengaduanBaru = $currentUser->isAdmin()
      ? \App\Models\Pengaduan::where('status', \App\Models\Pengaduan::STATUS_BARU)->count()
      : 0;
@endphp
<div class="sidebar">
  {{-- Logo & nama aplikasi --}}
  <div class="brand">
    <div class="brand-mark"><span>KR</span></div>
    <div>
      <div class="brand-name">Kedai Rasa</div>
      <div class="brand-sub">{{ $currentUser->isAdmin() ? 'Panel admin' : 'Area pelanggan' }}</div>
    </div>
  </div>

  @if ($currentUser->isAdmin())
    <div class="nav-group-label">Menu utama</div>
    <div class="nav">
      <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <span class="ic-dot"></span> Dashboard
        {{-- Lencana angka: berapa customer yang menunggu ACC --}}
        @if ($pendingCount > 0)
          <span class="nav-badge">{{ $pendingCount }}</span>
        @endif
      </a>

      <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.index', 'admin.users.create', 'admin.users.edit') ? 'active' : '' }}">
        <span class="ic-dot"></span> Kelola User
      </a>

      {{-- routeIs('admin.pengaduan.*') = menu tetap menyala di halaman detail pengaduan juga --}}
      <a href="{{ route('admin.pengaduan.index') }}" class="nav-item {{ request()->routeIs('admin.pengaduan.*') ? 'active' : '' }}">
        <span class="ic-dot"></span> Pengaduan
        @if ($pengaduanBaru > 0)
          <span class="nav-badge">{{ $pengaduanBaru }}</span>
        @endif
      </a>

      <a href="{{ route('admin.kategori.index') }}" class="nav-item {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}">
        <span class="ic-dot"></span> Kategori Pengaduan
      </a>
    </div>
  @else
    <div class="nav-group-label">Menu saya</div>
    <div class="nav">
      <a href="{{ route('customer.dashboard') }}" class="nav-item {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
        <span class="ic-dot"></span> Beranda
      </a>

      <a href="{{ route('customer.pengaduan.create') }}" class="nav-item {{ request()->routeIs('customer.pengaduan.create') ? 'active' : '' }}">
        <span class="ic-dot"></span> Buat Pengaduan
      </a>

      <a href="{{ route('customer.pengaduan.index') }}" class="nav-item {{ request()->routeIs('customer.pengaduan.index', 'customer.pengaduan.show') ? 'active' : '' }}">
        <span class="ic-dot"></span> Riwayat Pengaduan
      </a>
    </div>
  @endif

  {{-- Bagian bawah sidebar: info user + tombol keluar --}}
  <div class="sidebar-footer">
    <div class="user-chip" style="margin-bottom:14px;">
      <div class="avatar">{{ $initial }}</div>
      <div>
        <div class="name">{{ $currentUser->name }}</div>
        <div class="role">{{ $currentUser->isAdmin() ? 'Admin' : 'Customer' }}</div>
      </div>
    </div>
    {{-- Logout memakai form POST + @csrf demi keamanan --}}
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="nav-item" style="width:100%; border:none; background:none; text-align:left; cursor:pointer; font-family:inherit;">
        <span class="ic">&#8635;</span> Keluar
      </button>
    </form>
  </div>
</div>
