@extends('layouts.supplier')

@section('title', 'Products - Supplier')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Products You Sell</h2>
        <a href="{{ route('supplier.products.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded">Add Product</a>
    </div>

    <div class="bg-white shadow rounded">
        <table class="w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">#</th>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Category</th>
                    <th class="p-3 text-left">SKU</th>
                    <th class="p-3 text-right">Price</th>
                    <th class="p-3 text-right">Stock</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">{{ $product->id }}</td>
                        <td class="p-3">{{ $product->name }}</td>
                        <td class="p-3">{{ $product->category?->name }}</td>
                        <td class="p-3">{{ $product->sku }}</td>
                        <td class="p-3 text-right">Rp {{ number_format($product->price,0,',','.') }}</td>
                        <td class="p-3 text-right">{{ number_format($inventories[$product->id] ?? 0, 0, ',', '.') }}</td>
                        <td class="p-3 text-center">
                            <a href="{{ route('supplier.products.show', $product) }}" class="text-purple-600 mr-2">View</a>
                            <a href="{{ route('supplier.products.edit', $product) }}" class="text-yellow-600 mr-2">Edit</a>
                            <form action="{{ route('supplier.products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
@endsection
