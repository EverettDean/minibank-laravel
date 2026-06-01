<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController; // Panggil Controller di bagian atas
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ============================================================
// GOLONGAN A: PINTU LUAR (Bisa Diakses Tanpa Login)
// ============================================================

// Jalur Login (Tampilan dan aksi POST)
// update by dean 28052026
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.proses');

// Jalur Logout langsung ditangani oleh AuthController
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ============================================================
// GOLONGAN B: PINTU DALAM (Wajib Lolos Login / Middleware Auth)
// ============================================================
Route::middleware(['auth'])->group(function () {

    // ============================================================
    // 1. BISA DIAKSES OLEH SEMUA ROLE (Superadmin, Admin, Nasabah)
    // ============================================================

    // update by dean 28052026: Mengaktifkan dashboard dinamis via DashboardController agar data tabungan terhitung otomatis
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Fitur Ganti Password saat login pertama kali / via menu setting
    Route::get('/ganti-password', [UserController::class, 'changePasswordForm'])->name('password.change');
    Route::post('/ganti-password', [UserController::class, 'updatePassword'])->name('password.update');


    // ============================================================
    // 2. KHUSUS GRUP SUPERADMIN & ADMIN TU (Pihak Sekolah/Bank)
    // ============================================================
    Route::middleware(['role:superadmin,admin'])->group(function () {

        // update by dean 28052026: Mengarahkan rute master murid ke fungsi indexMurid agar dinamis
        Route::get('/master-murid', [SiswaController::class, 'indexMurid'])->name('murid.index');

        // Rute untuk Tambah Murid (Manual)
        Route::get('/master-murid/tambah', [UserController::class, 'createMurid'])->name('siswa.create');
        Route::post('/master-murid/simpan', [UserController::class, 'storeMurid'])->name('siswa.store');

        // Rute Detail Murid (Dinamis dengan ID)
        // update by dean 29052026
        Route::get('/master-murid/detail/{id}', [SiswaController::class, 'detailMurid'])->name('murid.detail');

        // RUTE DOWNLOAD/CETAK LAPORAN PDF (DITAMBAHKAN DI SINI)
        Route::get('/master-murid/detail/{id}/pdf', [SiswaController::class, 'downloadPDF'])->name('murid.pdf');

        Route::get('/master-murid/edit/{id}', [SiswaController::class, 'edit'])->name('murid.edit');
        Route::put('/master-murid/update/{id}', [SiswaController::class, 'update'])->name('murid.update');
        Route::delete('/master-murid/destroy/{id}', [SiswaController::class, 'destroy'])->name('murid.destroy');

        // Transaksi Petugas
        // update by dean 28052026
        Route::get('/transaksi-petugas', [UserController::class, 'indexPengajuanAdmin'])->name('admin.transaksi.index');
        Route::post('/transaksi-petugas/{id}/setujui', [UserController::class, 'setujuiTransaksi'])->name('admin.transaksi.setujui');
        Route::post('/transaksi-petugas/{id}/tolak', [UserController::class, 'tolakTransaksi'])->name('admin.transaksi.tolak');

        // Report Petugas
        // update by dean 01062026
        Route::get('/laporan-nasabah', [App\Http\Controllers\UserController::class, 'indexReport'])->name('admin.report');
        Route::get('/laporan-nasabah/pdf', [App\Http\Controllers\UserController::class, 'exportReportPDF'])->name('admin.report.pdf');
        Route::get('/laporan-nasabah/excel', [App\Http\Controllers\UserController::class, 'exportReportExcel'])->name('admin.report.excel');
    });


    // ============================================================
    // 3. KHUSUS SUPERADMIN SAJA (Manajemen Sistem)
    // ============================================================
    Route::middleware(['role:superadmin'])->group(function () {
        Route::get('/manajemen-user', [UserController::class, 'index'])->name('user.index');
        Route::get('/master-murid/tambah', [SiswaController::class, 'create'])->name('siswa.create');
        Route::post('/master-murid/simpan', [SiswaController::class, 'store'])->name('siswa.store');

        // update by dean 28052026: Rute Edit dan Update User
        Route::get('/manajemen-user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/manajemen-user/{id}', [UserController::class, 'update'])->name('user.update');
    });


    // ============================================================
    // 4. KHUSUS ROLE NASABAH SAJA (Siswa)
    // ============================================================
    Route::middleware(['role:nasabah'])->group(function () {

        // Halaman Riwayat Transaksi (Dinamis: Diarahkan ke UserController)
        Route::get('/transaksi/riwayat', [UserController::class, 'riwayatTransaksi'])->name('nasabah.transaksi');

        // Fitur Pengajuan Baru (Setor & Tarik Tunai Mandiri)
        Route::get('/transaksi/pengajuan', [UserController::class, 'createPengajuan'])->name('nasabah.pengajuan.create');
        Route::post('/transaksi/pengajuan', [UserController::class, 'storePengajuan'])->name('nasabah.pengajuan.store');

        // Halaman Pengaturan Akun Nasabah
        Route::get('/nasabah/pengaturan', function () {
            return view('nasabah.setting');
        })->name('nasabah.setting');

        // update by dean 28052026: Rute Notifikasi Nasabah
        Route::get('/nasabah/notifikasi', [UserController::class, 'indexNotifikasi'])->name('nasabah.notifikasi');
        Route::post('/nasabah/notifikasi/read-all', [UserController::class, 'markAllAsRead'])->name('nasabah.notifikasi.read');
    });
});
