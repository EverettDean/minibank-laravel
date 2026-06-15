<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniBank - Riwayat Transaksi</title>
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
                <header class="content-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div class="header-text">
                        <h1>Riwayat Transaksi</h1>
                        <p>Daftar mutasi <span class="highlight-user">Setor & Tarik Tunai</span> tabungan Anda</p>
                    </div>

                    <a href="{{ route('nasabah.pengajuan.create') }}" class="view-all-btn" style="background: #2d3748; color: #fff; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-plus"></i> Buat Pengajuan Baru
                    </a>
                </header>

                <div class="metrics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-top: 25px;">
                    <div class="metric-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                        <div style="color: #718096; font-size: 14px; font-weight: 600;">Saldo Utama Saat Ini</div>

                        @php
                        $total_saldo = 0;
                        foreach($data_pengajuan as $item) {
                        if($item->status == 'disetujui') {
                        if($item->jenis_transaksi == 'setor') {
                        $total_saldo += $item->nominal;
                        } elseif($item->jenis_transaksi == 'tarik') {
                        $total_saldo -= $item->nominal;
                        }
                        }
                        }
                        @endphp

                        <div style="font-size: 24px; font-weight: 700; color: #16a34a; margin-top: 5px;">
                            Rp {{ number_format($total_saldo, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <section class="records-container" style="margin-top: 25px;">
                    <div class="table-card">
                        <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #edf2f7; flex-wrap: wrap; gap: 15px;">
                            <div>
                                <h3 style="margin: 0;">Mutasi Rekening</h3>
                                <span style="font-size: 12px; color: #a0aec0;">Riwayat transaksi berdasarkan periode</span>
                            </div>

                            <form action="{{ route('nasabah.transaksi') }}" method="GET" style="display: flex; align-items: center; gap: 10px;">
                                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" style="padding: 6px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 13px;">
                                <span style="color: #a0aec0; font-size: 13px;">s/d</span>
                                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" style="padding: 6px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 13px;">

                                <button type="submit" style="background: #3182ce; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;">
                                    <i class="fa-solid fa-filter"></i>
                                </button>

                                @if(request()->has('tanggal_mulai') || request()->has('tanggal_akhir'))
                                <a href="{{ route('nasabah.transaksi') }}" style="color: #e53e3e; font-size: 14px; text-decoration: none;">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                                @endif
                            </form>
                        </div>

                        <div style="overflow-x: auto;">
                            <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr style="background-color: #f7fafc; border-bottom: 2px solid #edf2f7;">
                                        <th style="padding: 15px; color: #4a5568; font-weight: 600;">Tanggal</th>
                                        <th style="padding: 15px; color: #4a5568; font-weight: 600;">Kode Transaksi</th>
                                        <th style="padding: 15px; color: #4a5568; font-weight: 600;">Jenis</th>
                                        <th style="padding: 15px; color: #4a5568; font-weight: 600;">Jumlah</th>
                                        <th style="padding: 15px; color: #4a5568; font-weight: 600;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data_pengajuan as $index => $item)
                                    <tr style="border-bottom: 1px solid #edf2f7;">
                                        <td style="padding: 15px; color: #4a5568;">
                                            {{ date('d M Y H:i', strtotime($item->created_at)) }}
                                        </td>
                                        <td style="padding: 15px; color: #718096; font-family: monospace; font-weight: 600;">
                                            TRX-{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td style="padding: 15px; font-weight: 600; color: {{ $item->jenis_transaksi == 'setor' ? '#16a34a' : '#dc2626' }};">
                                            {{ strtoupper($item->jenis_transaksi) }}
                                        </td>
                                        <td style="padding: 15px; color: #2d3748; font-weight: 600;">
                                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                        </td>
                                        <td style="padding: 15px;">
                                            @if($item->status == 'pending')
                                            <span style="background-color: #fef3c7; color: #d97706; padding: 4px 10px; border-radius: 50px; font-size: 12px; font-weight: 600;">Pending</span>
                                            @elseif($item->status == 'disetujui')
                                            <span style="background-color: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 50px; font-size: 12px; font-weight: 600;">Disetujui</span>
                                            @else
                                            <span style="background-color: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 50px; font-size: 12px; font-weight: 600;">Ditolak</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td style="padding: 40px; color: #a0aec0;" colspan="5" align="center">
                                            <i class="fa-solid fa-receipt" style="font-size: 32px; display: block; margin-bottom: 12px; color: #cbd5e0;"></i>
                                            <span style="font-size: 14px; font-weight: 500;">Belum ada riwayat transaksi pada periode ini.</span>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/js/javascript.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 2. POP-UP BERHASIL
            @if(session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: "{!! session('success') !!}",
                icon: 'success',
                confirmButtonColor: '#3182ce',
                confirmButtonText: 'Oke, Mengerti'
            });
            @endif
        });
    </script>
</body>

</html>