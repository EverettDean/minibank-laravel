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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Siapa yang melakukan aktivitas
            $table->string('role');                // Role saat melakukan aktivitas (untuk mempermudah query)
            $table->string('icon');                // Untuk menyimpan class FontAwesome (fa-user-plus, fa-money-bill-transfer, dll)
            $table->string('bg_color');            // Untuk warna lingkaran ikon (bg-blue, bg-green, bg-gold)
            $table->text('deskripsi');             // Kalimat aktivitasnya
            $table->timestamps();                  // Mencatat waktu created_at secara otomatis

            // Hubungkan foreign key ke tabel users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};
