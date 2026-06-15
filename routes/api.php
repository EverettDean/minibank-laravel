<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NasabahApiController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Route Login (Bisa diakses publik untuk mendapatkan token)
Route::post('/login', [AuthController::class, 'login']);

// Route yang membutuhkan Token (Harus sudah Login)
Route::middleware('auth:sanctum')->group(function () {

    // Data User yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Fitur Nasabah
    Route::get('/nasabah', [NasabahApiController::class, 'index']);

    // Fitur Transaksi (Hanya bisa diakses jika ada token yang valid)
    Route::post('/transaksi/{id}/setujui', [NasabahApiController::class, 'setujuiTransaksi']);
});

Route::post('/logout', [AuthController::class, 'logout']);
