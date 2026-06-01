<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- update tutup by dean 28052026 -->
    <!-- <link rel="stylesheet" href="assets/css/style-dashboard.css"> -->

    <!-- update by dean 28052026 -->
    <link rel="stylesheet" href="{{ asset('assets/css/style-dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>MiniBank - Detail Akuntansi Murid</title>
</head>

<body>

    <div class="dt-container">

        <div class="dt-back-nav">
            <a href="{{ route('murid.index') }}" class="dt-btn-back">
                <span class="dt-icon-box">
                    <i class="fa-solid fa-arrow-left-long"></i>
                </span>
                <div class="dt-nav-text">
                    <span class="dt-nav-subtitle">Aplikasi MiniBank</span>
                    <span class="dt-nav-title">Kembali ke Data Master</span>
                </div>
            </a>
        </div>

        <div class="dt-layout-grid">

            <div class="dt-card dt-profile-card">
                <div class="dt-profile-header-glow"></div>

                <div class="dt-avatar-area">
                    <div class="dt-avatar-wrapper">
                        <img src="https://ui-avatars.com/api/?name=Muhammad+Rizaldy&background=B7B89F&color=fff&size=128" alt="Avatar" class="dt-avatar-img">
                        <div class="dt-status-dot"></div>
                    </div>
                    <h4>Muhammad Rizaldy</h4>
                    <p class="dt-sub-role">Nasabah MiniBank</p>
                    <span class="dt-badge-kelas">Kelas 6A</span>
                </div>

                <div class="dt-info-separator"></div>

                <div class="dt-info-list">
                    <div class="dt-info-item">
                        <div class="dt-info-icon"><i class="fa-solid fa-id-card"></i></div>
                        <div class="dt-info-text">
                            <span class="dt-label">Nomor Induk Siswa</span>
                            <span class="dt-value">2024001</span>
                        </div>
                    </div>
                    <div class="dt-info-item">
                        <div class="dt-info-icon"><i class="fa-solid fa-venus-mars"></i></div>
                        <div class="dt-info-text">
                            <span class="dt-label">Jenis Kelamin</span>
                            <span class="dt-value">Laki-laki</span>
                        </div>
                    </div>
                    <div class="dt-info-item">
                        <div class="dt-info-icon"><i class="fa-solid fa-calendar-check"></i></div>
                        <div class="dt-info-text">
                            <span class="dt-label">Bergabung Pada</span>
                            <span class="dt-value">12 Mei 2026</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dt-main-content">

                <div class="dt-card dt-balance-card-modern">
                    <div class="dt-balance-left">
                        <span class="dt-balance-meta">Total Saldo Tersedia</span>
                        <h2>Rp 750.000</h2>
                        <p class="dt-balance-statement"><i class="fa-solid fa-shield-check"></i> Terverifikasi oleh Sistem Akuntansi</p>
                    </div>
                    <div class="dt-balance-right">
                        <div class="dt-progress-circle-box">
                            <span class="dt-progress-title">Target Edukasi</span>
                            <div class="dt-progress-bar-bg">
                                <div class="dt-progress-bar-fill" style="width: 75%;"></div>
                            </div>
                            <span class="dt-progress-percent">75% Tercapai</span>
                        </div>
                    </div>
                </div>

                <div class="dt-card dt-history-card">

                    <div class="dt-section-header">
                        <h5>Riwayat Transaksi Terakhir</h5>
                        <a href="cetak_pdf.php?nis=2024001" target="_blank" class="dt-btn-download" title="Unduh Catatan Rekening Koran">
                            <i class="fa-solid fa-file-pdf"></i> Cetak PDF
                        </a>
                    </div>

                    <div class="dt-list-wrapper">

                        <div class="dt-list-item">
                            <div class="dt-icon-frame frame-masuk">
                                <i class="fa-solid fa-arrow-down-long"></i>
                            </div>
                            <div class="dt-item-details">
                                <p class="dt-item-title">Setoran Tunai</p>
                                <span class="dt-item-date">25 Mei 2026 • 09:30 WIB</span>
                            </div>
                            <div class="dt-item-action-price">
                                <span class="price-plus">+ Rp 50.000</span>
                                <span class="status-success">Berhasil</span>
                            </div>
                        </div>

                        <div class="dt-list-item">
                            <div class="dt-icon-frame frame-keluar">
                                <i class="fa-solid fa-arrow-up-long"></i>
                            </div>
                            <div class="dt-item-details">
                                <p class="dt-item-title">Penarikan Saldo</p>
                                <span class="dt-item-date">20 Mei 2026 • 14:15 WIB</span>
                            </div>
                            <div class="dt-item-action-price">
                                <span class="price-minus">- Rp 20.000</span>
                                <span class="status-success">Berhasil</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>