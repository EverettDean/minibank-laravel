<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Tambahkan ini untuk mempermudah deteksi user login

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
    // update tutup by dean 28052026
    // public function riwayatTransaksi()
    // {
    //     // Mengambil data pengajuan khusus milik nasabah yang sedang login saat ini
    //     $data_pengajuan = DB::table('pengajuans')
    //         ->where('user_id', Auth::user()->id)
    //         ->orderBy('created_at', 'desc') // Urutkan dari yang paling baru
    //         ->get();

    //     // Kirim data ke dalam folder views/nasabah/riwayat_transaksi.blade.php
    //     return view('nasabah.riwayat_transaksi', compact('data_pengajuan'));
    // }

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
    public function indexPengajuanAdmin()
    {
        // Mengambil semua data pengajuan digabung (join) dengan tabel users untuk mengambil nama & NISN pemohon
        $all_pengajuan = DB::table('pengajuans')
            ->join('users', 'pengajuans.user_id', '=', 'users.id')
            ->select('pengajuans.*', 'users.name as nama_nasabah', 'users.username as nisn')
            ->orderBy('pengajuans.created_at', 'desc')
            ->get();

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

    // ============================================================
    // PANEL DATA MASTER: RINGKASAN DATA PROFIL & SALDO MURID
    // ============================================================

    /**
     * 12. Menampilkan Daftar Semua Murid/Nasabah Beserta Saldo Akhir
     */
    public function indexMurid()
    {
        $all_nasabah = User::where('role', 'nasabah')
            ->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
            ->select(
                'users.id',
                'users.name',
                'nasabahs.nisn',
                'nasabahs.kelas',
                'nasabahs.jurusan',
                'nasabahs.no_telp',
                DB::raw("
                    (SELECT COALESCE(SUM(CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END), 0) 
                     FROM pengajuans 
                     WHERE pengajuans.user_id = users.id AND pengajuans.status = 'disetujui') as total_saldo
                ")
            )
            ->orderBy('nasabahs.kelas', 'asc')
            ->orderBy('users.name', 'asc')
            ->get();

        return view('master_murid', compact('all_nasabah'));
    }

    /**
     * 14. Menampilkan Halaman Form Edit User (Adaptif) - Update by Dean 28052026
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        $nasabah = null;
        if ($user->role == 'nasabah') {
            $nasabah = DB::table('nasabahs')->where('user_id', $user->id)->first();
        }

        return view('superadmin.edit_user', compact('user', 'nasabah'));
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

    /**
     * 16. Helper Fungsi Otomatis untuk Mencatat Log Aktivitas ke Database
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
     * 17.  Menampilkan daftar notifikasi nasabah
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
     * 18.  Fungsi ini akan mengubah status notifikasi menjadi sudah dibaca di database
     */
    public function markAllAsRead()
    {
        DB::table('notifications')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
