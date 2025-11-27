@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Edit Category: {{ $category->name }}</h1>
    <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Back to Categories
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Category Statistics -->
        <div class="mt-6 p-4 bg-blue-50 rounded">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Category Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-3 rounded">
                    <p class="text-sm text-gray-600">Products Count</p>
                    <p class="text-lg font-semibold">{{ $category->products_count ?? 0 }}</p>
                </div>
                <div class="bg-white p-3 rounded">
                    <p class="text-sm text-gray-600">Created At</p>
                    <p class="text-lg font-semibold">{{ $category->created_at->format('Y-m-d') }}</p>
                </div>
                <div class="bg-white p-3 rounded">
                    <p class="text-sm text-gray-600">Last Updated</p>
                    <p class="text-lg font-semibold">{{ $category->updated_at->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Update Category
            </button>
        </div>
    </form>
</div>
@endsection