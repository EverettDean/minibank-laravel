<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ISI DATA AKUN SUPERADMIN DI SINI
        // 1. Akun Superadmin
        DB::table('users')->insert([
            'name' => 'SuperAdmin',
            'username' => 'superadmin',
            'email' => 'superadmin@minibank.com',
            'password' => Hash::make('admin123'), // Mengamankan password dengan enkripsi hash bcrypt
            'role' => 'superadmin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Akun Admin TU
        DB::table('users')->insert([
            'name' => 'Staff Admin',
            'username' => 'admintu',
            'email' => 'admintu@minibank.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Akun Nasabah (Sebelumnya Siswa)
        DB::table('users')->insert([
            'name' => 'Budi Santoso',
            'username' => '2024001', // Menggunakan nomor identitas/NIS sebagai username
            'email' => 'budi@minibank.com',
            'password' => Hash::make('nasabah123'), // Password disesuaikan
            'role' => 'nasabah', // Ganti menjadi nasabah
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
