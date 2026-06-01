<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Murid - {{ $murid->name }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style-dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
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

        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
            color: #2d3748;
            outline: none;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #3182ce;
        }

        .btn-submit {
            background-color: #3182ce;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-submit:hover {
            background-color: #2b6cb0;
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
                        style="display: inline-flex; align-items: center; gap: 8px; background-color: #ffffff; border: 1px solid #e2e8f0; color: #4a5568; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Data Master
                    </a>
                </div>

                <header class="content-header" style="margin-bottom: 25px;">
                    <div class="header-text">
                        <h1>Edit Data Nasabah</h1>
                        <p>Perbarui informasi untuk nasabah <span class="highlight-user">{{ $murid->name }}</span></p>
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

                <div class="table-card" style="padding: 25px; max-width: 800px;">
                    <form action="{{ route('murid.update', $murid->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $murid->name) }}" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">NISN (Nomor Induk Siswa Nasional)</label>
                                <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $murid->nisn) }}" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $murid->email) }}" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nomor Telepon (Opsional)</label>
                                <input type="text" name="no_telp" class="form-control" value="{{ old('no_telp', $murid->no_telp) }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Kelas</label>
                                <select name="kelas" class="form-control" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    <option value="6A" {{ old('kelas', $murid->kelas) == 'XI' ? 'selected' : '' }}>XI</option>
                                    <option value="6B" {{ old('kelas', $murid->kelas) == 'XII' ? 'selected' : '' }}>XII</option>
                                    <option value="7A" {{ old('kelas', $murid->kelas) == 'XIII' ? 'selected' : '' }}>XIII</option>
                                    <!-- <option value="7B" {{ old('kelas', $murid->kelas) == '7B' ? 'selected' : '' }}>7B</option> -->
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Jurusan</label>
                                <input type="text" name="jurusan" class="form-control" value="{{ old('jurusan', $murid->jurusan) }}" required>
                            </div>
                        </div>

                        <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #edf2f7; text-align: right;">
                            <button type="submit" class="btn-submit">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Data
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