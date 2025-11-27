@extends('layouts.admin')

@section('title', 'Storage Locations')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Storage Locations Management</h1>
    <a href="{{ route('admin.storage.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        <i class="fas fa-plus mr-2"></i>Add Location
    </a>
</div>

<!-- Storage Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-500 rounded-full">
                <i class="fas fa-warehouse text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Locations</p>
                <p class="text-2xl font-bold">{{ $locations->total() }}</p>
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
                <p class="text-2xl font-bold">{{ $locations->where('is_filled', true)->count() }}</p>
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
                <p class="text-2xl font-bold">{{ $locations->where('is_filled', false)->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-500 rounded-full">
                <i class="fas fa-percentage text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Utilization</p>
                <p class="text-2xl font-bold">
                    {{ $locations->count() > 0 ? round(($locations->where('is_filled', true)->count() / $locations->count()) * 100, 1) : 0 }}%
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Storage Grid View -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Storage Map</h2>
        <div class="flex space-x-2">
            <button onclick="filterLocations('all')" class="filter-btn px-3 py-1 rounded text-sm bg-blue-500 text-white">All</button>
            <button onclick="filterLocations('filled')" class="filter-btn px-3 py-1 rounded text-sm bg-gray-200 text-gray-700">Filled</button>
            <button onclick="filterLocations('empty')" class="filter-btn px-3 py-1 rounded text-sm bg-gray-200 text-gray-700">Empty</button>
        </div>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3" id="storage-grid">
        @foreach($locations as $location)
            <div class="storage-item border-2 rounded-lg p-3 text-center cursor-pointer transition-all {{ $location->is_filled ? 'bg-green-50 border-green-300 hover:bg-green-100' : 'bg-gray-50 border-gray-300 hover:bg-gray-100' }}" 
                 data-status="{{ $location->is_filled ? 'filled' : 'empty' }}"
                 onclick="viewLocation({{ $location->id }})">
                <div class="text-xs font-medium text-gray-600">{{ $location->location_code }}</div>
                <div class="text-lg font-bold mt-1 {{ $location->is_filled ? 'text-green-600' : 'text-gray-400' }}">
                    @if($location->is_filled)
                        <i class="fas fa-box"></i>
                    @else
                        <i class="fas fa-box-open"></i>
                    @endif
                </div>
                <div class="text-xs text-gray-500 mt-1">{{ $location->capacity }} units</div>
            </div>
        @endforeach
    </div>
</div>

<!-- Storage Table View -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b">
        <div class="flex items-center space-x-4">
            <input type="text" placeholder="Search locations..." class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="filterTable(this.value)">
                <option value="all">All Locations</option>
                <option value="filled">Filled Only</option>
                <option value="empty">Empty Only</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full" id="storage-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($locations as $location)
                    <tr class="hover:bg-gray-50" data-status="{{ $location->is_filled ? 'filled' : 'empty' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $location->location_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $location->capacity }} units</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($location->is_filled)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Filled
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                    <i class="fas fa-times mr-1"></i>Empty
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $location->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="toggleStatus({{ $location->id }})" class="text-yellow-600 hover:text-yellow-900" title="Toggle Status">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <a href="{{ route('admin.storage.edit', $location) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.storage.destroy', $location) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this location?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No storage locations found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($locations->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $locations->links() }}
        </div>
    @endif
</div>

<script>
function filterLocations(status) {
    const items = document.querySelectorAll('.storage-item');
    const buttons = document.querySelectorAll('.filter-btn');
    
    // Update button styles
    buttons.forEach(btn => {
        btn.classList.remove('bg-blue-500', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    event.target.classList.remove('bg-gray-200', 'text-gray-700');
    event.target.classList.add('bg-blue-500', 'text-white');
    
    // Filter items
    items.forEach(item => {
        if (status === 'all' || item.dataset.status === status) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function filterTable(status) {
    const rows = document.querySelectorAll('#storage-table tbody tr');
    const searchInput = document.querySelector('input[placeholder="Search locations..."]');
    const searchTerm = searchInput?.value.toLowerCase() || '';
    
    rows.forEach(row => {
        if (row.querySelector('td:nth-child(5)') === null) return; // Skip empty rows
        
        const locationCode = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
        const matchesStatus = status === 'all' || row.dataset.status === status;
        const matchesSearch = locationCode.includes(searchTerm);
        
        row.style.display = matchesStatus && matchesSearch ? '' : 'none';
    });
}

// Location search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[placeholder="Search locations..."]');
    const statusSelect = document.querySelector('select');
    
    searchInput?.addEventListener('input', function() {
        const currentStatus = statusSelect?.value || 'all';
        filterTable(currentStatus);
    });
});

function toggleStatus(locationId) {
    if (confirm('Are you sure you want to toggle the status of this location?')) {
        fetch(`/admin/storage/${locationId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating location status');
            }
        })
        .catch(error => {
            console.log('Error:', error);
            alert('Error updating location status');
        });
    }
}

function viewLocation(locationId) {
    // Implement location details modal
    console.log('View location:', locationId);
}
</script>
@endsection