<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; // Panggil Controller di bagian atas

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

// 1. Halaman Login Utama (Awal Aplikasi)
Route::get('/login', function () {
    return view('login');
});

// 2. Route Halaman Dashboard Utama
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard.index'); // <-- Memberi nama route;

// 3. Route Halaman mastermurid 
Route::get('/master-murid', function () {
    return view('master_murid');
})->name('murid.index'); // <-- Memberi nama route;

// 4. Route Detail murid 
Route::get('/detail-murid', function () {
    return view('detail_murid');
});

// 5. Route logout (Method POST)
Route::post('/logout', function () {
    // Menendang kembali ke rute awal (Halaman Login)
    return redirect('/login');
});
