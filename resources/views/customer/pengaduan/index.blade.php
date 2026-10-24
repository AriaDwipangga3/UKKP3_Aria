{{--
  RIWAYAT PENGADUAN (desain: 07-customer-riwayat-pengaduan).
  Hanya pengaduan milik user yang login. Data dari Customer\PengaduanController@index.
  Klik judul untuk membuka detail.
--}}
@extends('layouts.app')

@section('title', 'Riwayat Pengaduan — Kedai Rasa')
@section('pageTitle', 'Riwayat Pengaduan')
@section('pageSubtitle', 'Semua pengaduan yang pernah kamu kirim.')

@section('content')

  <div class="head-row" style="margin-top:-6px;">
    <span></span>
    <a href="{{ route('customer.pengaduan.create') }}" class="btn btn-brass">+ Buat Pengaduan</a>
  </div>

  <div class="tcard">
    <table>
      <thead>
        <tr><th>Tanggal</th><th>Jenis</th><th>Judul</th><th>Status</th><th>Tanggapan</th></tr>
      </thead>
      <tbody>
        @forelse ($pengaduan as $p)
          <tr>
            <td>{{ $p->created_at->translatedFormat('d M Y') }}</td>
            <td><span class="pill pill-{{ $p->jenis }}">{{ $p->jenis }}</span></td>
            <td>
              <a href="{{ route('customer.pengaduan.show', $p) }}" class="judul-link">{{ \Illuminate\Support\Str::limit($p->judul, 40) }}</a>
              <span class="sub-kategori">{{ $p->kategori->nama }}</span>
            </td>
            <td><span class="pill {{ $p->statusCss() }}">{{ $p->status }}</span></td>
            <td class="muted">{{ $p->tanggapan ? \Illuminate\Support\Str::limit($p->tanggapan, 50) : 'Belum ada tanggapan' }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="empty">Kamu belum pernah mengirim pengaduan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $pengaduan->links() }}

@endsection
