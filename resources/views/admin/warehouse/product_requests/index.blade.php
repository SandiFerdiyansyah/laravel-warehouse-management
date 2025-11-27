@extends('layouts.admin')

@section('title', 'Permintaan Barang - Gudang')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Permintaan Barang - Gudang</h1>
    </div>

    <!-- Tabs Navigation -->
    <div class="tabs mb-6 border-b border-gray-300">
        <button class="tab-btn active px-4 py-2 font-semibold border-b-2 border-blue-500" data-tab="pending">
            📋 Menunggu Pemilihan Lokasi
        </button>
        <button class="tab-btn px-4 py-2 font-semibold border-b-2 border-transparent hover:border-gray-300" data-tab="processing">
            ⏳ Menunggu Verifikasi Operator
        </button>
        <button class="tab-btn px-4 py-2 font-semibold border-b-2 border-transparent hover:border-gray-300" data-tab="verified">
            ✓ Terverifikasi
        </button>
    </div>

    <!-- Tab: Pending (Membutuhkan Pemilihan Lokasi Storage) -->
    <div id="pending" class="tab-content">
        @if($pendingRequests->count())
            <div class="space-y-4">
                @foreach($pendingRequests as $req)
                    <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-bold text-lg">{{ $req->product->name }} 
                                    <span class="text-xs text-gray-600 font-normal">(SKU: {{ $req->product->sku }})</span>
                                </h3>
                                <p class="text-sm text-gray-600">Gudang: {{ $req->warehouse->name }}</p>
                                <p class="text-sm text-gray-600">Dari Toko: {{ $req->store->name }}</p>
                            </div>
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded text-sm font-semibold">
                                ⏳ Menunggu Lokasi
                            </span>
                        </div>

                        <div class="grid grid-cols-4 gap-4 mb-4 text-sm">
                            <div>
                                <p class="text-gray-600">Qty Diminta</p>
                                <p class="font-bold text-lg">{{ $req->quantity_requested }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Tanggal Permintaan</p>
                                <p class="text-xs">{{ $req->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="text-right col-span-2">
                                <a href="{{ route('admin.warehouse_product_requests.select_location', $req->id) }}" 
                                   class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-sm font-semibold">
                                   Pilih Lokasi Storage →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">{{ $pendingRequests->links() }}</div>
        @else
            <p class="text-gray-500 text-center py-12">Tidak ada permintaan menunggu pemilihan lokasi storage.</p>
        @endif
    </div>

    <!-- Tab: Processing (Sudah Dipilih Lokasi, Menunggu Verifikasi Operator) -->
    <div id="processing" class="tab-content hidden">
        @if($processingRequests->count())
            <div class="space-y-4">
                @foreach($processingRequests as $req)
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-bold text-lg">{{ $req->product->name }}</h3>
                                <p class="text-sm text-gray-600">Lokasi Storage: {{ $req->storageLocation->location_code }}</p>
                                <p class="text-sm text-gray-600">Dari Toko: {{ $req->store->name }}</p>
                            </div>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded text-sm font-semibold">
                                ⏳ Verifikasi Operator
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">
                            Qty: {{ $req->quantity_requested }} | Dipilih pada: {{ $req->updated_at->format('d M Y H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">{{ $processingRequests->links() }}</div>
        @else
            <p class="text-gray-500 text-center py-12">Tidak ada permintaan dalam tahap verifikasi operator.</p>
        @endif
    </div>

    <!-- Tab: Verified -->
    <div id="verified" class="tab-content hidden">
        @if($verifiedRequests->count())
            <div class="space-y-4">
                @foreach($verifiedRequests as $req)
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-bold text-lg">{{ $req->product->name }}</h3>
                                <p class="text-sm text-gray-600">Lokasi Storage: {{ $req->storageLocation->location_code }}</p>
                                <p class="text-sm text-gray-600">Dari Toko: {{ $req->store->name }}</p>
                            </div>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm font-semibold">
                                ✓ Terverifikasi
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">
                            Qty Verified: {{ $req->quantity_verified }} | Operator: {{ $req->operator->name }}
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">{{ $verifiedRequests->links() }}</div>
        @else
            <p class="text-gray-500 text-center py-12">Tidak ada permintaan yang terverifikasi.</p>
        @endif
    </div>

    <script>
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabName = this.dataset.tab;
                
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('border-blue-500');
                    b.classList.add('border-transparent');
                });
                
                document.getElementById(tabName).classList.remove('hidden');
                
                this.classList.remove('border-transparent');
                this.classList.add('border-blue-500');
            });
        });
    </script>
@endsection
