<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_code',
        'name',
        'location',
        'description',
    ];

    /**
     * Get all storage locations in this warehouse
     */
    public function storageLocations()
    {
        return $this->hasMany(StorageLocation::class);
    }

    /**
     * Get all product requests for this warehouse
     */
    public function productRequests()
    {
        return $this->hasMany(ProductRequest::class);
    }

    /**
     * Get total stock in warehouse
     */
    public function getTotalStockAttribute()
    {
        return $this->storageLocations()->sum('quantity');
    }

    /**
     * Get stock by product in warehouse
     */
    public function getStockByProduct($productId)
    {
        return $this->storageLocations()
            ->where('product_id', $productId)
            ->sum('quantity');
    }
}
