<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Admin\UserController: CRUD akun user oleh admin (menu "Kelola User").
 *
 *  index()   READ   -> GET    /admin/users              tabel semua akun + pencarian
 *  create()  CREATE -> GET    /admin/users/create       form tambah user
 *  store()   CREATE -> POST   /admin/users              simpan user baru (langsung approved)
 *  edit()    UPDATE -> GET    /admin/users/{id}/edit    form ubah user
 *  update()  UPDATE -> PUT    /admin/users/{id}         simpan perubahan
 *  destroy() DELETE -> DELETE /admin/users/{id}         hapus akun customer
 *
 * Tiga method tambahan dipakai tombol di dashboard admin (ACC pendaftaran):
 *  approve() -> tombol "Terima" : status jadi 'approved' (customer bisa login)
 *  reject()  -> tombol "Tolak"  : status jadi 'rejected' (customer tidak bisa login)
 *
 * Semua route di sini sudah dilindungi middleware auth + approved + role:admin (lihat routes/web.php).
 */
class UserController extends Controller
{
    // =====================================================================
    //  READ
    // =====================================================================

    /**
     * Daftar semua akun (admin & customer).
     * Query string: ?q=kata  -> cari nama atau email.
     */
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('q', ''));

        $users = User::query()
            // when() hanya menambah kondisi kalau kata kunci diisi.
            ->when($keyword !== '', function ($query) use ($keyword) {
                // Dikelompokkan dalam satu where(function...) supaya OR tidak merusak kondisi lain.
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString(); // kata kunci tetap terbawa saat pindah halaman

        return view('admin.users.index', ['users' => $users, 'keyword' => $keyword]);
    }

    // =====================================================================
    //  CREATE
    // =====================================================================

    /** Langkah 1: tampilkan form kosong. */
    public function create()
    {
        return view('admin.users.create');
    }

    /** Langkah 2: validasi form lalu simpan user baru. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'], // email tidak boleh kembar
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_CUSTOMER])],
        ]);

        $user = new User();
        // Password otomatis di-hash oleh casts() di model User, tidak perlu Hash::make().
        $user->fill($data);
        // Akun buatan admin langsung 'approved' (tidak perlu ACC lagi).
        $user->setStatus(User::STATUS_APPROVED)->save();

        return redirect()->route('admin.users.index')->with('success', "Akun {$user->name} berhasil ditambahkan.");
    }

    // =====================================================================
    //  UPDATE
    // =====================================================================

    /** Langkah 1: tampilkan form berisi data lama. $user diambil otomatis dari ID di URL. */
    public function edit(User $user)
    {
        // Dikirim sebagai $editUser (bukan $user) agar tidak tertukar dengan user yang sedang login.
        return view('admin.users.edit', ['editUser' => $user]);
    }

    /** Langkah 2: validasi lalu simpan perubahan. */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            // ignore($user->id): email boleh sama dengan email akun ini sendiri.
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'], // kosong = password tidak diubah
            'role'     => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_CUSTOMER])],
            'status'   => ['required', Rule::in([User::STATUS_PENDING, User::STATUS_APPROVED, User::STATUS_REJECTED])],
        ]);

        // Pengaman 1: admin tidak boleh mengubah role / status AKUNNYA SENDIRI,
        // supaya tidak sengaja mengunci dirinya (atau satu-satunya admin) dari aplikasi.
        if ($user->id === auth()->id()) {
            $data['role'] = $user->role;
            $data['status'] = $user->status;
        }

        // Pengaman 2: akun admin harus selalu 'approved'. Admin tidak lewat proses ACC,
        // jadi kalau statusnya pending/rejected dia tidak akan bisa login.
        if ($data['role'] === User::ROLE_ADMIN) {
            $data['status'] = User::STATUS_APPROVED;
        }

        $user->fill([
            'name'  => $data['name'],
            'email' => $data['email'],
            'role'  => $data['role'],
        ]);

        // Password hanya diganti kalau kolomnya diisi.
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        // setStatus() sekaligus mengatur kolom approved_at (lihat model User).
        $user->setStatus($data['status'])->save();

        return redirect()->route('admin.users.index')->with('success', "Akun {$user->name} berhasil diperbarui.");
    }

    // =====================================================================
    //  AKSI ACC (tombol di dashboard admin)
    // =====================================================================

    /**
     * Menerima (ACC) akun customer. Setelah ini customer bisa login.
     * $user diambil otomatis dari ID di URL (route model binding).
     */
    public function approve(User $user)
    {
        $this->pastikanCustomer($user);

        $user->setStatus(User::STATUS_APPROVED)->save();

        // back() = kembali ke halaman dashboard dengan tab/pencarian yang sama.
        return back()->with('success', "Akun {$user->name} diterima.");
    }

    /**
     * Menolak akun customer. Customer yang ditolak tidak bisa login.
     */
    public function reject(User $user)
    {
        $this->pastikanCustomer($user);

        $user->setStatus(User::STATUS_REJECTED)->save();

        return back()->with('success', "Akun {$user->name} ditolak.");
    }

    // =====================================================================
    //  DELETE
    // =====================================================================

    /**
     * Menghapus akun customer secara permanen.
     * Pengaduan milik customer itu ikut terhapus (cascade di database).
     */
    public function destroy(User $user)
    {
        $this->pastikanCustomer($user);

        $user->delete();

        return back()->with('success', 'Akun berhasil dihapus.');
    }

    /**
     * Pengaman: approve / reject / hapus hanya untuk akun customer.
     * Akun admin (termasuk akun admin sendiri) tidak bisa diproses dari sini.
     */
    private function pastikanCustomer(User $target): void
    {
        if (! $target->isCustomer()) {
            abort(403, 'Hanya akun customer yang bisa diproses.');
        }
    }
}
