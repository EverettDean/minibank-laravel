<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniBank - Master Murid</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style-dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <div class="main-wrapper">
        @include ('components.sidebar')

        <div class="main-content-area">

            @include ('components.navbar')

            <header class="content-header">
                <div class="header-text">
                    <h1>Data Master Murid</h1>
                    <p>Kelola informasi lengkap seluruh penabung MiniBank</p>
                </div>
            </header>

            <div class="table-controls" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; gap: 15px; flex-wrap: wrap;">
                <form id="searchForm" action="{{ route('murid.index') }}" method="GET"
                    style="display: flex; align-items: center; gap: 8px; background: #fff; padding: 5px 10px; border: 1px solid #ddd; border-radius: 6px; width: fit-content;">
                    <i class="fa-solid fa-magnifying-glass" style="color: #a0aec0; font-size: 14px;"></i>
                    <input type="text" name="search" id="searchInput" placeholder="Cari NIS atau Nama Murid..."
                        value="{{ request('search') }}"
                        style="border: none; outline: none; padding: 5px; width: 220px;">

                    @if(request()->filled('search'))
                    <a href="{{ route('murid.index') }}" style="color: #e53e3e; text-decoration: none; margin-left: 5px;">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                    @endif
                </form>

                <!-- <div class="header-actions" style="display: flex; gap: 10px;">
                    <button type="button" class="btn-import" onclick="document.getElementById('excelInput').click()"
                        style="background: #16a34a; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        <i class="fa-solid fa-file-excel"></i> Import Excel
                    </button>
                    <input type="file" id="excelInput" style="display: none;" accept=".xlsx, .xls">

                    <a href="{{ route('siswa.create') }}" class="btn-add" style="background: #2d3748; color: #fff; padding: 10px 15px; text-decoration: none; border-radius: 6px; font-weight: 600;">
                        <i class="fa-solid fa-plus"></i> Tambah Murid Baru
                    </a>
                </div> -->

                <div class="header-actions" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">

                    <form action="{{ route('murid.import') }}" method="POST" enctype="multipart/form-data" style="margin: 0;">
                        @csrf

                        <button type="button" class="btn-import" onclick="document.getElementById('excelInput').click()"
                            style="background: #16a34a; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            <i class="fa-solid fa-file-excel"></i> Import Excel
                        </button>

                        <input type="file" name="file_excel" id="excelInput" style="display: none;" accept=".xlsx, .xls, .csv" onchange="this.form.submit()">
                    </form>

                    <a href="{{ route('siswa.create') }}" class="btn-add" style="background: #2d3748; color: #fff; padding: 10px 15px; text-decoration: none; border-radius: 6px; font-weight: 600;">
                        <i class="fa-solid fa-plus"></i> Tambah Murid Baru
                    </a>

                </div>

                <div style="margin-top: 8px; font-size: 12px; color: #718096; width: 100%;">
                    *Catatan: Pastikan file Excel klien <b>tidak dipasangi password (Encrypt Document)</b> sebelum di-upload ke sistem.
                </div>

                @error('file_excel')
                <div style="color: #e53e3e; background: #fff5f5; border: 1px solid #feb2b2; padding: 10px 15px; border-radius: 6px; margin-top: 15px; font-weight: 600; width: 100%;">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                </div>
                @enderror
            </div>

            <section class="data-section">
                <div class="table-container">
                    <table class="master-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Identitas Nasabah</th>
                                <th>Nomor Rekening</th>
                                <th>Kelas</th>
                                <!-- <th>No. Telepon</th> -->
                                <th>Total Saldo</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($all_nasabah as $index => $murid)
                            <tr style="border-bottom: 1px solid #edf2f7;">
                                <td style="padding: 15px; text-align: center; color: #718096;">
                                    <!-- {{ $index + 1 }} -->
                                    {{ $all_nasabah->firstItem() + $index }}
                                </td>
                                <td style="padding: 15px;">
                                    <strong style="color: #2d3748; display: block;">{{ $murid->name }}</strong>
                                    <small style="color: #a0aec0; font-family: monospace; display: block;">NISN: {{ $murid->nisn }}</small>

                                    <!-- update tutup by dean 29052026 -->
                                    <!-- @if($murid->nomor_rekening)
                                    <span style="background-color: #ebf8ff; color: #3182ce; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: 700; font-family: monospace; margin-top: 4px; display: inline-block; border: 1px solid #bee3f8;">
                                        <i class="fa-solid fa-credit-card"></i> {{ $murid->nomor_rekening }}
                                    </span>
                                    @endif -->
                                </td>
                                <td style="padding: 15px;">
                                    <!-- <div style="color: #4a5568; font-size: 14px; font-weight: 600;">
                                        {{ $murid->kelas }} - {{ $murid->jurusan }}
                                    </div> -->
                                    <div style="color: #3182ce; font-size: 15px; font-family: monospace; font-weight: 700;">
                                        {{ $murid->nomor_rekening ?? 'Belum ada No. Rek' }}
                                    </div>
                                </td>
                                <!-- <td style="padding: 15px; color: #4a5568;">
                                    {{ $murid->no_telp ?? '-' }}
                                </td> -->
                                <td style="padding: 15px; color: #4a5568;">
                                    {{ $murid->kelas }} - {{ $murid->jurusan }}
                                </td>
                                <td style="padding: 15px; font-weight: 700; color: #16a34a;">
                                    Rp {{ number_format($murid->saldo_tabungan, 0, ',', '.') }}
                                </td>
                                <!-- <td>Rp {{ number_format($murid->total_saldo, 0, ',', '.') }}</td> -->
                                <td style="padding: 15px; text-align: center;">
                                    <div style="display: inline-flex; gap: 6px; justify-content: center; align-items: center;">
                                        <a href="{{ route('murid.detail', ['id' => $murid->id]) }}"
                                            style="background: #2d3748; color: #fff; padding: 6px 10px; text-decoration: none; border-radius: 4px; font-size: 12px; font-weight: 600;"
                                            title="Lihat Detail">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <a href="{{ route('murid.edit', ['id' => $murid->id]) }}"
                                            style="background: #d97706; color: #fff; padding: 6px 10px; text-decoration: none; border-radius: 4px; font-size: 12px; font-weight: 600;"
                                            title="Edit Data">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <form action="{{ route('murid.destroy', ['id' => $murid->id]) }}" method="POST" style="margin: 0;"
                                            onsubmit="return confirm('Yakin ingin menghapus data nasabah ini? Seluruh riwayat transaksinya juga akan ikut terhapus!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                style="background: #dc2626; color: #fff; border: none; padding: 6px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer;"
                                                title="Hapus Data">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="padding: 30px; color: #a0aec0; text-align: center; font-weight: 500;">
                                    <i class="fa-solid fa-users-slash" style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
                                    Belum ada data murid/nasabah yang terdaftar di sistem.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 15px; display: flex; justify-content: flex-end;">
                    {{ $all_nasabah->onEachSide(1)->links('pagination::bootstrap-4') }}
                </div>
            </section>
        </div>
    </div>

    <script src="{{ asset('assets/js/javascript.js')}}"></script>
</body>

</html>