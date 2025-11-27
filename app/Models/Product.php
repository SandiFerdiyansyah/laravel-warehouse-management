<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'category_id',
        'supplier_id',
        'price',
        'stock_quantity',
        'qr_code',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function productMovements()
    {
        return $this->hasMany(ProductMovement::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Selling prices per store
     */
    public function productPrices()
    {
        return $this->hasMany(\App\Models\StoreProductPrice::class, 'product_id');
    }

    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }

    public function isLowStock($threshold = 10)
    {
        return $this->stock_quantity <= $threshold;
    }
}