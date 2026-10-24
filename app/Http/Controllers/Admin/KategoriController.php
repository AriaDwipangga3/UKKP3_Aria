<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Admin\KategoriController: CRUD Kategori Pengaduan (khusus admin).
 *
 * Alur singkat tiap method (C-R-U-D):
 *  index()   READ   -> GET    /admin/kategori              tampilkan tabel semua kategori
 *  create()  CREATE -> GET    /admin/kategori/create       tampilkan form tambah
 *  store()   CREATE -> POST   /admin/kategori              simpan kategori baru
 *  edit()    UPDATE -> GET    /admin/kategori/{id}/edit    tampilkan form ubah
 *  update()  UPDATE -> PUT    /admin/kategori/{id}         simpan perubahan
 *  destroy() DELETE -> DELETE /admin/kategori/{id}         hapus (kalau belum dipakai)
 *
 * Semua route sudah dilindungi middleware auth + approved + role:admin (lihat routes/web.php).
 */
class KategoriController extends Controller
{
    /** READ: daftar kategori + jumlah pengaduan yang memakainya. */
    public function index()
    {
        $kategori = Kategori::query()
            ->withCount('pengaduan') // menambah kolom virtual `pengaduan_count` per kategori
            ->orderBy('nama')
            ->paginate(10);

        return view('admin.kategori.index', ['kategori' => $kategori]);
    }

    /** CREATE (langkah 1): tampilkan form kosong. */
    public function create()
    {
        return view('admin.kategori.create');
    }

    /** CREATE (langkah 2): validasi isian form, lalu simpan ke database. */
    public function store(Request $request)
    {
        // validate() otomatis membawa user kembali ke form + pesan error kalau ada yang salah.
        $data = $request->validate([
            'nama'      => ['required', 'string', 'max:100', 'unique:kategori_pengaduan,nama'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ]);

        Kategori::create($data);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /** UPDATE (langkah 1): tampilkan form berisi data lama. $kategori diambil otomatis dari ID di URL. */
    public function edit(Kategori $kategori)
    {
        return view('admin.kategori.edit', ['kategori' => $kategori]);
    }

    /** UPDATE (langkah 2): validasi lalu simpan perubahan. */
    public function update(Request $request, Kategori $kategori)
    {
        $data = $request->validate([
            // ->ignore($kategori->id): nama boleh sama dengan nama kategori ini sendiri
            // (kalau tidak, menyimpan tanpa mengubah nama akan dianggap "nama sudah dipakai").
            'nama'      => ['required', 'string', 'max:100', Rule::unique('kategori_pengaduan', 'nama')->ignore($kategori->id)],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ]);

        $kategori->update($data);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * DELETE: hapus kategori.
     * Kategori yang masih dipakai pengaduan TIDAK boleh dihapus, supaya pengaduan lama
     * tidak kehilangan kategorinya. (Database juga menjaga ini lewat restrictOnDelete.)
     */
    public function destroy(Kategori $kategori)
    {
        if ($kategori->pengaduan()->exists()) {
            return back()->withErrors([
                'kategori' => "Kategori \"{$kategori->nama}\" masih dipakai pengaduan, jadi tidak bisa dihapus.",
            ]);
        }

        $kategori->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
