<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
 protected $table = 'recipes';

    protected $fillable = [
        'menu_id',
    ];
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function recipeDetails(): HasMany
    {
        return $this->hasMany(RecipeDetail::class, 'recipe_id');
    }
}
