<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniBank - Manajemen Pengguna</title>

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

                <header class="content-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="header-text">
                        <h1>Manajemen Pengguna</h1>
                        <p>Kelola Tingkat Hak Akses <span class="highlight-user">Sistem Akuntansi</span></p>
                    </div>

                    <div style="margin-top: -10px;">
                        <a href="/tambah-user" class="view-all-btn" style="background-color: #2d3748; color: #ffffff; padding: 12px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; transition: background 0.2s;">
                            <i class="fa-solid fa-user-shield"></i> Tambah User Baru
                        </a>
                    </div>
                </header>

                <section class="records-container" style="margin-top: 25px;">
                    <div class="table-card">
                        <div class="table-header">
                            <h3>Daftar Akun Staff Aktif</h3>
                            <i class="fa-solid fa-users-gear" style="color: #a0aec0;"></i>
                        </div>

                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>NAMA LENGKAP</th>
                                    <th>USERNAME</th>
                                    <th>EMAIL</th>
                                    <th>ROLE / JABATAN</th>
                                    <th class="text-right" style="padding-right: 24px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($all_users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="student-info">
                                            <div class="student-img" style="background-color: #e2e8f0; color: #4a5568; font-weight: 700;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span style="font-weight: 600; color: #1a202c;">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <!-- Penyesuaian Badge Status Pengguna Berdasarkan Role Baru -->
                                        @if($user->role == 'superadmin')
                                        <span class="badge" style="background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">Superadmin</span>
                                        @elseif($user->role == 'admin')
                                        <span class="badge" style="background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a;">Admin TU</span>
                                        @else
                                        <!-- Teks diganti menjadi Nasabah -->
                                        <span class="badge" style="background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">Nasabah</span>
                                        @endif
                                    </td>
                                    <td class="text-right" style="padding-right: 24px;">
                                        <a href="{{ route('user.edit', $user->id) }}" class="btn-edit" style="background-color: #3182ce; color: #fff; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: 600;">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

            </main>
        </div>
    </div>

    <script src="{{ asset('assets/js/javascript.js') }}"></script>
</body>

</html>