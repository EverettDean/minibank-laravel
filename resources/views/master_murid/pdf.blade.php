<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Tabungan - {{ $murid->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2d3748;
            padding-bottom: 10px;
        }

        h1,
        h2,
        h3,
        h4 {
            margin: 0;
            padding: 5px 0;
            color: #2d3748;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        .table-data th,
        .table-data td {
            border: 1px solid #a0aec0;
            padding: 8px;
            text-align: left;
        }

        .table-data th {
            background-color: #f7fafc;
            font-weight: bold;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px;
            vertical-align: top;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-green {
            color: #16a34a;
            font-weight: bold;
        }

        .text-red {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>MINIBANK SEKOLAH</h2>
        <p>Laporan Mutasi Rekening Nasabah</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>Nama Nasabah</strong></td>
            <td width="2%">:</td>
            <td width="48%">{{ $murid->name }}</td>
            <td width="30%" rowspan="4" style="text-align: right;">
                <div style="background-color: #f7fafc; padding: 15px; border: 1px solid #cbd5e0; border-radius: 5px; text-align: center;">
                    <span style="font-size: 14px; color: #718096;">Total Saldo Aktif</span><br>
                    <span style="font-size: 20px; font-weight: bold; color: #2d3748;">Rp {{ number_format($total_saldo, 0, ',', '.') }}</span>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>NISN</strong></td>
            <td>:</td>
            <td>{{ $murid->nisn }}</td>
        </tr>
        <tr>
            <td><strong>Kelas/Jurusan</strong></td>
            <td>:</td>
            <td>{{ $murid->kelas }} - {{ $murid->jurusan }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Cetak</strong></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::now()->format('d M Y H:i') }}</td>
        </tr>
    </table>

    <h3>Riwayat Transaksi</h3>
    <table class="table-data">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="20%">Tanggal</th>
                <th width="15%">Jenis</th>
                <th width="25%">Keterangan</th>
                <th width="15%" class="text-right">Setor Masuk</th>
                <th width="15%" class="text-right">Tarik Keluar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat_transaksi as $index => $trx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($trx->created_at)->format('d/m/Y H:i') }}</td>
                <td style="text-transform: capitalize;">{{ $trx->jenis_transaksi }}</td>
                <td>{{ $trx->keterangan ?? '-' }}</td>
                <td class="text-right text-green">
                    {{ $trx->jenis_transaksi == 'setor' ? 'Rp '.number_format($trx->nominal, 0, ',', '.') : '-' }}
                </td>
                <td class="text-right text-red">
                    {{ $trx->jenis_transaksi == 'tarik' ? 'Rp '.number_format($trx->nominal, 0, ',', '.') : '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px;">Belum ada riwayat transaksi yang disetujui.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right; width: 100%;">
        <p>Petugas MiniBank,</p>
        <br><br><br>
        <p><strong>{{ Auth::user()->name }}</strong></p>
    </div>

</body>

</html>