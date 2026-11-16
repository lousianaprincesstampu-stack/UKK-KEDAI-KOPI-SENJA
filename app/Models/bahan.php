<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bahan extends Model
{
    protected $table = 'bahans';

    protected $fillable = [
        'nama_bahan',
        'kode_bahan',
        'kategori',
        'satuan_dasar',
        'stok',
        'stok_minimum',
        'kode_api',
        'harga_per_satuan',
        'harga_diperbarui',
    ];

    protected $casts = [
        'stok' => 'decimal:3',
        'stok_minimum' => 'decimal:3',
        'harga_per_satuan' => 'decimal:2',
        'harga_diperbarui' => 'date',
    ];

    // Relasi bahan dengan detail resep.
    public function recipeDetails(): HasMany
    {
        return $this->hasMany(RecipeDetail::class, 'bahan_id');
    }

    // Relasi bahan dengan catatan stok masuk.
    public function stockEntries(): HasMany
    {
        return $this->hasMany(StockEntry::class, 'bahan_id');
    }

    // Relasi bahan dengan riwayat pergerakan stok.
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'bahan_id');
    }

    // Relasi bahan dengan riwayat harga supplier.
    public function supplierPrices(): HasMany
    {
        return $this->hasMany(SupplierPrice::class, 'bahan_id');
    }
}