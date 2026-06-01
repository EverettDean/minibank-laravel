<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // WAJIB TAMBAHKAN INI untuk memeriksa hash password bawaan

class AuthController extends Controller
{
    // 1. Menampilkan Halaman Login Awal
    public function showLogin()
    {
        return view('login');
    }

    // 2. Memproses Data Form Login (Verifikasi Akun)
    public function login(Request $request)
    {
        // Validasi input wajib diisi
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Tangkap data kredensial input
        $credentials = $request->only('username', 'password');

        // Proses Sihir Laravel: Cek ke database (Otomatis mencocokkan password terenkripsi)
        if (Auth::attempt($credentials)) {
            // Jika cocok, buat ulang sesi baru untuk user tersebut
            $request->session()->regenerate();

            // Tangkap data user yang sedang login saat ini
            $user = Auth::user();

            // ============================================================
            // LOGIKA PENCEGATAN LOGIN PERTAMA KHUSUS NASABAH
            // ============================================================
            if ($user->role == 'nasabah') {
                // Periksa apakah password saat ini di database masih 'password123'
                if (Hash::check('password123', $user->password)) {
                    // Jika masih default, lempar paksa ke halaman ganti password baru
                    return redirect()->route('password.change')->with('info', 'Ini adalah login pertama Anda. Silakan ganti kata sandi bawaan demi keamanan akun.');
                }
            }
            // ============================================================

            // Jika dia Superadmin, Admin TU, atau Nasabah yang sudah pernah ganti password, lanjut ke dashboard
            return redirect()->intended('/dashboard');
        }

        // Jika salah, balikkan ke halaman login dengan pesan error text
        return back()->withErrors([
            'loginError' => 'Username atau Password yang Anda masukkan salah!',
        ])->withInput();
    }

    // 3. Memproses Aksi Keluar Sistem (Logout)
    public function logout(Request $request)
    {
        Auth::logout();

        // Hancurkan sesi token agar aman dari peretasan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Tendang kembali ke halaman login awal
        return redirect('/');
    }
}
