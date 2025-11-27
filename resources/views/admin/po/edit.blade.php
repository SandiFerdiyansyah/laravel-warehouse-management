@extends('layouts.admin')

@section('title', 'Edit Purchase Order')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Edit Purchase Order: {{ $purchaseOrder->po_number }}</h1>
    <a href="{{ route('admin.po.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Back to PO
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.po.update', $purchaseOrder) }}">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                <select id="supplier_id" name="supplier_id" disabled
                        class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                    <option value="{{ $purchaseOrder->supplier_id }}">{{ $purchaseOrder->supplier->name }}</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Supplier cannot be changed after PO creation</p>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="pending" {{ $purchaseOrder->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved_supplier" {{ $purchaseOrder->status === 'approved_supplier' ? 'selected' : '' }}>Approved</option>
                    <option value="shipped" {{ $purchaseOrder->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="received" {{ $purchaseOrder->status === 'received' ? 'selected' : '' }}>Received</option>
                    <option value="cancelled" {{ $purchaseOrder->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
            <textarea id="notes" name="notes" rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Additional notes for this purchase order...">{{ old('notes', $purchaseOrder->notes) }}</textarea>
        </div>

        <!-- Current Items -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Current Order Items</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Current Quantity</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
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
                            <td colspan="4" class="px-4 py-2 text-right font-semibold">Current Total:</td>
                            <td class="px-4 py-2 font-semibold">Rp. {{ number_format($purchaseOrder->purchaseOrderItems->sum(function($item) { return $item->quantity * $item->unit_price; }), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- PO Status Information -->
        <div class="mb-6 p-4 bg-gray-50 rounded">
            <h3 class="text-lg font-semibold mb-4">Order Status Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">PO Number</label>
                    <p class="mt-1 text-sm text-gray-900 font-bold">{{ $purchaseOrder->po_number }}</p>
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
        </div>

        <!-- Status Timeline -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Status Timeline</h3>
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

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.po.show', $purchaseOrder) }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Update Purchase Order
            </button>
        </div>
    </form>
</div>

<!-- Restrictions Information -->
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
    <h3 class="text-sm font-medium text-yellow-900 mb-2">
        <i class="fas fa-info-circle mr-2"></i>Edit Restrictions
    </h3>
    <ul class="text-sm text-yellow-800 space-y-1">
        <li>• Supplier cannot be changed after PO creation</li>
        <li>• Items cannot be modified after PO creation</li>
        <li>• Only status and notes can be updated</li>
        <li>• To modify items, cancel this PO and create a new one</li>
        <li>• Status changes will notify supplier automatically</li>
    </ul>
</div>
@endsection