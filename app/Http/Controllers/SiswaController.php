<?php

namespace App\Http\Controllers;

use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf; // <--- INI SANGAT PENTING UNTUK CETAK PDF

class SiswaController extends Controller
{
    public function create()
    {
        $kelas = DB::table('master_kelas')->orderBy('nama_kelas', 'asc')->get();
        $jurusans = DB::table('master_jurusans')->orderBy('nama_jurusan', 'asc')->get();
        return view('master_murid.create', compact('kelas', 'jurusans'));
    }

    public function indexMurid(Request $request)
    {
        $query = User::where('role', 'nasabah')
            ->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
            ->select(
                'users.id',
                'users.name',
                'nasabahs.nisn',
                'nasabahs.nomor_rekening', // <--- TAMBAHKAN INI
                'nasabahs.kelas',
                'nasabahs.jurusan',
                'nasabahs.no_telp',
                'nasabahs.saldo_tabungan',
                DB::raw("(SELECT COALESCE(SUM(CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END), 0) 
                         FROM pengajuans 
                         WHERE pengajuans.user_id = users.id AND pengajuans.status = 'disetujui') as total_saldo")
            );

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('users.name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('nasabahs.nisn', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // $all_nasabah = $query->orderBy('nasabahs.kelas', 'asc')->orderBy('users.name', 'asc')->get();
        // 1. Gunakan paginate(10) alih-alih get()
        $all_nasabah = $query->orderBy('nasabahs.kelas', 'asc')
            ->orderBy('users.name', 'asc')
            ->paginate(10); // <--- Tampilkan 10 data per halaman

        // 2. Tambahkan appends() agar parameter search tetap terbawa saat pindah halaman
        $all_nasabah->appends($request->query());
        return view('master_murid', compact('all_nasabah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'nisn'     => 'required|string|unique:nasabahs,nisn',
            'kelas'    => 'required|string|max:50',
            'jurusan'  => 'required|string|max:100',
            'no_telp'  => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($request) {
            $user_id = DB::table('users')->insertGetId([
                'name'                => $request->name,
                'username'            => $request->nisn,
                'email'               => $request->email,
                'password'            => Hash::make('password123'),
                'role'                => 'nasabah',
                'is_password_changed' => false,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            DB::table('nasabahs')->insert([
                'user_id'    => $user_id,
                'nisn'       => $request->nisn,
                'kelas'      => $request->kelas,
                'jurusan'    => $request->jurusan,
                'no_telp'    => $request->no_telp,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('murid.index')->with('success', 'Nasabah baru berhasil ditambahkan!');
    }

    public function detailMurid($id)
    {
        $murid = DB::table('users')
            ->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
            ->where('users.id', $id)
            ->select('users.id', 'users.name', 'users.email', 'nasabahs.nisn', 'nasabahs.kelas', 'nasabahs.jurusan', 'nasabahs.no_telp')
            ->first();

        if (!$murid) return redirect()->route('murid.index')->with('error', 'Data murid tidak ditemukan.');

        $total_saldo = DB::table('pengajuans')
            ->where('user_id', $id)
            ->where('status', 'disetujui')
            ->sum(DB::raw("CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END"));

        $riwayat_transaksi = DB::table('pengajuans')->where('user_id', $id)->orderBy('created_at', 'desc')->paginate(10);
        return view('master_murid.detail', compact('murid', 'total_saldo', 'riwayat_transaksi'));
    }

    // ============================================================
    // FUNGSI UNTUK MENCETAK LAPORAN PDF (YANG SEBELUMNYA HILANG)
    // ============================================================
    public function downloadPDF($id)
    {
        // 1. Ambil data murid
        $murid = DB::table('users')
            ->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
            ->where('users.id', $id)
            ->select('users.id', 'users.name', 'users.email', 'nasabahs.nisn', 'nasabahs.kelas', 'nasabahs.jurusan', 'nasabahs.no_telp')
            ->first();

        if (!$murid) return redirect()->route('murid.index')->with('error', 'Data tidak ditemukan.');

        // 2. Hitung total saldo
        $total_saldo = DB::table('pengajuans')
            ->where('user_id', $id)
            ->where('status', 'disetujui')
            ->sum(DB::raw("CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END"));

        // 3. Ambil riwayat transaksi (Gunakan get() bukan paginate() agar semua data tercetak di PDF)
        $riwayat_transaksi = DB::table('pengajuans')
            ->where('user_id', $id)
            ->where('status', 'disetujui')
            ->orderBy('created_at', 'desc')
            ->get();

        // 4. Proses render tampilan HTML menjadi PDF
        $pdf = Pdf::loadView('master_murid.pdf', compact('murid', 'total_saldo', 'riwayat_transaksi'));

        // Atur ukuran kertas ke A4 posisi berdiri (portrait)
        $pdf->setPaper('A4', 'portrait');

        // 5. Download file secara otomatis
        return $pdf->download('Buku_Tabungan_' . str_replace(' ', '_', $murid->name) . '.pdf');
    }

    public function edit($id)
    {
        $murid = DB::table('users')
            ->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
            ->select('users.id', 'users.name', 'users.email', 'nasabahs.nisn', 'nasabahs.kelas', 'nasabahs.jurusan', 'nasabahs.no_telp')
            ->where('users.id', $id)
            ->first();

        if (!$murid) return redirect()->route('murid.index')->with('error', 'Data tidak ditemukan.');

        $kelas = DB::table('master_kelas')->orderBy('nama_kelas', 'asc')->get();
        $jurusans = DB::table('master_jurusans')->orderBy('nama_jurusan', 'asc')->get();
        return view('master_murid.edit', compact('murid', 'kelas', 'jurusans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $id,
            'nisn'    => 'required|string',
            'kelas'   => 'required|string|max:50',
            'jurusan' => 'required|string|max:100',
            'no_telp' => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($request, $id) {
            DB::table('users')->where('id', $id)->update([
                'name'       => $request->name,
                'email'      => $request->email,
                'updated_at' => now(),
            ]);

            DB::table('nasabahs')->where('user_id', $id)->update([
                'nisn'       => $request->nisn,
                'kelas'      => $request->kelas,
                'jurusan'    => $request->jurusan,
                'no_telp'    => $request->no_telp,
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('murid.index')->with('success', 'Data nasabah ' . $request->name . ' berhasil diperbarui!');
    }

    public function destroy($id)
    {
        DB::table('users')->where('id', $id)->delete();
        return redirect()->route('murid.index')->with('success', 'Data murid berhasil dihapus!');
    }
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file_excel'));

            return redirect()->back()->with('success', 'Data siswa berhasil di-import dari file Excel!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['file_excel' => 'Gagal meng-import! Pastikan format kolom sudah benar (NO. REK, NAMA, KELAS, JURUSAN). Error: ' . $e->getMessage()]);
        }
    }
}
