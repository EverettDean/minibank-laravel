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
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            // Jembatan ke tabel users untuk tahu nasabah mana yang mengajukan
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->enum('jenis_transaksi', ['setor', 'tarik']); // Pilihan jenis pengajuan
            $table->bigInteger('nominal');                       // Jumlah uang yang diajukan
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending'); // Status persetujuan Admin
            $table->text('keterangan')->nullable();              // Catatan tambahan jika diperlukan
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
        Schema::dropIfExists('pengajuans');
    }
};
