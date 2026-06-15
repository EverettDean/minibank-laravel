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
                    <div class="table-card" style="padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">

                        <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
                            <h3 style="margin: 0; color: #2d3748; font-size: 18px;">Antrean Pengajuan </h3>

                            <form action="{{ url()->current() }}" method="GET" style="display: flex; align-items: center; gap: 8px; background: #f8fafc; padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 6px;">
                                <i class="fa-solid fa-magnifying-glass" style="color: #a0aec0; font-size: 14px;"></i>
                                <input type="text" name="search" placeholder="Cari NISN atau Nama Nasabah..." value="{{ request('search') }}" style="border: none; outline: none; padding: 4px; width: 220px; background: transparent; font-size: 13px; color: #4a5568;">

                                @if(request()->filled('search'))
                                <a href="{{ url()->current() }}" style="color: #e53e3e; text-decoration: none; margin-left: 5px; margin-right: 5px;" title="Reset Pencarian">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                                @endif

                                <button type="submit" style="background: #3182ce; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600; transition: background 0.2s;">
                                    Cari
                                </button>
                            </form>
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
                                        <th style="padding: 15px; color: #4a5568; font-weight: 600;">Petugas</th>
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
                                        <td style="padding: 15px;">
                                            <strong style="color: #2d3748; display: block;">{{ $item->nama_petugas }}</strong>
                                        </td>
                                        <td style="padding: 15px; text-align: center;">
                                            @if($item->status == 'pending')
                                            <div style="display: inline-flex; gap: 8px; justify-content: center; align-items: center;">
                                                <form action="{{ route('admin.transaksi.setujui', $item->id) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <!-- <button type="submit" style="background-color: #16a34a; color: #fff; border: none; padding: 8px 14px; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="fa-solid fa-check"></i> Setuju
                                                    </button> -->
                                                    <button type="button" onclick="bukaModal({{ $item->id }})" style="background-color: #16a34a; color: #fff; border: none; padding: 8px 14px; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class=" fa-solid fa-check"></i> Setuju
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
                                            @if(request()->filled('search'))
                                            Tidak ditemukan transaksi untuk nasabah dengan kata kunci "<strong>{{ request('search') }}</strong>".
                                            @else
                                            Belum ada antrean pengajuan transaksi saat ini.
                                            @endif
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top: 15px; padding: 15px 20px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end;">
                            {{ $all_pengajuan->links() }}
                        </div>

                    </div>
                </section>
            </main>
        </div>
    </div>
    <div id="modalSetuju" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
        <div style="background:white; padding:20px; border-radius:8px; width:300px;">
            <h3>Konfirmasi Petugas</h3>
            <form id="formSetuju" method="POST">
                @csrf
                <label>Nama Petugas:</label>
                <input type="text" name="nama_petugas" required style="width:100%; padding:8px; margin:10px 0;">
                <div style="text-align:right; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button"
                        onclick="document.getElementById('modalSetuju').style.display='none'; document.getElementById('formSetuju').reset();"
                        style="background:#dc2626; color:white; border:none; padding:8px 16px; border-radius:4px; font-weight:600; cursor:pointer;">
                        Batal
                    </button>

                    <button type="submit"
                        style="background:#16a34a; color:white; border:none; padding:8px 16px; border-radius:4px; font-weight:600; cursor:pointer;">
                        Simpan & Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaModal(id) {
            // Mencegah aksi default browser (seperti submit form yang tidak disengaja)
            event.preventDefault();

            // Menggunakan route() agar URL otomatis terbentuk
            let url = "{{ route('admin.transaksi.setujui', ':id') }}";
            url = url.replace(':id', id);

            document.getElementById('formSetuju').action = url;
            document.getElementById('modalSetuju').style.display = 'flex';
        }
    </script>

    <script src="{{ asset('assets/js/javascript.js') }}"></script>


</body>

</html>