@extends('layouts.operator')

@section('title', 'Operator Dashboard')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Storage Dashboard</h1>
    <a href="{{ route('operator.scan.index') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
        <i class="fas fa-qrcode mr-2"></i>Scan Product
    </a>
</div>

<!-- Storage Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-500 rounded-full">
                <i class="fas fa-warehouse text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Locations</p>
                <p class="text-2xl font-bold">{{ $storageLocations->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-500 rounded-full">
                <i class="fas fa-check-circle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Filled</p>
                <p class="text-2xl font-bold">{{ $storageLocations->where('is_filled', true)->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-gray-400 rounded-full">
                <i class="fas fa-times-circle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Empty</p>
                <p class="text-2xl font-bold">{{ $storageLocations->where('is_filled', false)->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Storage Utilization Chart -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Storage Utilization</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-600">Utilization Rate</span>
                <span class="text-lg font-bold text-blue-600">
                    {{ $storageLocations->count() > 0 ? round(($storageLocations->where('is_filled', true)->count() / $storageLocations->count()) * 100, 1) : 0 }}%
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4">
                @php
                    $utilizationRate = $storageLocations->count() > 0 ? ($storageLocations->where('is_filled', true)->count() / $storageLocations->count()) * 100 : 0;
                @endphp
                <div class="bg-blue-500 h-4 rounded-full" style="width: {{ $utilizationRate }}%"></div>
            </div>
        </div>
        <div>
            <div class="text-sm text-gray-600 mb-2">Storage Distribution</div>
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm">Rack A</span>
                    <div class="flex-1 mx-2 bg-gray-200 rounded-full h-2">
                        @php
                            $rackA = $storageLocations->filter(function($loc) { return str_starts_with($loc->location_code, 'A'); });
                            $rackAFilled = $rackA->where('is_filled', true)->count();
                            $rateA = $rackA->count() > 0 ? ($rackAFilled / $rackA->count()) * 100 : 0;
                        @endphp
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $rateA }}%"></div>
                    </div>
                    <span class="text-sm text-gray-600">{{ $rackAFilled }}/{{ $rackA->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm">Rack B</span>
                    <div class="flex-1 mx-2 bg-gray-200 rounded-full h-2">
                        @php
                            $rackB = $storageLocations->filter(function($loc) { return str_starts_with($loc->location_code, 'B'); });
                            $rackBfilled = $rackB->where('is_filled', true)->count();
                            $rateB = $rackB->count() > 0 ? ($rackBfilled / $rackB->count()) * 100 : 0;
                        @endphp
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $rateB }}%"></div>
                    </div>
                    <span class="text-sm text-gray-600">{{ $rackBfilled }}/{{ $rackB->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm">Rack C</span>
                    <div class="flex-1 mx-2 bg-gray-200 rounded-full h-2">
                        @php
                            $rackC = $storageLocations->filter(function($loc) { return str_starts_with($loc->location_code, 'C'); });
                            $rackCfilled = $rackC->where('is_filled', true)->count();
                            $rateC = $rackC->count() > 0 ? ($rackCfilled / $rackC->count()) * 100 : 0;
                        @endphp
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $rateC }}%"></div>
                    </div>
                    <span class="text-sm text-gray-600">{{ $rackCfilled }}/{{ $rackC->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm">Rack D</span>
                    <div class="flex-1 mx-2 bg-gray-200 rounded-full h-2">
                        @php
                            $rackD = $storageLocations->filter(function($loc) { return str_starts_with($loc->location_code, 'D'); });
                            $rackDfilled = $rackD->where('is_filled', true)->count();
                            $rateD = $rackD->count() > 0 ? ($rackDfilled / $rackD->count()) * 100 : 0;
                        @endphp
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $rateD }}%"></div>
                    </div>
                    <span class="text-sm text-gray-600">{{ $rackDfilled }}/{{ $rackD->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('operator.scan.index') }}" class="bg-green-500 text-white p-4 rounded-lg hover:bg-green-600 text-center">
            <i class="fas fa-qrcode text-2xl mb-2"></i>
            <p class="font-medium">Scan QR Code</p>
            <p class="text-sm opacity-90">Add products to storage</p>
        </a>
        <button onclick="findEmptyLocation()" class="bg-blue-500 text-white p-4 rounded-lg hover:bg-blue-600 text-center">
            <i class="fas fa-search text-2xl mb-2"></i>
            <p class="font-medium">Find Empty Location</p>
            <p class="text-sm opacity-90">View available storage</p>
        </button>
    </div>
</div>

<!-- Storage Grid View -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Storage Locations Map</h2>
        <div class="flex space-x-2">
            <button onclick="filterLocations('all')" class="filter-btn px-3 py-1 rounded text-sm bg-green-500 text-white">All</button>
            <button onclick="filterLocations('filled')" class="filter-btn px-3 py-1 rounded text-sm bg-gray-200 text-gray-700">Filled</button>
            <button onclick="filterLocations('empty')" class="filter-btn px-3 py-1 rounded text-sm bg-gray-200 text-gray-700">Empty</button>
        </div>
    </div>
    
    <!-- Storage Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3" id="storage-grid">
        @foreach($storageLocations as $location)
            <div class="storage-item border-2 rounded-lg p-3 text-center cursor-pointer transition-all {{ $location->is_filled ? 'bg-green-50 border-green-300 hover:bg-green-100' : 'bg-gray-50 border-gray-300 hover:bg-gray-100' }}" 
                 data-status="{{ $location->is_filled ? 'filled' : 'empty' }}"
                 data-location-code="{{ $location->location_code }}"
                 data-location-id="{{ $location->id }}"
                 onclick="selectLocation('{{ $location->location_code }}', {{ $location->id }}, {{ $location->is_filled ? 'true' : 'false' }})">
                <div class="text-xs font-medium text-gray-600">{{ $location->location_code }}</div>
                <div class="text-lg font-bold mt-1 mb-1 {{ $location->is_filled ? 'text-green-600' : 'text-gray-400' }}">
                    @if($location->is_filled)
                        <i class="fas fa-box"></i>
                    @else
                        <i class="fas fa-box-open"></i>
                    @endif
                </div>
                <div class="text-xs text-gray-500">{{ $location->capacity }} units</div>
                @if($location->is_filled)
                    <div class="text-xs text-green-600 mt-1">Occupied</div>
                @else
                    <div class="text-xs text-blue-600 mt-1">Available</div>
                @endif
            </div>
        @endforeach
    </div>
</div>

<!-- Selected Location -->
<div id="selected-location" class="bg-white rounded-lg shadow p-6 hidden">
    <h3 class="text-lg font-semibold mb-4">Selected Location</h3>
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-medium text-blue-900" id="selected-code">-</p>
                <p class="text-sm text-blue-700" id="selected-capacity">-</p>
            </div>
            <div class="text-blue-600">
                <i class="fas fa-map-marker-alt text-2xl"></i>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <button onclick="useForScanning()" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">
            <i class="fas fa-qrcode mr-2"></i>Use for Scanning
        </button>
    </div>
</div>

<!-- Recent Scans -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold mb-4">Recent Scans</h2>
    <div id="recent-scans" class="space-y-3">
        <div class="text-gray-500 text-center py-4">No recent scans</div>
    </div>
</div>

<!-- Storage Alerts -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold mb-4">Storage Alerts</h2>
    <div class="space-y-3">
        @if($storageLocations->where('is_filled', true)->count() >= ($storageLocations->count() * 0.8))
            <div class="flex items-center p-3 bg-orange-50 border border-orange-200 rounded">
                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-exclamation-triangle text-orange-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-orange-900">Storage Almost Full</p>
                    <p class="text-xs text-orange-700">Storage utilization is above 80%. Consider organizing or expanding.</p>
                </div>
            </div>
        @endif

        @if($storageLocations->where('is_filled', false)->count() <= 5)
            <div class="flex items-center p-3 bg-yellow-50 border border-yellow-200 rounded">
                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-info-circle text-yellow-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-yellow-900">Low Available Space</p>
                    <p class="text-xs text-yellow-700">Only {{ $storageLocations->where('is_filled', false)->count() }} empty locations remaining.</p>
                </div>
            </div>
        @endif

        @if($storageLocations->where('is_filled', false)->count() > 20)
            <div class="flex items-center p-3 bg-green-50 border border-green-200 rounded">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check-circle text-green-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-green-900">Good Availability</p>
                    <p class="text-xs text-green-700">{{ $storageLocations->where('is_filled', false)->count() }} empty locations available.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
let selectedLocationId = null;
let selectedLocationCode = null;

function filterLocations(status) {
    const items = document.querySelectorAll('.storage-item');
    const buttons = document.querySelectorAll('.filter-btn');
    
    // Update button styles
    buttons.forEach(btn => {
        btn.classList.remove('bg-green-500', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    event.target.classList.remove('bg-gray-200', 'text-gray-700');
    event.target.classList.add('bg-green-500', 'text-white');
    
    // Filter items
    items.forEach(item => {
        if (status === 'all' || item.dataset.status === status) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function selectLocation(code, id, isFilled) {
    selectedLocationId = id;
    selectedLocationCode = code;
    
    // Update UI
    document.getElementById('selected-location').classList.remove('hidden');
    document.getElementById('selected-code').textContent = code;
    
    // Find capacity
    const locationElement = document.querySelector(`[data-location-id="${id}"]`);
    const capacityText = locationElement.querySelector('.text-gray-500').textContent;
    document.getElementById('selected-capacity').textContent = capacityText;
    
    // Highlight selected
    document.querySelectorAll('.storage-item').forEach(item => {
        item.classList.remove('ring-2', 'ring-blue-500');
    });
    locationElement.classList.add('ring-2', 'ring-blue-500');
    
    // Scroll to selected location
    locationElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function useForScanning() {
    if (selectedLocationId) {
        window.location.href = `/operator/scan?location_id=${selectedLocationId}&location_code=${selectedLocationCode}`;
    }
}

function findEmptyLocation() {
    filterLocations('empty');
    // Scroll to first empty location
    setTimeout(() => {
        const firstEmpty = document.querySelector('[data-status="empty"]');
        if (firstEmpty) {
            firstEmpty.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstEmpty.classList.add('ring-2', 'ring-yellow-500');
            setTimeout(() => {
                firstEmpty.classList.remove('ring-2', 'ring-yellow-500');
            }, 2000);
        }
    }, 100);
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
                            <p class="text-sm text-gray-500">${scan.location_code} • ${scan.timestamp}</p>
                        </div>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                            <i class="fas fa-check mr-1"></i>Scanned
                        </span>
                    </div>
                `).join('');
            }
        })
        .catch(error => console.log('Error loading recent scans:', error));
}

// Load recent scans on page load
loadRecentScans();

// Auto-filter to empty locations on load for better UX
setTimeout(() => {
    filterLocations('empty');
}, 500);
</script>
@endsection