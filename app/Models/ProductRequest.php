<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRequest extends Model
{
    protected $fillable = [
        'store_id',
        'product_id',
        'warehouse_id',
        'storage_location_id',
        'quantity_requested',
        'quantity_verified',
        'status',
        'operator_id',
        'admin_id',
        'verification_notes',
        'rejection_reason',
        'verified_at',
        'approved_at',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Status: pending, verified, approved, rejected, shipped, delivered

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function storageLocation()
    {
        return $this->belongsTo(StorageLocation::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function shipment()
    {
        return $this->hasOne(ProductRequestShipment::class);
    }

    // Scope untuk pending requests (status pending, belum ada warehouse_id)
    public function scopePending($query)
    {
        return $query->where('status', 'pending')->whereNull('warehouse_id');
    }

    // Scope untuk requests yang perlu diverifikasi warehouse (pending, sudah punya warehouse_id, belum ada storage_location_id)
    public function scopeNeedsWarehouseSelection($query)
    {
        return $query->where('status', 'pending')->whereNotNull('warehouse_id')->whereNull('storage_location_id');
    }

    // Scope untuk requests yang perlu diverifikasi operator (status pending, sudah punya storage_location_id)
    public function scopeNeedsVerification($query)
    {
        return $query->where('status', 'pending')->whereNotNull('storage_location_id')->whereNull('operator_id');
    }

    // Scope untuk requests yang sudah diverifikasi, menunggu approval admin
    public function scopeAwaitingApproval($query)
    {
        return $query->where('status', 'verified')->whereNull('admin_id');
    }

    // Scope untuk approved requests siap dikirim
    public function scopeReadyToShip($query)
    {
        return $query->where('status', 'approved')->whereNull('shipped_at');
    }
}

