{{--
  DETAIL PENGADUAN sisi admin (desain: 13-admin-detail-pengaduan).
  Kiri : isi pengaduan + foto lampiran.   Kanan : form tanggapan (status + teks).
  Controller sudah mengubah status 'baru' -> 'diproses' sebelum halaman ini tampil.
  Form dikirim ke Admin\PengaduanController@respond.
--}}
@extends('layouts.app')

@section('title', 'Detail Pengaduan — Kedai Rasa')
@section('pageTitle', 'Detail Pengaduan')
@section('pageSubtitle', 'Pengaduan dari ' . $pengaduan->user->name . '.')

@section('content')

  <div class="grid-detail">

    {{-- Kartu kiri: isi pengaduan --}}
    <div class="card">
      <span class="pill pill-{{ $pengaduan->jenis }}">{{ $pengaduan->jenis }}</span>
      <span class="pill {{ $pengaduan->statusCss() }}">{{ $pengaduan->status }}</span>
      <span class="pill pill-kategori">{{ $pengaduan->kategori->nama }}</span>

      <div class="detail-title">{{ $pengaduan->judul }}</div>
      <div class="detail-meta">{{ $pengaduan->user->name }} · {{ $pengaduan->created_at->translatedFormat('d M Y, H:i') }}</div>

      {{-- nl2br(e(...)) = pertahankan baris baru, tapi tetap aman dari HTML berbahaya --}}
      <div class="detail-isi">{!! nl2br(e($pengaduan->isi)) !!}</div>

      <div class="card-label" style="margin:22px 0 8px;">Foto lampiran</div>
      @if ($pengaduan->fotoUrl())
        <a href="{{ $pengaduan->fotoUrl() }}" target="_blank" class="foto-box" style="display:block;">
          <img src="{{ $pengaduan->fotoUrl() }}" alt="Foto pengaduan">
        </a>
      @else
        <div class="foto-box muted" style="padding:28px;">Tidak ada foto</div>
      @endif
    </div>

    {{-- Kartu kanan: form tanggapan --}}
    <div class="card form-card">
      <div class="card-title">Tanggapan</div>
      <form method="POST" action="{{ route('admin.pengaduan.respond', $pengaduan) }}">
        @csrf
        <div class="form-row">
          <label>Status</label>
          <select name="status" required>
            @foreach (\App\Models\Pengaduan::DAFTAR_STATUS as $s)
              <option value="{{ $s }}" {{ old('status', $pengaduan->status) === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-row">
          <label>Tanggapan</label>
          <textarea name="tanggapan" placeholder="Tulis tanggapan untuk customer..." required maxlength="2000">{{ old('tanggapan', $pengaduan->tanggapan) }}</textarea>
        </div>
        <button type="submit" class="btn btn-brass btn-block">Simpan Tanggapan</button>
        <div class="hint-small">Membuka pengaduan berstatus 'baru' otomatis mengubahnya menjadi 'diproses'.</div>
      </form>
      <div style="margin-top:18px;"><a href="{{ route('admin.pengaduan.index') }}" class="panel-link">&larr; Kembali ke daftar</a></div>
    </div>

  </div>

@endsection
