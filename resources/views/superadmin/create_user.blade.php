<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniBank - Tambah Pengguna</title>

    <!-- update by dean 28052026 -->
    <link rel="stylesheet" href="{{ asset('assets/css/style-dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <!-- Wrapper utama yang sama persis dengan dashboard agar pembagian layar ke sidebar aman -->
    <div class="main-wrapper">

        <!-- Memanggil Sidebar Global -->
        @include('components.sidebar')

        <!-- Area konten utama (Sisi Kanan) -->
        <div class="main-content-area">

            <!-- Memanggil Navbar Global -->
            @include('components.navbar')

            <!-- Sub-bagian B: Isi Konten Form (Di bawah Top Nav) -->
            <main class="content">

                <!-- Header Konten: Mengikuti gaya penulisan dashboard -->
                <header class="content-header">
                    <div class="header-text">
                        <h1>Tambah Pengguna Baru</h1>
                        <p>Pendaftaran Akun Sistem <span class="highlight-user">Admin TU & Nasabah</span></p>
                    </div>
                </header>

                <!-- Wadah Utama Form (Memakai class records-container & table-card agar box-shadow-nya kembar dengan dashboard) -->
                <section class="records-container" style="margin-top: 25px; max-width: 650px;">
                    <div class="table-card" style="padding: 30px;">

                        <div class="table-header" style="margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #edf2f7;">
                            <h3>Form Formulir Pengguna</h3>
                            <i class="fa-solid fa-user-plus" style="color: #a0aec0;"></i>
                        </div>

                        <!-- FORM PROSES AKSI LARAVEL -->
                        <form action="{{ route('user.store') }}" method="POST">
                            @csrf

                            <!-- Pilihan Hak Akses / Role (Ditaruh di atas agar memicu interaksi JavaScript di bawah) -->
                            <div class="input-group" style="margin-bottom: 20px;">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; font-size: 14px;">Hak Akses (Role) <span style="color: #e53e3e;">*</span></label>
                                <select name="role" id="role-select" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; background-color: #fff; color: #2d3748;">
                                    <option value="" disabled selected>-- Pilih Hak Akses Pengguna --</option>
                                    <option value="admin">Admin TU</option>
                                    <option value="nasabah">Nasabah</option>
                                </select>
                            </div>

                            <!-- Input Nama Lengkap -->
                            <div class="input-group" style="margin-bottom: 20px;">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; font-size: 14px;">Nama Lengkap <span style="color: #e53e3e;">*</span></label>
                                <input type="text" name="name" placeholder="Masukkan nama lengkap..." required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; box-sizing: border-box;">
                            </div>

                            <!-- Input Username / NISN -->
                            <div class="input-group" style="margin-bottom: 20px;">
                                <label id="username-label" style="display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; font-size: 14px;">Nama Pengguna (Username) <span style="color: #e53e3e;">*</span></label>
                                <input type="text" name="username" id="username-input" placeholder="Masukkan username login..." required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; box-sizing: border-box;">
                            </div>

                            <!-- Input Email -->
                            <div class="input-group" style="margin-bottom: 20px;">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; font-size: 14px;">Alamat Email <span style="color: #e53e3e;">*</span></label>
                                <input type="email" name="email" placeholder="contoh@minibank.com" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; box-sizing: border-box;">
                            </div>

                            <!-- Input Password -->
                            <!-- update tutup by dean 28052026 -->
                            <!-- <div class="input-group" style="margin-bottom: 25px;">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; font-size: 14px;">Kata Sandi (Password) <span style="color: #e53e3e;">*</span></label>
                                <input type="password" name="password" placeholder="Minimal 4 karakter..." required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; box-sizing: border-box;">
                            </div> -->

                            <!-- update by dean 28052026 -->
                            <div class="input-group" style="margin-bottom: 25px; background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 12px; border-radius: 6px;">
                                <p style="margin: 0; color: #166534; font-size: 13px; font-weight: 600;">
                                    <i class="fa-solid fa-key" style="margin-right: 6px;"></i> Kata Sandi Otomatis:
                                    <span style="background: #dcfce7; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 14px;">password123</span>
                                </p>
                                <small style="color: #15803d; display: block; margin-top: 4px;">Nasabah akan diminta mengganti password ini saat pertama kali login ke sistem.</small>
                            </div>


                            <!-- BOX DOKUMEN KHUSUS NASABAH (Otomatis Sembunyi/Muncul Dinamis) -->
                            <div id="nasabah-fields-box" style="display: none; background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                                <h4 style="margin-top: 0; margin-bottom: 15px; color: #334155; font-size: 14px; display: flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-id-card" style="color: #64748b;"></i> Detail Profil Data Nasabah
                                </h4>

                                <!-- Input Kelas -->
                                <div class="input-group" style="margin-bottom: 15px;">
                                    <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #4a5568; font-size: 13px;">Kelas <span style="color: #e53e3e;">*</span></label>
                                    <input type="text" name="kelas" id="kelas-input" placeholder="Contoh: X, XI, XII atau 6A, 7B" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; background: #fff; box-sizing: border-box;">
                                </div>

                                <!-- Input Jurusan -->
                                <div class="input-group" style="margin-bottom: 15px;">
                                    <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #4a5568; font-size: 13px;">Jurusan <span style="color: #e53e3e;">*</span></label>
                                    <input type="text" name="jurusan" id="jurusan-input" placeholder="Contoh: Akuntansi, Rekayasa Perangkat Lunak" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; background: #fff; box-sizing: border-box;">
                                </div>

                                <!-- Input No Telp -->
                                <div class="input-group" style="margin-bottom: 5px;">
                                    <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #4a5568; font-size: 13px;">Nomor Telepon <span style="color: #e53e3e;">*</span></label>
                                    <input type="text" name="no_telp" id="notelp-input" placeholder="Contoh: 0812XXXXXXXX" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; background: #fff; box-sizing: border-box;">
                                </div>
                            </div>


                            <!-- TOMBOL AKSI FORM (Menggunakan class view-all-btn dashboard agar menyatu temanya) -->
                            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid #edf2f7; padding-top: 20px;">
                                <a href="{{ route('user.index') }}" class="view-all-btn" style="background: #e2e8f0; color: #4a5568; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px;">Batal</a>
                                <button type="submit" class="view-all-btn" style="background: #2d3748; color: #fff; padding: 12px 24px; border: none; border-radius: 6px; font-weight: 600; font-size: 14px; cursor: pointer;">Simpan User</button>
                            </div>

                        </form>

                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Logic JavaScript Adaptif -->
    <script>
        const roleSelect = document.getElementById('role-select');
        const nasabahBox = document.getElementById('nasabah-fields-box');
        const usernameLabel = document.getElementById('username-label');
        const usernameInput = document.getElementById('username-input');

        const kelasInput = document.getElementById('kelas-input');
        const jurusanInput = document.getElementById('jurusan-input');
        const notelpInput = document.getElementById('notelp-input');

        roleSelect.addEventListener('change', function() {
            if (this.value === 'nasabah') {
                // Munculkan input tambahan nasabah
                nasabahBox.style.display = 'block';
                // Ubah label nama pengguna agar lebih spesifik
                usernameLabel.innerHTML = 'Nomor Induk Siswa Nasional (NISN) <span style="color: #e53e3e;">*</span>';
                usernameInput.placeholder = 'Masukkan NISN nasabah...';

                // Set agar kolom tambahan wajib diisi
                kelasInput.required = true;
                jurusanInput.required = true;
                notelpInput.required = true;
            } else {
                // Sembunyikan input tambahan nasabah jika memilih Admin TU
                nasabahBox.style.display = 'none';
                usernameLabel.innerHTML = 'Nama Pengguna (Username) <span style="color: #e53e3e;">*</span>';
                usernameInput.placeholder = 'Masukkan username login...';

                // Matikan required agar admin bisa disimpan tanpa mengisi kelas
                kelasInput.required = false;
                jurusanInput.required = false;
                notelpInput.required = false;
            }
        });
    </script>

    <!-- update by dean 28052026 -->
    <script src="{{ asset('assets/js/javascript.js') }}"></script>
</body>

</html>