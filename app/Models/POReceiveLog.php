<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POReceiveLog extends Model
{
    use HasFactory;

    protected $table = 'po_receive_logs';

    protected $fillable = [
        'po_id',
        'po_item_id',
        'operator_id',
        'storage_location_id',
        'quantity_received',
        'received_at',
        'notes',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'po_item_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function storageLocation()
    {
        return $this->belongsTo(StorageLocation::class);
    }
}
