{{--
  FORM EDIT KATEGORI (UPDATE). Dikirim ke Admin\KategoriController@update.
  Form HTML hanya mengenal GET/POST, jadi @method('PUT') dipakai agar Laravel menganggapnya PUT.
  old('nama', $kategori->nama) = pakai isian lama kalau ada error, kalau tidak pakai data dari database.
--}}
@extends('layouts.app')

@section('title', 'Edit Kategori — Kedai Rasa')
@section('pageTitle', 'Edit Kategori')
@section('pageSubtitle', $kategori->nama)

@section('content')

  <div class="card form-card" style="max-width:520px;">
    <form method="POST" action="{{ route('admin.kategori.update', $kategori) }}">
      @csrf @method('PUT')
      <div class="form-row">
        <label>Nama kategori</label>
        <input type="text" name="nama" value="{{ old('nama', $kategori->nama) }}" maxlength="100" required>
      </div>
      <div class="form-row">
        <label>Deskripsi (opsional)</label>
        <textarea name="deskripsi" maxlength="500">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
      </div>
      <button type="submit" class="btn btn-brass">Simpan perubahan</button>
      <a href="{{ route('admin.kategori.index') }}" class="btn btn-white" style="margin-left:6px;">Batal</a>
    </form>
  </div>

@endsection
