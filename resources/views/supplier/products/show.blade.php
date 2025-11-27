@extends('layouts.supplier')

@section('title', 'Product Details - Supplier')

@section('content')
    <h2 class="text-2xl font-bold mb-4">{{ $product->name }}</h2>

    <div class="bg-white p-6 rounded shadow">
        <p><strong>SKU:</strong> {{ $product->sku }}</p>
        <p><strong>Category:</strong> {{ $product->category?->name }}</p>
            <p><strong>Price:</strong> Rp {{ number_format($product->price,0,',','.') }}</p>
            <p><strong>Stock:</strong> {{ number_format($inventoryQuantity ?? 0, 0, ',', '.') }}</p>
        <p class="mt-4"><strong>Description:</strong></p>
        <div class="mt-2">{!! nl2br(e($product->description)) !!}</div>
    </div>

    <div class="mt-4">
        <a href="{{ route('supplier.products.edit', $product) }}" class="bg-yellow-500 px-4 py-2 rounded text-white">Edit</a>
        <a href="{{ route('supplier.products.index') }}" class="ml-2 px-4 py-2 rounded border">Back to list</a>
    </div>
@endsection
