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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Ditujukan untuk nasabah siapa
            $table->string('judul');               // Contoh: "Transaksi Disetujui" / "Transaksi Ditolak"
            $table->text('pesan');                 // Isi pesan detail report
            $table->boolean('is_read')->default(false); // Status apakah nasabah sudah membaca notifnya
            $table->timestamps();

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
        Schema::dropIfExists('notifications');
    }
};
