<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniBank - Edit Pengguna</title>
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
                        <h1>Edit Pengguna</h1>
                        <p>Ubah informasi data akun <span class="highlight-user">{{ $user->name }}</span></p>
                    </div>
                </header>

                <section class="form-container" style="margin-top: 25px; max-width: 600px; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">

                    <form action="{{ route('user.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- GOLONGAN DATA UTAMA (SEMUA ROLE) -->
                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px; font-family: inherit;">
                            @error('name') <small style="color: #dc2626; font-weight: 600;">{{ $message }}</small> @enderror
                        </div>

                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Username / NISN</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px; font-family: inherit;">
                            @error('username') <small style="color: #dc2626; font-weight: 600;">{{ $message }}</small> @enderror
                        </div>

                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px; font-family: inherit;">
                            @error('email') <small style="color: #dc2626; font-weight: 600;">{{ $message }}</small> @enderror
                        </div>

                        <!-- GOLONGAN DATA SPESIFIK (HANYA MUNCUL JIKA USER ADALAH NASABAH) -->
                        @if($user->role == 'nasabah' && $nasabah)
                        <hr style="border: 0; border-top: 1px dashed #e2e8f0; margin: 25px 0;">
                        <h4 style="color: #2d3748; margin-bottom: 15px;"><i class="fa-solid fa-school" style="margin-right: 6px; color: #3182ce;"></i> Informasi Akademik Siswa</h4>

                        <!-- <div style="margin-bottom: 18px;">
                            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Kelas</label>
                            <input type="text" name="kelas" value="{{ old('kelas', $nasabah->kelas) }}" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                            @error('kelas') <small style="color: #dc2626; font-weight: 600;">{{ $message }}</small> @enderror
                        </div>

                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Jurusan</label>
                            <input type="text" name="jurusan" value="{{ old('jurusan', $nasabah->jurusan) }}" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                            @error('jurusan') <small style="color: #dc2626; font-weight: 600;">{{ $message }}</small> @enderror
                        </div> -->

                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Kelas</label>
                            <select name="kelas" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px; background-color: #fff; cursor: pointer; outline: none;">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                <option value="{{ $k->nama_kelas }}" {{ old('kelas', $nasabah->kelas ?? '') == $k->nama_kelas ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                                @endforeach
                            </select>
                            @error('kelas') <small style="color: #dc2626; font-weight: 600;">{{ $message }}</small> @enderror
                        </div>

                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Jurusan</label>
                            <select name="jurusan" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px; background-color: #fff; cursor: pointer; outline: none;">
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach($jurusans as $j)
                                <option value="{{ $j->nama_jurusan }}" {{ old('jurusan', $nasabah->jurusan ?? '') == $j->nama_jurusan ? 'selected' : '' }}>
                                    {{ $j->nama_jurusan }}
                                </option>
                                @endforeach
                            </select>
                            @error('jurusan') <small style="color: #dc2626; font-weight: 600;">{{ $message }}</small> @enderror
                        </div>

                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">No. Telepon / WhatsApp</label>
                            <input type="text" name="no_telp" value="{{ old('no_telp', $nasabah->no_telp) }}" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                            @error('no_telp') <small style="color: #dc2626; font-weight: 600;">{{ $message }}</small> @enderror
                        </div>
                        @endif

                        <!-- TOMBOL AKSI -->
                        <div style="margin-top: 30px; display: flex; gap: 10px;">
                            <button type="submit" style="background-color: #16a34a; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 14px;">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('user.index') }}" style="background-color: #718096; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: 600; font-size: 14px; text-align: center;">
                                Batal
                            </a>
                        </div>

                    </form>
                </section>
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/js/javascript.js') }}"></script>
</body>

</html>