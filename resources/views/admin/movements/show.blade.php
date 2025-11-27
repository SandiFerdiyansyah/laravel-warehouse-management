@extends('layouts.admin')

@section('title', 'Movement Detail')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.movements.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">&larr; Back to Movements</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-3xl">
        <h2 class="text-xl font-bold mb-4">Movement Detail</h2>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Product</p>
                <p class="font-medium">{{ $movement->product?->name ?? '-' }} ({{ $movement->product?->sku ?? '-' }})</p>

                <p class="text-sm text-gray-600 mt-3">Operator</p>
                <p class="font-medium">{{ $movement->user?->name ?? '-' }}</p>

                <p class="text-sm text-gray-600 mt-3">Timestamp</p>
                <p class="font-medium">{{ $movement->timestamp }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Type</p>
                <p class="font-medium">{{ strtoupper($movement->type) }}</p>

                <p class="text-sm text-gray-600 mt-3">Quantity</p>
                <p class="font-medium">{{ $movement->quantity }}</p>

                <p class="text-sm text-gray-600 mt-3">Storage Location</p>
                <p class="font-medium">{{ $movement->storageLocation?->location_code ?? '-' }}</p>
            </div>
        </div>

        <div class="mt-6">
            <h3 class="font-semibold">Product Current Stock</h3>
            <p class="mt-2">{{ $movement->product?->stock_quantity ?? 0 }}</p>
        </div>

        <div class="mt-6 flex space-x-3">
            @if(!$movement->approved)
                <form method="POST" action="{{ route('admin.movements.approve', $movement) }}">
                    @csrf
                    <button class="bg-green-600 text-white px-4 py-2 rounded">Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.movements.cancel', $movement) }}" onsubmit="return confirm('Cancel this movement? This cannot be undone.')">
                    @csrf
                    <button class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
                </form>
            @else
                <div class="px-4 py-2 rounded bg-gray-100 text-gray-700">Already approved by {{ $movement->approved_by ? \App\Models\User::find($movement->approved_by)?->name : '-' }} at {{ $movement->approved_at }}</div>
                <form method="POST" action="{{ route('admin.movements.cancel', $movement) }}" onsubmit="return confirm('Cancel this approved movement? This will revert stock.')">
                    @csrf
                    <button class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
                </form>
            @endif
        </div>
    </div>
@endsection
