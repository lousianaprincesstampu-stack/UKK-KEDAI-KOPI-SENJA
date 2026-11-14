<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bahan extends Model
{
    protected $table = 'bahans';

    protected $fillable = [
        'nama_bahan',
        'kategori',
        'satuan_dasar',
        'stok',
        'kode_api',
        'harga_per_satuan',
        'harga_diperbarui',
    ];

    protected $casts = [
        'stok' => 'decimal:3',
        'harga_per_satuan' => 'decimal:2',
        'harga_diperbarui' => 'date',
    ];

    public function recipeDetails(): HasMany
    {
        return $this->hasMany(RecipeDetail::class, 'bahan_id');
    }

  
    public function stockEntries(): HasMany
    {
        return $this->hasMany(StockEntry::class, 'bahan_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'bahan_id');
    }

    public function supplierPrices(): HasMany
    {
        return $this->hasMany(SupplierPrice::class, 'bahan_id');
    }
}
