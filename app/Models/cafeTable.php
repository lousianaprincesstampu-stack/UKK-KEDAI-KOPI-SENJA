<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
Use App\Models\Order;

class CafeTable extends Model
{

    protected $table = 'cafe_tables';

    protected $fillable = [
        'nomor_meja',
    ];

    public function orders():HasManny
    {
        return $this->hasMany(Order::calss, "cafe_tabele_id");
    }
}
