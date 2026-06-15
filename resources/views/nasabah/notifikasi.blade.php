<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Notifikasi - MiniBank</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style-dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <div class="main-wrapper">
        @include('components.sidebar')

        <div class="main-content-area">
            @include('components.navbar')

            <main class="content">
                <section class="notif-container" style="max-width: 800px; margin: 20px auto;">
                    <div class="header-section" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px;">
                        <div>
                            <h2 style="margin: 0 0 5px 0;">Notifikasi & Report</h2>
                            <p style="color: #666; margin: 0;">Daftar update terbaru transaksi Anda.</p>
                        </div>

                        <button onclick="tandaiSemuaDibaca(this)"
                            data-url="{{ route('nasabah.notifikasi.read') }}"
                            data-token="{{ csrf_token() }}"
                            id="btn-read-all"
                            style="background-color: #3182ce; color: white; padding: 10px 15px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="fa-solid fa-check-double"></i> Tandai Semua Sudah Dibaca
                        </button>
                    </div>

                    @forelse($notifikasis as $n)
                    <div class="notif-card" style="background-color: {{ $n->is_read ? '#ffffff' : '#ebf8ff' }}; padding: 15px 20px; border-radius: 10px; margin-bottom: 12px; border-left: 5px solid {{ $n->judul == 'Transaksi Disetujui 🎉' ? '#48bb78' : '#f56565' }}; box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: background-color 0.3s;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <strong style="color: #2d3748; font-size: 1.1em;">
                                {{ $n->judul }}
                                @if(!$n->is_read)
                                <span style="background-color: #e53e3e; color: white; font-size: 10px; padding: 2px 8px; border-radius: 12px; margin-left: 8px; vertical-align: middle;">Baru</span>
                                @endif
                            </strong>
                            <small style="color: #a0aec0;">{{ \Carbon\Carbon::parse($n->created_at)->format('d M Y, H:i') }}</small>
                        </div>
                        <p style="color: #4a5568; margin: 0; line-height: 1.5;">{{ $n->pesan }}</p>
                    </div>
                    @empty
                    <div style="text-align: center; padding: 40px; color: #a0aec0;">
                        <i class="fa-solid fa-bell-slash" style="font-size: 3em; margin-bottom: 15px;"></i>
                        <p>Tidak ada notifikasi baru saat ini.</p>
                    </div>
                    @endforelse
                </section>
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/js/javascript.js') }}"></script>
</body>

</html>