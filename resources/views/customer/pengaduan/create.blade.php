{{--
  FORM BUAT PENGADUAN (desain: 06-customer-buat-pengaduan).
  Dikirim ke Customer\PengaduanController@store.
  - enctype="multipart/form-data" WAJIB kalau form mengirim file (foto).
  - Field "Kategori" ditambahkan di luar desain Figma karena kategori adalah bagian fitur ini.
--}}
@extends('layouts.app')

@section('title', 'Buat Pengaduan — Kedai Rasa')
@section('pageTitle', 'Buat Pengaduan')
@section('pageSubtitle', 'Ceritakan keluhan atau saranmu.')

@section('content')

  {{-- Kalau admin belum membuat kategori, form tidak bisa dipakai --}}
  @if ($kategori->isEmpty())
    <div class="flash flash-error">Belum ada kategori pengaduan. Silakan hubungi admin.</div>
  @else
    <div class="grid-side">

      <div class="card form-card">
        <form method="POST" action="{{ route('customer.pengaduan.store') }}" enctype="multipart/form-data">
          @csrf

          {{-- Jenis: dua radio yang ditampilkan sebagai tombol bulat (gaya di dashboard.css .toggle) --}}
          <div class="form-row">
            <label>Jenis</label>
            <div class="toggle">
              <label><input type="radio" name="jenis" value="keluhan" {{ old('jenis', 'keluhan') === 'keluhan' ? 'checked' : '' }}><span>Keluhan</span></label>
              <label><input type="radio" name="jenis" value="saran" {{ old('jenis') === 'saran' ? 'checked' : '' }}><span>Saran</span></label>
            </div>
          </div>

          <div class="form-row">
            <label>Kategori</label>
            <select name="kategori_id" required>
              <option value="">-- Pilih kategori --</option>
              {{-- Pilihan diambil dari tabel kategori_pengaduan (dikelola admin) --}}
              @foreach ($kategori as $k)
                <option value="{{ $k->id }}" {{ (string) old('kategori_id') === (string) $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-row">
            <label>Judul</label>
            <input type="text" name="judul" value="{{ old('judul') }}" maxlength="255" placeholder="Ringkas masalahmu" required>
          </div>

          <div class="form-row">
            <label>Isi pengaduan</label>
            <textarea name="isi" placeholder="Jelaskan detailnya di sini... (min. 10 karakter)" required>{{ old('isi') }}</textarea>
          </div>

          {{-- Kotak unggah foto: <label> membungkus input file, jadi klik di mana saja membuka pemilih file --}}
          <div class="form-row">
            <label>Foto (opsional)</label>
            <label class="dropzone">
              <input type="file" name="foto" id="foto" accept="image/png,image/jpeg">
              <strong id="foto-nama">Pilih file gambar</strong>
              <small>JPG / PNG, maksimal 2 MB</small>
            </label>
          </div>

          <div style="display:flex; gap:10px; margin-top:24px;">
            <button type="submit" class="btn btn-brass">Kirim</button>
            <a href="{{ route('customer.pengaduan.index') }}" class="btn btn-white">Batal</a>
          </div>
        </form>
      </div>

      {{-- Kartu tips di kanan --}}
      <div class="card tips">
        <div class="card-title">Tips pengaduan</div>
        <ul>
          <li>Tulis judul yang jelas</li>
          <li>Sertakan foto bila ada</li>
          <li>Admin akan menanggapi dan mengubah statusnya</li>
        </ul>
      </div>

    </div>
  @endif

@endsection

@section('scripts')
  <script>
    // Tampilkan nama file yang dipilih di kotak unggah (supaya user tahu fotonya sudah terpilih).
    const inputFoto = document.getElementById('foto');
    if (inputFoto) {
      inputFoto.addEventListener('change', function () {
        document.getElementById('foto-nama').textContent =
          this.files.length ? this.files[0].name : 'Pilih file gambar';
      });
    }
  </script>
@endsection
