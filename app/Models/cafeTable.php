<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CafeTable extends Model
{

    protected $table = 'cafe_tables';

    protected $fillable = [
        'nomor_meja',
    ];
}
