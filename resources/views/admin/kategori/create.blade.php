{{--
  FORM TAMBAH KATEGORI (CREATE). Dikirim ke Admin\KategoriController@store.
  old('nama') = mengisi ulang isian lama kalau validasi gagal (supaya tidak mengetik ulang).
--}}
@extends('layouts.app')

@section('title', 'Tambah Kategori — Kedai Rasa')
@section('pageTitle', 'Tambah Kategori')
@section('pageSubtitle', 'Kategori baru langsung muncul di form pengaduan customer')

@section('content')

  <div class="card form-card" style="max-width:520px;">
    <form method="POST" action="{{ route('admin.kategori.store') }}">
      @csrf {{-- token keamanan wajib untuk setiap form POST --}}
      <div class="form-row">
        <label>Nama kategori</label>
        <input type="text" name="nama" value="{{ old('nama') }}" maxlength="100" required>
      </div>
      <div class="form-row">
        <label>Deskripsi (opsional)</label>
        <textarea name="deskripsi" maxlength="500">{{ old('deskripsi') }}</textarea>
      </div>
      <button type="submit" class="btn btn-brass">Simpan kategori</button>
      <a href="{{ route('admin.kategori.index') }}" class="btn btn-white" style="margin-left:6px;">Batal</a>
    </form>
  </div>

@endsection
