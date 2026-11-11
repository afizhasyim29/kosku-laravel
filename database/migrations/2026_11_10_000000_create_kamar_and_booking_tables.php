<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamars', function (Blueprint $table) {
            $table->id();
            $table->string('tipe_kamar', 100);
            $table->string('nomor_kamar', 30)->unique();
            $table->text('fasilitas')->nullable();
            $table->decimal('harga', 12, 2);
            $table->string('status', 30)->default('tersedia');
            $table->timestamps();
        });

        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('kamar_id')->constrained('kamars');
            $table->date('tanggal_masuk');
            $table->unsignedSmallInteger('durasi_bulan');
            $table->decimal('total_bayar', 12, 2);
            $table->string('status_pembayaran', 30)->default('menunggu');
            $table->string('kode_tiket', 100)->unique();
            $table->timestamps();

            $table->index(['user_id', 'status_pembayaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanans');
        Schema::dropIfExists('kamars');
    }
};
