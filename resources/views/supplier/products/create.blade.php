@extends('layouts.supplier')

@section('title', 'Add Product - Supplier')

@section('content')
    <h2 class="text-2xl font-bold mb-4">Add New Product</h2>

    <form action="{{ route('supplier.products.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block font-semibold">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border p-2 rounded" required>
            @error('name')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Category</label>
            <select name="category_id" class="w-full border p-2 rounded">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category_id')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Price</label>
            <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="w-full border p-2 rounded" required>
            @error('price')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Stock Quantity</label>
            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" class="w-full border p-2 rounded" required>
            @error('stock_quantity')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Description</label>
            <textarea name="description" class="w-full border p-2 rounded">{{ old('description') }}</textarea>
        </div>

        <button class="bg-purple-600 text-white px-4 py-2 rounded">Create Product</button>
    </form>
@endsection
