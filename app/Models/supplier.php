<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{

    protected $table = 'suppliers';

    protected $fillable = [
        'nama_supplier',
        'alamat',
        'no_telepon',
        'api_url',
        'api_key',
        'status',
    ];

    //Mengatur tipe data kolom status
    protected $casts = [
        'status' => 'boolean',
    ];

    //satu supplier dapat memiliki banyak riwayat harga bahan
    public function supplierPrices(): HasMany
    {
        return $this->hasMany(SupplierPrice::class, 'supplier_id');
    }
}
