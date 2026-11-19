<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $table = 'menus';

    protected $fillable = [
        'kode_menu',
        'nama_menu',
        'kategori',
        'harga_jual',
        'status',
        'status_stok',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function recipe(): HasOne
    {
        return $this->hasOne(Recipe::class, 'menu_id');
    }
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'menu_id');
    }
}
