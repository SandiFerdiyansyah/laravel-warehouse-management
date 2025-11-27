@extends('layouts.store')

@section('title', $product->name . ' - Store Products')

@section('content')
<div class="mb-6">
    <a href="{{ route('store.products.index') }}" class="text-blue-600 hover:underline">&larr; Back to Products</a>
    <h1 class="text-3xl font-bold mt-2">{{ $product->name }}</h1>
</div>

<div class="grid grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Product Details</h3>
        
        <div class="space-y-3">
            <div>
                <p class="text-gray-600 text-sm">SKU</p>
                <p class="text-lg font-semibold">{{ $product->sku }}</p>
            </div>
            
            <div>
                <p class="text-gray-600 text-sm">Category</p>
                <p class="text-lg">{{ $product->category->name }}</p>
            </div>
            
            <div>
                <p class="text-gray-600 text-sm">Cost Price</p>
                <p class="text-lg font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="col-span-2 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Set Selling Price</h3>
        
        <form action="{{ route('store.products.updatePrice', $product) }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label for="selling_price" class="block text-sm font-semibold mb-2">Selling Price (Rp)</label>
                <input type="number" 
                       name="selling_price" 
                       id="selling_price"
                       step="0.01"
                       min="0"
                       value="{{ $sellingPrice?->selling_price ?? $product->price }}"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('selling_price') border-red-500 @enderror"
                       required>
                @error('selling_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if($sellingPrice)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">
                        <strong>Margin:</strong> 
                        {{ number_format(($sellingPrice->selling_price - $product->price), 0, ',', '.') }} Rp
                        ({{ number_format((($sellingPrice->selling_price - $product->price) / $product->price * 100), 1, ',', '.') }}%)
                    </p>
                </div>
            @endif

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                Save Selling Price
            </button>
        </form>
    </div>
</div>
@endsection
