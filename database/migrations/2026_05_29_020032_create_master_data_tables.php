<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Membuat tabel master_kelas
        Schema::create('master_kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->timestamps();
        });

        // Membuat tabel master_jurusans
        Schema::create('master_jurusans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jurusan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('master_jurusans');
        Schema::dropIfExists('master_kelas');
    }
};
