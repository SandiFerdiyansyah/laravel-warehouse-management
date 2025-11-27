@extends('layouts.supplier')

@section('title', 'Purchase Orders')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Purchase Orders</h1>
    <a href="{{ route('supplier.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
    </a>
</div>

<!-- Filter Tabs -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="flex flex-wrap space-x-2">
        <a href="{{ route('supplier.orders.index') }}" class="filter-tab px-4 py-2 rounded text-sm {{ !request('status') ? 'bg-purple-500 text-white' : 'bg-gray-200 text-gray-700' }}">
            All Orders
        </a>
        <a href="{{ route('supplier.orders.index') }}?status=pending" class="filter-tab px-4 py-2 rounded text-sm {{ request('status') == 'pending' ? 'bg-purple-500 text-white' : 'bg-gray-200 text-gray-700' }}">
            Pending ({{ $purchaseOrders->where('status', 'pending')->count() }})
        </a>
        <a href="{{ route('supplier.orders.index') }}?status=approved_supplier" class="filter-tab px-4 py-2 rounded text-sm {{ request('status') == 'approved_supplier' ? 'bg-purple-500 text-white' : 'bg-gray-200 text-gray-700' }}">
            Approved ({{ $purchaseOrders->where('status', 'approved_supplier')->count() }})
        </a>
        <a href="{{ route('supplier.orders.index') }}?status=shipped" class="filter-tab px-4 py-2 rounded text-sm {{ request('status') == 'shipped' ? 'bg-purple-500 text-white' : 'bg-gray-200 text-gray-700' }}">
            Shipped ({{ $purchaseOrders->where('status', 'shipped')->count() }})
        </a>
        <a href="{{ route('supplier.orders.index') }}?status=received" class="filter-tab px-4 py-2 rounded text-sm {{ request('status') == 'received' ? 'bg-purple-500 text-white' : 'bg-gray-200 text-gray-700' }}">
            Received ({{ $purchaseOrders->where('status', 'received')->count() }})
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b">
        <div class="flex items-center space-x-4">
            <input type="text" placeholder="Search PO numbers..." value="{{ request('search') }}" 
                   class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
            <select class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option value="">Sort by Date</option>
                <option value="desc">Newest First</option>
                <option value="asc">Oldest First</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($purchaseOrders as $po)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $po->po_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $po->admin->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ $po->purchaseOrderItems->count() }} items
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Rp. {{ number_format($po->purchaseOrderItems->sum(function($item) { return $item->quantity * $item->unit_price; }), 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($po->status)
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
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $po->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('supplier.orders.show', $po) }}" class="text-purple-600 hover:text-purple-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($po->status === 'pending')
                                    <form action="{{ route('supplier.orders.approve', $po) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to approve this purchase order?')">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Approve Order">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($po->status === 'approved_supplier')
                                    <form action="{{ route('supplier.orders.ship', $po) }}" method="POST" class="inline" id="ship-form-{{ $po->id }}">
                                        @csrf
                                        <button type="button" onclick="showShippingModal({{ $po->id }})" class="text-orange-600 hover:text-orange-900" title="Mark as Shipped">
                                            <i class="fas fa-truck"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            @if(request('status'))
                                No {{ request('status') }} orders found
                            @else
                                No purchase orders found
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($purchaseOrders->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $purchaseOrders->links() }}
        </div>
    @endif
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

            <form method="POST" id="shipping-form" onsubmit="handleShippingSubmit(event)">
                @csrf
                <input type="hidden" name="po_id" id="modal-po-id">
                
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
// ===== PO Search Functionality =====
const poSearchInput = document.querySelector('input[placeholder="Search PO numbers..."]');
const poTable = document.querySelector('table tbody');

function filterPOTable() {
    const searchTerm = poSearchInput?.value.toLowerCase() || '';
    const rows = poTable.querySelectorAll('tr');

    rows.forEach(row => {
        if (row.querySelector('td:nth-child(7)') === null) return; // Skip empty rows
        
        const poNumber = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
        const createdBy = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
        
        const matchesSearch = poNumber.includes(searchTerm) || createdBy.includes(searchTerm);
        row.style.display = matchesSearch ? '' : 'none';
    });
}

poSearchInput?.addEventListener('input', filterPOTable);

// ===== Shipping & Courier Functionality =====
const courierDays = {
    'truck': 5,
    'express': 2
};

function showShippingModal(poId) {
    document.getElementById('modal-po-id').value = poId;
    // Generate tracking number
    const trackingNumber = generateTrackingNumber();
    document.getElementById('tracking_number').value = trackingNumber;
    document.getElementById('shipping-modal').classList.remove('hidden');
}

function closeShippingModal() {
    document.getElementById('shipping-modal').classList.add('hidden');
    document.getElementById('shipping-form').reset();
    document.getElementById('tracking_number').value = '';
    document.getElementById('estimated_delivery').value = '';
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
    
    // Format as YYYY-MM-DD
    const formatted = deliveryDate.toISOString().split('T')[0];
    document.getElementById('estimated_delivery').value = formatted;
    document.getElementById('delivery-info').textContent = `Estimated: ${days} days from now`;
}

function handleShippingSubmit(event) {
    event.preventDefault();
    
    const poId = document.getElementById('modal-po-id').value;
    const courierType = document.getElementById('courier_type').value;
    const shippingNotes = document.getElementById('shipping_notes').value;
    
    const formData = new FormData();
    formData.append('courier_type', courierType);
    formData.append('shipping_notes', shippingNotes);

    fetch(`/supplier/orders/${poId}/ship`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response');
        }
        
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error || data.message || 'Server error');
            });
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeShippingModal();
            location.reload();
        } else {
            alert('Error: ' + (data.error || data.message || 'Unable to update shipping'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating shipping: ' + error.message);
    });
}
</script>
@endsection