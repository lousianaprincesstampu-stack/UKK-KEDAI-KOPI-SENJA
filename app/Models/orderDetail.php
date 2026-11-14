<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{

    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'menu_id',
        'nama_menu',
        'jumlah',
        'harga',
        'hpp',
        'subtotal',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga' => 'decimal:2',
        'hpp' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
