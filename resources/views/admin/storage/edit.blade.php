@extends('layouts.admin')

@section('title', 'Edit Storage Location')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Edit Storage Location: {{ $storageLocation->location_code }}</h1>
    <a href="{{ route('admin.storage.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Back to Storage
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.storage.update', $storageLocation) }}">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="location_code" class="block text-sm font-medium text-gray-700 mb-2">Location Code *</label>
                <input type="text" id="location_code" name="location_code" value="{{ old('location_code', $storageLocation->location_code) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('location_code')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Format: Rack-Shelf-Row (e.g., A-01-R1)</p>
            </div>

            <div>
                <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Capacity *</label>
                <input type="number" id="capacity" name="capacity" value="{{ old('capacity', $storageLocation->capacity) }}" required min="1"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('capacity')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Maximum number of units this location can hold</p>
            </div>
        </div>

        <div class="mt-6">
            <label class="flex items-center">
                <input type="checkbox" id="is_filled" name="is_filled" value="1" {{ old('is_filled', $storageLocation->is_filled) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Mark as filled</span>
            </label>
            <p class="text-xs text-gray-500 mt-1">Check this if the location is currently occupied</p>
        </div>

        <!-- Location Statistics -->
        <div class="mt-6 p-4 bg-blue-50 rounded">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Location Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-3 rounded">
                    <p class="text-sm text-gray-600">Current Status</p>
                    <p class="text-lg font-semibold">
                        @if($storageLocation->is_filled)
                            <span class="text-green-600">Filled</span>
                        @else
                            <span class="text-gray-600">Empty</span>
                        @endif
                    </p>
                </div>
                <div class="bg-white p-3 rounded">
                    <p class="text-sm text-gray-600">Created At</p>
                    <p class="text-lg font-semibold">{{ $storageLocation->created_at->format('Y-m-d') }}</p>
                </div>
                <div class="bg-white p-3 rounded">
                    <p class="text-sm text-gray-600">Last Updated</p>
                    <p class="text-lg font-semibold">{{ $storageLocation->updated_at->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>

        <!-- Location Preview -->
        <div class="mt-6 p-4 bg-gray-50 rounded">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Preview</h3>
            <div class="bg-white p-4 rounded border">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 {{ old('is_filled', $storageLocation->is_filled) ? 'bg-green-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                            <i class="fas fa-{{ old('is_filled', $storageLocation->is_filled) ? 'box' : 'box-open' }} {{ old('is_filled', $storageLocation->is_filled) ? 'text-green-600' : 'text-gray-400' }}"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900" id="preview-code">{{ old('location_code', $storageLocation->location_code) }}</div>
                            <div class="text-sm text-gray-600">Capacity: <span id="preview-capacity">{{ old('capacity', $storageLocation->capacity) }}</span> units</div>
                        </div>
                    </div>
                    <div>
                        <span class="px-2 py-1 text-xs rounded-full {{ old('is_filled', $storageLocation->is_filled) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}" id="preview-status">
                            {{ old('is_filled', $storageLocation->is_filled) ? 'Filled' : 'Empty' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 p-4 bg-yellow-50 rounded">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Quick Actions</h3>
            <div class="flex space-x-2">
                <button type="button" onclick="toggleStatus()" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                    <i class="fas fa-sync-alt mr-2"></i>Toggle Status
                </button>
                <button type="button" onclick="printLabel()" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                    <i class="fas fa-print mr-2"></i>Print Label
                </button>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.storage.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Update Location
            </button>
        </div>
    </form>
</div>

<script>
// Live preview
document.getElementById('location_code').addEventListener('input', function(e) {
    document.getElementById('preview-code').textContent = e.target.value || 'LOC-XX-XX-XX';
});

document.getElementById('capacity').addEventListener('input', function(e) {
    document.getElementById('preview-capacity').textContent = e.target.value || '0';
});

document.getElementById('is_filled').addEventListener('change', function(e) {
    const status = e.target.checked ? 'Filled' : 'Empty';
    const statusClass = e.target.checked ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
    const icon = e.target.checked ? 'fa-box text-green-600' : 'fa-box-open text-gray-400';
    const iconClass = e.target.checked ? 'bg-green-100' : 'bg-gray-100';
    
    document.getElementById('preview-status').textContent = status;
    document.getElementById('preview-status').className = `px-2 py-1 text-xs rounded-full ${statusClass}`;
    
    const iconContainer = document.querySelector('#preview-code').parentElement.previousElementSibling;
    iconContainer.className = `w-12 h-12 ${iconClass} rounded-lg flex items-center justify-center`;
    iconContainer.innerHTML = `<i class="fas fa-${icon.split(' ')[1]} ${icon.split(' ')[2]}"></i>`;
});

function toggleStatus() {
    const checkbox = document.getElementById('is_filled');
    checkbox.checked = !checkbox.checked;
    checkbox.dispatchEvent(new Event('change'));
}

function printLabel() {
    window.print();
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