<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'contact_person',
        'phone',
        'address',
    ];

    /**
     * Get the user associated with the store
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all selling prices for this store
     */
    public function productPrices()
    {
        return $this->hasMany(StoreProductPrice::class);
    }

    /**
     * Get all shipments sent to this store
     */
    public function shipments()
    {
        return $this->hasMany(StoreShipment::class);
    }

    /**
     * Get all product requests from this store
     */
    public function productRequests()
    {
        return $this->hasMany(StoreProductRequest::class);
    }
}
