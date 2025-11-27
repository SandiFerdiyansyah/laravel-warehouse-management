@extends('layouts.store')

@section('title', 'Store Dashboard')

@section('content')
<div class="grid grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-semibold mb-2">Total Inventory</h3>
        <p class="text-3xl font-bold text-blue-600">{{ $inventory->sum('quantity') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-semibold mb-2">Products</h3>
        <p class="text-3xl font-bold text-green-600">{{ $inventory->count() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-semibold mb-2">Recent Shipments</h3>
        <p class="text-3xl font-bold text-purple-600">{{ $recentShipments->count() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-semibold mb-2">Total Revenue</h3>
        <p class="text-3xl font-bold text-orange-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <!-- Recent Shipments -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Recent Shipments</h2>
        <div class="space-y-3">
            @forelse($recentShipments as $shipment)
                <div class="border-l-4 border-green-500 pl-4 py-2">
                    <p class="font-semibold">{{ $shipment->product->name }}</p>
                    <p class="text-sm text-gray-600">Qty: {{ $shipment->quantity }} units</p>
                    <p class="text-xs text-gray-400">{{ $shipment->received_at?->format('d/m/Y H:i') }}</p>
                </div>
            @empty
                <p class="text-gray-500 text-sm">No shipments received yet</p>
            @endforelse
        </div>
    </div>

    <!-- Current Inventory -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Current Inventory</h2>
        <div class="space-y-3 max-h-96 overflow-y-auto">
            @forelse($inventory as $item)
                <div class="flex justify-between items-center border-b pb-2">
                    <div>
                        <p class="font-semibold">{{ $item['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ $item['sku'] }}</p>
                    </div>
                    <p class="text-lg font-bold text-blue-600">{{ $item['quantity'] }}</p>
                </div>
            @empty
                <p class="text-gray-500 text-sm">No inventory yet</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
