<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeDetail extends Model
{
    protected $table = 'recipe_details';

    protected $fillable = [
        'recipe_id',
        'bahan_id',
        'jumlah',
    ];

    protected $casts = [
        'jumlah' => 'decimal:3',
    ];

    //detail bahan dimiliki oleh satu resep
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class, 'recipe_id');
    }

    //detail resep menggunakan satu bahan
    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }
}