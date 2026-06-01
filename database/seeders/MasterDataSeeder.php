<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        // 1. Data Kelas (X, XI, XII)
        $kelas = [
            ['nama_kelas' => 'X', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kelas' => 'XI', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kelas' => 'XII', 'created_at' => $now, 'updated_at' => $now],
        ];

        // Kosongkan tabel dulu agar tidak dobel jika dijalankan ulang (Opsional)
        DB::table('master_kelas')->truncate();
        DB::table('master_kelas')->insert($kelas);


        // 2. Data Jurusan (DKV, MPLB, AKL, PH, BCF, PPLG)
        $jurusans = [
            ['nama_jurusan' => 'DKV', 'created_at' => $now, 'updated_at' => $now],
            ['nama_jurusan' => 'MPLB', 'created_at' => $now, 'updated_at' => $now],
            ['nama_jurusan' => 'AKL', 'created_at' => $now, 'updated_at' => $now],
            ['nama_jurusan' => 'PH', 'created_at' => $now, 'updated_at' => $now],
            ['nama_jurusan' => 'BCF', 'created_at' => $now, 'updated_at' => $now],
            ['nama_jurusan' => 'PPLG', 'created_at' => $now, 'updated_at' => $now],
        ];

        // Kosongkan tabel dulu agar tidak dobel jika dijalankan ulang (Opsional)
        DB::table('master_jurusans')->truncate();
        DB::table('master_jurusans')->insert($jurusans);
    }
}
