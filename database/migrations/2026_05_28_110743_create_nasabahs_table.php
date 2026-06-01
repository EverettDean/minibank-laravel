<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nasabahs', function (Blueprint $table) {
            $table->id();

            // JEMBATAN OTOMATIS: Menghubungkan profil nasabah dengan akun login di tabel users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('nisn')->unique();                   // Tetap ada sebagai identitas unik perbankan
            $table->string('kelas');
            $table->string('jurusan');
            $table->string('no_telp');
            $table->bigInteger('saldo_tabungan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nasabahs');
    }
};
