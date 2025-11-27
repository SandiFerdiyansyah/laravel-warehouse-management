@extends('layouts.admin')

@section('title', 'Supplier Details')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Supplier Details: {{ $supplier->name }}</h1>
    <div class="space-x-2">
        <a href="{{ route('admin.suppliers.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            <i class="fas fa-arrow-left mr-2"></i>Back to Suppliers
        </a>
        <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            <i class="fas fa-edit mr-2"></i>Edit Supplier
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <!-- Supplier Information -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Supplier Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Company Name</label>
                    <p class="mt-1 text-sm text-gray-900 font-bold">{{ $supplier->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Contact Person</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $supplier->contact_person }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $supplier->phone }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $supplier->email ?? 'Not provided' }}</p>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $supplier->address }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Products</label>
                    <p class="mt-1 text-lg font-semibold">{{ $supplier->products_count ?? 0 }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created At</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $supplier->created_at->format('Y-m-d') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $supplier->updated_at->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>

        <!-- Products List -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Products from this Supplier</h2>
            
            @if($supplier->products && $supplier->products->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($supplier->products->take(10) as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm">{{ $product->sku }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $product->name }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $product->category->name }}</td>
                                    <td class="px-4 py-2 text-sm">Rp. {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $product->stock_quantity > 10 ? 'bg-green-100 text-green-800' : ($product->stock_quantity > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        @if($product->stock_quantity > 10)
                                            <span class="text-green-600">In Stock</span>
                                        @elseif($product->stock_quantity > 0)
                                            <span class="text-yellow-600">Low Stock</span>
                                        @else
                                            <span class="text-red-600">Out of Stock</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.products.show', $product) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($supplier->products->count() > 10)
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.products.index') }}?supplier_id={{ $supplier->id }}" class="text-blue-600 hover:text-blue-800">
                            View all {{ $supplier->products->count() }} products <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <i class="fas fa-box-open text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No products found for this supplier</p>
                    <p class="text-sm text-gray-400 mt-2">Products from this supplier will appear here</p>
                </div>
            @endif
        </div>
    </div>

    <div>
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.products.create') }}?supplier_id={{ $supplier->id }}" class="block w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
                    <i class="fas fa-plus mr-2"></i>Add Product for this Supplier
                </a>
                <a href="{{ route('admin.po.create') }}?supplier_id={{ $supplier->id }}" class="block w-full bg-purple-500 text-white py-2 rounded hover:bg-purple-600">
                    <i class="fas fa-file-invoice mr-2"></i>Create PO for this Supplier
                </a>
                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="block w-full bg-yellow-500 text-white py-2 rounded hover:bg-yellow-600">
                    <i class="fas fa-edit mr-2"></i>Edit Supplier Information
                </a>
            </div>
        </div>

        <!-- Supplier Statistics -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4">Statistics</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Products</span>
                    <span class="font-medium">{{ $supplier->products_count ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Low Stock Items</span>
                    <span class="font-medium text-yellow-600">
                        {{ $supplier->products ? $supplier->products->where('stock_quantity', '<=', 10)->count() : 0 }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Out of Stock</span>
                    <span class="font-medium text-red-600">
                        {{ $supplier->products ? $supplier->products->where('stock_quantity', '=', 0)->count() : 0 }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Value</span>
                    <span class="font-medium">
                        Rp. {{ number_format($supplier->products ? $supplier->products->sum(function($product) { return $product->price * $product->stock_quantity; }) : 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4">Contact Information</h2>
            <div class="space-y-3">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium">{{ $supplier->contact_person }}</p>
                        <p class="text-xs text-gray-500">Contact Person</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-phone text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium">{{ $supplier->phone }}</p>
                        <p class="text-xs text-gray-500">Phone</p>
                    </div>
                </div>
                @if($supplier->email)
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-envelope text-purple-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">{{ $supplier->email }}</p>
                            <p class="text-xs text-gray-500">Email</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection