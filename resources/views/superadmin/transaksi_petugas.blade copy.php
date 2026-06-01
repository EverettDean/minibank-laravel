<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniBank - Kelola Transaksi</title>
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
                        <h1>Transaksi Petugas</h1>
                        <p>Validasi dan kelola permohonan <span class="highlight-user">Setor & Tarik Tunai</span> Nasabah</p>
                    </div>
                </header>

                @if(session('success'))
                <div style="background-color: #dcfce7; color: #15803d; padding: 12px; margin-top: 20px; border-radius: 6px; font-weight: 600; font-size: 14px; border: 1px solid #bbf7d0;">
                    <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> {{ session('success') }}
                </div>
                @endif

                <section class="records-container" style="margin-top: 25px;">
                    <div class="table-card">
                        <div class="table-header">
                            <h3>Antrean Pengajuan Rekening</h3>
                        </div>

                        <div style="overflow-x: auto;">
                            <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr style="background-color: #f7fafc; border-bottom: 2px solid #edf2f7;">
                                        <th style="padding: 15px; color: #4a5568; font-weight: 600;">Nasabah</th>
                                        <th style="padding: 15px; color: #4a5568; font-weight: 600;">Jenis</th>
                                        <th style="padding: 15px; color: #4a5568; font-weight: 600;">Nominal</th>
                                        <th style="padding: 15px; color: #4a5568; font-weight: 600;">Keterangan</th>
                                        <th style="padding: 15px; color: #4a5568; font-weight: 600;">Status</th>
                                        <th style="padding: 15px; color: #4a5568; font-weight: 600; text-align: center;">Aksi Validasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($all_pengajuan as $item)
                                    <tr style="border-bottom: 1px solid #edf2f7;">
                                        <td style="padding: 15px;">
                                            <strong style="color: #2d3748; display: block;">{{ $item->nama_nasabah }}</strong>
                                            <small style="color: #718096; font-family: monospace;">NISN: {{ $item->nisn }}</small>
                                        </td>
                                        <td style="padding: 15px; font-weight: 700; color: {{ $item->jenis_transaksi == 'setor' ? '#16a34a' : '#dc2626' }};">
                                            {{ strtoupper($item->jenis_transaksi) }}
                                        </td>
                                        <td style="padding: 15px; font-weight: 600; color: #2d3748;">
                                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                        </td>
                                        <td style="padding: 15px; color: #718096; font-size: 13px;">
                                            {{ $item->keterangan ?? '-' }}
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
                                        <td style="padding: 15px; text-align: center;">
                                            @if($item->status == 'pending')
                                            <div style="display: inline-flex; gap: 8px; justify-content: center; align-items: center;">
                                                <form action="{{ route('admin.transaksi.setujui', $item->id) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" style="background-color: #16a34a; color: #fff; border: none; padding: 8px 14px; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="fa-solid fa-check"></i> Setuju
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.transaksi.tolak', $item->id) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" style="background-color: #dc2626; color: #fff; border: none; padding: 8px 14px; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="fa-solid fa-xmark"></i> Tolak
                                                    </button>
                                                </form>
                                            </div>
                                            @else
                                            <span style="color: #a0aec0; font-size: 13px; font-style: italic; font-weight: 500;">Selesai diarsip</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" style="padding: 35px; color: #a0aec0; text-align: center; font-weight: 500;">
                                            <i class="fa-solid fa-folder-open" style="font-size: 24px; display: block; margin-bottom: 8px; color: #cbd5e0;"></i>
                                            Belum ada antrean pengajuan transaksi saat ini.
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
</body>

</html>