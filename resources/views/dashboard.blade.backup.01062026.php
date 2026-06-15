<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MiniBank - Dashboard</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style-dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <!-- Wrapper utama untuk membagi layar -->
    <div class="main-wrapper">
        <!-- update tutup by dean 28052026 -->
        <!-- <php include('components/sidebar.php') ?> -->

        <!-- update by dean 28052026 -->
        @include('components.sidebar')
        <!-- Bagian 2: Area konten utama (Sisi Kanan) -->
        <div class="main-content-area">
            <!-- update by dean 28052026  -->
            <!-- <php include('components/navbar.php') ?> -->

            <!-- update by dean 28052026 -->
            @include('components.navbar')
            <!-- Sub-bagian B: Isi dashboard (Di bawah Top Nav) -->
            <main class="content">

                <!-- Header Konten: Judul dan Sapaan dan Widget nanti di sini -->
                <!-- update tutup by dean  28052026 -->
                <!-- <header class="content-header">
                    <div class="header-text">
                        <h1>Dashboard</h1>
                        <p>Selamat Datang,<span class="highlight-user">Admin TU</span></p>
                    </div>
                </header> -->

                <!-- update by dean 28052026 -->
                <header class="content-header">
                    <div class="header-text">
                        <h1>Dashboard</h1>
                        <p>
                            Selamat Datang,
                            <span class="highlight-user">{{ Auth::user()->name }}</span>
                            <small style="font-size: 13px; color: #718096; font-weight: 600; background: #edf2f7; padding: 2px 8px; border-radius: 4px; margin-left: 5px;">
                                {{ strtoupper(Auth::user()->role) }}
                            </small>
                        </p>
                    </div>
                </header>

                <!-- Bagian Widget Statistik -->
                <section class="stats-grid">

                    <!-- Card 1: Fokus Utama (Data Murid) -->
                    <!-- update tutp by dean 28052026 -->
                    <!-- <div class="stat-card">
                        <div class="stat-content">
                            <p class="stat-label">Total Siswa</p> -->

                    <!-- Bungkus h3 dan ikon agar bisa disejajarkan ke samping -->
                    <!-- <div class="value-container">
                                <h3 class="stat-value">1,240</h3>
                                <div class="stat-icon-box bg-blue">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>
                            </div>
                            <span class="stat-desc">Siswa Terdaftar</span>
                        </div>
                    </div> -->

                    <!-- update  by dean 28052026 -->
                    @if(Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin')
                    <div class="stat-card">
                        <div class="stat-content">
                            <p class="stat-label">Total Siswa</p>

                            <div class="value-container">
                                <h3 class="stat-value">
                                    {{ number_format($metrics['total_nasabah'], 0, ',', '.') }}
                                </h3>
                                <div class="stat-icon-box bg-blue">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>
                            </div>
                            <span class="stat-desc">Siswa Terdaftar Aktif</span>
                        </div>
                    </div>
                    @endif

                    <!-- Card 2: Staff TU -->
                    <!-- update tutup by dean 28052026 -->
                    <!-- <div class="stat-card">
                        <div class="stat-content">
                            <p class="stat-label">Staff TU</p> -->

                    <!-- Bungkus h3 dan ikon agar bisa disejajarkan ke samping -->
                    <!-- <div class="value-container">
                                <h3 class="stat-value">2</h3>
                                <div class="stat-icon-box bg-green">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                            </div>
                            <span class="stat-desc">Akun Aktif</span>
                        </div>
                    </div> -->

                    <!-- update by dean 28052026 -->
                    @if(Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin')
                    <div class="stat-card">
                        <div class="stat-content">
                            <p class="stat-label">Staff TU</p>

                            <div class="value-container">
                                <h3 class="stat-value">
                                    {{ $metrics['total_staff'] }}
                                </h3>
                                <div class="stat-icon-box bg-green">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                            </div>
                            <span class="stat-desc">Akun Petugas Aktif</span>
                        </div>
                    </div>
                    @endif

                    <!-- Card 3: Total Tabungan -->
                    <!-- update tutup by dean 28052026 -->
                    <!-- <div class="stat-card">
                        <div class="stat-content">
                            <p class="stat-label">Total Tabungan</p> -->

                    <!-- Bungkus h3 dan ikon agar bisa disejajarkan ke samping -->
                    <!-- <div class="value-container">
                                <h3 class="stat-value">Rp 45.2M</h3>
                                <div class="stat-icon-box bg-gold">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                            </div>
                            <span class="stat-desc">Saldo Keseluruhan</span>
                        </div>
                    </div> -->

                    <!-- update by dean 28052026 -->
                    @if(Auth::user()->role == 'nasabah')
                    <!-- Kartu ini diisolasi ketat, HANYA akan dirender jika yang login adalah akun Nasabah -->
                    <div class="stat-card">
                        <div class="stat-content">
                            <p class="stat-label">Total Tabungan Saya</p>

                            <div class="value-container">
                                <h3 class="stat-value">
                                    Rp {{ number_format($metrics['saldo_pribadi'], 0, ',', '.') }}
                                </h3>

                                <div class="stat-icon-box bg-gold">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                            </div>

                            <span class="stat-desc">Saldo Aktif yang Dapat Ditarik</span>
                        </div>
                    </div>
                    @endif

                    <!-- Card 4: Transaksi Hari ini -->
                    <!-- update tutup by dean 29052026 -->
                    <!-- <div class="stat-card">
                        <div class="stat-content">
                            <p class="stat-label">Transaksi (MEI)</p> -->

                    <!-- Bungkus h3 dan ikon agar bisa disejajarkan ke samping -->
                    <!-- <div class="value-container">
                                <h3 class="stat-value">128</h3>
                                <div class="stat-icon-box bg-red">
                                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                </div>
                            </div>
                            <span class="stat-desc">↑ 12jt Masuk</span>
                        </div>
                    </div> -->

                    <!-- update by dean 29052026 -->
                    @if(Auth::user()->role == 'admin' || Auth::user()->role == 'superadmin')
                    <div class="stat-card">
                        <div class="stat-content">

                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <p class="stat-label" style="margin: 0;">Total Saldo Keseluruhan</p>

                                <form action="{{ route('dashboard.index') }}" method="GET" id="formFilterBulan">
                                    <select name="bulan" onchange="document.getElementById('formFilterBulan').submit()"
                                        style="padding: 2px 8px; border-radius: 4px; border: 1px solid #ddd; font-size: 11px; background: #f8fafc; color: #4a5568; cursor: pointer; outline: none;">
                                        <option value="06-2026" {{ $metrics['bulan_pilihan'] == '06-2026' ? 'selected' : '' }}>Jun 2026</option>
                                        <option value="05-2026" {{ $metrics['bulan_pilihan'] == '05-2026' ? 'selected' : '' }}>Mei 2026</option>
                                        <option value="04-2026" {{ $metrics['bulan_pilihan'] == '04-2026' ? 'selected' : '' }}>Apr 2026</option>
                                        <option value="03-2026" {{ $metrics['bulan_pilihan'] == '03-2026' ? 'selected' : '' }}>Mar 2026</option>
                                    </select>
                                </form>
                            </div>

                            <div class="value-container" style="margin-top: 10px;">
                                <h3 class="stat-value">Rp {{ number_format($metrics['total_dana_sekolah'], 0, ',', '.') }}</h3>
                                <div class="stat-icon-box bg-blue" style="background: #ebf8ff; color: #3182ce;">
                                    <i class="fa-solid fa-vault"></i>
                                </div>
                            </div>

                            <span class="stat-desc" style="color: #16a34a; font-weight: 500;">
                                <i class="fa-solid fa-arrow-trend-up"></i> + Rp {{ number_format($metrics['pemasukan_bulan_ini'], 0, ',', '.') }} (Pemasukan bulan ini)
                            </span>

                        </div>
                    </div>
                    @endif

                    <!-- Widget Pantauan Antrean Pending (Khusus Superadmin/Admin) -->
                    @if(Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin')
                    <div class="dashboard-card" style="background: #ffffff; padding: 20px; border-radius: 10px; border-left: 5px solid #d69e2e; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 18px; transition: 0.3s; margin-bottom: 20px;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">

                        <!-- Ikon Animasi -->
                        <div style="background: #fefcbf; color: #d69e2e; width: 55px; height: 55px; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 24px;">
                            <i class="fa-solid fa-hourglass-half fa-spin-pulse" style="--fa-animation-duration: 3s;"></i>
                        </div>

                        <!-- Informasi Angka -->
                        <div>
                            <h3 style="margin: 0 0 5px 0; color: #718096; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Antrean Pending</h3>

                            <div style="display: flex; align-items: baseline; gap: 8px;">
                                <!-- Memanggil variabel dari Controller kamu -->
                                <p style="margin: 0; font-size: 28px; font-weight: 800; color: #2d3748; line-height: 1;">
                                    {{ $metrics['antrean_pending'] }}
                                </p>
                                <span style="font-size: 14px; font-weight: 500; color: #a0aec0;">Menunggu Verifikasi</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </section>

                <section class="activity-container" style="margin-top: 25px;">
                    <div class="activity-card">

                        <div class="activity-header">
                            <h3>Aktivitas Terkini (Sistem Log)</h3>
                            <i class="fa-solid fa-clock-rotate-left" style="color: #a0aec0;"></i>
                        </div>

                        <!-- update tutup by dean 29052026 -->
                        <!-- <ul class="activity-list" style="list-style: none; padding: 0; margin: 0;"> -->

                        <!-- update by dean 29052026 -->
                        <ul class="activity-list" style="list-style: none; padding: 0; margin: 0; max-height: 350px; overflow-y: auto; padding-right: 10px;">
                            @forelse($logs as $log)
                            <li class="activity-item" style="display: flex; align-items: center; gap: 15px; padding: 15px 0; border-bottom: 1px solid #edf2f7;">
                                <div class="activity-icon {{ $log->bg_color }}" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff;">
                                    <i class="fa-solid {{ $log->icon }}"></i>
                                </div>

                                <div class="activity-detail">
                                    <p style="margin: 0; color: #2d3748; font-size: 14px;">
                                        <strong style="color: #4a5568;">{{ $log->nama_pelaku }}</strong>: {{ $log->deskripsi }}
                                    </p>
                                    <small style="color: #a0aec0; font-size: 12px; display: block; margin-top: 4px;">
                                        {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                                    </small>
                                </div>
                            </li>
                            @empty
                            <li style="padding: 30px; text-align: center; color: #a0aec0; font-style: italic; font-size: 14px;">
                                Belum ada rekaman jejak aktivitas yang tercatat untuk akun Anda.
                            </li>
                            @endforelse
                        </ul>

                    </div>
                </section>

                <!-- Bagian Table Riwayat -->
                <section class="records-container">
                    <!-- Hanya gunakan SATU table-card sebagai pembungkus putih -->
                    <div class="table-card">
                        <div class="table-header">
                            <h3>Riwayat Tabungan Terkini</h3>
                            <a href="#" class="view-all-btn">Lihat Semua</a>
                        </div>

                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>TANGGAL</th>
                                    <th>NIS</th>
                                    <th>NAMA MURID</th>
                                    <th>KELAS</th>
                                    <th>STATUS</th>
                                    <th class="text-right">NOMINAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Isi data murid Anda sudah benar -->
                                <tr>
                                    <td>02 Mei 2026</td>
                                    <td>2024001</td>
                                    <td>
                                        <div class="student-info">
                                            <div class="student-img">B</div>
                                            <span>Budi Santoso</span>
                                        </div>
                                    </td>
                                    <td>6A</td>
                                    <td><span class="badge success">Masuk</span></td>
                                    <td class="text-right amount-in">+ Rp 50.000</td>
                                </tr>
                                <tr>
                                    <td>02 Mei 2026</td>
                                    <td>2024002</td>
                                    <td>
                                        <div class="student-info">
                                            <div class="student-img">S</div>
                                            <span>Santos</span>
                                        </div>
                                    </td>
                                    <td>7B</td>
                                    <td><span class="badge success">Masuk</span></td>
                                    <td class="text-right amount-in">+ Rp 100.000</td>
                                </tr>
                                <!-- ... data lainnya ... -->
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- update tutup by dean 28052026 -->
    <!-- <script src="assets/js/javascript.js"></script> -->
    <!-- update by dean 28052026 -->
    <script src="{{ asset('assets/js/javascript.js')}}"></script>
</body>

</html>