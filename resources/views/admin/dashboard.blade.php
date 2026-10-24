{{--
  DASHBOARD ADMIN: 4 kartu ringkasan + tabel pendaftaran customer
  dengan tab filter, pencarian, dan tombol Terima / Tolak / Hapus.
  Data dikirim dari DashboardController@admin.
--}}
@extends('layouts.app')

@section('title', 'Dashboard Admin — Kedai Rasa')
@section('pageTitle', 'Halo, ' . auth()->user()->name)
@section('pageSubtitle', 'Terima atau tolak pendaftaran customer')

@section('content')

  {{-- Kartu ringkasan jumlah customer per status --}}
  <div class="stats">
    <div class="stat-card">
      <div class="stat-top">
        <span class="stat-label">Menunggu ACC</span>
        @if ($totalPending > 0)<span class="stat-badge badge-alert">Perlu tindakan</span>@endif
      </div>
      <div class="stat-value">{{ $totalPending }}</div>
      <div class="stat-sub">Pendaftar baru</div>
    </div>
    <div class="stat-card">
      <div class="stat-top"><span class="stat-label">Diterima</span></div>
      <div class="stat-value">{{ $totalApproved }}</div>
      <div class="stat-sub">Customer aktif</div>
    </div>
    <div class="stat-card">
      <div class="stat-top"><span class="stat-label">Ditolak</span></div>
      <div class="stat-value">{{ $totalRejected }}</div>
      <div class="stat-sub">Tidak bisa login</div>
    </div>
    <div class="stat-card">
      <div class="stat-top"><span class="stat-label">Total customer</span></div>
      <div class="stat-value">{{ $totalCustomer }}</div>
      <div class="stat-sub">Semua pendaftar</div>
    </div>
  </div>

  {{-- Tab filter status. Tab aktif ditandai class "active". Angka = jumlah customer. --}}
  <div class="tabs">
    <a href="{{ route('admin.dashboard', ['status' => 'pending', 'q' => $keyword]) }}" class="tab {{ $status === 'pending' ? 'active' : '' }}">Menunggu ({{ $totalPending }})</a>
    <a href="{{ route('admin.dashboard', ['status' => 'approved', 'q' => $keyword]) }}" class="tab {{ $status === 'approved' ? 'active' : '' }}">Diterima ({{ $totalApproved }})</a>
    <a href="{{ route('admin.dashboard', ['status' => 'rejected', 'q' => $keyword]) }}" class="tab {{ $status === 'rejected' ? 'active' : '' }}">Ditolak ({{ $totalRejected }})</a>
    <a href="{{ route('admin.dashboard', ['status' => 'all', 'q' => $keyword]) }}" class="tab {{ $status === 'all' ? 'active' : '' }}">Semua ({{ $totalCustomer }})</a>
  </div>

  <div class="panel">
    <div class="panel-head">
      <div class="panel-title">Pendaftaran customer</div>
      {{-- Form pencarian nama / email (tab yang aktif ikut terbawa lewat input hidden) --}}
      <form method="GET" action="{{ route('admin.dashboard') }}" class="search-form">
        <input type="hidden" name="status" value="{{ $status }}">
        <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari nama / email">
      </form>
    </div>

    <table>
      <thead>
        <tr>
          <th>Nama</th><th>Email</th><th>Mendaftar</th><th>Status</th><th class="th-aksi">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($customers as $u)
          <tr>
            <td><div class="cust"><div class="dot">{{ strtoupper(substr($u->name, 0, 2)) }}</div>{{ $u->name }}</div></td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->created_at->translatedFormat('d M Y, H:i') }}</td>
            <td>
              {{-- Label status akun --}}
              @if ($u->isApproved())
                <span class="status st-selesai"><span class="sd"></span>Diterima</span>
              @elseif ($u->isRejected())
                <span class="status st-baru"><span class="sd"></span>Ditolak</span>
              @else
                <span class="status st-proses"><span class="sd"></span>Menunggu</span>
              @endif
            </td>
            <td>
              {{-- Tombol aksi dibungkus div agar sel tabel tetap rapi --}}
              <div class="actions">
                {{-- Terima: tampil kalau akun belum diterima --}}
                @if (! $u->isApproved())
                  <form action="{{ route('admin.users.approve', $u) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-sm btn-ok">Terima</button>
                  </form>
                @endif

                {{-- Tolak: tampil kalau akun belum ditolak --}}
                @if (! $u->isRejected())
                  <form action="{{ route('admin.users.reject', $u) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-sm btn-no">Tolak</button>
                  </form>
                @endif

                {{-- Hapus: ada konfirmasi dulu karena tidak bisa dibatalkan --}}
                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Yakin hapus akun ini? Tindakan ini tidak bisa dibatalkan.');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-link-danger">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="empty">
              @if ($keyword !== '')
                Tidak ada customer yang cocok dengan "{{ $keyword }}".
              @elseif ($status === 'pending')
                Tidak ada pendaftar yang menunggu ACC.
              @else
                Belum ada data di tab ini.
              @endif
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Navigasi halaman (partials/pagination.blade.php) --}}
  {{ $customers->links() }}

@endsection
