@extends('layouts.operator')

@section('title', 'Receive PO Items - ' . $purchaseOrder->po_number)

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Receive PO: {{ $purchaseOrder->po_number }}</h1>
    <a href="{{ route('operator.po.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Back to POs
    </a>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        {{ session('error') }}
    </div>
@endif

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
                    <label class="block text-sm font-medium text-gray-700">Supplier</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->supplier->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Items</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->purchaseOrderItems->count() }} items</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- PO Items for Scanning -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Items to Scan</h2>
            
            <div class="space-y-4">
                @foreach($purchaseOrder->purchaseOrderItems as $item)
                    <div class="border border-gray-200 rounded-lg p-4 item-card" data-po-item-id="{{ $item->id }}" data-product-id="{{ $item->product_id }}" data-quantity="{{ $item->quantity }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Product</label>
                                <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Progress</label>
                                <div class="mt-1 flex items-center space-x-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" id="progress-{{ $item->id }}" style="width: 0%"></div>
                                    </div>
                                    <span class="text-sm font-medium item-progress" id="text-{{ $item->id }}">0 / {{ $item->quantity }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Warehouse & Storage Location Selection -->
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Warehouse</label>
                                <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $mainWarehouse->name }}</p>
                                <input type="hidden" id="warehouse-{{ $item->id }}" value="{{ $mainWarehouse->id }}">
                            </div>
                            <div>
                                <label for="location-{{ $item->id }}" class="block text-sm font-medium text-gray-700">Storage Location</label>
                                <select id="location-{{ $item->id }}" class="mt-1 block w-full location-select" data-item-id="{{ $item->id }}">
                                    <option value="">-- Select Storage Location --</option>
                                </select>
                            </div>
                        </div>

                        <!-- Scan Quantity Input -->
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="quantity-{{ $item->id }}" class="block text-sm font-medium text-gray-700">Scan Quantity</label>
                                <input type="number" 
                                    id="quantity-{{ $item->id }}" 
                                    class="mt-1 block w-full scan-quantity" 
                                    data-item-id="{{ $item->id }}"
                                    min="1" 
                                    max="{{ $item->quantity }}"
                                    value="1"
                                    placeholder="Enter quantity">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Available Capacity</label>
                                <p class="mt-1 text-sm text-gray-900 font-semibold location-capacity" id="capacity-{{ $item->id }}">-</p>
                            </div>
                            <div class="flex items-end">
                                <button type="button" 
                                    class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 scan-btn"
                                    data-po-id="{{ $purchaseOrder->id }}"
                                    data-item-id="{{ $item->id }}">
                                    <i class="fas fa-barcode mr-2"></i>Scan Item
                                </button>
                            </div>
                        </div>

                        <!-- Scanned Items List -->
                        <div class="mt-4" id="scanned-{{ $item->id }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Scanned Items</label>
                            <div class="space-y-2 scanned-list"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div>
        <!-- Summary & Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Receive Summary</h2>
            
            <div class="space-y-3 mb-6">
                <div>
                    <label class="text-sm font-medium text-gray-700">Total Items</label>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $purchaseOrder->purchaseOrderItems->sum(function($i) { return $i->quantity; }) }}
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Items Received</label>
                    <p class="text-2xl font-bold text-blue-600" id="total-received">0</p>
                </div>
                <div class="h-2 bg-gray-200 rounded-full">
                    <div class="h-2 bg-green-500 rounded-full transition-all" id="overall-progress" style="width: 0%"></div>
                </div>
            </div>

            <form action="{{ route('operator.po.complete', $purchaseOrder) }}" method="POST" id="completeForm">
                @csrf
                <button type="button" 
                    onclick="markAsReceived()"
                    class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600 font-semibold"
                    id="completeBtn"
                    disabled>
                    <i class="fas fa-check-double mr-2"></i>Mark as Fully Received
                </button>
            </form>

            <p class="text-xs text-gray-500 mt-3 text-center">
                <i class="fas fa-info-circle mr-1"></i>All items must be scanned to mark as complete
            </p>

            <!-- Recent Activity -->
            <div class="mt-6 pt-6 border-t">
                <h3 class="font-semibold mb-3">Activity Log</h3>
                <div id="activityLog" class="space-y-2 text-xs max-h-64 overflow-y-auto">
                    <p class="text-gray-500">No activity yet</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const poId = @json($purchaseOrder->id);
let totalExpected = @json($purchaseOrder->purchaseOrderItems->sum(function($i) { return $i->quantity; }));
let scannedItems = {};

// Initialize scanned items object
@foreach($purchaseOrder->purchaseOrderItems as $item)
    scannedItems[@json($item->id)] = [];
@endforeach

// Use the single main warehouse for all items
const mainWarehouseId = @json($mainWarehouse->id ?? null);

// For each item, automatically load storage locations from the main warehouse
document.querySelectorAll('.item-card').forEach(card => {
    const itemId = card.dataset.poItemId;
    const productId = card.dataset.productId;
    const locationSelect = document.getElementById(`location-${itemId}`);
    const capacityEl = document.getElementById(`capacity-${itemId}`);

    if (!mainWarehouseId) {
        locationSelect.innerHTML = '<option value="">No main warehouse configured</option>';
        locationSelect.disabled = true;
        capacityEl.textContent = '-';
        return;
    }

    fetch(`{{ route('operator.po.storage-locations') }}?warehouse_id=${mainWarehouseId}&product_id=${productId}`)
        .then(r => r.text().then(text => {
            // If server returned non-JSON (HTML error page), try to surface text; otherwise parse JSON
            if (!r.ok) {
                let msg = text;
                try {
                    const obj = JSON.parse(text);
                    msg = obj.error || obj.message || text;
                } catch (e) {
                    // leave msg as raw text
                }
                throw new Error(msg || 'Error fetching locations');
            }

            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid JSON response from server');
            }
        }))
        .then(locations => {
            locationSelect.innerHTML = '<option value="">-- Select Storage Location --</option>';
            locations.forEach(loc => {
                const opt = document.createElement('option');
                opt.value = loc.id;
                opt.textContent = `${loc.location_code} (Qty: ${loc.quantity}/${loc.capacity})`;
                opt.dataset.capacity = loc.capacity;
                opt.dataset.quantity = loc.quantity;
                locationSelect.appendChild(opt);
            });
            locationSelect.disabled = false;
            capacityEl.textContent = '-';
        })
        .catch(err => {
            console.error(err);
            const display = err.message && err.message.length < 200 ? err.message : 'Error loading locations';
            locationSelect.innerHTML = `<option value="">${display}</option>`;
            locationSelect.disabled = true;
        });
});

// Storage location selection - show available capacity
document.querySelectorAll('.location-select').forEach(select => {
    select.addEventListener('change', function() {
        const itemId = this.dataset.itemId;
        const capacityEl = document.getElementById(`capacity-${itemId}`);
        
        if (!this.value) {
            capacityEl.textContent = '-';
            return;
        }

        const opt = this.options[this.selectedIndex];
        const capacity = parseInt(opt.dataset.capacity) || 0;
        const currentQty = parseInt(opt.dataset.quantity) || 0;
        const available = capacity - currentQty;

        capacityEl.textContent = `${available} / ${capacity}`;
    });
});

// Scan button click
document.querySelectorAll('.scan-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const poId = this.dataset.poId;
        const itemId = this.dataset.itemId;
        const warehouseId = document.getElementById(`warehouse-${itemId}`).value;
        const locationId = document.getElementById(`location-${itemId}`).value;
        const quantity = parseInt(document.getElementById(`quantity-${itemId}`).value) || 0;

        if (!warehouseId || !locationId || quantity < 1) {
            alert('Please fill in all fields and enter a valid quantity');
            return;
        }

        try {
            const response = await fetch('{{ route("operator.po.scan-item") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    po_id: poId,
                    po_item_id: itemId,
                    warehouse_id: warehouseId,
                    storage_location_id: locationId,
                    scanned_quantity: quantity
                })
            });

            const data = await response.json();

            if (!response.ok) {
                alert(data.error || 'Error scanning item');
                return;
            }

            // Add to scanned items
            if (!scannedItems[itemId]) scannedItems[itemId] = [];
            scannedItems[itemId].push({
                storageLocationId: locationId,
                quantity: quantity,
                timestamp: new Date().toLocaleTimeString()
            });

            // Update UI
            updateScannedList(itemId);
            updateProgress(itemId, data.po_item_received, data.po_item_remaining);
            updateOverallProgress();
            addActivityLog(`Scanned ${quantity} items for product (${data.po_item_received}/${document.querySelector(`[data-po-item-id="${itemId}"]`).dataset.quantity})`);

            // Reset input
            document.getElementById(`quantity-${itemId}`).value = '1';

            // Check if all items received
            checkCompletion();

        } catch (err) {
            console.error(err);
            alert('Error: ' + err.message);
        }
    });
});

function updateScannedList(itemId) {
    const list = document.querySelector(`#scanned-${itemId} .scanned-list`);
    list.innerHTML = '';
    
    if (!scannedItems[itemId] || scannedItems[itemId].length === 0) {
        list.innerHTML = '<p class="text-xs text-gray-500">No items scanned yet</p>';
        return;
    }

    scannedItems[itemId].forEach((item, idx) => {
        const div = document.createElement('div');
        div.className = 'bg-green-50 border border-green-200 rounded p-2 flex justify-between items-center';
        div.innerHTML = `
            <span class="text-xs">
                <strong>${item.quantity}</strong> items @ ${item.timestamp}
            </span>
            <button type="button" onclick="undoScan(${itemId}, ${idx})" class="text-red-600 hover:text-red-800 text-xs">
                <i class="fas fa-trash"></i>
            </button>
        `;
        list.appendChild(div);
    });
}

function updateProgress(itemId, received, remaining) {
    const total = received + remaining;
    const percent = (received / total) * 100;
    
    document.getElementById(`progress-${itemId}`).style.width = percent + '%';
    document.getElementById(`text-${itemId}`).textContent = `${received} / ${total}`;
}

function updateOverallProgress() {
    let totalReceived = 0;
    for (let itemId in scannedItems) {
        totalReceived += scannedItems[itemId].reduce((sum, item) => sum + item.quantity, 0);
    }

    document.getElementById('total-received').textContent = totalReceived;
    const percent = (totalReceived / totalExpected) * 100;
    document.getElementById('overall-progress').style.width = percent + '%';
}

function checkCompletion() {
    let totalReceived = 0;
    for (let itemId in scannedItems) {
        totalReceived += scannedItems[itemId].reduce((sum, item) => sum + item.quantity, 0);
    }

    const completeBtn = document.getElementById('completeBtn');
    if (totalReceived === totalExpected) {
        completeBtn.disabled = false;
        completeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        completeBtn.disabled = true;
        completeBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

function markAsReceived() {
    if (confirm('Are you sure you want to mark this PO as fully received? This will update the inventory.')) {
        document.getElementById('completeForm').submit();
    }
}

function addActivityLog(message) {
    const log = document.getElementById('activityLog');
    if (log.querySelector('p.text-gray-500')) {
        log.innerHTML = '';
    }

    const entry = document.createElement('p');
    entry.className = 'text-xs text-gray-700 bg-gray-50 p-2 rounded';
    entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
    log.insertBefore(entry, log.firstChild);

    // Keep only last 10 entries
    while (log.children.length > 10) {
        log.removeChild(log.lastChild);
    }
}

async function undoScan(itemId, scanIdx) {
    if (!confirm('Undo this scan?')) return;

    const scan = scannedItems[itemId][scanIdx];
    
    try {
        const response = await fetch('{{ route("operator.po.undo-scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                po_item_id: itemId,
                storage_location_id: scan.storageLocationId,
                quantity_to_undo: scan.quantity
            })
        });

        const data = await response.json();

        if (!response.ok) {
            alert(data.error || 'Error undoing scan');
            return;
        }

        scannedItems[itemId].splice(scanIdx, 1);
        updateScannedList(itemId);
        updateProgress(itemId, data.po_item_received, 0);
        updateOverallProgress();
        addActivityLog(`Undid scan of ${scan.quantity} items`);
        checkCompletion();

    } catch (err) {
        console.error(err);
        alert('Error: ' + err.message);
    }
}

// Initialize
checkCompletion();
</script>

@endsection
