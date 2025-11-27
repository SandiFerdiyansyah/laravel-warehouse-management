@extends('layouts.admin')

@section('title', 'Purchase Order Details')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Purchase Order: {{ $purchaseOrder->po_number }}</h1>
    <div class="space-x-2">
        <a href="{{ route('admin.po.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>Create New PO
        </a>
        <a href="{{ route('admin.po.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            <i class="fas fa-arrow-left mr-2"></i>Back to PO
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <!-- PO Information -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Purchase Order Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">PO Number</label>
                    <p class="mt-1 text-sm text-gray-900 font-bold">{{ $purchaseOrder->po_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <p class="mt-1">
                        @switch($purchaseOrder->status)
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
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Supplier</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->supplier->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created By</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->admin->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            @if($purchaseOrder->notes)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $purchaseOrder->notes }}</p>
                </div>
            @endif
        </div>

        <!-- PO Items -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Order Items</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($purchaseOrder->purchaseOrderItems as $item)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $item->product->name }}</td>
                                <td class="px-4 py-2 text-sm">{{ $item->product->sku }}</td>
                                <td class="px-4 py-2 text-sm">{{ $item->quantity }}</td>
                                <td class="px-4 py-2 text-sm">Rp. {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-sm font-medium">Rp. {{ number_format($item->getTotalPriceAttribute(), 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-4 py-2 text-right font-semibold">Grand Total:</td>
                            <td class="px-4 py-2 font-semibold">Rp. {{ number_format($purchaseOrder->purchaseOrderItems->sum(function($item) { return $item->quantity * $item->unit_price; }), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div>
        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Actions</h2>
            <div class="space-y-3">
                @if($purchaseOrder->status === 'shipped')
                    <form action="{{ route('admin.po.receive', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark this PO as received? This will update the stock levels.')">
                        @csrf
                        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">
                            <i class="fas fa-check-double mr-2"></i>Mark as Received
                        </button>
                    </form>
                @endif

                @if(in_array($purchaseOrder->status, ['pending', 'approved_supplier']))
                    <form action="{{ route('admin.po.cancel', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this purchase order?')">
                        @csrf
                        <button type="submit" class="w-full bg-red-500 text-white py-2 rounded hover:bg-red-600">
                            <i class="fas fa-times mr-2"></i>Cancel PO
                        </button>
                    </form>
                @endif

                <button onclick="printPO()" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
                    <i class="fas fa-print mr-2"></i>Print PO
                </button>

                <button onclick="exportPDF()" class="w-full bg-purple-500 text-white py-2 rounded hover:bg-purple-600">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </button>
            </div>
        </div>

        <!-- Status Timeline -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4">Status Timeline</h2>
            <div class="space-y-3">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium">PO Created</p>
                        <p class="text-xs text-gray-500">{{ $purchaseOrder->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>

                @if($purchaseOrder->status !== 'pending')
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Approved by Supplier</p>
                            <p class="text-xs text-gray-500">{{ $purchaseOrder->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if(in_array($purchaseOrder->status, ['shipped', 'received']))
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Shipped</p>
                            <p class="text-xs text-gray-500">{{ $purchaseOrder->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if($purchaseOrder->status === 'received')
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Received</p>
                            <p class="text-xs text-gray-500">{{ $purchaseOrder->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if($purchaseOrder->status === 'cancelled')
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-times text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Cancelled</p>
                            <p class="text-xs text-gray-500">{{ $purchaseOrder->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function printPO() {
    window.print();
}

function exportPDF() {
    // Implement PDF export functionality
    alert('PDF export feature will be implemented soon.');
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