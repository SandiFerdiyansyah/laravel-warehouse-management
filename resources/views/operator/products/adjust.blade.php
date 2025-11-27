@extends('layouts.operator')

@section('title', 'Adjust Product Stock')

@section('content')
<div class="mb-6">
    <a href="{{ route('operator.products.show', $product) }}" class="text-sm text-gray-600 hover:underline">&larr; Back to product</a>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-xl">
    <h2 class="text-lg font-semibold mb-4">Adjust Stock for {{ $product->name }} ({{ $product->sku }})</h2>

    <form action="{{ route('operator.products.adjust.submit', $product) }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Type</label>
            <select name="type" class="w-full border px-3 py-2 rounded">
                <option value="in">Add (In)</option>
                <option value="out">Remove (Out)</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Quantity</label>
            <input type="number" name="quantity" min="1" value="1" class="w-full border px-3 py-2 rounded">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Notes (optional)</label>
            <textarea name="notes" class="w-full border px-3 py-2 rounded" rows="3"></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Submit</button>
        </div>
    </form>
</div>
@endsection
