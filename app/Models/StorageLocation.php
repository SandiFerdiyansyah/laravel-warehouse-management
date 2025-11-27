<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'location_code',
        'capacity',
        'quantity',
        'is_filled',
    ];

    protected $casts = [
        'is_filled' => 'boolean',
    ];

    /**
     * Get the warehouse that owns this storage location
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the product in this storage location
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}