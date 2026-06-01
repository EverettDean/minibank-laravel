<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniBank - Login</title>
    <!-- update tutup by dean 28052026 -->
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->

    <!-- update by dean 28052026 -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- Menggunakan font inter sesuaikan pada figma -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<!-- 1. Tambahkan class "login-page" pada tag body agar CSS pemusat aktif -->

<body class="login-page">

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <!-- Logo Circle -->
                <div class="logo-circle"></div>
                <h1>MiniBank</h1>
                <p>Sistem Rekapitulasi Tabungan</p>
            </div>

            <!-- Bagian Form Login -->
            <form action="/login" method="POST">
                @csrf

                @error('loginError')
                <div style="background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; font-weight: 600; text-align: center;">
                    <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> {{ $message }}
                </div>
                @enderror

                <div class="input-group">
                    <label for="username">Nama Pengguna</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" value="{{ old('username') }}" required autocomplete="username">
                </div>

                <div class="input-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                </div>

                <button type="submit" class="btn-login">MASUK</button>

                <div class="forgot-password">
                    <a href="#">Lupa Password?</a>
                </div>
            </form>
        </div>
    </div>

</body>

</html>