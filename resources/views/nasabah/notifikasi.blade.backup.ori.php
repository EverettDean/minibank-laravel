<section class="notif-container" style="max-width: 800px; margin: 20px auto;">
    <div class="header-section" style="margin-bottom: 20px;">
        <h2>Notifikasi & Report</h2>
        <p style="color: #666;">Daftar update terbaru transaksi Anda.</p>
    </div>

    @forelse($notifikasis as $n)
    <div class="notif-card" style="background: #fff; padding: 15px 20px; border-radius: 10px; margin-bottom: 12px; border-left: 5px solid {{ $n->judul == 'Transaksi Disetujui 🎉' ? '#48bb78' : '#f56565' }}; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <strong style="color: #2d3748; font-size: 1.1em;">{{ $n->judul }}</strong>
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