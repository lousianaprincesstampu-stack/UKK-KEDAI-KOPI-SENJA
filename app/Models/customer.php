<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use HasApiTokens;
    
     protected $table = 'customers';

    protected $fillable = [
        'nama',
        'no_hp',
        'password',
        'must_change_password',
    ];
     protected $hidden = [
        'password',
    ];
    protected $casts = [
        'must_change_password' => 'boolean',
    ];
 public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}