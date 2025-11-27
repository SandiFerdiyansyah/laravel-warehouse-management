<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'storage_location_id',
        'store_id',
        'quantity',
        'status',
        'created_by',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function storageLocation()
    {
        return $this->belongsTo(StorageLocation::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
