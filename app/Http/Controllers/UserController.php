<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Tambahkan ini untuk mempermudah deteksi user login
use Barryvdh\DomPDF\Facade\Pdf; // <--- INI SANGAT PENTING UNTUK CETAK PDF

class UserController extends Controller
{
    /**
     * 1. Menampilkan Halaman Tabel Semua User
     */
    public function index()
    {
        $all_users = User::all();
        return view('superadmin.index_user', compact('all_users'));
    }

    /**
     * 2. Menampilkan Halaman Form Tambah User
     */
    public function create()
    {
        return view('superadmin.create_user');
    }

    /**
     * 3. Memproses Penyimpanan Data dari Form ke Database (2 Tabel) + Pencatatan Log
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email'    => 'required|string|email|max:255|unique:users',
            'role'     => 'required|in:admin,nasabah',

            'kelas'    => 'required_if:role,nasabah',
            'jurusan'  => 'required_if:role,nasabah',
            'no_telp'  => 'required_if:role,nasabah',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'username' => $request->username,
                'email'    => $request->email,
                'password' => Hash::make('password123'),
                'role'     => $request->role,
            ]);

            if ($request->role == 'nasabah') {
                Nasabah::create([
                    'user_id'   => $user->id,
                    'nisn'      => $request->username,
                    'kelas'     => $request->kelas,
                    'jurusan'   => $request->jurusan,
                    'no_telp'   => $request->no_telp,
                ]);
            }
        });

        // update by dean 28052026: Catat aktivitas pembuatan user baru oleh Superadmin
        $roleLabel = $request->role == 'admin' ? 'Admin TU' : 'Nasabah';
        $this->simpanLog('fa-user-plus', 'bg-blue', 'Menambahkan user baru dengan peran ' . $roleLabel . ' bernama ' . $request->name);

        return redirect()->route('user.index')->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    /**
     * 4. Menampilkan Halaman Form Ganti Password (Cegatan Pertama)
     */
    public function changePasswordForm()
    {
        // Membuka file resources/views/superadmin/change_password.blade.php
        return view('superadmin.change_password');
    }

    /**
     * 5. Memproses Update Password Baru Nasabah ke Database
     */
    public function updatePassword(Request $request)
    {
        // Validasi agar password baru minimal 4 karakter dan cocok dengan konfirmasi
        $request->validate([
            'password' => 'required|string|min:4|confirmed',
        ]);

        // Mengambil data user yang sedang login saat ini
        $user = User::find(Auth::user()->id);

        // Ganti password lama ('password123') menjadi password baru yang diinput
        $user->password = Hash::make($request->password);
        $user->save();

        // Setelah sukses, lempar langsung ke halaman dashboard utama sistem
        return redirect()->route('dashboard.index')->with('success', 'Kata sandi akun Anda berhasil diperbarui!');
    }

    // ============================================================
    // MENU TERBARU: FITUR PENGAJUAN SETOR & TARIK TUNAI NASABAH
    // ============================================================

    /**
     * 6. Menampilkan Halaman Form Pengajuan Setor/Tarik untuk Nasabah
     */
    public function createPengajuan()
    {
        return view('nasabah.create_pengajuan');
    }

    /**
     * 7. Memproses Penyimpanan Data Pengajuan Baru ke Database + Pencatatan Log
     */
    public function storePengajuan(Request $request)
    {
        // Validasi input data dari form pengajuan
        $request->validate([
            'jenis_transaksi' => 'required|in:setor,tarik',
            'nominal'         => 'required|numeric|min:1000', // Minimal pengajuan Rp 1.000
            'keterangan'      => 'nullable|string|max:255',
        ]);

        // Ambil ID user nasabah yang sedang aktif melakukan pengajuan
        $userId = Auth::user()->id;

        // Amankan penyimpanan transaksi menggunakan Query Builder langsung ke tabel 'pengajuans'
        DB::table('pengajuans')->insert([
            'user_id'         => $userId,
            'jenis_transaksi' => $request->jenis_transaksi,
            'nominal'         => $request->nominal,
            'status'          => 'pending', // Status awal default antrean aman
            'keterangan'      => $request->keterangan,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // update by dean 28052026: Catat aktivitas pengajuan mandiri oleh Nasabah
        $this->simpanLog('fa-file-invoice-dollar', 'bg-blue', 'Membuat pengajuan ' . strtoupper($request->jenis_transaksi) . ' tunai sebesar Rp ' . number_format($request->nominal, 0, ',', '.'));

        // Setelah berhasil dikirim, lempar kembali ke riwayat transaksi dengan flash session success
        return redirect()->route('nasabah.transaksi')->with('success', 'Pengajuan transaksi berhasil dikirim! Menunggu konfirmasi Admin TU.');
    }

    /**
     * 8. Menampilkan Halaman Riwayat Transaksi & Pengajuan Milik Nasabah
     */
    // update by dean 28052026
    public function riwayatTransaksi(Request $request)
    {
        $query = DB::table('pengajuans')->where('user_id', Auth::user()->id);

        // Cek apakah ada filter tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }

        $data_pengajuan = $query->orderBy('created_at', 'desc')->get();

        return view('nasabah.riwayat_transaksi', compact('data_pengajuan'));
    }

    // ============================================================
    // PANEL PETUGAS: PROSES PERSETUJUAN TRANSAKSI NASABAH
    // ============================================================

    /**
     * 9. Menampilkan Semua Antrean Pengajuan Transaksi di Sisi Admin
     */
    public function indexPengajuanAdmin(Request $request)
    {
        // 1. Mulai query dasar (Join tabel pengajuans, users, dan nasabahs)
        $query = DB::table('pengajuans')
            ->join('users', 'pengajuans.user_id', '=', 'users.id')
            ->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
            ->select(
                'pengajuans.*',
                'users.name as nama_nasabah',
                'nasabahs.nisn'
            );

        // 2. Logika Pencarian (Search) ditambahkan di sini
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('users.name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('nasabahs.nisn', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // 3. Eksekusi query dengan urutan: Pending di atas, lalu berdasarkan tanggal terbaru
        $all_pengajuan = $query->orderByRaw("FIELD(pengajuans.status, 'pending', 'disetujui', 'ditolak')")
            ->orderBy('pengajuans.created_at', 'desc')
            // untuk setting banyaknya pagination
            ->paginate(5)
            // Append query string agar saat pindah halaman (pagination), keyword pencarian tidak hilang
            ->withQueryString();

        // 4. Lempar data ke view (Pastikan nama view sesuai dengan nama file Blade kamu)
        return view('superadmin.transaksi_petugas', compact('all_pengajuan'));
    }

    /**
     * 10. Aksi Mengubah Status Menjadi DISETUJUI + Pencatatan Log Petugas & Report Notifikasi Nasabah
     */
    public function setujuiTransaksi($id)
    {
        // update by dean 28052026: Ambil detail data pengajuan terlebih dahulu untuk menyusun deskripsi log
        $pengajuan = DB::table('pengajuans')
            ->join('users', 'pengajuans.user_id', '=', 'users.id')
            ->where('pengajuans.id', $id)
            ->select('users.name as nama_nasabah', 'pengajuans.user_id', 'pengajuans.jenis_transaksi', 'pengajuans.nominal')
            ->first();

        DB::table('pengajuans')->where('id', $id)->update([
            'status'     => 'disetujui',
            'updated_at' => now()
        ]);

        if ($pengajuan) {
            // FIXED BY DEAN: Menggunakan properti nama_nasabah agar sinkron dengan alias select di atas
            $this->simpanLog('fa-money-bill-transfer', 'bg-green', 'Menyetujui pengajuan ' . strtoupper($pengajuan->jenis_transaksi) . ' tunai senilai Rp ' . number_format($pengajuan->nominal, 0, ',', '.') . ' milik nasabah ' . $pengajuan->nama_nasabah);

            // update by dean 28052026: Kirim report notifikasi internal langsung ke akun Nasabah terkait
            DB::table('notifications')->insert([
                'user_id'    => $pengajuan->user_id,
                'judul'      => 'Transaksi Disetujui 🎉',
                'pesan'      => 'Pengajuan ' . strtoupper($pengajuan->jenis_transaksi) . ' tunai senilai Rp ' . number_format($pengajuan->nominal, 0, ',', '.') . ' telah sukses diverifikasi oleh petugas dan dibukukan ke saldo tabungan Anda.',
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Transaksi berhasil disetujui! Saldo nasabah otomatis diperbarui.');
    }

    /**
     * 11. Aksi Mengubah Status Menjadi DITOLAK + Pencatatan Log Petugas & Report Notifikasi Nasabah
     */
    public function tolakTransaksi($id)
    {
        // update by dean 28052026: Ambil detail data pengajuan terlebih dahulu untuk deskripsi log
        $pengajuan = DB::table('pengajuans')
            ->join('users', 'pengajuans.user_id', '=', 'users.id')
            ->where('pengajuans.id', $id)
            ->select('users.name as nama_nasabah', 'pengajuans.user_id', 'pengajuans.jenis_transaksi', 'pengajuans.nominal')
            ->first();

        DB::table('pengajuans')->where('id', $id)->update([
            'status'     => 'ditolak',
            'updated_at' => now()
        ]);

        if ($pengajuan) {
            // FIXED BY DEAN: Menggunakan properti nama_nasabah agar sinkron dengan alias select di atas
            $this->simpanLog('fa-xmark', 'bg-red', 'Menolak pengajuan ' . strtoupper($pengajuan->jenis_transaksi) . ' tunai senilai Rp ' . number_format($pengajuan->nominal, 0, ',', '.') . ' milik nasabah ' . $pengajuan->nama_nasabah);

            // update by dean 28052026: Kirim report notifikasi internal langsung ke akun Nasabah terkait
            DB::table('notifications')->insert([
                'user_id'    => $pengajuan->user_id,
                'judul'      => 'Transaksi Ditolak ❌',
                'pesan'      => 'Mohon maaf, pengajuan ' . strtoupper($pengajuan->jenis_transaksi) . ' tunai senilai Rp ' . number_format($pengajuan->nominal, 0, ',', '.') . ' telah ditolak dan dibatalkan oleh petugas.',
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Transaksi telah ditolak dan dibatalkan.');
    }

    /**
     * 14. Menampilkan Halaman Form Edit User (Adaptif) - Update by Dean 28052026
     */
    // public function edit($id)
    // {
    //     $user = User::findOrFail($id);

    //     $nasabah = null;
    //     if ($user->role == 'nasabah') {
    //         $nasabah = DB::table('nasabahs')->where('user_id', $user->id)->first();
    //     }

    //     return view('superadmin.edit_user', compact('user', 'nasabah'));
    // }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $nasabah = null;
        if ($user->role == 'nasabah') {
            $nasabah = DB::table('nasabahs')->where('user_id', $user->id)->first();
        }

        // TAMBAHKAN 2 BARIS INI: Ambil data untuk dropdown
        $kelas = DB::table('master_kelas')->orderBy('nama_kelas', 'asc')->get();
        $jurusans = DB::table('master_jurusans')->orderBy('nama_jurusan', 'asc')->get();

        // JANGAN LUPA KIRIMKAN JUGA KE VIEW
        return view('superadmin.edit_user', compact('user', 'nasabah', 'kelas', 'jurusans'));
    }

    /**
     * 15. Memproses Pembaruan Data User ke 2 Tabel + Pencatatan Log Audit - Update by Dean 28052026
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,

            'kelas'    => $user->role == 'nasabah' ? 'required|string' : 'nullable',
            'jurusan'  => $user->role == 'nasabah' ? 'required|string' : 'nullable',
            'no_telp'  => $user->role == 'nasabah' ? 'required|string' : 'nullable',
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name'     => $request->name,
                'username' => $request->username,
                'email'    => $request->email,
            ]);

            if ($user->role == 'nasabah') {
                DB::table('nasabahs')->where('user_id', $user->id)->update([
                    'nisn'       => $request->username,
                    'kelas'      => $request->kelas,
                    'jurusan'    => $request->jurusan,
                    'no_telp'    => $request->no_telp,
                    'updated_at' => now(),
                ]);
            }
        });

        $roleLabel = $user->role == 'admin' ? 'Admin TU' : 'Nasabah';
        $this->simpanLog('fa-pen-to-square', 'bg-gold', 'Memperbarui data profil ' . $roleLabel . ' bernama ' . $user->name);

        return redirect()->route('user.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    // ============================================================
    // MENU REPORT (SUPERADMIN & ADMIN TU) - DITAMBAHKAN BARU
    // ============================================================

    /**
     * 16. Menampilkan Halaman Laporan Filter Bulanan, Kelas, dan Jurusan
     */
    public function indexReport(Request $request)
    {
        // 1. Ambil data master untuk Dropdown Filter
        $data_kelas = DB::table('master_kelas')->orderBy('nama_kelas', 'asc')->get();
        $data_jurusan = DB::table('master_jurusans')->orderBy('nama_jurusan', 'asc')->get();

        // 2. Mulai Query Dasar (Ambil data Nasabah)
        $query = DB::table('users')
            ->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
            ->where('users.role', 'nasabah')
            ->select('users.id', 'users.name', 'nasabahs.nisn', 'nasabahs.kelas', 'nasabahs.jurusan');

        // 3. LOGIKA FILTER KELAS & JURUSAN
        if ($request->filled('kelas') && $request->kelas != 'semua') {
            $query->where('nasabahs.kelas', $request->kelas);
        }
        if ($request->filled('jurusan') && $request->jurusan != 'semua') {
            $query->where('nasabahs.jurusan', $request->jurusan);
        }

        // 4. LOGIKA FILTER BULAN (Default: Bulan Ini)
        $bulan_pilihan = $request->filled('bulan') ? $request->bulan : date('m');
        $tahun_pilihan = $request->filled('tahun') ? $request->tahun : date('Y');

        // Menambahkan Subquery untuk menghitung total transaksi di bulan yang difilter
        $query->addSelect(DB::raw("(
            SELECT COALESCE(SUM(CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END), 0) 
            FROM pengajuans 
            WHERE pengajuans.user_id = users.id 
            AND pengajuans.status = 'disetujui'
            AND MONTH(pengajuans.created_at) = '$bulan_pilihan'
            AND YEAR(pengajuans.created_at) = '$tahun_pilihan'
        ) as transaksi_bulan_ini"));

        // 5. Eksekusi query dengan pagination
        $laporan_nasabah = $query->orderBy('nasabahs.kelas', 'asc')
            ->orderBy('users.name', 'asc')
            ->paginate(25)
            ->withQueryString();

        return view('superadmin.report', compact('laporan_nasabah', 'data_kelas', 'data_jurusan', 'bulan_pilihan', 'tahun_pilihan'));
    }

    /**
     * 17. Helper Fungsi Otomatis untuk Mencatat Log Aktivitas ke Database
     */
    private function simpanLog($icon, $bg_color, $deskripsi)
    {
        DB::table('activity_logs')->insert([
            'user_id'    => Auth::user()->id,
            'role'       => Auth::user()->role,
            'icon'       => $icon,
            'bg_color'   => $bg_color,
            'deskripsi'  => $deskripsi,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * 18.  Menampilkan daftar notifikasi nasabah
     */
    public function indexNotifikasi()
    {
        // Ambil notifikasi milik user yang login
        $notifikasis = DB::table('notifications')
            ->where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Tandai semua notifikasi sudah dibaca
        DB::table('notifications')
            ->where('user_id', Auth::user()->id)
            ->update(['is_read' => true]);

        return view('nasabah.notifikasi', compact('notifikasis'));
    }

    /**
     * 19.  Fungsi ini akan mengubah status notifikasi menjadi sudah dibaca di database
     */
    public function markAllAsRead()
    {
        DB::table('notifications')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * 20. Ekspor Laporan ke PDF
     */
    public function exportReportPDF(Request $request)
    {
        $query = DB::table('users')->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
            ->where('users.role', 'nasabah')
            ->select('users.id', 'users.name', 'nasabahs.nisn', 'nasabahs.kelas', 'nasabahs.jurusan');

        if ($request->filled('kelas') && $request->kelas != 'semua') $query->where('nasabahs.kelas', $request->kelas);
        if ($request->filled('jurusan') && $request->jurusan != 'semua') $query->where('nasabahs.jurusan', $request->jurusan);

        $bulan_pilihan = $request->filled('bulan') ? $request->bulan : date('m');
        $tahun_pilihan = $request->filled('tahun') ? $request->tahun : date('Y');

        $query->addSelect(DB::raw("(SELECT COALESCE(SUM(CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END), 0) FROM pengajuans WHERE pengajuans.user_id = users.id AND pengajuans.status = 'disetujui' AND MONTH(pengajuans.created_at) = '$bulan_pilihan' AND YEAR(pengajuans.created_at) = '$tahun_pilihan') as transaksi_bulan_ini"));

        $laporan_nasabah = $query->orderBy('nasabahs.kelas', 'asc')->orderBy('users.name', 'asc')->get();

        $pdf = Pdf::loadView('superadmin.report_cetak', compact('laporan_nasabah', 'bulan_pilihan', 'tahun_pilihan', 'request'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('Laporan_Nasabah_' . $bulan_pilihan . '_' . $tahun_pilihan . '.pdf');
    }

    /**
     * 21. Ekspor Laporan ke Excel
     */
    public function exportReportExcel(Request $request)
    {
        $query = DB::table('users')->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
            ->where('users.role', 'nasabah')
            ->select('users.id', 'users.name', 'nasabahs.nisn', 'nasabahs.kelas', 'nasabahs.jurusan');

        if ($request->filled('kelas') && $request->kelas != 'semua') $query->where('nasabahs.kelas', $request->kelas);
        if ($request->filled('jurusan') && $request->jurusan != 'semua') $query->where('nasabahs.jurusan', $request->jurusan);

        $bulan_pilihan = $request->filled('bulan') ? $request->bulan : date('m');
        $tahun_pilihan = $request->filled('tahun') ? $request->tahun : date('Y');

        $query->addSelect(DB::raw("(SELECT COALESCE(SUM(CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END), 0) FROM pengajuans WHERE pengajuans.user_id = users.id AND pengajuans.status = 'disetujui' AND MONTH(pengajuans.created_at) = '$bulan_pilihan' AND YEAR(pengajuans.created_at) = '$tahun_pilihan') as transaksi_bulan_ini"));

        $laporan_nasabah = $query->orderBy('nasabahs.kelas', 'asc')->orderBy('users.name', 'asc')->get();

        // Trik memaksa browser mengunduh HTML sebagai file Excel
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan_Nasabah_" . $bulan_pilihan . "_" . $tahun_pilihan . ".xls");

        return view('superadmin.report_cetak', compact('laporan_nasabah', 'bulan_pilihan', 'tahun_pilihan', 'request'));
    }
}
