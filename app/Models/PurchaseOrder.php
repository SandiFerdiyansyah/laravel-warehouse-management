<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'admin_id',
        'supplier_id',
        'status',
        'notes',
        'tracking_number',
        'courier_type',
        'estimated_delivery',
        'shipping_notes',
        'shipped_at',
        'received_by',
        'received_at',
    ];

    protected $casts = [
        'estimated_delivery' => 'date',
        'shipped_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'po_id');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved_supplier';
    }

    public function isShipped()
    {
        return $this->status === 'shipped';
    }

    public function isReceived()
    {
        return $this->status === 'received';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Generate tracking number
     */
    public static function generateTrackingNumber()
    {
        $prefix = 'TRK';
        $timestamp = date('YmdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        return $prefix . $timestamp . $random;
    }

    /**
     * Get estimated delivery days based on courier type
     */
    public static function getEstimatedDays($courierType)
    {
        return [
            'truck' => 5,    // 5 hari untuk truck
            'express' => 2,  // 2 hari untuk express
        ][$courierType] ?? 5;
    }

    /**
     * Calculate estimated delivery date
     */
    public function calculateEstimatedDelivery($courierType)
    {
        $days = self::getEstimatedDays($courierType);
        return now()->addDays($days)->toDateString();
    }
}