<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    // Pastikan nama tabelnya sesuai dengan nama tabel di database kamu
    protected $table = 'pengajuans';

    // Daftar kolom yang bisa diisi via API
    protected $fillable = [
        'status',
        'nama_petugas',
        'user_id',
        'jenis_transaksi',
        'nominal'
    ];
}
