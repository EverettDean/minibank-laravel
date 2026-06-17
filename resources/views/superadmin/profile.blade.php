<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengaturan Profile - MiniBank</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style-dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .profile-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #4a5568;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .btn-save {
            background: #3182ce;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        @include('components.sidebar')
        <div class="main-content-area">
            @include('components.navbar')
            <main class="content">
                <header class="content-header">
                    <div class="header-text">
                        <h1>Pengaturan Profile</h1>
                        <p>Kelola informasi akun dan keamanan Anda.</p>
                    </div>
                </header>

                @if(session('success'))
                <div style="background: #c6f6d5; color: #22543d; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
                @endif

                <div class="grid-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px;">

                    <div class="profile-card">
                        <h3 class="card-title"><i class="fa-solid fa-user-circle" style="color: #3182ce;"></i> Data Diri</h3>
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
                            </div>
                            <button type="submit" class="btn-save" style="background: #3182ce; color: #fff;">Update Profil</button>
                        </form>
                    </div>

                    <div class="profile-card">
                        <h3 class="card-title"><i class="fa-solid fa-shield-halved" style="color: #e53e3e;"></i> Keamanan Akun</h3>
                        <form action="{{ route('profile.update_password') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Password Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="Min. 4 karakter" required>
                            </div>
                            <div class="form-group">
                                <label>Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                            </div>
                            <button type="submit" class="btn-save" style="background: #e53e3e; color: #fff;">Simpan Password</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="{{ asset('assets/js/javascript.js') }}"></script>
</body>

</html>