<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Customer\PengaduanController: sisi CUSTOMER dari fitur pengaduan.
 *
 *  index()  -> GET  /customer/pengaduan          riwayat pengaduan milik sendiri
 *  create() -> GET  /customer/pengaduan/create   form "Buat Pengaduan"
 *  store()  -> POST /customer/pengaduan          simpan pengaduan baru
 *  show()   -> GET  /customer/pengaduan/{id}     detail + tanggapan admin
 *
 * Aturan penting: customer HANYA boleh melihat pengaduan miliknya sendiri.
 */
class PengaduanController extends Controller
{
    /** Riwayat: semua pengaduan milik user yang sedang login (terbaru di atas). */
    public function index()
    {
        // auth()->user()->pengaduan() otomatis menambah WHERE user_id = id user yang login,
        // jadi data customer lain tidak akan pernah ikut terambil.
        $pengaduan = auth()->user()->pengaduan()
            ->with('kategori') // ambil kategori sekalian (menghindari query berulang per baris)
            ->latest()
            ->paginate(10);

        return view('customer.pengaduan.index', ['pengaduan' => $pengaduan]);
    }

    /** Tampilkan form. Daftar kategori diambil dari tabel yang dikelola admin. */
    public function create()
    {
        return view('customer.pengaduan.create', [
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    /**
     * Proses form: validasi -> simpan foto (kalau ada) -> simpan pengaduan -> kembali ke riwayat.
     * Urutan ini sama dengan Sequence Diagram "Pengaduan".
     */
    public function store(Request $request)
    {
        // 1) VALIDASI. Kalau ada yang salah, Laravel otomatis kembali ke form + pesan error.
        $data = $request->validate([
            'jenis'       => ['required', Rule::in(Pengaduan::DAFTAR_JENIS)],
            'kategori_id' => ['required', 'exists:kategori_pengaduan,id'], // harus kategori yang benar-benar ada
            'judul'       => ['required', 'string', 'max:255'],
            'isi'         => ['required', 'string', 'min:10'],
            'foto'        => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // maks 2 MB (2048 KB)
        ]);

        // 2) SIMPAN FOTO (opsional). store() menaruh file di storage/app/public/pengaduan
        //    dengan nama acak, lalu mengembalikan path-nya untuk disimpan di kolom `foto`.
        $pathFoto = $request->hasFile('foto')
            ? $request->file('foto')->store('pengaduan', 'public')
            : null;

        // 3) SIMPAN PENGADUAN. user_id diisi otomatis dari relasi (bukan dari form -> tidak bisa dipalsukan),
        //    dan status otomatis 'baru'.
        $request->user()->pengaduan()->create([
            'jenis'       => $data['jenis'],
            'kategori_id' => $data['kategori_id'],
            'judul'       => $data['judul'],
            'isi'         => $data['isi'],
            'foto'        => $pathFoto,
            'status'      => Pengaduan::STATUS_BARU,
        ]);

        // 4) Kembali ke riwayat + pesan sukses.
        return redirect()->route('customer.pengaduan.index')->with('success', 'Pengaduan berhasil dikirim.');
    }

    /** Detail satu pengaduan (termasuk tanggapan admin kalau sudah ada). */
    public function show(Pengaduan $pengaduan)
    {
        // Pengaman: kalau pengaduan ini bukan milik user yang login -> tampilkan 404.
        // (404, bukan 403, supaya orang lain tidak bisa menebak ID pengaduan yang ada.)
        abort_unless($pengaduan->user_id === auth()->id(), 404);

        $pengaduan->load('kategori');

        return view('customer.pengaduan.show', ['pengaduan' => $pengaduan]);
    }
}
