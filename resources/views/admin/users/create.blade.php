{{--
  KELOLA USER - CREATE (desain: 11-admin-form-user).
  Dikirim ke Admin\UserController@store. Akun yang dibuat admin otomatis 'approved'.
--}}
@extends('layouts.app')

@section('title', 'Tambah User — Kedai Rasa')
@section('pageTitle', 'Tambah User')
@section('pageSubtitle', 'Akun yang dibuat admin langsung berstatus approved.')

@section('content')

  <div class="card form-card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.users.store') }}">
      @csrf {{-- token keamanan wajib untuk setiap form POST --}}

      <div class="form-row">
        <label>Nama lengkap</label>
        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama user" required>
      </div>
      <div class="form-row">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
      </div>
      <div class="form-row">
        <label>Password</label>
        <input type="password" name="password" placeholder="Minimal 8 karakter" required>
      </div>
      <div class="form-row">
        <label>Role</label>
        <select name="role" required>
          <option value="customer" {{ old('role', 'customer') === 'customer' ? 'selected' : '' }}>customer</option>
          <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>admin</option>
        </select>
      </div>

      <div class="info-box">
        Status otomatis 'approved' karena dibuat oleh admin.<br>
        Role yang tersedia: admin dan customer.
      </div>

      <div style="display:flex; gap:10px;">
        <button type="submit" class="btn btn-brass">Simpan</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-white">Batal</a>
      </div>
    </form>
  </div>

@endsection
