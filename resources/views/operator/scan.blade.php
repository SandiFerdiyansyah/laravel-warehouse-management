@extends('layouts.operator')

@section('title', 'Scan QR Code')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Scan QR Code</h1>
    <a href="{{ route('operator.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
    </a>
</div>

<script>
    // Storage locations data for filtering
    window.allStorageLocations = {!! json_encode($storageLocations->map(function($loc) {
        return [
            'id' => $loc->id,
            'code' => $loc->location_code,
            'capacity' => $loc->capacity,
            'is_filled' => $loc->is_filled
        ];
    })->values()) !!};
</script>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Scanner Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Product Scanner</h2>
        
        <form method="POST" action="{{ route('operator.scan.process') }}" id="scan-form">
            @csrf
            
            <div class="mb-4">
                <label for="qr_code" class="block text-sm font-medium text-gray-700 mb-2">QR Code / SKU</label>
                <div class="flex space-x-2">
                    <input type="text" id="qr_code" name="qr_code" required
                           placeholder="Enter QR code or SKU manually"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                           onkeyup="verifyProduct(this.value)">
                    <button type="button" onclick="startCamera()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                @error('qr_code')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Camera View -->
            <div id="camera-container" class="hidden mb-4">
                <video id="qr-video" class="w-full rounded-lg border"></video>
                <div class="mt-2 flex space-x-2">
                    <button type="button" onclick="stopCamera()" class="flex-1 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        <i class="fas fa-stop"></i> Stop Camera
                    </button>
                    <button type="button" onclick="captureQR()" class="flex-1 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        <i class="fas fa-camera"></i> Capture
                    </button>
                </div>
            </div>

            <!-- Product Verification -->
            <div id="product-verification" class="hidden mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="font-medium text-blue-900 mb-2">Product Verified</h3>
                <div id="product-details"></div>
            </div>

            <div class="mb-4">
                <label for="storage_location_id" class="block text-sm font-medium text-gray-700 mb-2">Storage Location *</label>
                <select id="storage_location_id" name="storage_location_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Select Storage Location</option>
                </select>
                <p id="location-capacity" class="text-xs text-gray-600 mt-2"></p>
                @error('storage_location_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @if(request('location_code'))
                    <p class="text-xs text-green-600 mt-1">Pre-selected: {{ request('location_code') }}</p>
                @endif
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="type" value="in" checked class="form-radio">
                        <span class="ml-2">Store (In)</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="type" value="out" class="form-radio">
                        <span class="ml-2">Remove (Out)</span>
                    </label>
                    <div class="ml-4">
                        <input type="number" name="quantity" min="1" value="1" class="w-24 px-2 py-1 border rounded">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700" id="submit-btn" disabled>
                <i class="fas fa-plus-circle mr-2"></i>Store Product
            </button>
        </form>
    </div>

    <!-- Instructions & Recent Scans -->
    <div class="space-y-6">
        <!-- Instructions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Instructions</h2>
            <div class="space-y-3">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-green-600 font-bold text-sm">1</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Scan QR Code</p>
                        <p class="text-xs text-gray-600">Use camera or enter QR code/SKU manually</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-green-600 font-bold text-sm">2</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Verify Product</p>
                        <p class="text-xs text-gray-600">System will verify product details automatically</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-green-600 font-bold text-sm">3</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Select Location</p>
                        <p class="text-xs text-gray-600">Choose an empty storage location</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-green-600 font-bold text-sm">4</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Store Product</p>
                        <p class="text-xs text-gray-600">Click store to complete the process</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Tips -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h3 class="font-medium text-yellow-900 mb-2">
                <i class="fas fa-lightbulb mr-2"></i>Quick Tips
            </h3>
            <ul class="text-sm text-yellow-800 space-y-1">
                <li>• Ensure good lighting when using camera</li>
                <li>• Hold QR code steady and centered</li>
                <li>• Only select empty storage locations</li>
                <li>• Admin will verify and update stock levels</li>
                <li>• Check location code before confirming</li>
            </ul>
        </div>

        <!-- Recent Scans -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Recent Scans</h2>
            <div id="recent-scans" class="space-y-3">
                <div class="text-gray-500 text-center py-4">No recent scans</div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center h-full">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2">Product Stored Successfully!</h3>
                <p class="text-gray-600 mb-4">The product has been scanned and stored. Admin verification is pending for stock update.</p>
                <button onclick="closeSuccessModal()" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                    Continue Scanning
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let stream = null;
let verifiedProduct = null;

function startCamera() {
    const video = document.getElementById('qr-video');
    const container = document.getElementById('camera-container');
    
    container.classList.remove('hidden');
    
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(function(mediaStream) {
            stream = mediaStream;
            video.srcObject = stream;
            video.play();
        })
        .catch(function(err) {
            console.log('Camera access denied:', err);
            alert('Camera access denied. Please enter QR code manually.');
        });
}

function stopCamera() {
    const video = document.getElementById('qr-video');
    const container = document.getElementById('camera-container');
    
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    
    video.srcObject = null;
    container.classList.add('hidden');
}

function captureQR() {
    // Simulate QR code capture
    const mockQRCode = 'PRD-' + Math.random().toString(36).substr(2, 9).toUpperCase();
    document.getElementById('qr_code').value = mockQRCode;
    verifyProduct(mockQRCode);
    stopCamera();
}

function verifyProduct(qrCode) {
    if (!qrCode) {
        document.getElementById('product-verification').classList.add('hidden');
        document.getElementById('submit-btn').disabled = true;
        return;
    }

    fetch('/operator/scan/verify', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ qr_code: qrCode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            verifiedProduct = data.product;
            showProductVerification(data.product);
            document.getElementById('submit-btn').disabled = false;
        } else {
            document.getElementById('product-verification').classList.add('hidden');
            document.getElementById('submit-btn').disabled = true;
            alert('Product not found. Please check the QR code or SKU.');
        }
    })
    .catch(error => {
        console.log('Error verifying product:', error);
        document.getElementById('product-verification').classList.add('hidden');
        document.getElementById('submit-btn').disabled = true;
    });
}

function showProductVerification(product) {
    const verificationDiv = document.getElementById('product-verification');
    const detailsDiv = document.getElementById('product-details');
    
    detailsDiv.innerHTML = `
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-blue-700">Product Name</p>
                <p class="font-medium">${product.name}</p>
            </div>
            <div>
                <p class="text-xs text-blue-700">SKU</p>
                <p class="font-medium">${product.sku}</p>
            </div>
            <div>
                <p class="text-xs text-blue-700">Category</p>
                <p class="font-medium">${product.category}</p>
            </div>
            <div>
                <p class="text-xs text-blue-700">Supplier</p>
                <p class="font-medium">${product.supplier}</p>
            </div>
            <div>
                <p class="text-xs text-blue-700 mt-2">Current Stock</p>
                <p class="font-medium">${product.stock}</p>
            </div>
        </div>
    `;
    
    verificationDiv.classList.remove('hidden');
    verifiedProduct.stock = product.stock;
}

function closeSuccessModal() {
    document.getElementById('success-modal').classList.add('hidden');
    // Reset form
    document.getElementById('scan-form').reset();
    document.getElementById('product-verification').classList.add('hidden');
    document.getElementById('submit-btn').disabled = true;
    verifiedProduct = null;
}

function loadRecentScans() {
    fetch('/operator/recent-scans')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('recent-scans');
            if (data.length > 0) {
                container.innerHTML = data.map(scan => `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <div>
                            <p class="font-medium">${scan.product_name}</p>
                            <p class="text-sm text-gray-500">${scan.type.toUpperCase()} • ${scan.quantity} • ${scan.location_code ? scan.location_code + ' • ' : ''}${scan.timestamp}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="${scan.approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'} px-2 py-1 rounded text-xs">${scan.approved ? 'Approved' : 'Pending'}</span>
                            <button onclick="deleteMovement(${scan.id})" class="text-red-600 text-sm">Delete</button>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(error => console.log('Error loading recent scans:', error));
}

function deleteMovement(id) {
    if (!confirm('Delete this scan/movement?')) return;
    const baseUrl = "{{ url('/operator/movements') }}";
    const url = baseUrl + '/' + id;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-HTTP-Method-Override': 'DELETE',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    }).then(async res => {
        if (res.ok) {
            loadRecentScans();
        } else {
            let text = await res.text();
            try {
                const j = JSON.parse(text);
                alert('Error deleting movement: ' + (j.message || text));
            } catch (e) {
                alert('Error deleting movement: ' + text);
            }
        }
    }).catch(err => alert('Error: ' + err));
}

// Auto-focus on QR code input
document.getElementById('qr_code').focus();

// Client-side validation helpers
function setLocationCapacityDisplay() {
    const select = document.getElementById('storage_location_id');
    const capEl = document.getElementById('location-capacity');
    const qty = parseInt(document.querySelector('input[name="quantity"]').value || '0', 10);
    const type = document.querySelector('input[name="type"]:checked').value;

    if (!select || !capEl) return;
    const opt = select.options[select.selectedIndex];
    if (opt && opt.dataset && opt.dataset.capacity) {
        const cap = parseInt(opt.dataset.capacity, 10);
        capEl.textContent = 'Capacity: ' + cap;
    } else {
        capEl.textContent = '';
    }

    validateQuantity();
}

function validateQuantity() {
    const qtyInput = document.querySelector('input[name="quantity"]');
    const qty = parseInt(qtyInput.value || '0', 10);
    const type = document.querySelector('input[name="type"]:checked').value;
    const submitBtn = document.getElementById('submit-btn');
    const capEl = document.getElementById('location-capacity');

    let valid = true;
    let msg = '';

    if (type === 'in') {
        // Must have storage selected
        const select = document.getElementById('storage_location_id');
        if (!select || !select.value) {
            valid = false;
            msg = 'Please select a storage location.';
        } else {
            const opt = select.options[select.selectedIndex];
            const cap = opt && opt.dataset ? parseInt(opt.dataset.capacity || '0', 10) : 0;
            if (qty > cap) {
                valid = false;
                msg = 'Quantity exceeds location capacity (' + cap + ').';
            }
        }
    } else {
        // out: ensure product stock available (from verifiedProduct)
        if (!verifiedProduct || typeof verifiedProduct.stock === 'undefined') {
            valid = false;
            msg = 'Verify product first.';
        } else if (qty > verifiedProduct.stock) {
            valid = false;
            msg = 'Quantity exceeds current stock (' + verifiedProduct.stock + ').';
        }
    }

    // show message near submit
    let errEl = document.getElementById('quantity-error');
    if (!errEl) {
        errEl = document.createElement('p');
        errEl.id = 'quantity-error';
        errEl.className = 'text-red-500 text-xs mt-2';
        const form = document.getElementById('scan-form');
        form.appendChild(errEl);
    }

    if (!valid) {
        errEl.textContent = msg;
        submitBtn.disabled = true;
    } else {
        errEl.textContent = '';
        // only enable submit if a product has been verified
        submitBtn.disabled = !verifiedProduct;
    }
}

// Wire events
const storageSelect = document.getElementById('storage_location_id');
if (storageSelect) storageSelect.addEventListener('change', setLocationCapacityDisplay);
const qtyInput = document.querySelector('input[name="quantity"]');
if (qtyInput) qtyInput.addEventListener('input', validateQuantity);
const typeInputs = document.querySelectorAll('input[name="type"]');
if (typeInputs) typeInputs.forEach(input => input.addEventListener('change', function() {
    // Update location select based on action type
    updateLocationSelect();
    // disable storage selection for out
    const select = document.getElementById('storage_location_id');
    if (this.value === 'out') {
        if (select) select.removeAttribute('required');
        document.getElementById('location-capacity').textContent = '';
    } else {
        if (select) select.setAttribute('required', 'required');
        setLocationCapacityDisplay();
    }
    validateQuantity();
}));

// Filter storage locations based on action type
function updateLocationSelect() {
    const typeVal = document.querySelector('input[name="type"]:checked').value;
    const select = document.getElementById('storage_location_id');
    const currentVal = select.value;

    select.innerHTML = '<option value="">Select Storage Location</option>';

    if (typeVal === 'in') {
        // Store (In): show only empty locations
        window.allStorageLocations.forEach(loc => {
            if (!loc.is_filled) {
                const opt = document.createElement('option');
                opt.value = loc.id;
                opt.dataset.capacity = loc.capacity;
                opt.textContent = loc.code + ' (Capacity: ' + loc.capacity + ')';
                select.appendChild(opt);
            }
        });
    } else {
        // Remove (Out): show only filled locations
        window.allStorageLocations.forEach(loc => {
            if (loc.is_filled) {
                const opt = document.createElement('option');
                opt.value = loc.id;
                opt.dataset.capacity = loc.capacity;
                opt.textContent = loc.code;
                select.appendChild(opt);
            }
        });
    }

    select.value = currentVal;
}

// Initialize location select on load
updateLocationSelect();

// Load recent scans on page load
loadRecentScans();

// Handle form submission success
document.getElementById('scan-form').addEventListener('submit', function(e) {
    // Form will submit normally, but we'll show success on response
});
</script>
@endsection