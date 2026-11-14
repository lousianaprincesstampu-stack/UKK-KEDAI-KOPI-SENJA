<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPrice extends Model
{

    protected $table = 'supplier_prices';

    protected $fillable = [
        'supplier_id',
        'bahan_id',
        'harga',
        'tanggal_harga',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'tanggal_harga' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }
}
