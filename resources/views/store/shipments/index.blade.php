@extends('layouts.store')

@section('title', 'Shipments - Store')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Shipments</h1>
    <p class="text-gray-600">Manage incoming stock shipments</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold">Date</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Product</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Location</th>
                <th class="px-6 py-3 text-right text-sm font-semibold">Quantity</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                <th class="px-6 py-3 text-center text-sm font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shipments as $shipment)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">{{ $shipment->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-sm font-semibold">{{ $shipment->product->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $shipment->storageLocation->location ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-right">{{ $shipment->quantity }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($shipment->status === 'pending')
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                Pending
                            </span>
                        @elseif($shipment->status === 'delivered')
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                Delivered
                            </span>
                        @else
                            <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold">
                                {{ ucfirst($shipment->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($shipment->status === 'pending')
                            <form action="{{ route('store.shipments.receive', $shipment) }}" method="POST" class="inline-block">
                                @csrf
                                <input type="hidden" name="received_quantity" value="{{ $shipment->quantity }}">
                                <button type="submit" class="text-green-600 hover:text-green-800 text-sm font-semibold"
                                        onclick="return confirm('Confirm receipt of this shipment?')">
                                    Receive
                                </button>
                            </form>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No shipments found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($shipments->hasPages())
    <div class="mt-6">
        {{ $shipments->links() }}
    </div>
@endif
@endsection
