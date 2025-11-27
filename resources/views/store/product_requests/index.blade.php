@extends('layouts.store')

@section('title', 'Permintaan Barang - Toko')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Permintaan Barang</h1>
        <a href="{{ route('store.product_requests.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">+ Buat Permintaan</a>
    </div>

    <!-- Tabs Navigation -->
    <div class="tabs mb-6 border-b border-gray-300">
        <button class="tab-btn active px-4 py-2 font-semibold border-b-2 border-blue-500" data-tab="all">
            Semua
        </button>
        <button class="tab-btn px-4 py-2 font-semibold border-b-2 border-transparent hover:border-gray-300" data-tab="pending">
            📋 Pending
        </button>
        <button class="tab-btn px-4 py-2 font-semibold border-b-2 border-transparent hover:border-gray-300" data-tab="in_process">
            ⏳ Dalam Proses
        </button>
        <button class="tab-btn px-4 py-2 font-semibold border-b-2 border-transparent hover:border-gray-300" data-tab="shipped">
            📦 Dalam Pengiriman
        </button>
        <button class="tab-btn px-4 py-2 font-semibold border-b-2 border-transparent hover:border-gray-300" data-tab="delivered">
            ✅ Diterima
        </button>
        <button class="tab-btn px-4 py-2 font-semibold border-b-2 border-transparent hover:border-gray-300" data-tab="rejected">
            ✗ Ditolak
        </button>
    </div>

    <!-- Tab: All Requests -->
    <div id="all" class="tab-content">
        @if($requests->count())
            <div class="space-y-4">
                @foreach($requests as $req)
                    <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-bold text-lg">{{ $req->product->name }} 
                                    <span class="text-xs text-gray-600 font-normal">(SKU: {{ $req->product->sku }})</span>
                                </h3>
                                <p class="text-sm text-gray-600">Gudang: {{ $req->warehouse->name ?? 'Belum dipilih' }}</p>
                                @if($req->storageLocation)
                                    <p class="text-sm text-gray-600">Lokasi Storage: {{ $req->storageLocation->location_code }}</p>
                                @endif
                            </div>
                            <span class="
                                @if($req->status === 'pending') bg-gray-100 text-gray-800
                                @elseif($req->status === 'verified') bg-yellow-100 text-yellow-800
                                @elseif($req->status === 'approved') bg-blue-100 text-blue-800
                                @elseif($req->status === 'shipped') bg-blue-100 text-blue-800
                                @elseif($req->status === 'delivered') bg-green-100 text-green-800
                                @elseif($req->status === 'rejected') bg-red-100 text-red-800
                                @endif
                                px-3 py-1 rounded text-sm font-semibold">
                                @if($req->status === 'pending') 📋 Pending
                                @elseif($req->status === 'verified') ⏳ Terverifikasi
                                @elseif($req->status === 'approved') ✓ Disetujui
                                @elseif($req->status === 'shipped') 📦 Dalam Pengiriman
                                @elseif($req->status === 'delivered') ✅ Diterima
                                @elseif($req->status === 'rejected') ✗ Ditolak
                                @endif
                            </span>
                        </div>

                        <div class="grid grid-cols-5 gap-4 mb-4 text-sm">
                            <div>
                                <p class="text-gray-600">Qty Diminta</p>
                                <p class="font-bold text-lg">{{ $req->quantity_requested }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Qty Terverifikasi</p>
                                <p class="font-bold text-lg">{{ $req->quantity_verified ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Tanggal Permintaan</p>
                                <p class="text-xs">{{ $req->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div>
                                @if($req->shipment)
                                    <p class="text-gray-600">No. Tracking</p>
                                    <p class="font-mono text-xs font-semibold">{{ $req->shipment->tracking_number }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <a href="{{ route('store.product_requests.show', $req->id) }}" class="text-blue-500 hover:underline text-sm">Detail →</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">{{ $requests->links() }}</div>
        @else
            <p class="text-gray-500 text-center py-12">Belum ada permintaan barang. <a href="{{ route('store.product_requests.create') }}" class="text-blue-500 hover:underline">Buat permintaan sekarang</a></p>
        @endif
    </div>

    <!-- Tab: Pending -->
    <div id="pending" class="tab-content hidden">
        @php $pendingReqs = $requests->filter(fn($r) => $r->status === 'pending'); @endphp
        @if($pendingReqs->count())
            <div class="space-y-4">
                @foreach($pendingReqs as $req)
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="font-bold">{{ $req->product->name }}</h3>
                        <p class="text-sm text-gray-600">Qty: {{ $req->quantity_requested }} | {{ $req->created_at->format('d M Y') }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-12">Tidak ada permintaan dengan status pending.</p>
        @endif
    </div>

    <!-- Tab: In Process -->
    <div id="in_process" class="tab-content hidden">
        @php $inProcessReqs = $requests->filter(fn($r) => in_array($r->status, ['verified', 'approved'])); @endphp
        @if($inProcessReqs->count())
            <div class="space-y-4">
                @foreach($inProcessReqs as $req)
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="font-bold">{{ $req->product->name }}</h3>
                        <p class="text-sm text-gray-600">Status: {{ ucfirst($req->status) }} | Qty Terverifikasi: {{ $req->quantity_verified ?? '-' }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-12">Tidak ada permintaan dalam proses.</p>
        @endif
    </div>

    <!-- Tab: Shipped -->
    <div id="shipped" class="tab-content hidden">
        @php $shippedReqs = $requests->filter(fn($r) => $r->status === 'shipped'); @endphp
        @if($shippedReqs->count())
            <div class="space-y-4">
                @foreach($shippedReqs as $req)
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold">{{ $req->product->name }}</h3>
                                <p class="text-sm text-gray-600">Tracking: {{ $req->shipment?->tracking_number }}</p>
                            </div>
                            <a href="{{ route('store.product_requests.show', $req->id) }}" class="text-blue-500 text-sm hover:underline">Track →</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-12">Tidak ada permintaan dalam pengiriman.</p>
        @endif
    </div>

    <!-- Tab: Delivered -->
    <div id="delivered" class="tab-content hidden">
        @php $deliveredReqs = $requests->filter(fn($r) => $r->status === 'delivered'); @endphp
        @if($deliveredReqs->count())
            <div class="space-y-4">
                @foreach($deliveredReqs as $req)
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="font-bold">{{ $req->product->name }}</h3>
                        <p class="text-sm text-gray-600">Diterima: {{ $req->delivered_at?->format('d M Y H:i') }} | Qty: {{ $req->quantity_verified }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-12">Tidak ada permintaan yang diterima.</p>
        @endif
    </div>

    <!-- Tab: Rejected -->
    <div id="rejected" class="tab-content hidden">
        @php $rejectedReqs = $requests->filter(fn($r) => $r->status === 'rejected'); @endphp
        @if($rejectedReqs->count())
            <div class="space-y-4">
                @foreach($rejectedReqs as $req)
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="font-bold">{{ $req->product->name }}</h3>
                        <p class="text-sm text-red-600">Alasan: {{ $req->rejection_reason ?? $req->verification_notes }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-12">Tidak ada permintaan yang ditolak.</p>
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
