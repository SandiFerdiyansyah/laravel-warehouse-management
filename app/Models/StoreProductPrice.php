<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreProductPrice extends Model
{
    protected $table = 'store_product_prices';

    protected $fillable = [
        'store_id',
        'product_id',
        'selling_price',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
    ];

    /**
     * Get the store
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
