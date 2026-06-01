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
        <p>
            Periode: <strong>Bulan {{ $bulan_pilihan }} Tahun {{ $tahun_pilihan }}</strong><br>
            Filter Kelas: <strong>{{ request('kelas') ?? 'Semua' }}</strong> |
            Filter Jurusan: <strong>{{ request('jurusan') ?? 'Semua' }}</strong>
        </p>
    </div>

    <table class="table-data">
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
    </table>
</body>

</html>