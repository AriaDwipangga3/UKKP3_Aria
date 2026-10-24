{{--
  DASHBOARD CUSTOMER (desain: 05-customer-dashboard).
  Isi: kartu info akun | kartu ajakan buat pengaduan | tabel 3 pengaduan terakhir.
  Data $terakhir dikirim dari DashboardController@customer.
--}}
@extends('layouts.app')

@section('title', 'Beranda — Kedai Rasa')
@section('pageTitle', 'Halo, ' . auth()->user()->name)
@section('pageSubtitle', 'Selamat datang di Kedai Rasa.')

@section('content')

  <div class="grid-half">
    {{-- Kartu kiri: info akun --}}
    <div class="card">
      <div class="card-label">Akunmu</div>
      <div class="detail-title" style="margin:6px 0 4px;">{{ auth()->user()->name }}</div>
      <div style="color:var(--ink-soft); font-size:14px; margin-bottom:12px;">{{ auth()->user()->email }}</div>
      <span class="pill pill-selesai">{{ auth()->user()->status }}</span>
    </div>

    {{-- Kartu kanan (gelap): ajakan membuat pengaduan --}}
    <div class="cta-card">
      <h2>Ada keluhan atau saran?</h2>
      <p>Sampaikan langsung, kami baca dan tanggapi.</p>
      <a href="{{ route('customer.pengaduan.create') }}" class="btn btn-brass">+ Buat Pengaduan</a>
    </div>
  </div>

  {{-- Tabel pengaduan terakhir --}}
  <div class="section-head">
    <h3>Pengaduan terakhir</h3>
    <a href="{{ route('customer.pengaduan.index') }}" class="panel-link">Lihat semua</a>
  </div>

  <div class="tcard">
    <table>
      <thead>
        <tr><th>Tanggal</th><th>Jenis</th><th>Judul</th><th>Status</th></tr>
      </thead>
      <tbody>
        @forelse ($terakhir as $p)
          <tr>
            <td>{{ $p->created_at->translatedFormat('d M Y') }}</td>
            <td><span class="pill pill-{{ $p->jenis }}">{{ $p->jenis }}</span></td>
            <td><a href="{{ route('customer.pengaduan.show', $p) }}" class="judul-link">{{ $p->judul }}</a></td>
            <td><span class="pill {{ $p->statusCss() }}">{{ $p->status }}</span></td>
          </tr>
        @empty
          <tr><td colspan="4" class="empty">Belum ada pengaduan. Klik "Buat Pengaduan" untuk mulai.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

@endsection
