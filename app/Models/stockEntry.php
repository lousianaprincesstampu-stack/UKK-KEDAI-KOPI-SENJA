<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockEntry extends Model
{

    protected $table = 'stock_entries';

    protected $fillable = [
        'bahan_id',
        'jumlah',
        'tanggal',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'jumlah' => 'decimal:3',
        'tanggal' => 'datetime',
    ];

    //stok masuk berkaitan dengan satu bahan
    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    //stok masuk dicatat oleh satu user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
