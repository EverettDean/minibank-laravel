<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Murid Baru - MiniBank</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style-dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* CSS Khusus untuk merapikan form input */
        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #4a5568;
            font-size: 14px;
        }

        /* Desain standar untuk Input Teks */
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
            color: #2d3748;
            outline: none;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.15);
        }

        /* Desain Premium Khusus untuk Select Dropdown */
        select.form-control {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-color: #f8fafc !important;
            cursor: pointer;
            padding-right: 40px !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23a0aec0' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 15px center !important;
            background-size: 18px !important;
        }

        select.form-control:hover {
            border-color: #cbd5e0 !important;
            background-color: #ffffff !important;
        }

        /* Desain Tombol Submit */
        .btn-submit {
            background-color: #2d3748;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #1a202c;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        @include('components.sidebar')
        <div class="main-content-area">
            @include('components.navbar')

            <main class="content">
                <div style="margin-bottom: 20px;">
                    <a href="{{ route('murid.index') }}"
                        style="display: inline-flex; align-items: center; gap: 8px; background-color: #ffffff; border: 1px solid #e2e8f0; color: #4a5568; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s ease;"
                        onmouseover="this.style.backgroundColor='#f7fafc'; this.style.borderColor='#cbd5e0';"
                        onmouseout="this.style.backgroundColor='#ffffff'; this.style.borderColor='#e2e8f0';">
                        <i class="fa-solid fa-arrow-left" style="color: #a0aec0;"></i> Kembali ke Data Master
                    </a>
                </div>

                <header class="content-header" style="margin-bottom: 20px;">
                    <div class="header-text">
                        <h1>Tambah Murid Baru</h1>
                        <p>Masukkan data nasabah baru secara manual.</p>
                    </div>
                </header>

                @if ($errors->any())
                <div style="background-color: #fee2e2; color: #991b1b; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f87171;">
                    <strong style="display: block; margin-bottom: 5px;"><i class="fa-solid fa-triangle-exclamation"></i> Terdapat kesalahan:</strong>
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="form-card" style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #edf2f7; max-width: 800px;">
                    <form action="{{ route('siswa.store') }}" method="POST">
                        @csrf

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso">
                            </div>

                            <div class="form-group">
                                <label class="form-label">NISN</label>
                                <input type="text" name="nisn" class="form-control" value="{{ old('nisn') }}" required placeholder="Nomor Induk Siswa Nasional">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Kelas</label>
                                <select name="kelas" class="form-control" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                    <option value="{{ $k->nama_kelas }}" {{ old('kelas') == $k->nama_kelas ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Jurusan</label>
                                <select name="jurusan" class="form-control" required>
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach($jurusans as $j)
                                    <option value="{{ $j->nama_jurusan }}" {{ old('jurusan') == $j->nama_jurusan ? 'selected' : '' }}>
                                        {{ $j->nama_jurusan }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email (Untuk Login)</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="email@sekolah.com">
                            </div>

                            <div class="form-group">
                                <label class="form-label">No. Telepon (Opsional)</label>
                                <input type="text" name="no_telp" class="form-control" value="{{ old('no_telp') }}" placeholder="08123456789">
                            </div>
                        </div>

                        <div style="margin-top: 15px; background: #ebf8ff; padding: 12px; border-radius: 6px; border: 1px solid #bee3f8;">
                            <small style="color: #2b6cb0; font-size: 13px;">
                                <i class="fa-solid fa-circle-info" style="margin-right: 5px;"></i>
                                Password default akun nasabah otomatis diatur menjadi: <strong>password123</strong>
                            </small>
                        </div>

                        <div style="margin-top: 25px; display: flex; justify-content: flex-end; gap: 12px; padding-top: 15px; border-top: 1px solid #e2e8f0;">

                            <a href="{{ route('murid.index') }}"
                                style="background-color: #cbd5e0; color: #4a5568; padding: 12px 25px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px; transition: 0.2s;"
                                onmouseover="this.style.backgroundColor='#a0aec0';"
                                onmouseout="this.style.backgroundColor='#cbd5e0';">
                                Batal
                            </a>

                            <button type="submit" class="btn-submit">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan Data Murid
                            </button>

                        </div>

                    </form>
                </div>
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/js/javascript.js') }}"></script>
</body>

</html>