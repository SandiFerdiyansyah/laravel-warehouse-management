@extends('layouts.store')

@section('title', 'Products - Store')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Products</h1>
    <p class="text-gray-600">Manage selling prices for your store</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold">SKU</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Product Name</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Category</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Cost Price</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Store Stock</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Selling Price</th>
                <th class="px-6 py-3 text-center text-sm font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">
                        <code class="bg-gray-100 px-2 py-1 rounded">{{ $product->sku }}</code>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('store.products.show', $product) }}" class="text-blue-600 hover:underline">
                            {{ $product->name }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $product->category->name }}</td>
                    <td class="px-6 py-4 text-sm">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @php
                            $stock = $storeStock[$product->id] ?? 0;
                        @endphp
                        @if($stock > 0)
                            <span class="text-sm font-semibold text-green-700">{{ $stock }}</span>
                        @else
                            <span class="text-sm font-semibold text-red-600">0</span>
                            <div class="text-xs text-red-500 mt-1">Out of stock — <a href="{{ route('store.product_requests.create') }}" class="underline">Request stock</a></div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold">
                        @php
                            $storePrice = $product->productPrices->first();
                        @endphp
                        @if($storePrice)
                            Rp {{ number_format($storePrice->selling_price, 0, ',', '.') }}
                        @else
                            <span class="text-gray-500 italic">Not set</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('store.products.show', $product) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                            Edit
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No products available
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
