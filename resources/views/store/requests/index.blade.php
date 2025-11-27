@extends('layouts.store')

@section('title', 'Product Requests - Store')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold">Product Requests</h1>
        <p class="text-gray-600">Request products from admin</p>
    </div>
    <a href="{{ route('store.requests.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
        + New Request
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold">Request ID</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Date</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Items</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                <th class="px-6 py-3 text-center text-sm font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-semibold">#{{ $request->id }}</td>
                    <td class="px-6 py-4 text-sm">{{ $request->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-sm">{{ $request->items->count() }} item(s)</td>
                    <td class="px-6 py-4 text-sm">
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
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('store.requests.show', $request) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                            View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        No requests yet. <a href="{{ route('store.requests.create') }}" class="text-blue-600 hover:underline">Create one now</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($requests->hasPages())
    <div class="mt-6">
        {{ $requests->links() }}
    </div>
@endif
@endsection
