@extends('layouts.store')

@section('title', 'Product Request #' . $request->id . ' - Store')

@section('content')
<div class="mb-6">
    <a href="{{ route('store.requests.index') }}" class="text-blue-600 hover:underline">&larr; Back to Requests</a>
    <h1 class="text-3xl font-bold mt-2">Product Request #{{ $request->id }}</h1>
</div>

<div class="grid grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Request Information</h3>
        
        <div class="space-y-3">
            <div>
                <p class="text-gray-600 text-sm">Request Date</p>
                <p class="text-lg font-semibold">{{ $request->created_at->format('d/m/Y H:i') }}</p>
            </div>
            
            <div>
                <p class="text-gray-600 text-sm">Status</p>
                <p class="mt-1">
                    @if($request->status === 'pending')
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                            Pending
                        </span>
                    @elseif($request->status === 'approved')
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                            Approved
                        </span>
                    @else
                        <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold">
                            {{ ucfirst($request->status) }}
                        </span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="col-span-2 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Requested Items</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Product</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold">Quantity</th>
                        <th class="px-4 py-2 text-center text-sm font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($request->items as $item)
                        <tr class="border-b">
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-semibold">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->product->sku }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($item->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold">
                                        Pending
                                    </span>
                                @elseif($item->status === 'fulfilled')
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">
                                        Fulfilled
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-semibold">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-center text-gray-500">
                                No items in this request
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($request->notes)
            <div class="mt-6 border-t pt-6">
                <h4 class="font-semibold mb-2">Notes</h4>
                <p class="text-gray-700 whitespace-pre-line">{{ $request->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
