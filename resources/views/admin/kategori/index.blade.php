{{--
  DAFTAR KATEGORI (READ): tabel semua kategori pengaduan + tombol Edit / Hapus.
  Data dikirim dari Admin\KategoriController@index.
  Pesan sukses / error (mis. "kategori masih dipakai") tampil lewat partials/flash di layout.
--}}
@extends('layouts.app')

@section('title', 'Kategori Pengaduan — Kedai Rasa')
@section('pageTitle', 'Kategori Pengaduan')
@section('pageSubtitle', 'Atur pilihan kategori yang tampil di form pengaduan customer')

@section('content')

  <div class="head-row" style="margin-top:-6px;">
    <span></span>
    <a href="{{ route('admin.kategori.create') }}" class="btn btn-brass">+ Tambah Kategori</a>
  </div>

  <div class="tcard">

    <table>
      <thead>
        <tr><th>Nama</th><th>Deskripsi</th><th>Dipakai</th><th class="th-aksi">Aksi</th></tr>
      </thead>
      <tbody>
        {{-- @forelse = perulangan; kalau datanya kosong, tampilkan bagian @empty --}}
        @forelse ($kategori as $k)
          <tr>
            <td><strong>{{ $k->nama }}</strong></td>
            <td>{{ $k->deskripsi ?: '-' }}</td>
            {{-- pengaduan_count berasal dari withCount('pengaduan') di controller --}}
            <td>{{ $k->pengaduan_count }} pengaduan</td>
            <td>
              <div class="actions">
                <a href="{{ route('admin.kategori.edit', $k) }}" class="panel-link">Edit</a>

                {{-- Hapus: form DELETE (browser hanya bisa GET/POST, jadi @method('DELETE') memalsukannya) --}}
                <form action="{{ route('admin.kategori.destroy', $k) }}" method="POST"
                      onsubmit="return confirm('Yakin hapus kategori ini?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-link-danger">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="empty">Belum ada kategori. Tambahkan dulu agar customer bisa membuat pengaduan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $kategori->links() }}

@endsection
