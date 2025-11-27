@extends('layouts.operator')

@section('title', 'My Movements')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold">My Scans / Movements</h1>
    <a href="{{ route('operator.scan.index') }}" class="bg-green-500 text-white px-4 py-2 rounded">Back to Scanner</a>
</div>

<div class="bg-white rounded-lg shadow p-4">
    <table class="w-full text-sm">
        <thead class="text-xs text-gray-500 text-left">
            <tr>
                <th class="py-2">Timestamp</th>
                <th class="py-2">Product</th>
                <th class="py-2">Type</th>
                <th class="py-2">Qty</th>
                <th class="py-2">Approved</th>
                <th class="py-2">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($movements as $m)
            <tr>
                <td class="py-2">{{ $m->timestamp }}</td>
                <td class="py-2">{{ $m->product?->name ?? '-' }}</td>
                <td class="py-2">{{ strtoupper($m->type) }}</td>
                <td class="py-2">{{ $m->quantity }}</td>
                <td class="py-2">{{ $m->storageLocation?->location_code ?? '-' }}</td>
                <td class="py-2">{{ $m->approved ? 'Yes' : 'Pending' }}</td>
                <td class="py-2">
                    <form action="{{ route('operator.movements.destroy', $m) }}" method="POST" onsubmit="return confirm('Delete this movement?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $movements->links() }}
    </div>
</div>
@endsection
