<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerpanjanganPesanan extends Model
{
    protected $fillable = [
        'pesanan_id',
        'user_id',
        'durasi_bulan',
        'total_bayar',
        'status_pembayaran',
        'kode_tiket',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'durasi_bulan' => 'integer',
            'total_bayar' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
