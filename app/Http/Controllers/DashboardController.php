<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * DashboardController: halaman beranda setelah login.
 */
class DashboardController extends Controller
{
    /**
     * Pintu masuk umum (/dashboard).
     * Mengarahkan user ke dashboard yang sesuai dengan rolenya.
     */
    public function index()
    {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('customer.dashboard');
    }

    /**
     * Dashboard admin: ringkasan jumlah customer per status + tabel pendaftaran
     * dengan tombol Terima / Tolak / Hapus.
     *
     * Query string yang didukung:
     *   ?status=pending|approved|rejected|all  -> tab filter (bawaan: pending)
     *   ?q=kata                                -> cari nama atau email
     */
    public function admin(Request $request)
    {
        // Tab yang dipilih; nilai tidak valid dikembalikan ke 'pending'.
        $status = $request->query('status', 'pending');
        if (! in_array($status, ['pending', 'approved', 'rejected', 'all'], true)) {
            $status = 'pending';
        }

        $keyword = trim((string) $request->query('q', ''));

        // Hanya akun customer yang ditampilkan (admin tidak perlu di-ACC).
        $customers = User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // filter tetap terbawa saat pindah halaman

        // Hitung customer per status untuk kartu ringkasan dan angka di tab.
        $hitung = fn (?string $s) => User::where('role', User::ROLE_CUSTOMER)
            ->when($s, fn ($query) => $query->where('status', $s))
            ->count();

        return view('admin.dashboard', [
            'customers'     => $customers,
            'status'        => $status,
            'keyword'       => $keyword,
            'totalPending'  => $hitung(User::STATUS_PENDING),
            'totalApproved' => $hitung(User::STATUS_APPROVED),
            'totalRejected' => $hitung(User::STATUS_REJECTED),
            'totalCustomer' => $hitung(null),
        ]);
    }

    /**
     * Dashboard customer: kartu info akun, ajakan membuat pengaduan,
     * dan tabel 3 pengaduan terakhir miliknya.
     */
    public function customer()
    {
        // auth()->user()->pengaduan() = hanya pengaduan milik user yang login.
        $terakhir = auth()->user()->pengaduan()
            ->with('kategori')
            ->latest()
            ->take(3)
            ->get();

        return view('customer.dashboard', ['terakhir' => $terakhir]);
    }
}
