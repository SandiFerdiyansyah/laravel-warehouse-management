@extends('layouts.store')

@section('title', 'Buat Permintaan Barang')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Buat Permintaan Barang</h1>
        <a href="{{ route('store.product_requests.index') }}" class="text-gray-600 hover:text-gray-800">← Kembali</a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow max-w-2xl">
        <form action="{{ route('store.product_requests.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Product Selection -->
            <div>
                <label class="block text-sm font-semibold mb-2">Pilih Produk *</label>
                <select name="product_id" id="product-select" class="w-full border p-3 rounded @error('product_id') border-red-500 @enderror" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-sku="{{ $product->sku }}">
                            {{ $product->name }} ({{ $product->sku }})
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Auto-selected Warehouse Info (read-only) -->
            @if($mainWarehouse)
                <div class="bg-blue-50 border border-blue-200 p-4 rounded">
                    <p class="text-sm text-gray-600">Permintaan akan dikirim ke:</p>
                    <p class="font-semibold text-blue-900">{{ $mainWarehouse->name }} ({{ $mainWarehouse->warehouse_code }})</p>
                    <p class="text-xs text-gray-500">📍 {{ $mainWarehouse->location }}</p>
                </div>
            @endif

            <!-- Quantity -->
            <div>
                <label class="block text-sm font-semibold mb-2">Jumlah yang Diminta *</label>
                <input type="number" name="quantity_requested" id="quantity-input" 
                       class="w-full border p-3 rounded @error('quantity_requested') border-red-500 @enderror" 
                       min="1" required>
                @error('quantity_requested')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p id="max-qty-info" class="text-sm text-gray-600 mt-1"></p>
            </div>

            <!-- Submit -->
            <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 font-semibold">Buat Permintaan</button>
                <a href="{{ route('store.product_requests.index') }}" class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500 font-semibold">Batal</a>
            </div>
        </form>
    </div>

    <script>
        // Update max quantity based on product stock when product is selected
        document.addEventListener('DOMContentLoaded', function() {
            const productSelect = document.getElementById('product-select');
            const maxQtyInfo = document.getElementById('max-qty-info');
            const qtyInput = document.getElementById('quantity-input');

            // For now, set max qty to a high value (admin will verify stock)
            qtyInput.max = '9999';
            maxQtyInfo.textContent = 'Stok akan diverifikasi oleh admin setelah permintaan disubmit';
        });
    </script>
@endsection
