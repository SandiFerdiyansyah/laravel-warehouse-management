@extends('layouts.operator')

@section('title', 'Products')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Products</h1>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="flex items-center space-x-4 mb-4">
        <form method="GET" class="flex-1" action="{{ route('operator.products.index') }}">
            <input type="text" name="search" placeholder="Search by name, sku, category, supplier" value="{{ request('search') }}"
                   class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
        </form>
        <div>
            <a href="#" class="bg-green-500 text-white px-4 py-2 rounded">Export CSV</a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">QR</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">SKU</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Category</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Supplier</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Stock</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Price</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            @if($product->qr_code)
                                <img src="{{ asset('storage/' . $product->qr_code) }}" alt="QR" class="w-16 h-16 object-contain">
                            @else
                                <div class="w-16 h-16 bg-gray-100 flex items-center justify-center text-xs text-gray-500">No QR</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm font-medium">{{ $product->sku }}</td>
                        <td class="px-4 py-3 text-sm">{{ $product->name }}</td>
                        <td class="px-4 py-3 text-sm">{{ $product->category?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">{{ $product->supplier?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 rounded-full {{ $product->isInStock() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }} text-xs">{{ $product->stock_quantity }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">Rp. {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('operator.products.show', $product) }}" class="text-blue-600 hover:text-blue-800 mr-2">View</a>
                            <a href="{{ route('operator.products.adjust', $product) }}" class="text-gray-600 hover:text-gray-800">Adjust</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection
