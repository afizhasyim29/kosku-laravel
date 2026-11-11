<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pesanan extends Model
{
    protected $table = 'pesanans';

    protected $fillable = [
        'user_id',
        'kamar_id',
        'tanggal_masuk',
        'durasi_bulan',
        'total_bayar',
        'status_pembayaran',
        'kode_tiket',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'durasi_bulan' => 'integer',
            'total_bayar' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    public function perpanjangans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PerpanjanganPesanan::class);
    }
}
