@extends('layouts.supplier')

@section('title', 'Supplier Dashboard')

@section('content')
@if(session()->has('warning'))
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">
        {{ session('warning') }}
    </div>
@endif

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Supplier Dashboard</h1>
    <a href="{{ route('supplier.orders.index') }}" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
        <i class="fas fa-list mr-2"></i>View All Orders
    </a>
</div>

<!-- Order Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-500 rounded-full">
                <i class="fas fa-clock text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Pending Orders</p>
                <p class="text-2xl font-bold">{{ $stats['pending_orders'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-500 rounded-full">
                <i class="fas fa-check text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Approved Orders</p>
                <p class="text-2xl font-bold">{{ $stats['approved_orders'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-orange-500 rounded-full">
                <i class="fas fa-truck text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Shipped Orders</p>
                <p class="text-2xl font-bold">{{ $stats['shipped_orders'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-500 rounded-full">
                <i class="fas fa-check-double text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Received Orders</p>
                <p class="text-2xl font-bold">{{ $stats['received_orders'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Recent Purchase Orders</h2>
        <a href="{{ route('supplier.orders.index') }}" class="text-purple-600 hover:text-purple-800 text-sm">
            View All <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    @if($purchaseOrders->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">PO Number</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($purchaseOrders->take(5) as $po)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm font-medium">{{ $po->po_number }}</td>
                            <td class="px-4 py-2 text-sm">{{ $po->purchaseOrderItems->count() }} items</td>
                            <td class="px-4 py-2 text-sm">Rp. {{ number_format($po->purchaseOrderItems->sum(function($item) { return $item->quantity * $item->unit_price; }), 0, ',', '.') }}</td>
                            <td class="px-4 py-2">
                                @switch($po->status)
                                    @case('pending')
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                        @break
                                    @case('approved_supplier')
                                        <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Approved</span>
                                        @break
                                    @case('shipped')
                                        <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Shipped</span>
                                        @break
                                    @case('received')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Received</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $po->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-2 text-sm">
                                <a href="{{ route('supplier.orders.show', $po) }}" class="text-purple-600 hover:text-purple-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
            <p class="text-gray-500">No purchase orders found</p>
            <p class="text-sm text-gray-400 mt-2">Purchase orders from the warehouse will appear here</p>
        </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
        <div class="space-y-3">
            <a href="{{ route('supplier.orders.index') }}?status=pending" class="block w-full bg-yellow-500 text-white py-2 rounded hover:bg-yellow-600 text-center">
                <i class="fas fa-clock mr-2"></i>View Pending Orders
            </a>
            <a href="{{ route('supplier.orders.index') }}?status=approved_supplier" class="block w-full bg-purple-500 text-white py-2 rounded hover:bg-purple-600 text-center">
                <i class="fas fa-check mr-2"></i>View Approved Orders
            </a>
            <a href="{{ route('supplier.orders.index') }}?status=shipped" class="block w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600 text-center">
                <i class="fas fa-truck mr-2"></i>Track Shipments
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Performance Summary</h2>
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Total Orders</span>
                <span class="font-medium">{{ $stats['pending_orders'] + $stats['approved_orders'] + $stats['shipped_orders'] + $stats['received_orders'] }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Completion Rate</span>
                <span class="font-medium text-green-600">
                    @php
                        $total = $stats['pending_orders'] + $stats['approved_orders'] + $stats['shipped_orders'] + $stats['received_orders'];
                        $completed = $stats['received_orders'];
                        $rate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
                    @endphp
                    {{ $rate }}%
                </span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Pending Action</span>
                <span class="font-medium text-yellow-600">{{ $stats['pending_orders'] + $stats['approved_orders'] }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Notifications -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold mb-4">Notifications</h2>
    <div class="space-y-3">
        @if($stats['pending_orders'] > 0)
            <div class="flex items-center p-3 bg-yellow-50 border border-yellow-200 rounded">
                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-yellow-900">You have {{ $stats['pending_orders'] }} pending order(s)</p>
                    <p class="text-xs text-yellow-700">Review and approve to proceed with fulfillment</p>
                </div>
            </div>
        @endif

        @if($stats['approved_orders'] > 0)
            <div class="flex items-center p-3 bg-purple-50 border border-purple-200 rounded">
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check text-purple-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-purple-900">{{ $stats['approved_orders'] }} order(s) ready for shipping</p>
                    <p class="text-xs text-purple-700">Mark as shipped when ready</p>
                </div>
            </div>
        @endif

        @if($stats['shipped_orders'] > 0)
            <div class="flex items-center p-3 bg-orange-50 border border-orange-200 rounded">
                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-truck text-orange-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-orange-900">{{ $stats['shipped_orders'] }} order(s) in transit</p>
                    <p class="text-xs text-orange-700">Waiting for warehouse confirmation</p>
                </div>
            </div>
        @endif

        @if($stats['pending_orders'] == 0 && $stats['approved_orders'] == 0 && $stats['shipped_orders'] == 0)
            <div class="text-center py-4">
                <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                <p class="text-gray-500">All orders are completed!</p>
                <p class="text-sm text-gray-400">New orders will appear here</p>
            </div>
        @endif
    </div>
</div>
@endsection