@extends('layouts.admin')

@section('title', 'Suppliers')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Suppliers Management</h1>
    <a href="{{ route('admin.suppliers.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        <i class="fas fa-plus mr-2"></i>Add Supplier
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b">
        <div class="flex items-center space-x-4">
            <input type="text" placeholder="Search suppliers..." class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="window.location.href='{{ route('admin.suppliers.index') }}?search='+this.value">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->name }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Person</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products Count</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($suppliers as $supplier)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-building text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $supplier->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $supplier->contact_person }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $supplier->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                {{ $supplier->products_count ?? 0 }} products
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="viewDetails({{ $supplier->id }})" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this supplier?')">
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
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No suppliers found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($suppliers->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $suppliers->links() }}
        </div>
    @endif
</div>

<!-- Supplier Details Modal -->
<div id="supplier-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center h-full">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Supplier Details</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="supplier-details"></div>
        </div>
    </div>
</div>

<!-- Supplier Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-500 rounded-full">
                <i class="fas fa-building text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Suppliers</p>
                <p class="text-2xl font-bold">{{ $suppliers->total() }}</p>
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
                <p class="text-2xl font-bold">{{ $suppliers->sum('products_count') ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-500 rounded-full">
                <i class="fas fa-chart-line text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Avg Products/Supplier</p>
                <p class="text-2xl font-bold">
                    {{ $suppliers->count() > 0 ? round(($suppliers->sum('products_count') ?? 0) / $suppliers->count(), 1) : 0 }}
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function filterSuppliers() {
    const searchInput = document.querySelector('input[placeholder="Search suppliers..."]');
    const searchTerm = searchInput?.value.toLowerCase() || '';
    const rows = document.querySelectorAll('table tbody tr');

    rows.forEach(row => {
        if (row.querySelector('td:nth-child(6)') === null) return; // Skip empty rows
        
        const name = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
        const contact = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
        const phone = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';

        const matchesSearch = name.includes(searchTerm) || contact.includes(searchTerm) || phone.includes(searchTerm);
        row.style.display = matchesSearch ? '' : 'none';
    });
}

// Search input event listener
document.querySelector('input[placeholder="Search suppliers..."]')?.addEventListener('input', filterSuppliers);

function viewDetails(supplierId) {
    // Fetch supplier details via AJAX
    fetch(`/admin/suppliers/${supplierId}/details`)
        .then(response => response.json())
        .then(data => {
            const detailsHtml = `
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Company Name</label>
                            <p class="mt-1 text-sm text-gray-900">${data.name}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Contact Person</label>
                            <p class="mt-1 text-sm text-gray-900">${data.contact_person}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">${data.phone}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Products Count</label>
                            <p class="mt-1 text-sm text-gray-900">${data.products_count} products</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Address</label>
                        <p class="mt-1 text-sm text-gray-900">${data.address}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created At</label>
                        <p class="mt-1 text-sm text-gray-900">${data.created_at}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('supplier-details').innerHTML = detailsHtml;
            document.getElementById('supplier-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.log('Error fetching supplier details:', error);
            alert('Error loading supplier details');
        });
}

function closeModal() {
    document.getElementById('supplier-modal').classList.add('hidden');
}
</script>
@endsection