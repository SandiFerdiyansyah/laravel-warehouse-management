@extends('layouts.admin')

@section('title', 'Purchase Orders')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Purchase Orders Management</h1>
    <a href="{{ route('admin.po.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        <i class="fas fa-plus mr-2"></i>Create PO
    </a>
</div>

<!-- PO Statistics -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-500 rounded-full">
                <i class="fas fa-file-invoice text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total PO</p>
                <p class="text-2xl font-bold">{{ $purchaseOrders->total() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-500 rounded-full">
                <i class="fas fa-clock text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Pending</p>
                <p class="text-2xl font-bold">{{ $purchaseOrders->where('status', 'pending')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-500 rounded-full">
                <i class="fas fa-check text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Approved</p>
                <p class="text-2xl font-bold">{{ $purchaseOrders->where('status', 'approved_supplier')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-orange-500 rounded-full">
                <i class="fas fa-truck text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Shipped</p>
                <p class="text-2xl font-bold">{{ $purchaseOrders->where('status', 'shipped')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-500 rounded-full">
                <i class="fas fa-check-double text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Received</p>
                <p class="text-2xl font-bold">{{ $purchaseOrders->where('status', 'received')->count() }}</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b">
        <div class="flex items-center space-x-4">
            <input type="text" placeholder="Search PO..." class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved_supplier">Approved</option>
                <option value="shipped">Shipped</option>
                <option value="received">Received</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <select class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Suppliers</option>
                @foreach($suppliers ?? [] as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($purchaseOrders as $po)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $po->po_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $po->supplier->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $po->admin->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ $po->purchaseOrderItems->count() }} items
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Rp. {{ number_format($po->purchaseOrderItems->sum(function($item) { return $item->quantity * $item->unit_price; }), 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($po->status)
                                @case('pending')
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>Pending
                                    </span>
                                    @break
                                @case('approved_supplier')
                                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                        <i class="fas fa-check mr-1"></i>Approved
                                    </span>
                                    @break
                                @case('shipped')
                                    <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">
                                        <i class="fas fa-truck mr-1"></i>Shipped
                                    </span>
                                    @break
                                @case('received')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-double mr-1"></i>Received
                                    </span>
                                    @break
                                @case('cancelled')
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times mr-1"></i>Cancelled
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $po->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.po.show', $po) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($po->status === 'shipped')
                                    <form action="{{ route('admin.po.receive', $po) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to mark this PO as received?')">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Mark as Received">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    </form>
                                @endif
                                @if(in_array($po->status, ['pending', 'approved_supplier']))
                                    <form action="{{ route('admin.po.cancel', $po) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this PO?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Cancel PO">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No purchase orders found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($purchaseOrders->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $purchaseOrders->links() }}
        </div>
    @endif
</div>

<script>
// PO Search & Filter Functionality
const poSearchInput = document.querySelector('input[placeholder="Search PO..."]');
const statusSelect = document.querySelectorAll('select')[0];
const supplierSelect = document.querySelectorAll('select')[1];
const poTable = document.querySelector('table tbody');

function filterPOTable() {
    const searchTerm = poSearchInput?.value.toLowerCase() || '';
    const selectedStatus = statusSelect?.value || '';
    const selectedSupplier = supplierSelect?.value || '';

    poTable.querySelectorAll('tr').forEach(row => {
        if (row.querySelector('td:nth-child(8)') === null) return; // Skip empty rows
        
        const poNumber = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
        const supplier = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
        const status = row.getAttribute('data-status') || row.querySelector('td:nth-child(6)')?.textContent || '';
        const supplierName = row.getAttribute('data-supplier') || supplier;

        const matchesSearch = poNumber.includes(searchTerm);
        const matchesStatus = !selectedStatus || status.includes(selectedStatus);
        const matchesSupplier = !selectedSupplier || supplierName.includes(selectedSupplier);

        row.style.display = matchesSearch && matchesStatus && matchesSupplier ? '' : 'none';
    });
}

poSearchInput?.addEventListener('input', filterPOTable);
statusSelect?.addEventListener('change', filterPOTable);
supplierSelect?.addEventListener('change', filterPOTable);
</script>
@endsection