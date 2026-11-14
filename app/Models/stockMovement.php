<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{

    protected $table = 'stock_movements';

    protected $fillable = [
        'bahan_id',
        'tipe',
        'jumlah',
        'keterangan',
        'order_id',
        'user_id',
    ];

    protected $casts = [
        'jumlah' => 'decimal:3',
    ];

    //pergerakan stok berkaitan dengan satu bahan
    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    //pergerakan stok dapat berkaitan dengan satu pesanan
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    //pergerakan stok dapat dicatat oleh satu user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
