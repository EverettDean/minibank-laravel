<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara spesifik
    protected $table = 'nasabahs';

    // Mendaftarkan kolom yang boleh diisi melalui Form/Database
    protected $fillable = [
        'user_id',
        'nisn',
        'nomor_rekening', // Ganti no_rek menjadi nomor_rekening
        'kelas',
        'jurusan',
        'saldo_tabungan',
        'no_telp'
    ];

    // ============================================================
    // MASUKKAN KODE HUBUNGAN BALIK DI SINI:
    // ============================================================
    /**
     * Hubungan Balik: Profil Nasabah ini dimiliki oleh salah satu User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
