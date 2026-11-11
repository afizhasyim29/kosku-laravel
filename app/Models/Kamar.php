<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    protected $table = 'kamars';

    protected $fillable = [
        'tipe_kamar',
        'nomor_kamar',
        'fasilitas',
        'harga',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
        ];
    }

    public function pesanans(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'kamar_id');
    }

    public function laporans(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }
}
