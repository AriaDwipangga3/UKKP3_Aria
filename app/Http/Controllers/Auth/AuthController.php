<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * AuthController: mengurus login, register, dan logout.
 *
 * Alur ACC:
 *  1. Customer mengisi form register  -> akun dibuat dengan status 'pending'.
 *  2. Customer belum bisa login sampai admin menekan "Terima" di dashboard admin.
 *  3. Setelah di-ACC (status 'approved'), customer bisa login seperti biasa.
 */
class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Memproses form login.
     * Urutan pengecekan: email+password benar -> status akun approved -> baru boleh masuk.
     * Pesan "salah email/password" sengaja dibuat sama agar tidak membocorkan email yang terdaftar.
     */
    public function login(Request $request)
    {
        // 1. Validasi input form.
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Cari user lalu cocokkan password.
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // 3. Password benar, tapi akun harus sudah di-ACC admin.
        if ($user->isPending()) {
            throw ValidationException::withMessages([
                'email' => 'Akunmu belum di-ACC admin. Silakan tunggu persetujuan.',
            ]);
        }

        if ($user->isRejected()) {
            throw ValidationException::withMessages([
                'email' => 'Pendaftaran akunmu ditolak oleh admin.',
            ]);
        }

        // 4. Semua lolos: login-kan user dan perbarui session (cegah session fixation).
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Rute 'dashboard' akan mengarahkan ke dashboard admin atau customer sesuai role.
        return redirect()->route('dashboard');
    }

    /**
     * Menampilkan halaman form register.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Memproses form register.
     * Akun baru SELALU berrole 'customer' dan berstatus 'pending'.
     * User TIDAK langsung login; ia diarahkan ke halaman login dengan pesan menunggu ACC.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // Password otomatis di-hash oleh cast 'hashed' di model User.
        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'role'     => User::ROLE_CUSTOMER,
            'status'   => User::STATUS_PENDING,
        ]);

        return redirect()->route('login')->with(
            'success',
            'Pendaftaran berhasil! Akunmu akan aktif setelah di-ACC admin.'
        );
    }

    /**
     * Logout: hapus login, matikan session, dan buat token CSRF baru.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
