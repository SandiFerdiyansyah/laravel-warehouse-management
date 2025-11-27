@extends('layouts.admin')

@section('title', 'Scan QR Code')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Scan QR Code</h1>
    <a href="{{ route('admin.products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Back to Products
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Scan QR Code</h2>
        
        <form method="POST" action="{{ route('admin.products.processScan') }}">
            @csrf
            
            <div class="mb-4">
                <label for="qr_code" class="block text-sm font-medium text-gray-700 mb-2">QR Code / SKU</label>
                <div class="flex space-x-2">
                    <input type="text" id="qr_code" name="qr_code" required
                           placeholder="Enter QR code or SKU manually"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="button" onclick="startCamera()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                @error('qr_code')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div id="camera-container" class="hidden mb-4">
                <video id="qr-video" class="w-full rounded-lg border"></video>
                <button type="button" onclick="stopCamera()" class="mt-2 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    <i class="fas fa-stop"></i> Stop Camera
                </button>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                <i class="fas fa-plus-circle mr-2"></i>Add to Stock
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Recent Scans</h2>
        
        <div id="recent-scans" class="space-y-3">
            <!-- Recent scans will be loaded here -->
            <div class="text-gray-500 text-center py-4">No recent scans</div>
        </div>
    </div>
</div>

<!-- QR Code Scanner Modal -->
<div id="qr-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center h-full">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-semibold mb-4">Scanning QR Code...</h3>
            <div id="qr-reader"></div>
            <button onclick="closeModal()" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
let stream = null;

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

function closeModal() {
    document.getElementById('qr-modal').classList.add('hidden');
    stopCamera();
}

// Auto-focus on QR code input
document.getElementById('qr_code').focus();

// Load recent scans via AJAX
function loadRecentScans() {
    fetch('/api/recent-scans')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('recent-scans');
            if (data.length > 0) {
                container.innerHTML = data.map(scan => `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <div>
                            <p class="font-medium">${scan.product_name}</p>
                            <p class="text-sm text-gray-500">${scan.sku} • ${scan.timestamp}</p>
                        </div>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                            +${scan.quantity}
                        </span>
                    </div>
                `).join('');
            }
        })
        .catch(error => console.log('Error loading recent scans:', error));
}

// Load recent scans on page load
loadRecentScans();
</script>
@endsection