<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Nasabah;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class SiswaImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    public function model(array $row)
    {
        // dd($row);
        // 1. Ambil data dari array $row
        // Pastikan nama key di sini sama dengan judul kolom di file Excel (lowercase)
        // Menggunakan no_rek sebagai pengganti nisn jika nisn belum ada di Excel
        $nisn    = $row['nisn'] ?? $row['no_rek'] ?? null;
        $nama    = $row['nama'] ?? 'Tanpa Nama';
        $no_rek  = $row['no_rek'] ?? null;
        $kelas   = $row['kelas'] ?? '-';
        $jurusan = $row['jurusan'] ?? '-';
        $no_telp = $row['no_telp'] ?? '-';

        // Jika NISN tidak ada, skip baris ini karena NISN adalah kunci login
        if (!$nisn) {
            return null;
        }

        // Cek apakah user dengan username (NISN) ini sudah ada di tabel users
        if (User::where('username', $nisn)->exists()) {
            return null;
        }

        // 2. Buat User
        $user = User::create([
            'name'     => $nama,
            'username' => $nisn, // Login menggunakan NISN
            'email'    => $nisn . '@minibank.local',
            'password' => Hash::make('password123'),
            'role'     => 'nasabah',
        ]);

        // 3. Simpan ke Nasabah
        return new Nasabah([
            'user_id'        => $user->id,
            'nisn'           => $nisn,
            'nomor_rekening' => $no_rek, // Sesuai dengan nama kolom di database kamu
            'kelas'          => $kelas,
            'jurusan'        => $jurusan,
            'saldo_tabungan' => 0,
            'no_telp'        => $no_telp,
        ]);
    }
}
