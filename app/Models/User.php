<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username', // <-- WAJIB TAMBAHKAN INI
        'email',
        'password',
        'role',     // <-- WAJIB TAMBAHKAN INI
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ============================================================
    // MASUKKAN KODE RELASI UTAMA DI SINI (Di dalam Model User.php):
    // ============================================================
    /**
     * Hubungan: Satu Akun User memiliki Satu Profil Nasabah
     */
    public function nasabah()
    {
        return $this->hasOne(Nasabah::class, 'user_id');
    }
}
