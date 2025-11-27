@extends('layouts.admin')

@section('title', 'Product Movements')

@section('content')
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">Product Movements</h1>

    <div class="bg-white rounded shadow p-4">
        <table class="w-full text-sm">
            <thead class="text-xs text-gray-500 text-left">
                <tr>
                    <th class="py-2">Timestamp</th>
                    <th class="py-2">Product</th>
                    <th class="py-2">Type</th>
                    <th class="py-2">Qty</th>
                    <th class="py-2">Operator</th>
                    <th class="py-2">Approved</th>
                    <th class="py-2">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($movements as $m)
                <tr>
                    <td class="py-2">{{ $m->timestamp }}</td>
                    <td class="py-2"><a href="{{ route('admin.movements.show', $m) }}" class="text-blue-600 hover:underline">{{ $m->product?->name ?? '-' }}</a></td>
                    <td class="py-2">{{ strtoupper($m->type) }}</td>
                    <td class="py-2">{{ $m->quantity }}</td>
                    <td class="py-2">{{ $m->user?->name ?? '-' }}</td>
                    <td class="py-2">{{ $m->approved ? 'Yes' : 'No' }}</td>
                    <td class="py-2">
                        @if(!$m->approved)
                        <form action="{{ route('admin.movements.approve', $m) }}" method="POST" style="display:inline">
                            @csrf
                            <button class="bg-green-600 text-white px-3 py-1 rounded">Approve</button>
                        </form>
                        @else
                            <span class="text-sm text-gray-500">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $movements->links() }}
        </div>
    </div>
</div>
@endsection
