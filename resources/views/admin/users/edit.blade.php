{{--
  KELOLA USER - UPDATE: ubah nama, email, password (opsional), role, dan status ACC.
  Kalau yang diedit adalah akun admin yang sedang login, role & status dikunci
  (juga dipaksa di Admin\UserController@update). Dikirim lewat PUT ke @update.
--}}
@extends('layouts.app')

@section('title', 'Ubah User — Kedai Rasa')
@section('pageTitle', 'Ubah User')
@section('pageSubtitle', $editUser->name)

@section('content')

  @php $akunSendiri = $editUser->id === auth()->id(); @endphp

  <div class="card form-card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.users.update', $editUser) }}">
      @csrf @method('PUT') {{-- form HTML hanya GET/POST, jadi PUT dipalsukan lewat @method --}}

      <div class="form-row">
        <label>Nama lengkap</label>
        <input type="text" name="name" value="{{ old('name', $editUser->name) }}" required>
      </div>
      <div class="form-row">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $editUser->email) }}" required>
      </div>
      <div class="form-row">
        <label>Password baru (kosongkan jika tidak diubah)</label>
        <input type="password" name="password" placeholder="Minimal 8 karakter">
      </div>
      <div class="form-row">
        <label>Role</label>
        <select name="role" required {{ $akunSendiri ? 'disabled' : '' }}>
          <option value="customer" {{ old('role', $editUser->role) === 'customer' ? 'selected' : '' }}>customer</option>
          <option value="admin" {{ old('role', $editUser->role) === 'admin' ? 'selected' : '' }}>admin</option>
        </select>
      </div>
      <div class="form-row">
        <label>Status ACC</label>
        <select name="status" required {{ $akunSendiri ? 'disabled' : '' }}>
          <option value="pending" {{ old('status', $editUser->status) === 'pending' ? 'selected' : '' }}>pending</option>
          <option value="approved" {{ old('status', $editUser->status) === 'approved' ? 'selected' : '' }}>approved</option>
          <option value="rejected" {{ old('status', $editUser->status) === 'rejected' ? 'selected' : '' }}>rejected</option>
        </select>
      </div>

      {{-- Select yang disabled tidak ikut terkirim, jadi nilai aslinya dikirim lewat input hidden --}}
      @if ($akunSendiri)
        <input type="hidden" name="role" value="{{ $editUser->role }}">
        <input type="hidden" name="status" value="{{ $editUser->status }}">
        <div class="info-box">Role dan status akunmu sendiri tidak bisa diubah.</div>
      @else
        <div class="info-box">Akun dengan role admin selalu berstatus approved.</div>
      @endif

      <div style="display:flex; gap:10px;">
        <button type="submit" class="btn btn-brass">Simpan</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-white">Batal</a>
      </div>
    </form>
  </div>

@endsection
