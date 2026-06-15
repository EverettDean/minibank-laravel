<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PotongAdminBulanan extends Command
{
    // Nama perintah yang akan dipanggil di terminal
    protected $signature = 'minibank:potong-admin';

    // Deskripsi perintah
    protected $description = 'Memotong saldo nasabah sebesar Rp 1.000 untuk biaya admin bulanan secara otomatis';

    public function handle()
    {
        $this->info('Memulai proses pemotongan biaya admin...');

        $nasabahs = DB::table('users')->where('role', 'nasabah')->get();
        $count = 0;

        foreach ($nasabahs as $nasabah) {
            // Hitung saldo riil saat ini
            $total_setor = DB::table('pengajuans')->where('user_id', $nasabah->id)->where('status', 'disetujui')->where('jenis_transaksi', 'setor')->sum('nominal');
            $total_tarik = DB::table('pengajuans')->where('user_id', $nasabah->id)->where('status', 'disetujui')->where('jenis_transaksi', 'tarik')->sum('nominal');

            $saldo = $total_setor - $total_tarik;

            // Potong saldo HANYA jika saldo nasabah tidak minus (minimal ada Rp 1.000)
            if ($saldo >= 1000) {
                DB::table('pengajuans')->insert([
                    'user_id'         => $nasabah->id,
                    'jenis_transaksi' => 'tarik',
                    'nominal'         => 1000,
                    'status'          => 'disetujui', // Langsung disetujui tanpa campur tangan Admin TU
                    'keterangan'      => 'Biaya Admin Bulanan - ' . Carbon::now()->translatedFormat('F Y'),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                // 2. UPDATE KOLOM saldo_tabungan di tabel nasabahs
                DB::table('nasabahs')
                    ->where('user_id', $nasabah->id)
                    ->decrement('saldo_tabungan', 1000); // Mengurangi saldo sebesar 1000

                // Kirim notifikasi ke nasabah yang bersangkutan
                DB::table('notifications')->insert([
                    'user_id'    => $nasabah->id,
                    'judul'      => 'Biaya Admin Bulanan 💸',
                    'pesan'      => 'Saldo Anda telah dipotong sebesar Rp 1.000 untuk biaya administrasi bulan ' . Carbon::now()->translatedFormat('F Y') . '.',
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $count++;
            }
        }

        $this->info("Proses selesai! Biaya admin berhasil dipotong dari $count nasabah.");
    }
}
