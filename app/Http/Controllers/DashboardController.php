<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Pastikan Carbon dipanggil untuk mempermudah manipulasi tanggal

class DashboardController extends Controller
{
    /**
     * Mengatur Tampilan Konten Dashboard Utama Secara Dinamis Sesuai Role
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $metrics = [];

        // --- AMBIL PARAMETER BULAN DARI REQUEST ATAU GUNAKAN BULAN SAAT INI ---
        // Format yang kita pakai di dropdown adalah 'mm-yyyy', misal '05-2026'
        $bulanPilihan = $request->input('bulan', now()->format('m-Y'));

        // Pecah menjadi variabel bulan (05) dan tahun (2026)
        list($bulan, $tahun) = explode('-', $bulanPilihan);

        // Simpan ke metrics agar bisa dipanggil di blade untuk menandai <option selected>
        $metrics['bulan_pilihan'] = $bulanPilihan;

        // 1. Logika perhitungan metrik dinamis
        if ($user->role == 'superadmin' || $user->role == 'admin') {
            // A. Saldo Global (Seluruh Waktu)
            $total_setor = DB::table('pengajuans')->where('status', 'disetujui')->where('jenis_transaksi', 'setor')->sum('nominal');
            $total_tarik = DB::table('pengajuans')->where('status', 'disetujui')->where('jenis_transaksi', 'tarik')->sum('nominal');
            $metrics['total_dana_sekolah'] = $total_setor - $total_tarik;

            // B. Pemasukan (Setor) KHUSUS di bulan dan tahun yang dipilih
            $metrics['pemasukan_bulan_ini'] = DB::table('pengajuans')
                ->where('status', 'disetujui')
                ->where('jenis_transaksi', 'setor')
                ->whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->sum('nominal');

            $metrics['total_nasabah'] = DB::table('users')->where('role', 'nasabah')->count();
            $metrics['antrean_pending'] = DB::table('pengajuans')->where('status', 'pending')->count();
            $metrics['total_staff'] = DB::table('users')->where('role', 'admin')->count();
        } else {
            // Logika Nasabah tetap seperti semula
            $my_setor = DB::table('pengajuans')->where('user_id', $user->id)->where('status', 'disetujui')->where('jenis_transaksi', 'setor')->sum('nominal');
            $my_tarik = DB::table('pengajuans')->where('user_id', $user->id)->where('status', 'disetujui')->where('jenis_transaksi', 'tarik')->sum('nominal');
            $metrics['saldo_pribadi'] = $my_setor - $my_tarik;
            $metrics['total_transaksi_saya'] = DB::table('pengajuans')->where('user_id', $user->id)->count();
        }

        // ============================================================
        // SEKTOR PENYARINGAN LOG
        // ============================================================
        $query_log = DB::table('activity_logs')
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('activity_logs.*', 'users.name as nama_pelaku')
            ->orderBy('activity_logs.created_at', 'desc')
            ->limit(10);

        if ($user->role == 'superadmin') {
            // superadmin melihat smeua log
            $logs = $query_log->get();
        } elseif ($user->role == 'admin') {
            // Admin melihat log sistem atau admin lainnya
            $logs = $query_log->where('activity_logs.role', 'admin')->get();
        } else {
            // Nasabah: filter berdasarkan user_id nasabah yang login
            $logs = $query_log->where('activity_logs.user_id', $user->id)->get();
        }

        // dd($logs);
        return view('dashboard', compact('metrics', 'logs'));
    }
}
