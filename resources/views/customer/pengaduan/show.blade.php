{{--
  DETAIL PENGADUAN (sisi customer): isi pengaduan di kiri, tanggapan admin di kanan.
  Hanya pemilik pengaduan yang bisa membukanya (dicek di Customer\PengaduanController@show).
--}}
@extends('layouts.app')

@section('title', 'Detail Pengaduan — Kedai Rasa')
@section('pageTitle', 'Detail Pengaduan')
@section('pageSubtitle', 'Pengaduan yang kamu kirim.')

@section('content')

  <div class="grid-detail">

    <div class="card">
      <span class="pill pill-{{ $pengaduan->jenis }}">{{ $pengaduan->jenis }}</span>
      <span class="pill {{ $pengaduan->statusCss() }}">{{ $pengaduan->status }}</span>
      <span class="pill pill-kategori">{{ $pengaduan->kategori->nama }}</span>

      <div class="detail-title">{{ $pengaduan->judul }}</div>
      <div class="detail-meta">{{ $pengaduan->created_at->translatedFormat('d M Y, H:i') }}</div>

      {{-- nl2br(e(...)) = pertahankan baris baru, tapi tetap aman dari HTML berbahaya --}}
      <div class="detail-isi">{!! nl2br(e($pengaduan->isi)) !!}</div>

      @if ($pengaduan->fotoUrl())
        <div class="card-label" style="margin:22px 0 8px;">Foto lampiran</div>
        <div class="foto-box"><img src="{{ $pengaduan->fotoUrl() }}" alt="Foto pengaduan"></div>
      @endif
    </div>

    <div class="card">
      <div class="card-title">Tanggapan admin</div>
      @if ($pengaduan->tanggapan)
        <div class="detail-isi">{!! nl2br(e($pengaduan->tanggapan)) !!}</div>
      @else
        <div class="muted">Belum ada tanggapan. Admin akan segera memprosesnya.</div>
      @endif
      <div style="margin-top:22px;">
        <a href="{{ route('customer.pengaduan.index') }}" class="btn btn-white">&larr; Kembali</a>
      </div>
    </div>

  </div>

@endsection
