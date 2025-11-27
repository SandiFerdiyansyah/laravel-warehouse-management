@extends('layouts.supplier')

@section('title', 'Purchase Order Details')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Purchase Order: {{ $purchaseOrder->po_number }}</h1>
    <div class="space-x-2">
        <a href="{{ route('supplier.orders.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            <i class="fas fa-arrow-left mr-2"></i>Back to Orders
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <!-- PO Information -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Purchase Order Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">PO Number</label>
                    <p class="mt-1 text-sm text-gray-900 font-bold">{{ $purchaseOrder->po_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <p class="mt-1">
                        @switch($purchaseOrder->status)
                            @case('pending')
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                                @break
                            @case('approved_supplier')
                                <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                    <i class="fas fa-check mr-1"></i>Approved
                                </span>
                                @break
                            @case('shipped')
                                <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">
                                    <i class="fas fa-truck mr-1"></i>Shipped
                                </span>
                                @break
                            @case('received')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-double mr-1"></i>Received
                                </span>
                                @break
                        @endswitch
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created By</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->admin->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            @if($purchaseOrder->notes)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $purchaseOrder->notes }}</p>
                </div>
            @endif
        </div>

        <!-- PO Items -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Order Items</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($purchaseOrder->purchaseOrderItems as $item)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $item->product->name }}</td>
                                <td class="px-4 py-2 text-sm">{{ $item->product->sku }}</td>
                                <td class="px-4 py-2 text-sm">{{ $item->quantity }}</td>
                                <td class="px-4 py-2 text-sm">Rp. {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-sm font-medium">Rp. {{ number_format($item->getTotalPriceAttribute(), 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-4 py-2 text-right font-semibold">Grand Total:</td>
                            <td class="px-4 py-2 font-semibold">Rp. {{ number_format($purchaseOrder->purchaseOrderItems->sum(function($item) { return $item->quantity * $item->unit_price; }), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div>
        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Actions</h2>
            <div class="space-y-3">
                @if($purchaseOrder->status === 'pending')
                    <form action="{{ route('supplier.orders.approve', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Are you sure you want to approve this purchase order?')">
                        @csrf
                        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">
                            <i class="fas fa-check mr-2"></i>Approve Order
                        </button>
                    </form>
                @endif

                @if($purchaseOrder->status === 'approved_supplier')
                    <button onclick="showShippingModal()" class="w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600">
                        <i class="fas fa-truck mr-2"></i>Mark as Shipped
                    </button>
                @endif

                <button onclick="printPO()" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
                    <i class="fas fa-print mr-2"></i>Print PO
                </button>

                <button onclick="exportPDF()" class="w-full bg-purple-500 text-white py-2 rounded hover:bg-purple-600">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </button>
            </div>
        </div>

        <!-- Status Timeline -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4">Status Timeline</h2>
            <div class="space-y-3">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium">PO Created</p>
                        <p class="text-xs text-gray-500">{{ $purchaseOrder->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>

                @if($purchaseOrder->status !== 'pending')
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Approved by Supplier</p>
                            <p class="text-xs text-gray-500">{{ $purchaseOrder->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if(in_array($purchaseOrder->status, ['shipped', 'received']))
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Shipped</p>
                            <p class="text-xs text-gray-500">{{ $purchaseOrder->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if($purchaseOrder->status === 'received')
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Received</p>
                            <p class="text-xs text-gray-500">{{ $purchaseOrder->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Shipping Information (jika sudah ada) -->
        @if($purchaseOrder->tracking_number || ($purchaseOrder->isApproved() || $purchaseOrder->isShipped()))
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Shipping Information</h2>
                    @if($purchaseOrder->isApproved() && !$purchaseOrder->isShipped())
                        <button onclick="editShippingModal()" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>
                    @endif
                </div>

                @if($purchaseOrder->tracking_number)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tracking Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tracking Number</label>
                            <p class="mt-1 text-sm font-mono bg-gray-50 p-2 rounded text-gray-900">{{ $purchaseOrder->tracking_number }}</p>
                        </div>

                        <!-- Courier Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Courier Type</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($purchaseOrder->courier_type === 'truck')
                                    <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-800 text-xs">🚚 Truck (5 Days)</span>
                                @elseif($purchaseOrder->courier_type === 'express')
                                    <span class="px-2 py-1 rounded-full bg-red-100 text-red-800 text-xs">⚡ Express (2 Days)</span>
                                @endif
                            </p>
                        </div>

                        <!-- Estimated Delivery -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estimated Delivery</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->estimated_delivery?->format('Y-m-d') ?? 'Not set' }}</p>
                        </div>

                        <!-- Shipped At -->
                        @if($purchaseOrder->shipped_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Shipped At</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->shipped_at->format('Y-m-d H:i') }}</p>
                            </div>
                        @endif

                        <!-- Shipping Notes -->
                        @if($purchaseOrder->shipping_notes)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Shipping Notes</label>
                                <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $purchaseOrder->shipping_notes }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500">Shipping information not yet added. Click "Mark as Shipped" to add.</p>
                @endif
            </div>
        @endif
            <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Total Items:</span>
                    <span class="font-medium">{{ $purchaseOrder->purchaseOrderItems->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Total Quantity:</span>
                    <span class="font-medium">{{ $purchaseOrder->purchaseOrderItems->sum('quantity') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Order Value:</span>
                    <span class="font-medium">Rp. {{ number_format($purchaseOrder->purchaseOrderItems->sum(function($item) { return $item->quantity * $item->unit_price; }), 0, ',', '.') }}</span>
                </div>
                <div class="border-t pt-3">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium">Expected Delivery:</span>
                        <span class="text-sm text-gray-600">{{ $purchaseOrder->created_at->addDays(7)->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Shipping Modal -->
<div id="shipping-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center h-full">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Add Shipping Information</h3>
                <button onclick="closeShippingModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="shipping-form" onsubmit="handleShippingSubmit(event)">
                @csrf
                
                <!-- Tracking Number (Auto-generated) -->
                <div class="mb-4">
                    <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Tracking Number (Auto-generated)
                    </label>
                    <input type="text" id="tracking_number" disabled
                           class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-600"
                           placeholder="Will be generated automatically">
                    <p class="text-xs text-gray-500 mt-1">Automatically generated upon shipping</p>
                </div>

                <!-- Courier Type Selection -->
                <div class="mb-4">
                    <label for="courier_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Courier Type *
                    </label>
                    <select id="courier_type" name="courier_type" required onchange="updateEstimatedDelivery()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">-- Select Courier --</option>
                        <option value="truck">🚚 Truck (5 Days)</option>
                        <option value="express">⚡ Express (2 Days)</option>
                    </select>
                </div>

                <!-- Estimated Delivery (Auto-calculated) -->
                <div class="mb-4">
                    <label for="estimated_delivery" class="block text-sm font-medium text-gray-700 mb-2">
                        Estimated Delivery Date
                    </label>
                    <input type="date" id="estimated_delivery" name="estimated_delivery" readonly
                           class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-600">
                    <p class="text-xs text-gray-500 mt-1" id="delivery-info">Select courier to calculate date</p>
                </div>

                <!-- Shipping Notes -->
                <div class="mb-4">
                    <label for="shipping_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Shipping Notes (Optional)
                    </label>
                    <textarea id="shipping_notes" name="shipping_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                              placeholder="Additional shipping details, special handling, etc."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeShippingModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                        <i class="fas fa-truck mr-2"></i>Mark as Shipped
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const courierDays = {
    'truck': 5,
    'express': 2
};

function showShippingModal() {
    const trackingNumber = generateTrackingNumber();
    document.getElementById('tracking_number').value = trackingNumber;
    document.getElementById('shipping-modal').classList.remove('hidden');
}

function editShippingModal() {
    document.getElementById('courier_type').value = '{{ $purchaseOrder->courier_type ?? '' }}';
    if ('{{ $purchaseOrder->estimated_delivery }}') {
        document.getElementById('estimated_delivery').value = '{{ $purchaseOrder->estimated_delivery }}';
    }
    document.getElementById('shipping_notes').value = '{{ $purchaseOrder->shipping_notes ?? '' }}';
    document.getElementById('tracking_number').value = '{{ $purchaseOrder->tracking_number ?? '' }}';
    
    // Update delivery info
    const courierType = '{{ $purchaseOrder->courier_type ?? '' }}';
    if (courierType) {
        const days = courierDays[courierType];
        document.getElementById('delivery-info').textContent = `Estimated: ${days} days from now`;
    }
    
    // Show modal with delete button for edit mode
    document.getElementById('shipping-modal').classList.remove('hidden');
    
    // Add delete button if not in edit mode
    const deleteBtn = document.getElementById('delete-shipping-btn');
    if (!deleteBtn && '{{ $purchaseOrder->isApproved() }}' === '' && '{{ $purchaseOrder->isShipped() }}' === '') {
        const form = document.getElementById('shipping-form');
        const deleteButton = document.createElement('button');
        deleteButton.id = 'delete-shipping-btn';
        deleteButton.type = 'button';
        deleteButton.textContent = 'Delete';
        deleteButton.className = 'bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600';
        deleteButton.onclick = function() {
            if (confirm('Are you sure you want to delete shipping information?')) {
                deleteShipping();
            }
        };
        form.parentElement.querySelector('.flex').insertBefore(deleteButton, form.parentElement.querySelector('.flex').children[0]);
    }
}

function closeShippingModal() {
    document.getElementById('shipping-modal').classList.add('hidden');
    document.getElementById('shipping-form').reset();
    document.getElementById('tracking_number').value = '';
    document.getElementById('estimated_delivery').value = '';
    const deleteBtn = document.getElementById('delete-shipping-btn');
    if (deleteBtn) deleteBtn.remove();
}

function generateTrackingNumber() {
    const prefix = 'TRK';
    const timestamp = new Date().toISOString().slice(0, 19).replace(/[-:]/g, '');
    const random = Math.random().toString(36).substring(2, 8).toUpperCase();
    return prefix + timestamp + random;
}

function updateEstimatedDelivery() {
    const courierType = document.getElementById('courier_type').value;
    
    if (!courierType) {
        document.getElementById('estimated_delivery').value = '';
        document.getElementById('delivery-info').textContent = 'Select courier to calculate date';
        return;
    }

    const days = courierDays[courierType];
    const today = new Date();
    const deliveryDate = new Date(today.setDate(today.getDate() + days));
    
    const formatted = deliveryDate.toISOString().split('T')[0];
    document.getElementById('estimated_delivery').value = formatted;
    document.getElementById('delivery-info').textContent = `Estimated: ${days} days from now`;
}

function handleShippingSubmit(event) {
    event.preventDefault();
    
    const courierType = document.getElementById('courier_type').value;
    const shippingNotes = document.getElementById('shipping_notes').value;

    const formData = new FormData();
    formData.append('courier_type', courierType);
    formData.append('shipping_notes', shippingNotes);

    fetch(`{{ route('supplier.orders.ship', $purchaseOrder) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.message) {
            closeShippingModal();
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unable to update shipping'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating shipping status');
    });
}

function deleteShipping() {
    fetch(`{{ route('supplier.orders.deleteShipping', $purchaseOrder) }}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeShippingModal();
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unable to delete shipping'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting shipping information');
    });
}

function printPO() {
    window.print();
}

function exportPDF() {
    alert('PDF export feature will be implemented soon.');
}
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endsection