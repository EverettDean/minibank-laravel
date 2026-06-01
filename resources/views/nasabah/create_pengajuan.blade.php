<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniBank - Pengajuan Transaksi</title>
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
                        <h1>Pengajuan Transaksi</h1>
                        <p>Formulir permohonan <span class="highlight-user">Setor atau Tarik Tunai</span> mandiri</p>
                    </div>
                </header>

                <section class="records-container" style="margin-top: 25px; max-width: 600px;">
                    <div class="table-card" style="padding: 30px;">

                        <form action="{{ route('nasabah.pengajuan.store') }}" method="POST">
                            @csrf

                            <div class="input-group" style="margin-bottom: 20px;">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; font-size: 14px;">Jenis Transaksi <span style="color: #e53e3e;">*</span></label>
                                <select name="jenis_transaksi" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; background-color: #fff; box-sizing: border-box;">
                                    <option value="" disabled selected>-- Pilih Jenis Transaksi --</option>
                                    <option value="setor">Setor Tunai (Menabung)</option>
                                    <option value="tarik">Tarik Tunai (Mengambil Uang)</option>
                                </select>
                            </div>

                            <div class="input-group" style="margin-bottom: 20px;">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; font-size: 14px;">Nominal Transaksi (Rp) <span style="color: #e53e3e;">*</span></label>
                                <input type="number" name="nominal" placeholder="Contoh: 50000" min="1000" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; box-sizing: border-box;">
                            </div>

                            <div class="input-group" style="margin-bottom: 25px;">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; font-size: 14px;">Catatan / Keterangan (Opsional)</label>
                                <textarea name="keterangan" placeholder="Contoh: Tabungan mingguan atau keperluan jajan sekolah..." style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 14px; min-height: 80px; box-sizing: border-box; resize: vertical;"></textarea>
                            </div>

                            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                                <a href="{{ route('nasabah.transaksi') }}" class="view-all-btn" style="background: #e2e8f0; color: #4a5568; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; display: inline-block; text-align: center;">Batal</a>
                                <button type="submit" class="view-all-btn" style="background: #2d3748; color: #fff; padding: 12px 24px; border: none; border-radius: 6px; font-weight: 600; font-size: 14px; cursor: pointer;">Kirim Pengajuan</button>
                            </div>

                        </form>

                    </div>
                </section>
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/js/javascript.js') }}"></script>
</body>

</html>