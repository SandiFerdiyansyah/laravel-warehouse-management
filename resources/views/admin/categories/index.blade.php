@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Categories Management</h1>
    <a href="{{ route('admin.categories.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        <i class="fas fa-plus mr-2"></i>Add Category
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b">
        <div class="flex items-center space-x-4">
            <input type="text" placeholder="Search categories..." class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products Count</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ Str::limit($category->description ?? '-', 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ $category->products_count ?? 0 }} products
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $category->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
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
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No categories found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $categories->links() }}
        </div>
    @endif
</div>

<!-- Category Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-500 rounded-full">
                <i class="fas fa-tags text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Categories</p>
                <p class="text-2xl font-bold">{{ $categories->total() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-500 rounded-full">
                <i class="fas fa-box text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Products</p>
                <p class="text-2xl font-bold">{{ $categories->sum('products_count') ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-500 rounded-full">
                <i class="fas fa-chart-line text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Avg Products/Category</p>
                <p class="text-2xl font-bold">
                    {{ $categories->count() > 0 ? round(($categories->sum('products_count') ?? 0) / $categories->count(), 1) : 0 }}
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Category Search Functionality
const categorySearchInput = document.querySelector('input[placeholder="Search categories..."]');
const categoryTable = document.querySelector('table tbody');

function filterCategories() {
    const searchTerm = categorySearchInput?.value.toLowerCase() || '';
    const rows = categoryTable.querySelectorAll('tr');

    rows.forEach(row => {
        if (row.querySelector('td:nth-child(5)') === null) return; // Skip empty rows
        
        const name = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
        const description = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';

        const matchesSearch = name.includes(searchTerm) || description.includes(searchTerm);
        row.style.display = matchesSearch ? '' : 'none';
    });
}

categorySearchInput?.addEventListener('input', filterCategories);
</script>
@endsection