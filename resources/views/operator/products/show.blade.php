@extends('layouts.operator')

@section('title', 'Product Detail')

@section('content')
<div class="mb-6">
    <a href="{{ route('operator.products.index') }}" class="text-sm text-gray-600 hover:underline">&larr; Back to products</a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-start space-x-6">
        <div>
            @if($product->qr_code)
                <img src="{{ asset('storage/' . $product->qr_code) }}" alt="QR" class="w-32 h-32 object-contain">
            @else
                <div class="w-32 h-32 bg-gray-100 flex items-center justify-center">No QR</div>
            @endif
        </div>
        <div class="flex-1">
            <h2 class="text-xl font-bold">{{ $product->name }}</h2>
            <p class="text-sm text-gray-600">SKU: {{ $product->sku }}</p>
            <p class="mt-2">Category: {{ $product->category?->name ?? '-' }}</p>
            <p>Supplier: {{ $product->supplier?->name ?? '-' }}</p>
            <p class="mt-2">Price: Rp. {{ number_format($product->price, 0, ',', '.') }}</p>
            <p class="mt-2">Stock: <span class="px-2 py-1 rounded-full {{ $product->isInStock() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ $product->stock_quantity }}</span></p>
        </div>
        <div class="w-56">
            <a href="{{ route('operator.products.adjust', $product) }}" class="block bg-yellow-500 text-white px-4 py-2 rounded mb-2 text-center">Adjust Stock</a>
        </div>
    </div>

    <div class="mt-6">
        <h3 class="font-semibold">Product Movements</h3>
        <div class="mt-3">
            @if($product->productMovements && $product->productMovements->count())
                <table class="w-full text-sm">
                    <thead class="text-left text-xs text-gray-500">
                        <tr>
                            <th class="py-2">Timestamp</th>
                            <th class="py-2">Type</th>
                            <th class="py-2">Quantity</th>
                            <th class="py-2">By</th>
                            <th class="py-2">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($product->productMovements as $mv)
                            <tr>
                                <td class="py-2">{{ $mv->timestamp }}</td>
                                <td class="py-2">{{ $mv->type }}</td>
                                <td class="py-2">{{ $mv->quantity }}</td>
                                <td class="py-2">{{ $mv->user?->name ?? '-' }}</td>
                                <td class="py-2">{{ $mv->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-sm text-gray-500">No movements recorded.</div>
            @endif
        </div>
    </div>
</div>
@endsection
