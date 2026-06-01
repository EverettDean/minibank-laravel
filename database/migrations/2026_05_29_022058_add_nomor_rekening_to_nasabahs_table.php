<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nasabahs', function (Blueprint $table) {
            // Menambahkan kolom nomor_rekening setelah kolom nisn
            // Dibuat nullable() sementara agar tidak error jika sudah ada data lama
            $table->string('nomor_rekening')->unique()->nullable()->after('nisn');
        });
    }

    public function down()
    {
        Schema::table('nasabahs', function (Blueprint $table) {
            $table->dropColumn('nomor_rekening');
        });
    }
};
