@extends('layouts.admin')

@section('title', 'Verifikasi Permintaan Barang')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Verifikasi Permintaan Barang</h1>

    <div class="grid grid-cols-2 gap-6 mb-8">
        <!-- Request Details -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Detail Permintaan</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">ID Permintaan</p>
                    <p class="font-semibold">{{ $request->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Toko</p>
                    <p class="font-semibold">{{ $request->store->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Kontak</p>
                    <p class="text-sm">{{ $request->store->phone ?? '-' }}<br>{{ $request->store->address ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Produk</p>
                    <p class="font-semibold">{{ $product->name }}</p>
                    <p class="text-sm text-gray-600">SKU: {{ $product->sku }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Qty Diminta</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $request->quantity_requested }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tanggal Permintaan</p>
                    <p class="text-sm">{{ $request->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Warehouse & Storage Location Info -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Gudang & Lokasi Storage</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Gudang</p>
                    <p class="font-semibold text-lg">{{ $warehouse->name }} ({{ $warehouse->warehouse_code }})</p>
                    @if($warehouse->location)
                        <p class="text-xs text-gray-500">📍 {{ $warehouse->location }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-gray-600">Lokasi Storage Kode</p>
                    <p class="font-semibold text-lg">{{ $storage->location_code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Kapasitas</p>
                    <p class="font-semibold">{{ $storage->capacity ?? '-' }} unit</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 p-4 rounded">
                    <p class="text-sm text-gray-600 mb-1">Stock Tersedia</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $availableQty }}</p>
                    <p class="text-xs text-gray-600 mt-2">
                        @if($availableQty >= $request->quantity_requested)
                            <span class="text-green-600">✓ Stock mencukupi untuk qty diminta</span>
                        @else
                            <span class="text-red-600">✗ Stock kurang! Hanya {{ $availableQty }} tersedia</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Form -->
    <div class="bg-white p-6 rounded-lg shadow max-w-2xl">
        <h2 class="text-lg font-bold mb-4 border-b pb-2">Form Verifikasi</h2>

        <form action="{{ route('operator.product_requests.verify.store', $request->id) }}" method="POST" class="space-y-4">
            @csrf

            <!-- Verification Notes -->
            <div>
                <label class="block text-sm font-semibold mb-2">Catatan Verifikasi *</label>
                <textarea name="verification_notes" rows="4" class="w-full border p-3 rounded @error('verification_notes') border-red-500 @enderror" placeholder="Masukkan hasil pemeriksaan produk, kondisi, pengecekan nomor seri, dll..." required>{{ old('verification_notes') }}</textarea>
                @error('verification_notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Selection -->
            <div class="border-t pt-4">
                <p class="text-sm font-semibold mb-4">Hasil Verifikasi</p>
                
                <div class="space-y-4">
                    <!-- Approve -->
                    <div class="border rounded p-4 hover:bg-green-50 cursor-pointer" onclick="selectAction('approve')">
                        <div class="flex items-center mb-3">
                            <input type="radio" id="action_approve" name="action" value="approve" required @change="onActionChange()">
                            <label for="action_approve" class="ml-3 font-semibold cursor-pointer">✓ Setujui - Barang OK</label>
                        </div>
                        <div id="approve_options" class="ml-6 hidden space-y-3">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Quantity Terverifikasi *</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="quantity_verified" id="quantity_verified" min="1" max="{{ $availableQty }}" class="w-32 border p-2 rounded" placeholder="Qty">
                                    <span class="text-sm text-gray-600">dari {{ $availableQty }} tersedia</span>
                                </div>
                                @error('quantity_verified')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Reject -->
                    <div class="border rounded p-4 hover:bg-red-50 cursor-pointer" onclick="selectAction('reject')">
                        <div class="flex items-center">
                            <input type="radio" id="action_reject" name="action" value="reject" required @change="onActionChange()">
                            <label for="action_reject" class="ml-3 font-semibold cursor-pointer">✗ Tolak - Barang tidak OK</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 font-semibold">Simpan Verifikasi</button>
                <a href="{{ route('operator.product_requests.index') }}" class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500 font-semibold">Batal</a>
            </div>
        </form>
    </div>

    <script>
        function selectAction(action) {
            document.getElementById('action_' + action).checked = true;
            onActionChange();
        }

        function onActionChange() {
            const isApprove = document.getElementById('action_approve').checked;
            const approveOptions = document.getElementById('approve_options');
            const qtyInput = document.getElementById('quantity_verified');
            
            if (isApprove) {
                approveOptions.classList.remove('hidden');
                qtyInput.required = true;
            } else {
                approveOptions.classList.add('hidden');
                qtyInput.required = false;
            }
        }

        // Set initial max value for quantity
        document.getElementById('quantity_verified').max = {{ $availableQty }};
        document.getElementById('quantity_verified').value = {{ $request->quantity_requested }};
    </script>
@endsection
