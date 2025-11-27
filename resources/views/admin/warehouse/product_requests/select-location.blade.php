@extends('layouts.admin')

@section('title', 'Pilih Lokasi Storage')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Pilih Lokasi Storage</h1>
        <a href="{{ route('admin.warehouse_product_requests.index') }}" class="text-gray-600 hover:text-gray-800">← Kembali</a>
    </div>

    <div class="grid grid-cols-3 gap-6 mb-6">
        <!-- Request Info -->
        <div class="col-span-2">
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">Informasi Permintaan</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">Produk</p>
                            <p class="font-bold text-lg">{{ $request->product->name }}</p>
                            <p class="text-xs text-gray-500">SKU: {{ $request->product->sku }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Jumlah Diminta</p>
                            <p class="font-bold text-2xl">{{ $request->quantity_requested }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">Toko Peminta</p>
                            <p class="font-bold">{{ $request->store->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Gudang</p>
                            <p class="font-bold">{{ $request->warehouse->name }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Tanggal Permintaan</p>
                        <p class="text-sm">{{ $request->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Stock Summary -->
        <div class="bg-blue-50 p-6 rounded-lg shadow border-l-4 border-blue-500">
            <h3 class="font-bold text-lg mb-4">📊 Total Stock</h3>
            <p class="text-3xl font-bold text-blue-600">
                {{ $storageLocations->sum('quantity') }}
            </p>
            <p class="text-sm text-gray-600 mt-2">Unit tersedia di {{ $storageLocations->count() }} lokasi</p>
        </div>
    </div>

    <!-- Select Location Form -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4">Pilih Lokasi Storage</h2>
        
        <form action="{{ route('admin.warehouse_product_requests.store_location', $request->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-3 mb-6">
                @foreach($storageLocations as $location)
                    <label class="flex items-center p-4 border rounded cursor-pointer hover:bg-blue-50 @error('storage_location_id') border-red-500 @enderror">
                        <input type="radio" name="storage_location_id" value="{{ $location->id }}" 
                               class="mr-4" required>
                        <div class="flex-1">
                            <div class="flex justify-between items-center">
                                <p class="font-semibold">{{ $location->location_code }}</p>
                                <p class="text-lg font-bold text-green-600">{{ $location->quantity }} unit</p>
                            </div>
                            <p class="text-sm text-gray-600">
                                Kapasitas: {{ $location->capacity }} | 
                                @if($location->is_filled)
                                    <span class="text-red-600">Penuh</span>
                                @else
                                    <span class="text-green-600">Tersedia</span>
                                @endif
                            </p>
                        </div>
                    </label>
                @endforeach
            </div>

            @error('storage_location_id')
                <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
            @enderror

            <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 font-semibold">
                    ✓ Pilih Lokasi
                </button>
                <a href="{{ route('admin.warehouse_product_requests.index') }}" 
                   class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500 font-semibold">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
