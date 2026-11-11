<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('tenant')->after('password');
        });

        Schema::table('pesanans', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('kode_tiket');
        });

        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kamar_id')->constrained('kamars')->cascadeOnDelete();
            $table->text('keluhan');
            $table->string('status', 30)->default('baru');
            $table->timestamp('tanggal_lapor')->useCurrent();
            $table->timestamps();

            $table->index(['status', 'tanggal_lapor']);
        });

        Schema::create('pesanan_layanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('jenis_layanan', 100);
            $table->text('catatan')->nullable();
            $table->string('status', 30)->default('baru');
            $table->timestamp('tanggal_pesan')->useCurrent();
            $table->timestamps();

            $table->index(['status', 'tanggal_pesan']);
        });

        Schema::create('perpanjangan_pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('durasi_bulan');
            $table->decimal('total_bayar', 12, 2);
            $table->string('status_pembayaran', 30)->default('menunggu_pembayaran');
            $table->string('kode_tiket', 100)->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status_pembayaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perpanjangan_pesanans');
        Schema::dropIfExists('pesanan_layanans');
        Schema::dropIfExists('laporans');

        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn('paid_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
