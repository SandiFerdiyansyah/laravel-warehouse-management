@extends('layouts.admin')

@section('title', 'Product Details')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Product Details</h1>
    <div class="space-x-2">
        <a href="{{ route('admin.products.scan') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
            <i class="fas fa-qrcode mr-2"></i>Scan This Product
        </a>
        <a href="{{ route('admin.products.edit', $product) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
        <a href="{{ route('admin.products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Product Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">SKU</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $product->sku }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $product->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $product->category->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Supplier</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $product->supplier->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <p class="mt-1 text-sm text-gray-900">Rp. {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                    <p class="mt-1">
                        <span class="px-2 py-1 text-xs rounded-full {{ $product->stock_quantity > 10 ? 'bg-green-100 text-green-800' : ($product->stock_quantity > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $product->stock_quantity }} units
                        </span>
                    </p>
                </div>
            </div>

            @if($product->description)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $product->description }}</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">QR Code</h2>
            <div class="text-center">
                @if($product->qr_code)
                    <img src="{{ asset('storage/' . $product->qr_code) }}" alt="QR Code" class="mx-auto mb-4" style="max-width: 200px;">
                    <p class="text-sm text-gray-600">SKU: {{ $product->sku }}</p>
                @else
                    <div class="bg-gray-100 p-8 rounded-lg">
                        <i class="fas fa-qrcode text-6xl text-gray-400"></i>
                        <p class="mt-2 text-gray-600">QR Code not available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.products.scan') }}?qr_code={{ $product->sku }}" class="block w-full bg-green-500 text-white text-center py-2 rounded hover:bg-green-600">
                    <i class="fas fa-plus mr-2"></i>Add Stock
                </a>
                <button onclick="printQRCode()" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
                    <i class="fas fa-print mr-2"></i>Print QR Code
                </button>
                <a href="{{ route('admin.products.edit', $product) }}" class="block w-full bg-yellow-500 text-white text-center py-2 rounded hover:bg-yellow-600">
                    <i class="fas fa-edit mr-2"></i>Edit Product
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4">Stock Status</h2>
            <div class="text-center">
                <div class="text-4xl font-bold {{ $product->stock_quantity > 10 ? 'text-green-600' : ($product->stock_quantity > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $product->stock_quantity }}
                </div>
                <p class="text-sm text-gray-600 mt-1">Units Available</p>
                @if($product->stock_quantity <= 10)
                    <div class="mt-3 p-2 bg-yellow-100 rounded">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Low Stock Alert
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h2 class="text-lg font-semibold mb-4">Movement History</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($product->productMovements as $movement)
                    <tr>
                        <td class="px-4 py-2 text-sm">{{ $movement->timestamp->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2">
                            @if($movement->type === 'in')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">IN</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">OUT</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm">
                            @if($movement->type === 'in')
                                <span class="text-green-600">+{{ $movement->quantity }}</span>
                            @else
                                <span class="text-red-600">-{{ $movement->quantity }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm">{{ $movement->user->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-center text-gray-500">No movement history</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function printQRCode() {
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