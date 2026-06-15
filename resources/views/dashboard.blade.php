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
    <div class="main-wrapper">
        @include('components.sidebar')
        <div class="main-content-area">
            @include('components.navbar')
            <main class="content">
                <header class="content-header">
                    <div class="header-text">
                        <h1>Dashboard</h1>
                        <p>Selamat Datang, <span class="highlight-user">{{ Auth::user()->name }}</span>
                            <small style="font-size: 13px; color: #718096; font-weight: 600; background: #edf2f7; padding: 2px 8px; border-radius: 4px; margin-left: 5px;">
                                {{ strtoupper(Auth::user()->role) }}
                            </small>
                        </p>
                    </div>
                </header>

                <section class="stats-grid">
                    @if(Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin')
                    <div class="stat-card" style="border-left: 4px solid #3182ce;">
                        <div class="stat-content">
                            <p class="stat-label">Total Siswa</p>
                            <div class="value-container">
                                <h3 class="stat-value">{{ number_format($metrics['total_nasabah'], 0, ',', '.') }}</h3>
                                <div class="stat-icon-box bg-blue"><i class="fa-solid fa-user-graduate"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card" style="border-left: 4px solid #38a169;">
                        <div class="stat-content">
                            <p class="stat-label">Staff TU</p>
                            <div class="value-container">
                                <h3 class="stat-value">{{ $metrics['total_staff'] }}</h3>
                                <div class="stat-icon-box bg-green"><i class="fa-solid fa-user-tie"></i></div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(Auth::user()->role == 'nasabah')
                    <div class="stat-card" style="border-left: 4px solid #d69e2e;">
                        <div class="stat-content">
                            <p class="stat-label">Total Tabungan Saya</p>
                            <div class="value-container">
                                <h3 class="stat-value">Rp {{ number_format($metrics['saldo_pribadi'], 0, ',', '.') }}</h3>
                                <div class="stat-icon-box bg-gold"><i class="fa-solid fa-wallet"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card" style="border-left: 4px solid #667eea; background: #f8fafc;">
                        <div class="stat-content">
                            <p class="stat-label">Ajukan Transaksi</p>
                            <div style="margin-top: 15px; display: flex; gap: 10px;">
                                <a href="{{ route('nasabah.pengajuan.create') }}" style="flex:1; padding:10px; background:#3182ce; color:white; border-radius:6px; text-decoration:none; font-size:12px; font-weight:600; text-align:center;"><i class="fa-solid fa-plus-circle"></i> Setor</a>
                                <a href="{{ route('nasabah.pengajuan.create') }}" style="flex:1; padding:10px; background:#e53e3e; color:white; border-radius:6px; text-decoration:none; font-size:12px; font-weight:600; text-align:center;"><i class="fa-solid fa-minus-circle"></i> Tarik</a>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin')
                    <a href="{{ route('admin.transaksi.index') }}" style="text-decoration:none;">
                        <div class="stat-card" style="border-left: 4px solid #dd6b20;">
                            <div class="stat-content">
                                <p class="stat-label">Antrean Pending</p>
                                <div class="value-container">
                                    <h3 class="stat-value">{{ $metrics['antrean_pending'] }}</h3>
                                    <div class="stat-icon-box" style="background:#fefcbf; color:#dd6b20;"><i class="fa-solid fa-hourglass-half fa-spin-pulse"></i></div>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endif
                </section>

                <section class="activity-container" style="margin-top: 25px;">
                    <div class="activity-card">
                        <div class="activity-header">
                            <!-- <i class="fa-solid fa-clock-rotate-left" style="color: #a0aec0; font-size: 1.1em;"></i> -->
                            <h3>Aktivitas Terkini (Sistem Log)</h3>
                        </div>
                        <ul class="activity-list" style="list-style: none; padding: 0; margin: 0; max-height: 350px; overflow-y: auto;">
                            @forelse($logs as $log)
                            <li class="activity-item" style="display: flex; align-items: center; gap: 15px; padding: 15px 0; border-bottom: 1px solid #edf2f7;">
                                <div class="activity-icon {{ $log->bg_color }}" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff;">
                                    <i class="fa-solid {{ $log->icon }}"></i>
                                </div>
                                <div class="activity-detail">
                                    <p style="margin: 0; color: #2d3748; font-size: 14px;">
                                        <strong>{{ $log->nama_pelaku }}</strong>: {{ $log->deskripsi }}
                                    </p>
                                    <small style="color: #a0aec0;">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</small>
                                </div>
                            </li>
                            @empty
                            <li style="padding: 20px; text-align: center;">Tidak ada aktivitas.</li>
                            @endforelse
                        </ul>
                    </div>
                </section>
            </main>
        </div>
    </div>
    <script src="{{ asset('assets/js/javascript.js') }}"></script>
</body>

</html>