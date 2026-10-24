<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Admin\PengaduanController: sisi ADMIN dari fitur pengaduan.
 *
 *  index()   -> GET  /admin/pengaduan                daftar semua pengaduan (+ filter)
 *  show()    -> GET  /admin/pengaduan/{id}           detail; status 'baru' otomatis jadi 'diproses'
 *  respond() -> POST /admin/pengaduan/{id}/respond   simpan tanggapan + ubah status
 */
class PengaduanController extends Controller
{
    /**
     * Daftar pengaduan.
     *
     * Query string yang didukung (semuanya opsional):
     *   ?status=baru|diproses|selesai   -> tab filter status
     *   ?kategori=ID                    -> filter per kategori
     *   ?q=kata                         -> cari judul atau nama pelapor
     */
    public function index(Request $request)
    {
        // Nilai status yang tidak dikenal diabaikan (dianggap "semua").
        $status = $request->query('status');
        if (! in_array($status, Pengaduan::DAFTAR_STATUS, true)) {
            $status = null;
        }

        $kategoriId = $request->query('kategori');
        $keyword = trim((string) $request->query('q', ''));

        // Query dibangun bertahap; when() hanya menambah kondisi kalau filternya diisi.
        $pengaduan = Pengaduan::query()
            ->with(['user', 'kategori'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($kategoriId, fn ($q) => $q->where('kategori_id', $kategoriId))
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('judul', 'like', "%{$keyword}%")
                        // whereHas = cari juga di tabel users (nama pelapor)
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$keyword}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // filter tetap terbawa saat pindah halaman

        return view('admin.pengaduan.index', [
            'pengaduan'    => $pengaduan,
            'status'       => $status,
            'kategoriId'   => $kategoriId,
            'keyword'      => $keyword,
            'daftarKategori' => Kategori::orderBy('nama')->get(),
            // angka di tab filter
            'totalSemua'    => Pengaduan::count(),
            'totalBaru'     => Pengaduan::where('status', Pengaduan::STATUS_BARU)->count(),
            'totalDiproses' => Pengaduan::where('status', Pengaduan::STATUS_DIPROSES)->count(),
            'totalSelesai'  => Pengaduan::where('status', Pengaduan::STATUS_SELESAI)->count(),
        ]);
    }

    /** Detail pengaduan + form tanggapan. */
    public function show(Pengaduan $pengaduan)
    {
        // Sesuai diagram: begitu admin MEMBUKA pengaduan yang masih 'baru',
        // statusnya otomatis menjadi 'diproses' (tanda sudah dilihat admin).
        if ($pengaduan->status === Pengaduan::STATUS_BARU) {
            $pengaduan->update(['status' => Pengaduan::STATUS_DIPROSES]);
        }

        $pengaduan->load(['user', 'kategori']);

        return view('admin.pengaduan.show', ['pengaduan' => $pengaduan]);
    }

    /** Simpan tanggapan admin dan status terbaru. */
    public function respond(Request $request, Pengaduan $pengaduan)
    {
        $data = $request->validate([
            'status'    => ['required', Rule::in(Pengaduan::DAFTAR_STATUS)],
            'tanggapan' => ['required', 'string', 'max:2000'],
        ]);

        $pengaduan->update($data);

        // Kembali ke halaman detail yang sama; customer akan melihat tanggapan ini di riwayatnya.
        return redirect()->route('admin.pengaduan.show', $pengaduan)->with('success', 'Tanggapan berhasil disimpan.');
    }
}
