<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CafeTable extends Model
{
    protected $table = 'cafe_tables';

    protected $fillable = [
        'nomor_meja',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'cafe_table_id');
    }
}
