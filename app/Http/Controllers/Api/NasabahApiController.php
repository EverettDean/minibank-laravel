<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\NasabahResource;

class NasabahApiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Inisialisasi Query
        $query = Nasabah::query()->with('user');

        // 2. Terapkan Filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('nisn', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereHas('user', function ($sub) use ($searchTerm) {
                        $sub->where('name', 'LIKE', '%' . $searchTerm . '%');
                    });
            });
        }

        // 3. Jalankan Pagination
        $nasabahs = $query->paginate(10);

        // 4. Return dengan Resource agar format JSON rapi
        return NasabahResource::collection($nasabahs);
    }

    public function setujuiTransaksi(Request $request, $id)
    {
        // 1. Cari data pengajuan berdasarkan ID
        $pengajuan = \App\Models\Pengajuan::find($id);

        if (!$pengajuan) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // 2. Gunakan DB Transaction agar saldo selalu konsisten
        DB::transaction(function () use ($pengajuan, $request) {
            // Update status pengajuan
            $pengajuan->update([
                'status' => 'disetujui',
                'nama_petugas' => $request->nama_petugas
            ]);

            // --- TAMBAHKAN DI SINI ---
            // Update saldo nasabah
            $nasabah = \App\Models\Nasabah::where('user_id', $pengajuan->user_id)->first();

            // Pengecekan keamanan
            if (!$nasabah) {
                throw new \Exception("Data nasabah dengan user_id " . $pengajuan->user_id . " tidak ditemukan!");
            }
            // -------------------------

            if ($pengajuan->jenis_transaksi == 'setor') {
                $nasabah->saldo_tabungan += $pengajuan->nominal;
            } else {
                $nasabah->saldo_tabungan -= $pengajuan->nominal;
            }
            $nasabah->save();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Saldo berhasil diperbarui'
        ]);
    }
}
