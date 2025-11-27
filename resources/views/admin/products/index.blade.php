@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Products Management</h1>
    <div class="space-x-2">
        <a href="{{ route('admin.products.scan') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
            <i class="fas fa-qrcode mr-2"></i>Scan QR Code
        </a>
        <a href="{{ route('admin.products.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>Add Product
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b">
        <div class="flex items-center space-x-4">
            <input type="text" placeholder="Search products..." class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                @foreach($categories ?? [] as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <select class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Suppliers</option>
                @foreach($suppliers ?? [] as $supplier)
                    <option value="{{ $supplier -> id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->sku }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->supplier->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp. {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $product->stock_quantity > 10 ? 'bg-green-100 text-green-800' : ($product->stock_quantity > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $product->stock_quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->stock_quantity > 10)
                                <span class="text-green-600 text-sm">In Stock</span>
                            @elseif($product->stock_quantity > 0)
                                <span class="text-yellow-600 text-sm">Low Stock</span>
                            @else
                                <span class="text-red-600 text-sm">Out of Stock</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.products.show', $product) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No products found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        <div class="px-6 py-4 border-t">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>

<script>
    // Product Table Search & Filter
    const searchInput = document.querySelector('input[placeholder="Search products..."]');
    const categorySelect = document.querySelectorAll('select')[0];
    const supplierSelect = document.querySelectorAll('select')[1];
    const table = document.querySelector('table tbody');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categorySelect.value;
        const selectedSupplier = supplierSelect.value;

        table.querySelectorAll('tr').forEach(row => {
            if (row.querySelector('td:nth-child(8)') === null) return; // Skip empty rows
            
            const sku = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
            const name = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
            const category = row.getAttribute('data-category') || row.querySelector('td:nth-child(3)')?.textContent || '';
            const supplier = row.getAttribute('data-supplier') || row.querySelector('td:nth-child(4)')?.textContent || '';

            const matchesSearch = sku.includes(searchTerm) || name.includes(searchTerm);
            const matchesCategory = !selectedCategory || category.includes(selectedCategory);
            const matchesSupplier = !selectedSupplier || supplier.includes(selectedSupplier);

            row.style.display = matchesSearch && matchesCategory && matchesSupplier ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input', filterTable);
    categorySelect?.addEventListener('change', filterTable);
    supplierSelect?.addEventListener('change', filterTable);
</script>
            @endsection