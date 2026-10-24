{{--
  KELOLA USER - READ (desain: 10-admin-kelola-user).
  Tabel semua akun + kotak cari + tombol Ubah / Hapus. Data dari Admin\UserController@index.
  Catatan: tombol Terima / Tolak pendaftaran ada di Dashboard admin, bukan di sini.
--}}
@extends('layouts.app')

@section('title', 'Kelola User — Kedai Rasa')
@section('pageTitle', 'Kelola User')
@section('pageSubtitle', 'Tambah, ubah, dan hapus akun.')

@section('content')

  <div class="head-row" style="margin-top:-6px;">
    {{-- Kotak cari: Enter untuk mencari (form GET, kata kunci ada di URL) --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="search-pill">
      <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari nama atau email...">
    </form>
    <a href="{{ route('admin.users.create') }}" class="btn btn-brass">+ Tambah User</a>
  </div>

  <div class="tcard">
    <table>
      <thead>
        <tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse ($users as $u)
          <tr>
            <td class="cell-strong">{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td><span class="pill pill-{{ $u->role }}">{{ $u->role }}</span></td>
            <td><span class="pill pill-{{ $u->status }}">{{ $u->status }}</span></td>
            <td>
              <div class="row-actions">
                <a href="{{ route('admin.users.edit', $u) }}" class="btn-outline-sm">Ubah</a>

                {{-- Hapus hanya untuk akun customer (akun admin dilindungi, lihat UserController@destroy) --}}
                @if ($u->isCustomer())
                  <form action="{{ route('admin.users.destroy', $u) }}" method="POST"
                        onsubmit="return confirm('Yakin hapus akun ini? Pengaduannya ikut terhapus dan tidak bisa dibatalkan.');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger-sm">Hapus</button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="empty">Tidak ada akun yang cocok.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $users->links() }}

@endsection
