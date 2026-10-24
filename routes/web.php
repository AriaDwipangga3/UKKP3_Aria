<?php

use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\PengaduanController as AdminPengaduanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\PengaduanController as CustomerPengaduanController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Daftar route (alamat halaman) aplikasi
|--------------------------------------------------------------------------
| Middleware yang dipakai:
|   guest    -> hanya untuk yang BELUM login
|   auth     -> harus sudah login
|   approved -> akun harus sudah di-ACC admin
|   role:xxx -> hanya role tertentu (admin / customer)
*/

// ================= AUTH: login & register (hanya untuk tamu / belum login) =================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');       // form login
    Route::post('/login', [AuthController::class, 'login']);                          // proses login
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register'); // form register
    Route::post('/register', [AuthController::class, 'register']);                    // proses register
});

// Logout (harus login dulu)
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Halaman utama "/": diteruskan ke /dashboard. Kalau belum login, otomatis dilempar ke /login.
Route::redirect('/', '/dashboard');

// ================= DASHBOARD UMUM (semua user yang sudah login & di-ACC) =================
// /dashboard memilih tujuan sesuai role: admin -> admin.dashboard, customer -> customer.dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'approved'])
    ->name('dashboard');

// ================= ADMIN (khusus role admin) =================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'approved', 'role:admin'])->group(function () {
    // Dashboard admin: ringkasan + daftar pendaftaran dengan tombol Terima / Tolak
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // --- Kelola User (CRUD) ---
    // only([...]) = hanya 5 route ini yang dibuat otomatis; 'destroy' ditulis manual di bawah
    // (bersama approve & reject) karena punya aturan khusus.
    // Nama route: admin.users.index, admin.users.create, admin.users.store, admin.users.edit, admin.users.update
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update']);

    // Aksi admin pada akun customer (tombol di tabel dashboard):
    Route::patch('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve'); // Terima
    Route::patch('/users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');    // Tolak
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');        // Hapus

    // --- Kategori Pengaduan (CRUD) ---
    // Route::resource membuat 6 route sekaligus (index, create, store, edit, update, destroy)
    // dengan nama admin.kategori.index, admin.kategori.create, dst. 'show' tidak dipakai.
    Route::resource('kategori', KategoriController::class)
        ->except('show')
        ->parameters(['kategori' => 'kategori']); // pastikan parameter URL bernama {kategori} (cocok dengan $kategori di controller)

    // --- Pengaduan (sisi admin) ---
    Route::get('/pengaduan', [AdminPengaduanController::class, 'index'])->name('pengaduan.index');            // daftar + filter
    Route::get('/pengaduan/{pengaduan}', [AdminPengaduanController::class, 'show'])->name('pengaduan.show'); // detail (baru -> diproses)
    Route::post('/pengaduan/{pengaduan}/respond', [AdminPengaduanController::class, 'respond'])->name('pengaduan.respond'); // simpan tanggapan
});

// ================= CUSTOMER (khusus role customer yang sudah di-ACC) =================
Route::prefix('customer')->name('customer.')->middleware(['auth', 'approved', 'role:customer'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'customer'])->name('dashboard');

    // --- Pengaduan (sisi customer) ---
    // Urutan penting: '/pengaduan/create' harus di atas '/pengaduan/{pengaduan}',
    // kalau terbalik kata "create" akan dianggap sebagai ID pengaduan.
    Route::get('/pengaduan', [CustomerPengaduanController::class, 'index'])->name('pengaduan.index');           // riwayat
    Route::get('/pengaduan/create', [CustomerPengaduanController::class, 'create'])->name('pengaduan.create'); // form buat
    Route::post('/pengaduan', [CustomerPengaduanController::class, 'store'])->name('pengaduan.store');         // simpan
    Route::get('/pengaduan/{pengaduan}', [CustomerPengaduanController::class, 'show'])->name('pengaduan.show'); // detail + tanggapan
});
