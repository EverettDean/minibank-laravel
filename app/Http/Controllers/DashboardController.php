<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $metrics = [];
        $riwayat = []; // Inisialisasi agar tidak error di view

        // --- AMBIL PARAMETER BULAN ---
        $bulanPilihan = $request->input('bulan', now()->format('m-Y'));
        list($bulan, $tahun) = explode('-', $bulanPilihan);
        $metrics['bulan_pilihan'] = $bulanPilihan;

        // 1. LOGIKA METRIK
        if ($user->role == 'superadmin' || $user->role == 'admin') {
            $total_setor = DB::table('pengajuans')->where('status', 'disetujui')->where('jenis_transaksi', 'setor')->sum('nominal');
            $total_tarik = DB::table('pengajuans')->where('status', 'disetujui')->where('jenis_transaksi', 'tarik')->sum('nominal');
            $metrics['total_dana_sekolah'] = $total_setor - $total_tarik;

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
            // Logika Nasabah
            $my_setor = DB::table('pengajuans')->where('user_id', $user->id)->where('status', 'disetujui')->where('jenis_transaksi', 'setor')->sum('nominal');
            $my_tarik = DB::table('pengajuans')->where('user_id', $user->id)->where('status', 'disetujui')->where('jenis_transaksi', 'tarik')->sum('nominal');
            $metrics['saldo_pribadi'] = $my_setor - $my_tarik;

            // Ambil riwayat untuk nasabah
            $riwayat = DB::table('pengajuans')
                ->where('user_id', $user->id)
                ->latest()
                ->take(1)
                ->get();
        }

        // 2. SEKTOR PENYARINGAN LOG (Diletakkan di luar IF agar DRY - Don't Repeat Yourself)
        $query_log = DB::table('activity_logs')
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('activity_logs.*', 'users.name as nama_pelaku')
            ->orderBy('activity_logs.created_at', 'desc')
            ->limit(10);

        if ($user->role == 'superadmin') {
            $logs = $query_log->get();
        } elseif ($user->role == 'admin') {
            // Asumsi: admin melihat log yang role-nya admin (sesuaikan logika filter Anda)
            $logs = $query_log->where('users.role', 'admin')->get();
        } else {
            $logs = $query_log->where('activity_logs.user_id', $user->id)->get();
        }

        // 3. SATU RETURN VIEW
        return view('dashboard', compact('metrics', 'riwayat', 'logs'));
    }
}
