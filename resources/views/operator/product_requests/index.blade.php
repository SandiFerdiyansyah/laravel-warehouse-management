@extends('layouts.admin')

@section('title', 'Permintaan Barang - Operator')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Permintaan Barang untuk Verifikasi</h1>

    <!-- Tabs Navigation -->
    <div class="tabs mb-6 border-b border-gray-300">
        <button class="tab-btn active px-4 py-2 font-semibold border-b-2 border-blue-500" data-tab="pending">
            📋 Menunggu Verifikasi
        </button>
        <button class="tab-btn px-4 py-2 font-semibold border-b-2 border-transparent hover:border-gray-300" data-tab="verified">
            ✓ Terverifikasi
        </button>
        <button class="tab-btn px-4 py-2 font-semibold border-b-2 border-transparent hover:border-gray-300" data-tab="shipped">
            📦 Dalam Pengiriman
        </button>
    </div>

    <!-- Tab: Pending Requests -->
    <div id="pending" class="tab-content">
        @if($pendingRequests->count())
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2 text-left">#</th>
                            <th class="border p-2 text-left">Toko</th>
                            <th class="border p-2 text-left">Produk</th>
                            <th class="border p-2 text-left">Lokasi Storage</th>
                            <th class="border p-2 text-center">Qty Diminta</th>
                            <th class="border p-2 text-left">Tanggal Permintaan</th>
                            <th class="border p-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRequests as $req)
                            <tr class="hover:bg-gray-50">
                                <td class="border p-2">{{ $req->id }}</td>
                                <td class="border p-2">{{ $req->store->name }}</td>
                                <td class="border p-2">{{ $req->product->name }} ({{ $req->product->sku }})</td>
                                <td class="border p-2">{{ $req->storageLocation->location_code }}</td>
                                <td class="border p-2 text-center font-semibold">{{ $req->quantity_requested }}</td>
                                <td class="border p-2">{{ $req->created_at->format('d M Y H:i') }}</td>
                                <td class="border p-2 text-center">
                                    <a href="{{ route('operator.product_requests.verify', $req->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">Verifikasi</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $pendingRequests->links() }}</div>
        @else
            <p class="text-gray-500 text-center py-8">Tidak ada permintaan barang yang perlu diverifikasi.</p>
        @endif
    </div>

    <!-- Tab: Verified Requests -->
    <div id="verified" class="tab-content hidden">
        @if($verifiedRequests->count())
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2 text-left">#</th>
                            <th class="border p-2 text-left">Toko</th>
                            <th class="border p-2 text-left">Produk</th>
                            <th class="border p-2 text-center">Qty Terverifikasi</th>
                            <th class="border p-2 text-left">Status</th>
                            <th class="border p-2 text-left">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($verifiedRequests as $req)
                            <tr class="hover:bg-gray-50">
                                <td class="border p-2">{{ $req->id }}</td>
                                <td class="border p-2">{{ $req->store->name }}</td>
                                <td class="border p-2">{{ $req->product->name }}</td>
                                <td class="border p-2 text-center font-semibold">{{ $req->quantity_verified }}</td>
                                <td class="border p-2"><span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">{{ ucfirst($req->status) }}</span></td>
                                <td class="border p-2 text-xs">{{ Str::limit($req->verification_notes, 50) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $verifiedRequests->links() }}</div>
        @else
            <p class="text-gray-500 text-center py-8">Tidak ada permintaan yang sudah diverifikasi.</p>
        @endif
    </div>

    <!-- Tab: Shipped Requests -->
    <div id="shipped" class="tab-content hidden">
        @if($shippedRequests->count())
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2 text-left">#</th>
                            <th class="border p-2 text-left">Toko</th>
                            <th class="border p-2 text-left">Produk</th>
                            <th class="border p-2 text-left">Tracking</th>
                            <th class="border p-2 text-left">Status Pengiriman</th>
                            <th class="border p-2 text-left">Tanggal Kirim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shippedRequests as $req)
                            <tr class="hover:bg-gray-50">
                                <td class="border p-2">{{ $req->id }}</td>
                                <td class="border p-2">{{ $req->store->name }}</td>
                                <td class="border p-2">{{ $req->product->name }}</td>
                                <td class="border p-2 font-mono text-xs">{{ $req->shipment?->tracking_number ?? '-' }}</td>
                                <td class="border p-2">
                                    @if($req->shipment && $req->shipment->status === 'delivered')
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Delivered</span>
                                    @else
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">In Transit</span>
                                    @endif
                                </td>
                                <td class="border p-2">{{ $req->shipped_at?->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $shippedRequests->links() }}</div>
        @else
            <p class="text-gray-500 text-center py-8">Tidak ada permintaan dalam pengiriman.</p>
        @endif
    </div>

    <script>
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabName = this.dataset.tab;
                
                // Hide all tabs
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Remove active from all buttons
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('border-blue-500');
                    b.classList.add('border-transparent');
                });
                
                // Show selected tab
                document.getElementById(tabName).classList.remove('hidden');
                
                // Mark button as active
                this.classList.remove('border-transparent');
                this.classList.add('border-blue-500');
            });
        });
    </script>
@endsection
