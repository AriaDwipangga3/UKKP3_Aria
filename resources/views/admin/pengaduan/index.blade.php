{{--
  DAFTAR PENGADUAN sisi admin (desain: 12-admin-daftar-pengaduan).
  Tab status + (tambahan di luar desain) filter kategori & pencarian.
  Data dari Admin\PengaduanController@index. Tombol "Lihat" membuka detail
  (pengaduan 'baru' otomatis jadi 'diproses').
--}}
@extends('layouts.app')

@section('title', 'Pengaduan — Kedai Rasa')
@section('pageTitle', 'Pengaduan')
@section('pageSubtitle', 'Lihat, tanggapi, dan ubah status pengaduan.')

@section('content')

  {{-- Link tab membawa filter kategori & kata kunci yang sedang aktif --}}
  @php $bawa = ['kategori' => $kategoriId, 'q' => $keyword]; @endphp
  <div class="filter-row">
    <div class="tabs tabs-lg">
      <a href="{{ route('admin.pengaduan.index', $bawa) }}" class="tab {{ ! $status ? 'active' : '' }}">Semua {{ $totalSemua }}</a>
      <a href="{{ route('admin.pengaduan.index', ['status' => 'baru'] + $bawa) }}" class="tab {{ $status === 'baru' ? 'active' : '' }}">Baru {{ $totalBaru }}</a>
      <a href="{{ route('admin.pengaduan.index', ['status' => 'diproses'] + $bawa) }}" class="tab {{ $status === 'diproses' ? 'active' : '' }}">Diproses {{ $totalDiproses }}</a>
      <a href="{{ route('admin.pengaduan.index', ['status' => 'selesai'] + $bawa) }}" class="tab {{ $status === 'selesai' ? 'active' : '' }}">Selesai {{ $totalSelesai }}</a>
    </div>

    {{-- Form filter memakai GET supaya hasilnya ada di URL (bisa di-bookmark) --}}
    <form method="GET" action="{{ route('admin.pengaduan.index') }}" class="search-form" style="display:flex; gap:10px;">
      @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
      <select name="kategori" onchange="this.form.submit()">
        <option value="">Semua kategori</option>
        @foreach ($daftarKategori as $k)
          <option value="{{ $k->id }}" {{ (string) $kategoriId === (string) $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
        @endforeach
      </select>
      <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari judul / pelanggan">
    </form>
  </div>

  <div class="tcard">
    <table>
      <thead>
        <tr><th>Tanggal</th><th>Pelanggan</th><th>Jenis</th><th>Judul</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse ($pengaduan as $p)
          <tr>
            <td>{{ $p->created_at->translatedFormat('d M Y') }}</td>
            <td>{{ $p->user->name }}</td>
            <td><span class="pill pill-{{ $p->jenis }}">{{ $p->jenis }}</span></td>
            <td>
              {{ \Illuminate\Support\Str::limit($p->judul, 40) }}
              <span class="sub-kategori">{{ $p->kategori->nama }}</span>
            </td>
            <td><span class="pill {{ $p->statusCss() }}">{{ $p->status }}</span></td>
            <td><a href="{{ route('admin.pengaduan.show', $p) }}" class="btn btn-dark-sm">Lihat</a></td>
          </tr>
        @empty
          <tr><td colspan="6" class="empty">Tidak ada pengaduan yang cocok.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $pengaduan->links() }}

@endsection
