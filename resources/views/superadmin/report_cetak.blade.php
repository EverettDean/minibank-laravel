<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Nasabah MiniBank</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .title {
            text-align: center;
            margin-bottom: 20px;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
        }

        .table-data th,
        .table-data td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .table-data th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="title">
        <h2>LAPORAN DATA & MUTASI NASABAH MINIBANK</h2>
        <!-- <p>
            Periode: <strong>Bulan {{ $bulan_pilihan }} Tahun {{ $tahun_pilihan }}</strong><br>
            Filter Kelas: <strong>{{ request('kelas') ?? 'Semua' }}</strong> |
            Filter Jurusan: <strong>{{ request('jurusan') ?? 'Semua' }}</strong>
        </p> -->
        <p>
            Periode: <strong>
                {{ \Carbon\Carbon::createFromFormat('m', $bulan_pilihan)->translatedFormat('F') }}
                {{ $tahun_pilihan }}
            </strong><br>
            Filter Kelas: <strong>{{ request('kelas') ?? 'Semua' }}</strong> |
            Filter Jurusan: <strong>{{ request('jurusan') ?? 'Semua' }}</strong>
        </p>
    </div>

    <!-- update tutup by dean 02062026 -->
    <!-- <table class="table-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Nama Nasabah</th>
                <th width="15%">NISN</th>
                <th width="20%">Kelas / Jurusan</th>
                <th width="35%">Total Transaksi Mutasi Bulan Ini (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $total_seluruh_mutasi = 0; @endphp

            @forelse($laporan_nasabah as $index => $item)
            @php $total_seluruh_mutasi += $item->transaksi_bulan_ini; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->nisn }}</td>
                <td>{{ $item->kelas }} - {{ $item->jurusan }}</td>
                <td class="text-right">{{ number_format($item->transaksi_bulan_ini, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">Tidak ada data pada periode dan filter ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: right;">TOTAL MUTASI:</th>
                <th class="text-right">Rp {{ number_format($total_seluruh_mutasi, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table> -->

    <!-- update by dean 02062026 -->
    <!-- <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>No</th>
                <th>Nama Nasabah</th>
                <th>NISN</th>
                <th>Kelas/Jurusan</th>
                <th>Mutasi Bersih</th>
                <th>Biaya Admin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan_nasabah as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->nisn }}</td>
                <td>{{ $item->kelas }} - {{ $item->jurusan }}</td>
                <td>Rp {{ number_format($item->transaksi_bulan_ini, 0, ',', '.') }}</td>
                <td style="color: red;">
                    {{ $item->total_biaya_admin > 0 ? 'Rp ' . number_format($item->total_biaya_admin, 0, ',', '.') : '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table> -->

    <table border="1" style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="border: 1px solid #000; padding: 5px;">No</th>
                <th style="border: 1px solid #000; padding: 5px;">Nama Nasabah</th>
                <th style="border: 1px solid #000; padding: 5px;">NISN</th>
                <th style="border: 1px solid #000; padding: 5px;">Kelas/Jurusan</th>
                <th style="border: 1px solid #000; padding: 5px;">Mutasi Bersih</th>
                <th style="border: 1px solid #000; padding: 5px;">Biaya Admin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan_nasabah as $index => $item)
            <tr>
                <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000; padding: 5px;">{{ $item->name }}</td>
                <td style="border: 1px solid #000; padding: 5px;">{{ $item->nisn }}</td>
                <td style="border: 1px solid #000; padding: 5px;">{{ $item->kelas }} - {{ $item->jurusan }}</td>
                <!-- <td style="border: 1px solid #000; padding: 5px; color: green;">Rp {{ number_format($item->transaksi_bulan_ini, 0, ',', '.') }}</td> -->
                <td style="border: 1px solid #000; padding: 5px; color: {{ $item->transaksi_bulan_ini < 0 ? 'red' : 'green' }};">
                    Rp {{ number_format($item->transaksi_bulan_ini, 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #000; padding: 5px; color: red;">
                    {{ $item->total_biaya_admin > 0 ? 'Rp ' . number_format($item->total_biaya_admin, 0, ',', '.') : '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>