<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $roleLabel = $request->role == 'admin' ? 'Admin TU' : 'Nasabah';
        $this->simpanLog('fa-user-plus', 'bg-blue', 'Menambahkan user baru dengan peran ' . $roleLabel . ' bernama ' . $request->name);

        // ==========================================================
        // FITUR BARU: KIRIM NOTIFIKASI KE DIRI SENDIRI (SUPERADMIN)
        // ==========================================================
        DB::table('notifications')->insert([
            'user_id'    => Auth::user()->id,
            'judul'      => 'Pengguna Baru Disimpan 👤',
            'pesan'      => "Akun dengan nama {$request->name} (sebagai $roleLabel) telah sukses dibuat dan dimasukkan ke database.",
            'is_read'    => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('user.index')->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    /**
     * 4. Menampilkan Halaman Form Ganti Password (Cegatan Pertama)
     */
    public function changePasswordForm()
    {
        return view('superadmin.change_password');
    }

    /**
     * 5. Memproses Update Password Baru Nasabah ke Database
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:4|confirmed',
        ]);

        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($request->password);
        $user->save();

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
            'nominal'         => 'required|numeric|min:1000',
            'keterangan'      => 'nullable|string|max:255',
        ]);

        $userId = Auth::user()->id;
        $nasabahName = Auth::user()->name;

        // ==========================================================
        // FITUR BARU: CEK SALDO MENGENDAP (MINIMAL RP 50.000)
        // ==========================================================
        if ($request->jenis_transaksi == 'tarik') {
            $total_setor = DB::table('pengajuans')->where('user_id', $userId)->where('status', 'disetujui')->where('jenis_transaksi', 'setor')->sum('nominal');
            $total_tarik = DB::table('pengajuans')->where('user_id', $userId)->where('status', 'disetujui')->where('jenis_transaksi', 'tarik')->sum('nominal');
            $pending_tarik = DB::table('pengajuans')->where('user_id', $userId)->where('status', 'pending')->where('jenis_transaksi', 'tarik')->sum('nominal');

            $saldo_saat_ini = $total_setor - $total_tarik - $pending_tarik;

            if (($saldo_saat_ini - $request->nominal) < 50000) {
                $saldo_bisa_ditarik = max(0, $saldo_saat_ini - 50000);

                return redirect()->back()->withErrors([
                    'nominal' => 'Penarikan gagal! Harus tersisa Saldo Mengendap minimal Rp 50.000. Maksimal yang bisa Anda tarik saat ini adalah Rp ' . number_format($saldo_bisa_ditarik, 0, ',', '.')
                ])->withInput();
            }
        }
        // ==========================================================

        // Simpan transaksi
        DB::table('pengajuans')->insert([
            'user_id'         => $userId,
            'jenis_transaksi' => $request->jenis_transaksi,
            'nominal'         => $request->nominal,
            'status'          => 'pending',
            'keterangan'      => $request->keterangan,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $this->simpanLog('fa-file-invoice-dollar', 'bg-blue', 'Membuat pengajuan ' . strtoupper($request->jenis_transaksi) . ' tunai sebesar Rp ' . number_format($request->nominal, 0, ',', '.'));

        // Kirim Notifikasi ke Admin TU
        $semuaAdmin = DB::table('users')->where('role', 'admin')->get();
        $jenis = strtoupper($request->jenis_transaksi);
        $nominalRupiah = number_format($request->nominal, 0, ',', '.');

        foreach ($semuaAdmin as $admin) {
            DB::table('notifications')->insert([
                'user_id'    => $admin->id,
                'judul'      => 'Antrean ' . $jenis . ' Baru 🔔',
                'pesan'      => "Nasabah $nasabahName telah mengajukan $jenis tunai sebesar Rp $nominalRupiah. Segera tinjau di panel transaksi.",
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ==========================================================
        // PESAN SUKSES DINAMIS UNTUK POP-UP SWEETALERT2
        // ==========================================================
        $pesanSukses = $request->jenis_transaksi == 'setor'
            ? "Berhasil mengajukan setoran sebesar Rp $nominalRupiah. Menunggu konfirmasi Admin TU."
            : "Berhasil mengajukan penarikan sebesar Rp $nominalRupiah. Menunggu konfirmasi Admin TU.";

        return redirect()->route('nasabah.transaksi')->with('success', $pesanSukses);
    }

    /**
     * 8. Menampilkan Halaman Riwayat Transaksi & Pengajuan Milik Nasabah
     */
    public function riwayatTransaksi(Request $request)
    {
        $query = DB::table('pengajuans')->where('user_id', Auth::user()->id);

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
        $query = DB::table('pengajuans')
            ->join('users', 'pengajuans.user_id', '=', 'users.id')
            ->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
            ->select(
                'pengajuans.*',
                'users.name as nama_nasabah',
                'nasabahs.nisn'
            );

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('users.name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('nasabahs.nisn', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $all_pengajuan = $query->orderByRaw("FIELD(pengajuans.status, 'pending', 'disetujui', 'ditolak')")
            ->orderBy('pengajuans.created_at', 'desc')
            ->paginate(5)
            ->withQueryString();

        return view('superadmin.transaksi_petugas', compact('all_pengajuan'));
    }

    /**
     * 10. Aksi Mengubah Status Menjadi DISETUJUI + Pencatatan Log Petugas & Report Notifikasi Nasabah
     */
    // public function setujuiTransaksi($id)
    // {
    //     $pengajuan = DB::table('pengajuans')
    //         ->join('users', 'pengajuans.user_id', '=', 'users.id')
    //         ->where('pengajuans.id', $id)
    //         ->select('users.name as nama_nasabah', 'pengajuans.user_id', 'pengajuans.jenis_transaksi', 'pengajuans.nominal')
    //         ->first();

    //     DB::table('pengajuans')->where('id', $id)->update([
    //         'status'     => 'disetujui',
    //         'updated_at' => now()
    //     ]);

    //     if ($pengajuan) {
    //         $this->simpanLog('fa-money-bill-transfer', 'bg-green', 'Menyetujui pengajuan ' . strtoupper($pengajuan->jenis_transaksi) . ' tunai senilai Rp ' . number_format($pengajuan->nominal, 0, ',', '.') . ' milik nasabah ' . $pengajuan->nama_nasabah);

    //         DB::table('notifications')->insert([
    //             'user_id'    => $pengajuan->user_id,
    //             'judul'      => 'Transaksi Disetujui 🎉',
    //             'pesan'      => 'Pengajuan ' . strtoupper($pengajuan->jenis_transaksi) . ' tunai senilai Rp ' . number_format($pengajuan->nominal, 0, ',', '.') . ' telah sukses diverifikasi oleh petugas dan dibukukan ke saldo tabungan Anda.',
    //             'is_read'    => false,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     }

    //     return redirect()->back()->with('success', 'Transaksi berhasil disetujui! Saldo nasabah otomatis diperbarui.');
    // }

    public function setujuiTransaksi(Request $request, $id)
    {

        $request->validate(['nama_petugas' => 'required|string']);
        // 1. Ambil data pengajuan
        $pengajuan = DB::table('pengajuans')
            ->join('users', 'pengajuans.user_id', '=', 'users.id')
            ->where('pengajuans.id', $id)
            ->select('users.name as nama_nasabah', 'pengajuans.user_id', 'pengajuans.jenis_transaksi', 'pengajuans.nominal')
            ->first();

        if (!$pengajuan) {
            return redirect()->back()->with('error', 'Data transaksi tidak ditemukan.');
        }

        // 2. Update saldo di tabel nasabahs menggunakan DB transaction agar aman
        DB::transaction(function () use ($id, $pengajuan, $request) {
            // Update status pengajuan
            DB::table('pengajuans')->where('id', $id)->update([
                'status'     => 'disetujui',
                'nama_petugas' => $request->nama_petugas,
                'updated_at' => now()
            ]);

            // Cari data nasabah berdasarkan user_id
            $nasabah = \App\Models\Nasabah::where('user_id', $pengajuan->user_id)->first();

            if ($nasabah) {
                if ($pengajuan->jenis_transaksi == 'setor') {
                    // Tambah saldo
                    $nasabah->saldo_tabungan += $pengajuan->nominal;
                } elseif ($pengajuan->jenis_transaksi == 'tarik') {
                    // Kurangi saldo
                    $nasabah->saldo_tabungan -= $pengajuan->nominal;
                }
                $nasabah->save();
            }
        });

        // 3. Log dan Notifikasi (seperti kode aslimu)
        $this->simpanLog('fa-money-bill-transfer', 'bg-green', 'Menyetujui pengajuan ' . strtoupper($pengajuan->jenis_transaksi) . ' tunai senilai Rp ' . number_format($pengajuan->nominal, 0, ',', '.') . ' milik nasabah ' . $pengajuan->nama_nasabah);

        DB::table('notifications')->insert([
            'user_id'    => $pengajuan->user_id,
            'judul'      => 'Transaksi Disetujui 🎉',
            'pesan'      => 'Pengajuan ' . strtoupper($pengajuan->jenis_transaksi) . ' tunai senilai Rp ' . number_format($pengajuan->nominal, 0, ',', '.') . ' telah sukses diverifikasi dan saldo Anda telah diperbarui.',
            'is_read'    => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil disetujui! Saldo nasabah otomatis diperbarui.');
    }

    /**
     * 11. Aksi Mengubah Status Menjadi DITOLAK + Pencatatan Log Petugas & Report Notifikasi Nasabah
     */
    public function tolakTransaksi($id)
    {
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
            $this->simpanLog('fa-xmark', 'bg-red', 'Menolak pengajuan ' . strtoupper($pengajuan->jenis_transaksi) . ' tunai senilai Rp ' . number_format($pengajuan->nominal, 0, ',', '.') . ' milik nasabah ' . $pengajuan->nama_nasabah);

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
     * 14. Menampilkan Halaman Form Edit User (Adaptif)
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        $nasabah = null;
        if ($user->role == 'nasabah') {
            $nasabah = DB::table('nasabahs')->where('user_id', $user->id)->first();
        }

        $kelas = DB::table('master_kelas')->orderBy('nama_kelas', 'asc')->get();
        $jurusans = DB::table('master_jurusans')->orderBy('nama_jurusan', 'asc')->get();

        return view('superadmin.edit_user', compact('user', 'nasabah', 'kelas', 'jurusans'));
    }

    /**
     * 15. Memproses Pembaruan Data User ke 2 Tabel + Pencatatan Log Audit
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
    // MENU REPORT (SUPERADMIN & ADMIN TU)
    // ============================================================

    /**
     * 16. Menampilkan Halaman Laporan Filter Bulanan, Kelas, dan Jurusan
     */
    // public function indexReport(Request $request)
    // {
    //     $data_kelas = DB::table('master_kelas')->orderBy('nama_kelas', 'asc')->get();
    //     $data_jurusan = DB::table('master_jurusans')->orderBy('nama_jurusan', 'asc')->get();

    //     $query = DB::table('users')
    //         ->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
    //         ->where('users.role', 'nasabah')
    //         ->select('users.id', 'users.name', 'nasabahs.nisn', 'nasabahs.kelas', 'nasabahs.jurusan');

    //     if ($request->filled('kelas') && $request->kelas != 'semua') {
    //         $query->where('nasabahs.kelas', $request->kelas);
    //     }
    //     if ($request->filled('jurusan') && $request->jurusan != 'semua') {
    //         $query->where('nasabahs.jurusan', $request->jurusan);
    //     }

    //     $bulan_pilihan = $request->filled('bulan') ? $request->bulan : date('m');
    //     $tahun_pilihan = $request->filled('tahun') ? $request->tahun : date('Y');

    //     $query->addSelect(DB::raw("(
    //         SELECT COALESCE(SUM(CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END), 0) 
    //         FROM pengajuans 
    //         WHERE pengajuans.user_id = users.id 
    //         AND pengajuans.status = 'disetujui'
    //         AND MONTH(pengajuans.created_at) = '$bulan_pilihan'
    //         AND YEAR(pengajuans.created_at) = '$tahun_pilihan'
    //     ) as transaksi_bulan_ini"));

    //     $laporan_nasabah = $query->orderBy('nasabahs.kelas', 'asc')
    //         ->orderBy('users.name', 'asc')
    //         ->paginate(25)
    //         ->withQueryString();

    //     return view('superadmin.report', compact('laporan_nasabah', 'data_kelas', 'data_jurusan', 'bulan_pilihan', 'tahun_pilihan'));
    // }

    // public function indexReport(Request $request)
    // {
    //     // 1. Inisialisasi Filter
    //     $bulan = $request->filled('bulan') ? $request->bulan : date('m');
    //     $tahun = $request->filled('tahun') ? $request->tahun : date('Y');
    //     $tanggal = $request->tanggal;
    //     $jenis = $request->jenis_transaksi;

    //     // 2. Data Master
    //     $data_kelas = DB::table('master_kelas')->orderBy('nama_kelas', 'asc')->get();
    //     $data_jurusan = DB::table('master_jurusans')->orderBy('nama_jurusan', 'asc')->get();

    //     // 3. Logika Filter String (Digunakan di Query Utama & Subquery)
    //     $dateFilter = $tanggal
    //         ? "AND DATE(pengajuans.created_at) = " . DB::getPdo()->quote($tanggal)
    //         : "AND MONTH(pengajuans.created_at) = " . DB::getPdo()->quote($bulan) . " AND YEAR(pengajuans.created_at) = " . DB::getPdo()->quote($tahun);

    //     $jenisFilter = ($jenis && $jenis != 'semua') ? "AND pengajuans.jenis_transaksi = " . DB::getPdo()->quote($jenis) : "";

    //     // 4. Query Utama
    //     $query = DB::table('users')
    //         ->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
    //         ->where('users.role', 'nasabah');

    //     // Filter Kelas & Jurusan
    //     if ($request->filled('kelas') && $request->kelas != 'semua') $query->where('nasabahs.kelas', $request->kelas);
    //     if ($request->filled('jurusan') && $request->jurusan != 'semua') $query->where('nasabahs.jurusan', $request->jurusan);

    //     // FILTER WAJIB: Hanya ambil nasabah yang memiliki transaksi sesuai filter
    //     $query->whereRaw("EXISTS (SELECT 1 FROM pengajuans WHERE pengajuans.user_id = users.id AND pengajuans.status = 'disetujui' $dateFilter $jenisFilter)");

    //     // 5. Select & Subquery (Disesuaikan dengan filter)
    //     $query->select('users.id', 'users.name', 'nasabahs.nisn', 'nasabahs.kelas', 'nasabahs.jurusan')
    //         ->addSelect(DB::raw("(SELECT COALESCE(SUM(CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END), 0) 
    //         FROM pengajuans 
    //         WHERE pengajuans.user_id = users.id 
    //         AND pengajuans.status = 'disetujui' $dateFilter $jenisFilter) as transaksi_bulan_ini"))
    //         ->addSelect(DB::raw("(SELECT COALESCE(SUM(nominal), 0) 
    //         FROM pengajuans 
    //         WHERE pengajuans.user_id = users.id 
    //         AND pengajuans.status = 'disetujui' 
    //         AND pengajuans.jenis_transaksi = 'tarik' 
    //         AND pengajuans.keterangan LIKE 'Biaya Admin Bulanan%' $dateFilter) as total_biaya_admin"));

    //     $laporan_nasabah = $query->orderBy('nasabahs.kelas', 'asc')
    //         ->orderBy('users.name', 'asc')
    //         ->paginate(10)
    //         ->withQueryString();

    //     return view('superadmin.report', compact(
    //         'laporan_nasabah',
    //         'data_kelas',
    //         'data_jurusan',
    //         'bulan',
    //         'tahun',
    //         'tanggal',
    //         'jenis'
    //     ));
    // }

    public function indexReport(Request $request)
    {
        // 1. Inisialisasi Filter
        $bulan = $request->filled('bulan') ? $request->bulan : date('m');
        $tahun = $request->filled('tahun') ? $request->tahun : date('Y');
        $tglAwal = $request->tanggal_awal;
        $tglAkhir = $request->tanggal_akhir;
        $jenis = $request->jenis_transaksi;

        // 2. Data Master
        $data_kelas = DB::table('master_kelas')->orderBy('nama_kelas', 'asc')->get();
        $data_jurusan = DB::table('master_jurusans')->orderBy('nama_jurusan', 'asc')->get();

        // 3. Logika Filter String
        if ($tglAwal && $tglAkhir) {
            $dateFilter = "AND DATE(pengajuans.created_at) BETWEEN " . DB::getPdo()->quote($tglAwal) . " AND " . DB::getPdo()->quote($tglAkhir);
        } else {
            $dateFilter = "AND MONTH(pengajuans.created_at) = " . DB::getPdo()->quote($bulan) . " AND YEAR(pengajuans.created_at) = " . DB::getPdo()->quote($tahun);
        }

        $jenisFilter = ($jenis && $jenis != 'semua') ? "AND pengajuans.jenis_transaksi = " . DB::getPdo()->quote($jenis) : "";

        // 4. Query Utama
        $query = DB::table('users')
            ->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
            ->where('users.role', 'nasabah');

        // Filter Kelas & Jurusan
        if ($request->filled('kelas') && $request->kelas != 'semua') $query->where('nasabahs.kelas', $request->kelas);
        if ($request->filled('jurusan') && $request->jurusan != 'semua') $query->where('nasabahs.jurusan', $request->jurusan);

        // --- LOGIKA FILTER DINAMIS ---
        // Hanya gunakan EXISTS (menyaring nasabah) jika user memilih Jenis Transaksi spesifik (Tarik/Setor)
        // Jika 'semua', maka EXISTS tidak dipasang agar nasabah Rp 0 tetap tampil
        if ($jenis && $jenis != 'semua') {
            $query->whereRaw("EXISTS (SELECT 1 FROM pengajuans WHERE pengajuans.user_id = users.id AND pengajuans.status = 'disetujui' $dateFilter $jenisFilter)");
        }

        // 5. Select & Subquery (Tetap menghitung nominal meskipun nasabah tidak punya transaksi)
        $query->select('users.id', 'users.name', 'nasabahs.nisn', 'nasabahs.kelas', 'nasabahs.jurusan')
            ->addSelect(DB::raw("(SELECT COALESCE(SUM(CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END), 0) 
                FROM pengajuans 
                WHERE pengajuans.user_id = users.id 
                AND pengajuans.status = 'disetujui' $dateFilter $jenisFilter) as transaksi_bulan_ini"))
            ->addSelect(DB::raw("(SELECT COALESCE(SUM(nominal), 0) 
                FROM pengajuans 
                WHERE pengajuans.user_id = users.id 
                AND pengajuans.status = 'disetujui' 
                AND pengajuans.jenis_transaksi = 'tarik' 
                AND pengajuans.keterangan LIKE 'Biaya Admin Bulanan%' $dateFilter) as total_biaya_admin"))
            ->addSelect(DB::raw("(SELECT jenis_transaksi 
                FROM pengajuans 
                WHERE pengajuans.user_id = users.id 
                AND pengajuans.status = 'disetujui' 
                $dateFilter 
                $jenisFilter 
                ORDER BY created_at DESC LIMIT 1) as jenis_transaksi_terakhir"));

        $laporan_nasabah = $query->orderBy('nasabahs.kelas', 'asc')
            ->orderBy('users.name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.report', compact(
            'laporan_nasabah',
            'data_kelas',
            'data_jurusan',
            'bulan',
            'tahun',
            'tglAwal',
            'tglAkhir',
            'jenis'
        ));
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
        $notifikasis = DB::table('notifications')
            ->where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

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
    // public function exportReportPDF(Request $request)
    // {
    //     // date_default_timezone_set('Asia/Jakarta'); // Paksa PHP untuk menggunakan WIB
    //     $query = DB::table('users')->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
    //         ->where('users.role', 'nasabah')
    //         ->select('users.id', 'users.name', 'nasabahs.nisn', 'nasabahs.kelas', 'nasabahs.jurusan');

    //     if ($request->filled('kelas') && $request->kelas != 'semua') $query->where('nasabahs.kelas', $request->kelas);
    //     if ($request->filled('jurusan') && $request->jurusan != 'semua') $query->where('nasabahs.jurusan', $request->jurusan);

    //     $bulan_pilihan = $request->filled('bulan') ? $request->bulan : date('m');
    //     $tahun_pilihan = $request->filled('tahun') ? $request->tahun : date('Y');

    //     $query->addSelect(DB::raw("(SELECT COALESCE(SUM(CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END), 0) FROM pengajuans WHERE pengajuans.user_id = users.id AND pengajuans.status = 'disetujui' AND MONTH(pengajuans.created_at) = '$bulan_pilihan' AND YEAR(pengajuans.created_at) = '$tahun_pilihan') as transaksi_bulan_ini"));

    //     $laporan_nasabah = $query->orderBy('nasabahs.kelas', 'asc')->orderBy('users.name', 'asc')->get();

    //     $pdf = Pdf::loadView('superadmin.report_cetak', compact('laporan_nasabah', 'bulan_pilihan', 'tahun_pilihan', 'request'));
    //     $pdf->setPaper('A4', 'portrait');
    //     return $pdf->download('Laporan_Nasabah_' . $bulan_pilihan . '_' . $tahun_pilihan . '.pdf');
    // }

    // public function exportReportPDF(Request $request)
    // {
    //     date_default_timezone_set('Asia/Jakarta');

    //     // 1. Inisialisasi Filter
    //     $bulan = $request->filled('bulan') ? $request->bulan : date('m');
    //     $tahun = $request->filled('tahun') ? $request->tahun : date('Y');
    //     $tanggal = $request->tanggal;
    //     $jenis = $request->jenis_transaksi;

    //     // 2. Base Query
    //     $query = DB::table('users')
    //         ->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
    //         ->where('users.role', 'nasabah')
    //         ->select('users.id', 'users.name', 'nasabahs.nisn', 'nasabahs.kelas', 'nasabahs.jurusan');

    //     // 3. Filter Kelas & Jurusan
    //     if ($request->filled('kelas') && $request->kelas != 'semua') $query->where('nasabahs.kelas', $request->kelas);
    //     if ($request->filled('jurusan') && $request->jurusan != 'semua') $query->where('nasabahs.jurusan', $request->jurusan);

    //     // 4. String Filter (Date & Jenis)
    //     $dateFilter = $tanggal
    //         ? "AND DATE(pengajuans.created_at) = " . DB::getPdo()->quote($tanggal)
    //         : "AND MONTH(pengajuans.created_at) = " . DB::getPdo()->quote($bulan) . " AND YEAR(pengajuans.created_at) = " . DB::getPdo()->quote($tahun);

    //     $jenisFilter = ($jenis && $jenis != 'semua') ? "AND pengajuans.jenis_transaksi = " . DB::getPdo()->quote($jenis) : "";

    //     // 5. Filter Wajib (Agar nasabah tanpa transaksi hilang)
    //     $query->whereRaw("EXISTS (SELECT 1 FROM pengajuans WHERE pengajuans.user_id = users.id AND pengajuans.status = 'disetujui' $dateFilter $jenisFilter)");

    //     // 6. Select dengan Filter Lengkap (Subqueries)
    //     $query->addSelect(DB::raw("(SELECT COALESCE(SUM(CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END), 0) 
    //     FROM pengajuans 
    //     WHERE pengajuans.user_id = users.id 
    //     AND pengajuans.status = 'disetujui' 
    //     $dateFilter $jenisFilter) as transaksi_bulan_ini"));

    //     $query->addSelect(DB::raw("(SELECT COALESCE(SUM(nominal), 0) 
    //     FROM pengajuans 
    //     WHERE pengajuans.user_id = users.id 
    //     AND pengajuans.status = 'disetujui' 
    //     AND pengajuans.jenis_transaksi = 'tarik' 
    //     AND pengajuans.keterangan LIKE 'Biaya Admin Bulanan%' 
    //     $dateFilter $jenisFilter) as total_biaya_admin"));

    //     $laporan_nasabah = $query->orderBy('nasabahs.kelas', 'asc')->orderBy('users.name', 'asc')->get();

    //     // 7. Siapkan variabel untuk View
    //     $bulan_pilihan = $bulan;
    //     $tahun_pilihan = $tahun;

    //     // 8. Load View PDF
    //     $pdf = Pdf::loadView('superadmin.report_cetak', compact('laporan_nasabah', 'bulan_pilihan', 'tahun_pilihan'));

    //     return $pdf->download('Laporan_Nasabah_' . ($tanggal ?? $bulan_pilihan . '_' . $tahun_pilihan) . '.pdf');
    // }

    public function exportReportPDF(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        // 1. Inisialisasi Filter
        $bulan = $request->filled('bulan') ? $request->bulan : date('m');
        $tahun = $request->filled('tahun') ? $request->tahun : date('Y');
        $tglAwal = $request->tanggal_awal;
        $tglAkhir = $request->tanggal_akhir;
        $jenis = $request->jenis_transaksi;

        // 2. Base Query
        $query = DB::table('users')
            ->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
            ->where('users.role', 'nasabah')
            ->select('users.id', 'users.name', 'nasabahs.nisn', 'nasabahs.kelas', 'nasabahs.jurusan');

        // 3. Filter Kelas & Jurusan
        if ($request->filled('kelas') && $request->kelas != 'semua') $query->where('nasabahs.kelas', $request->kelas);
        if ($request->filled('jurusan') && $request->jurusan != 'semua') $query->where('nasabahs.jurusan', $request->jurusan);

        // 4. String Filter (Date Range & Jenis)
        // Logika: Jika ada rentang tanggal, pakai BETWEEN. Jika tidak, pakai bulan/tahun.
        if ($tglAwal && $tglAkhir) {
            $dateFilter = "AND DATE(pengajuans.created_at) BETWEEN " . DB::getPdo()->quote($tglAwal) . " AND " . DB::getPdo()->quote($tglAkhir);
        } else {
            $dateFilter = "AND MONTH(pengajuans.created_at) = " . DB::getPdo()->quote($bulan) . " AND YEAR(pengajuans.created_at) = " . DB::getPdo()->quote($tahun);
        }

        $jenisFilter = ($jenis && $jenis != 'semua') ? "AND pengajuans.jenis_transaksi = " . DB::getPdo()->quote($jenis) : "";

        // 5. Filter Wajib (Agar nasabah tanpa transaksi hilang)
        $query->whereRaw("EXISTS (SELECT 1 FROM pengajuans WHERE pengajuans.user_id = users.id AND pengajuans.status = 'disetujui' $dateFilter $jenisFilter)");

        // 6. Select dengan Filter Lengkap (Subqueries)
        $query->addSelect(DB::raw("(SELECT COALESCE(SUM(CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END), 0) 
        FROM pengajuans 
        WHERE pengajuans.user_id = users.id 
        AND pengajuans.status = 'disetujui' 
        $dateFilter $jenisFilter) as transaksi_bulan_ini"));

        $query->addSelect(DB::raw("(SELECT COALESCE(SUM(nominal), 0) 
        FROM pengajuans 
        WHERE pengajuans.user_id = users.id 
        AND pengajuans.status = 'disetujui' 
        AND pengajuans.jenis_transaksi = 'tarik' 
        AND pengajuans.keterangan LIKE 'Biaya Admin Bulanan%' 
        $dateFilter $jenisFilter) as total_biaya_admin"));

        $laporan_nasabah = $query->orderBy('nasabahs.kelas', 'asc')->orderBy('users.name', 'asc')->get();

        // 7. Siapkan variabel untuk View
        $bulan_pilihan = $bulan;
        $tahun_pilihan = $tahun;

        // 8. Load View PDF
        $pdf = Pdf::loadView('superadmin.report_cetak', compact(
            'laporan_nasabah',
            'bulan_pilihan',
            'tahun_pilihan',
            'tglAwal',
            'tglAkhir'
        ));

        // Membuat nama file lebih deskriptif berdasarkan filter yang aktif
        $fileName = 'Laporan_Nasabah_' . ($tglAwal ? $tglAwal . '_sd_' . $tglAkhir : $bulan_pilihan . '_' . $tahun_pilihan) . '.pdf';

        return $pdf->download($fileName);
    }
    /**
     * 21. Ekspor Laporan ke Excel
     */
    // public function exportReportExcel(Request $request)
    // {
    //     $query = DB::table('users')->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
    //         ->where('users.role', 'nasabah')
    //         ->select('users.id', 'users.name', 'nasabahs.nisn', 'nasabahs.kelas', 'nasabahs.jurusan');

    //     if ($request->filled('kelas') && $request->kelas != 'semua') $query->where('nasabahs.kelas', $request->kelas);
    //     if ($request->filled('jurusan') && $request->jurusan != 'semua') $query->where('nasabahs.jurusan', $request->jurusan);

    //     $bulan_pilihan = $request->filled('bulan') ? $request->bulan : date('m');
    //     $tahun_pilihan = $request->filled('tahun') ? $request->tahun : date('Y');

    //     $query->addSelect(DB::raw("(SELECT COALESCE(SUM(CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END), 0) FROM pengajuans WHERE pengajuans.user_id = users.id AND pengajuans.status = 'disetujui' AND MONTH(pengajuans.created_at) = '$bulan_pilihan' AND YEAR(pengajuans.created_at) = '$tahun_pilihan') as transaksi_bulan_ini"));

    //     $laporan_nasabah = $query->orderBy('nasabahs.kelas', 'asc')->orderBy('users.name', 'asc')->get();

    //     header("Content-type: application/vnd-ms-excel");
    //     header("Content-Disposition: attachment; filename=Laporan_Nasabah_" . $bulan_pilihan . "_" . $tahun_pilihan . ".xls");

    //     return view('superadmin.report_cetak', compact('laporan_nasabah', 'bulan_pilihan', 'tahun_pilihan', 'request'));
    // }

    public function exportReportExcel(Request $request)
    {
        $query = DB::table('users')->join('nasabahs', 'users.id', '=', 'nasabahs.user_id')
            ->where('users.role', 'nasabah')
            ->select('users.id', 'users.name', 'nasabahs.nisn', 'nasabahs.kelas', 'nasabahs.jurusan');

        if ($request->filled('kelas') && $request->kelas != 'semua') $query->where('nasabahs.kelas', $request->kelas);
        if ($request->filled('jurusan') && $request->jurusan != 'semua') $query->where('nasabahs.jurusan', $request->jurusan);

        $bulan_pilihan = $request->filled('bulan') ? $request->bulan : date('m');
        $tahun_pilihan = $request->filled('tahun') ? $request->tahun : date('Y');

        // 1. Subquery Transaksi (Setor - Tarik)
        $query->addSelect(DB::raw("(SELECT COALESCE(SUM(CASE WHEN jenis_transaksi = 'setor' THEN nominal WHEN jenis_transaksi = 'tarik' THEN -nominal ELSE 0 END), 0) 
        FROM pengajuans 
        WHERE pengajuans.user_id = users.id 
        AND pengajuans.status = 'disetujui' 
        AND MONTH(pengajuans.created_at) = '$bulan_pilihan' 
        AND YEAR(pengajuans.created_at) = '$tahun_pilihan') as transaksi_bulan_ini"));

        // 2. Subquery Biaya Admin (KOLOM INI YANG TADI MENGAKIBATKAN ERROR)
        $query->addSelect(DB::raw("(SELECT COALESCE(SUM(nominal), 0) 
        FROM pengajuans 
        WHERE pengajuans.user_id = users.id 
        AND pengajuans.status = 'disetujui' 
        AND pengajuans.jenis_transaksi = 'tarik' 
        AND pengajuans.keterangan LIKE 'Biaya Admin Bulanan%' 
        AND MONTH(pengajuans.created_at) = '$bulan_pilihan' 
        AND YEAR(pengajuans.created_at) = '$tahun_pilihan') as total_biaya_admin"));

        $laporan_nasabah = $query->orderBy('nasabahs.kelas', 'asc')->orderBy('users.name', 'asc')->get();

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan_Nasabah_" . $bulan_pilihan . "_" . $tahun_pilihan . ".xls");

        return view('superadmin.report_cetak', compact('laporan_nasabah', 'bulan_pilihan', 'tahun_pilihan', 'request'));
    }
}
