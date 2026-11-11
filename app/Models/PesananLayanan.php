<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesananLayanan extends Model
{
    protected $fillable = [
        'user_id',
        'jenis_layanan',
        'catatan',
        'status',
        'tanggal_pesan',
    ];

    protected function casts(): array
    {
        return ['tanggal_pesan' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
