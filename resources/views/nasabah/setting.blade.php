<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniBank - Pengaturan</title>
    <!-- update by dean 28052026 -->
    <link rel="stylesheet" href="{{ asset('assets/css/style-dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <div class="main-wrapper">
        <!-- Memanggil Sidebar Global -->
        @include('components.sidebar')

        <div class="main-content-area">
            <!-- Memanggil Navbar Global -->
            @include('components.navbar')

            <main class="content">
                <header class="content-header">
                    <div class="header-text">
                        <h1>Pengaturan Akun</h1>
                        <p>Kelola keamanan dan <span class="highlight-user">Kata Sandi</span> profil Anda</p>
                    </div>
                </header>

                <section class="records-container" style="margin-top: 25px; max-width: 600px;">
                    <div class="table-card" style="padding: 30px;">
                        <div class="table-header" style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #edf2f7;">
                            <h3>Keamanan Akun</h3>
                            <i class="fa-solid fa-lock" style="color: #a0aec0;"></i>
                        </div>

                        <p style="color: #4a5568; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                            Demi menjaga keamanan saldo tabungan Anda di MiniBank, disarankan untuk memperbarui kata sandi secara berkala menggunakan kombinasi karakter yang kuat.
                        </p>

                        <!-- Tombol Aksi yang menembak rute ganti password buatan kita -->
                        <a href="{{ route('password.change') }}" class="view-all-btn" style="background: #2d3748; color: #fff; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-key"></i> Ubah Kata Sandi Akun
                        </a>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- update by dean 28052026 -->
    <script src="{{ asset('assets/js/javascript.js') }}"></script>
</body>

</html>