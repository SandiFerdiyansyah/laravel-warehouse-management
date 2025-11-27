<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRequestShipment extends Model
{
    protected $fillable = [
        'product_request_id',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Status: in_transit, delivered

    public function productRequest()
    {
        return $this->belongsTo(ProductRequest::class);
    }
}
