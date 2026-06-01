<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniBank - Laporan Bulanan</title>
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
                <!-- update tutup by dean 01062026 -->
                <!-- <header class="content-header">
                    <div class="header-text">
                        <h1>Laporan Nasabah</h1>
                        <p>Rekapitulasi data dan mutasi nasabah per bulan</p>
                    </div>
                </header> -->

                <header class="content-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div class="header-text">
                        <h1>Laporan Nasabah</h1>
                        <p>Rekapitulasi data dan mutasi nasabah per bulan</p>
                    </div>

                    <div class="header-actions" style="display: flex; gap: 10px;">
                        <a href="{{ route('admin.report.pdf', request()->all()) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; background-color: #dc2626; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 13px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="fa-solid fa-file-pdf"></i> Cetak PDF
                        </a>
                        <a href="{{ route('admin.report.excel', request()->all()) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; background-color: #16a34a; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 13px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="fa-solid fa-file-excel"></i> Cetak Excel
                        </a>
                    </div>
                </header>

                <section class="records-container" style="margin-top: 25px;">

                    <div class="table-card" style="padding: 20px; background: #fff; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <form action="{{ url()->current() }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">

                            <div style="flex: 1; min-width: 150px;">
                                <label style="font-size: 13px; color: #4a5568; font-weight: 600;">Bulan</label>
                                <select name="bulan" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; margin-top: 5px;">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $bulan_pilihan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                        @endfor
                                </select>
                            </div>

                            <div style="flex: 1; min-width: 150px;">
                                <label style="font-size: 13px; color: #4a5568; font-weight: 600;">Kelas</label>
                                <select name="kelas" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; margin-top: 5px;">
                                    <option value="semua">- Semua Kelas -</option>
                                    @foreach($data_kelas as $k)
                                    <option value="{{ $k->nama_kelas }}" {{ request('kelas') == $k->nama_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="flex: 1; min-width: 150px;">
                                <label style="font-size: 13px; color: #4a5568; font-weight: 600;">Jurusan</label>
                                <select name="jurusan" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; margin-top: 5px;">
                                    <option value="semua">- Semua Jurusan -</option>
                                    @foreach($data_jurusan as $j)
                                    <option value="{{ $j->nama_jurusan }}" {{ request('jurusan') == $j->nama_jurusan ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="flex: 1; min-width: 150px;">
                                <button type="submit" style="width: 100%; background: #3182ce; color: #fff; border: none; padding: 10px; border-radius: 6px; font-weight: 600; cursor: pointer;">
                                    <i class="fa-solid fa-filter"></i> Terapkan Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="table-card" style="padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="overflow-x: auto;">
                            <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr style="background-color: #f7fafc; border-bottom: 2px solid #edf2f7;">
                                        <th style="padding: 15px; color: #4a5568;">No</th>
                                        <th style="padding: 15px; color: #4a5568;">Nama Nasabah</th>
                                        <th style="padding: 15px; color: #4a5568;">NISN</th>
                                        <th style="padding: 15px; color: #4a5568;">Kelas/Jurusan</th>
                                        <th style="padding: 15px; color: #4a5568;">Mutasi Bulan Ini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($laporan_nasabah as $index => $item)
                                    <tr style="border-bottom: 1px solid #edf2f7;">
                                        <td style="padding: 15px; text-align: center; color: #718096;">
                                            {{ $laporan_nasabah->firstItem() + $index }}
                                        </td>
                                        <td style="padding: 15px; font-weight: 600; color: #2d3748;">
                                            {{ $item->name }}
                                        </td>
                                        <td style="padding: 15px; color: #718096; font-family: monospace;">
                                            {{ $item->nisn }}
                                        </td>
                                        <td style="padding: 15px; color: #4a5568;">
                                            {{ $item->kelas }} - {{ $item->jurusan }}
                                        </td>
                                        <td style="padding: 15px; font-weight: 700; color: {{ $item->transaksi_bulan_ini < 0 ? '#dc2626' : '#16a34a' }};">
                                            Rp {{ number_format($item->transaksi_bulan_ini, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" style="padding: 30px; text-align: center; color: #a0aec0;">
                                            Tidak ada data nasabah untuk filter yang dipilih.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top: 15px; display: flex; justify-content: flex-end;">
                            {{ $laporan_nasabah->links() }}
                        </div>
                    </div>

                </section>
            </main>
        </div>
    </div>
    <script src="{{ asset('assets/js/javascript.js') }}"></script>
</body>

</html>