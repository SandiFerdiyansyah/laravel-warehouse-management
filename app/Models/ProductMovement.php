<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'timestamp',
        'approved',
        'approved_by',
        'approved_at',
        'storage_location_id',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'approved' => 'boolean',
        'approved_at' => 'datetime',
        'storage_location_id' => 'integer',
    ];

    public function storageLocation()
    {
        return $this->belongsTo(StorageLocation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}