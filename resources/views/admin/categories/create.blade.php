@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Create New Category</h1>
    <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Back to Categories
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g., Elektronik, Furniture, Alat Tulis">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Describe the category and what types of products it contains...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Category Preview -->
        <div class="mt-6 p-4 bg-gray-50 rounded">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Preview</h3>
            <div class="bg-white p-4 rounded border">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tag text-blue-600"></i>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900" id="preview-name">{{ old('name') ?: 'Category Name' }}</div>
                        <div class="text-sm text-gray-500" id="preview-description">{{ old('description') ?: 'Category description will appear here...' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Create Category
            </button>
        </div>
    </form>
</div>

<script>
// Live preview
document.getElementById('name').addEventListener('input', function(e) {
    const preview = document.getElementById('preview-name');
    preview.textContent = e.target.value || 'Category Name';
});

document.getElementById('description').addEventListener('input', function(e) {
    const preview = document.getElementById('preview-description');
    preview.textContent = e.target.value || 'Category description will appear here...';
});
</script>
@endsection