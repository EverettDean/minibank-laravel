<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniBank - Pembaruan Kata Sandi</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style-dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <div class="main-wrapper">
        <div class="main-content-area" style="width: 100%; padding: 40px 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f7fafc;">

            <section class="records-container" style="max-width: 500px; width: 100%;">
                <div class="table-card" style="padding: 35px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">

                    <div style="text-align: center; margin-bottom: 25px;">
                        <div style="background-color: #fef3c7; color: #d97706; width: 60px; height: 60px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px;">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <h2 style="margin: 0; font-size: 20px; color: #1a202c;">Pembaruan Keamanan</h2>
                        <p style="margin: 5px 0 0 0; font-size: 14px; color: #718096;">Silakan ganti kata sandi bawaan Anda untuk melanjutkan.</p>
                    </div>

                    @if(session('info'))
                    <div style="background-color: #fffaf0; border-left: 4px solid #dd6b20; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                        <p style="margin: 0; font-size: 13px; color: #dd6b20; font-weight: 500; line-height: 1.5;">
                            {{ session('info') }}
                        </p>
                    </div>
                    @endif

                    @if($errors->any())
                    <div style="background-color: #fff5f5; border-left: 4px solid #e53e3e; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                        <p style="margin: 0; font-size: 13px; color: #e53e3e; font-weight: 600;">
                            Kata sandi baru dan konfirmasi tidak cocok atau terlalu pendek (min 4 karakter)!
                        </p>
                    </div>
                    @endif

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf

                        <div class="input-group" style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; font-size: 14px;">Kata Sandi Baru</label>
                            <input type="password" name="password" placeholder="Masukkan kata sandi baru..." required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; box-sizing: border-box;">
                        </div>

                        <div class="input-group" style="margin-bottom: 25px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; font-size: 14px;">Ulangi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" placeholder="Konfirmasi kata sandi baru..." required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; box-sizing: border-box;">
                        </div>

                        <button type="submit" class="view-all-btn" style="width: 100%; background: #2d3748; color: #fff; padding: 14px; border: none; border-radius: 6px; font-weight: 600; font-size: 14px; cursor: pointer; text-align: center; display: block;">
                            Simpan Kata Sandi & Masuk
                        </button>
                    </form>

                </div>
            </section>

        </div>
    </div>

</body>

</html>