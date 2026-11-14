<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{

    protected $table = 'orders';

    protected $fillable = [
        'kode_pesanan',
        'customer_id',
        'user_id',
        'tanggal_pesanan',
        'status_pesanan',
        'status_pembayaran',
        'metode_pembayaran',
        'subtotal',
        'service',
        'pajak',
        'total',
    ];

    // Mengatur tipe data kolom
    protected $casts = [
        'tanggal_pesanan' => 'datetime',
        'subtotal' => 'decimal:2',
        'service' => 'decimal:2',
        'pajak' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'order_id');
    }
}
