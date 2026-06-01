<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Nasabah - {{ $murid->name }}</title>
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
                <div style="margin-bottom: 25px;">
                    <a href="{{ route('murid.index') }}"
                        style="display: inline-flex; align-items: center; gap: 8px; background-color: #ffffff; border: 1px solid #e2e8f0; color: #4a5568; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s ease;"
                        onmouseover="this.style.backgroundColor='#f7fafc'; this.style.borderColor='#cbd5e0';"
                        onmouseout="this.style.backgroundColor='#ffffff'; this.style.borderColor='#e2e8f0';">
                        <i class="fa-solid fa-arrow-left" style="color: #a0aec0;"></i> Kembali ke Data Master
                    </a>
                </div>

                <header class="content-header" style="margin-bottom: 25px;">
                    <div class="header-text">
                        <h1>Detail Nasabah</h1>
                        <div style="margin-bottom: 20px;">
                            <a href="{{ route('murid.pdf', $murid->id) }}" target="_blank"
                                style="display: inline-flex; align-items: center; gap: 8px; background-color: #dc2626; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px; box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2); transition: all 0.2s ease;">
                                <i class="fa-solid fa-file-pdf"></i> Cetak Laporan PDF
                            </a>
                        </div>
                        <p>Informasi lengkap dan riwayat transaksi buku tabungan.</p>
                    </div>
                </header>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px;">

                    <div class="table-card" style="padding: 25px;">
                        <h3 style="margin-top: 0; margin-bottom: 20px; color: #2d3748; border-bottom: 1px solid #edf2f7; padding-bottom: 10px;">
                            <i class="fa-solid fa-address-card" style="color: #3182ce; margin-right: 8px;"></i> Profil Identitas
                        </h3>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <small style="color: #a0aec0; font-weight: 600; display: block;">Nama Lengkap</small>
                                <strong style="color: #2d3748; font-size: 16px;">{{ $murid->name }}</strong>
                            </div>
                            <div>
                                <small style="color: #a0aec0; font-weight: 600; display: block;">NISN</small>
                                <strong style="color: #2d3748; font-size: 16px;">{{ $murid->nisn }}</strong>
                            </div>
                            <!-- <div>
                                <small style="color: #a0aec0; font-weight: 600; display: block;">Nomor Rekening Bank</small>
                                <strong style="color: #3182ce; font-size: 15px; font-family: monospace; background: #ebf8ff; padding: 4px 8px; border-radius: 4px; display: inline-block; margin-top: 4px; border: 1px solid #bee3f8;">
                                    <i class="fa-solid fa-credit-card"></i> {{ $murid->nomor_rekening ?? 'Belum Ada' }}
                                </strong>
                            </div> -->
                            <div>
                                <small style="color: #a0aec0; font-weight: 600; display: block;">Kelas / Jurusan</small>
                                <strong style="color: #2d3748; font-size: 16px;">{{ $murid->kelas }} - {{ $murid->jurusan }}</strong>
                            </div>
                            <div>
                                <small style="color: #a0aec0; font-weight: 600; display: block;">Email Login</small>
                                <strong style="color: #2d3748; font-size: 16px;">{{ $murid->email }}</strong>
                            </div>
                            <div>
                                <small style="color: #a0aec0; font-weight: 600; display: block;">Nomor Telepon</small>
                                <strong style="color: #2d3748; font-size: 16px;">{{ $murid->no_telp ?? 'Tidak ada data' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="table-card" style="padding: 25px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%); color: white;">
                        <i class="fa-solid fa-wallet" style="font-size: 40px; color: #fbbf24; margin-bottom: 15px;"></i>
                        <h4 style="margin: 0; color: #e2e8f0; font-weight: 500;">Saldo Saat Ini</h4>
                        <h1 style="margin: 10px 0 0 0; font-size: 28px; color: #fff;">Rp {{ number_format($total_saldo, 0, ',', '.') }}</h1>
                    </div>

                </div>

                <div class="table-card">
                    <div class="table-header" style="padding: 20px 25px; border-bottom: 1px solid #edf2f7;">
                        <h3 style="margin: 0;"><i class="fa-solid fa-clock-rotate-left" style="color: #4a5568; margin-right: 8px;"></i> Riwayat Transaksi</h3>
                    </div>

                    <div style="overflow-x: auto;">
                        <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr style="background-color: #f7fafc; border-bottom: 2px solid #edf2f7;">
                                    <th style="padding: 15px; color: #4a5568;">Tanggal</th>
                                    <th style="padding: 15px; color: #4a5568;">Jenis</th>
                                    <th style="padding: 15px; color: #4a5568;">Nominal</th>
                                    <th style="padding: 15px; color: #4a5568;">Status</th>
                                    <th style="padding: 15px; color: #4a5568;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat_transaksi as $trx)
                                <tr style="border-bottom: 1px solid #edf2f7;">
                                    <td style="padding: 15px; color: #718096; font-size: 14px;">
                                        {{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y, H:i') }}
                                    </td>
                                    <td style="padding: 15px; font-weight: 700; color: {{ $trx->jenis_transaksi == 'setor' ? '#16a34a' : '#dc2626' }};">
                                        {{ strtoupper($trx->jenis_transaksi) }}
                                    </td>
                                    <td style="padding: 15px; font-weight: 600; color: #2d3748;">
                                        Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                    </td>
                                    <td style="padding: 15px;">
                                        @if($trx->status == 'pending')
                                        <span style="background-color: #fef3c7; color: #d97706; padding: 4px 10px; border-radius: 50px; font-size: 12px; font-weight: 600;">Pending</span>
                                        @elseif($trx->status == 'disetujui')
                                        <span style="background-color: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 50px; font-size: 12px; font-weight: 600;">Disetujui</span>
                                        @else
                                        <span style="background-color: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 50px; font-size: 12px; font-weight: 600;">Ditolak</span>
                                        @endif
                                    </td>
                                    <td style="padding: 15px; color: #718096; font-size: 13px;">
                                        {{ $trx->keterangan ?? '-' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="padding: 30px; text-align: center; color: #a0aec0;">
                                        <i class="fa-solid fa-receipt" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                                        Belum ada riwayat transaksi untuk nasabah ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div style="padding: 15px 25px;">
                        {{ $riwayat_transaksi->links() }}
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="{{ asset('assets/js/javascript.js') }}"></script>
</body>

</html>