@extends('layouts.admin')

@section('title', 'Permintaan Barang - Admin')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Manajemen Permintaan Barang dari Toko</h1>

    <!-- Tabs Navigation -->
    <div class="tabs mb-6 border-b border-gray-300">
        <button class="tab-btn active px-4 py-2 font-semibold border-b-2 border-blue-500" data-tab="pending">
            📋 Pending ({{ $pendingRequests->total() }})
        </button>
        <button class="tab-btn px-4 py-2 font-semibold border-b-2 border-transparent hover:border-gray-300" data-tab="awaiting">
            ⏳ Menunggu Approval ({{ $awaitingApprovalRequests->total() }})
        </button>
        <button class="tab-btn px-4 py-2 font-semibold border-b-2 border-transparent hover:border-gray-300" data-tab="approved">
            ✓ Approved ({{ $approvedRequests->total() }})
        </button>
        <button class="tab-btn px-4 py-2 font-semibold border-b-2 border-transparent hover:border-gray-300" data-tab="shipped">
            📦 Shipped ({{ $shippedRequests->total() }})
        </button>
        <button class="tab-btn px-4 py-2 font-semibold border-b-2 border-transparent hover:border-gray-300" data-tab="delivered">
            ✅ Delivered ({{ $deliveredRequests->total() }})
        </button>
    </div>

    <!-- Tab: Pending -->
    <div id="pending" class="tab-content">
        <p class="text-sm text-gray-600 mb-4">Permintaan yang baru dibuat, belum diverifikasi operator.</p>
        @if($pendingRequests->count())
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2 text-left">#</th>
                            <th class="border p-2 text-left">Toko</th>
                            <th class="border p-2 text-left">Produk</th>
                            <th class="border p-2 text-left">Storage</th>
                            <th class="border p-2 text-center">Qty</th>
                            <th class="border p-2 text-left">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRequests as $req)
                            <tr class="hover:bg-gray-50">
                                <td class="border p-2">{{ $req->id }}</td>
                                <td class="border p-2">{{ $req->store->name }}</td>
                                <td class="border p-2">{{ $req->product->name }}</td>
                                <td class="border p-2">{{ $req->storageLocation->location_code }}</td>
                                <td class="border p-2 text-center">{{ $req->quantity_requested }}</td>
                                <td class="border p-2">{{ $req->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $pendingRequests->links() }}</div>
        @else
            <p class="text-gray-500 text-center py-8">Tidak ada permintaan pending.</p>
        @endif
    </div>

    <!-- Tab: Awaiting Approval -->
    <div id="awaiting" class="tab-content hidden">
        <p class="text-sm text-gray-600 mb-4">Permintaan yang sudah diverifikasi operator, menunggu approval dari admin.</p>
        @if($awaitingApprovalRequests->count())
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2 text-left">#</th>
                            <th class="border p-2 text-left">Toko</th>
                            <th class="border p-2 text-left">Produk</th>
                            <th class="border p-2 text-center">Qty Diminta</th>
                            <th class="border p-2 text-center">Qty Terverifikasi</th>
                            <th class="border p-2 text-left">Operator</th>
                            <th class="border p-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($awaitingApprovalRequests as $req)
                            <tr class="hover:bg-gray-50">
                                <td class="border p-2">{{ $req->id }}</td>
                                <td class="border p-2">{{ $req->store->name }}</td>
                                <td class="border p-2">{{ $req->product->name }}</td>
                                <td class="border p-2 text-center">{{ $req->quantity_requested }}</td>
                                <td class="border p-2 text-center font-semibold">{{ $req->quantity_verified }}</td>
                                <td class="border p-2">{{ $req->operator->name ?? '-' }}</td>
                                <td class="border p-2 text-center">
                                    <a href="{{ route('admin.product_requests.show', $req->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $awaitingApprovalRequests->links() }}</div>
        @else
            <p class="text-gray-500 text-center py-8">Tidak ada permintaan menunggu approval.</p>
        @endif
    </div>

    <!-- Tab: Approved -->
    <div id="approved" class="tab-content hidden">
        <p class="text-sm text-gray-600 mb-4">Permintaan yang sudah disetujui, siap dikirim ke toko.</p>
        @if($approvedRequests->count())
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2 text-left">#</th>
                            <th class="border p-2 text-left">Toko</th>
                            <th class="border p-2 text-left">Produk</th>
                            <th class="border p-2 text-center">Qty</th>
                            <th class="border p-2 text-left">Admin Approve</th>
                            <th class="border p-2 text-left">Tanggal Approve</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvedRequests as $req)
                            <tr class="hover:bg-gray-50">
                                <td class="border p-2">{{ $req->id }}</td>
                                <td class="border p-2">{{ $req->store->name }}</td>
                                <td class="border p-2">{{ $req->product->name }}</td>
                                <td class="border p-2 text-center">{{ $req->quantity_verified }}</td>
                                <td class="border p-2">{{ $req->admin->name ?? '-' }}</td>
                                <td class="border p-2">{{ $req->approved_at?->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $approvedRequests->links() }}</div>
        @else
            <p class="text-gray-500 text-center py-8">Tidak ada permintaan yang approved.</p>
        @endif
    </div>

    <!-- Tab: Shipped -->
    <div id="shipped" class="tab-content hidden">
        <p class="text-sm text-gray-600 mb-4">Permintaan yang dalam pengiriman ke toko.</p>
        @if($shippedRequests->count())
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2 text-left">#</th>
                            <th class="border p-2 text-left">Toko</th>
                            <th class="border p-2 text-left">Produk</th>
                            <th class="border p-2 text-center">Qty</th>
                            <th class="border p-2 text-left">Tracking</th>
                            <th class="border p-2 text-left">Tanggal Kirim</th>
                            <th class="border p-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shippedRequests as $req)
                            <tr class="hover:bg-gray-50">
                                <td class="border p-2">{{ $req->id }}</td>
                                <td class="border p-2">{{ $req->store->name }}</td>
                                <td class="border p-2">{{ $req->product->name }}</td>
                                <td class="border p-2 text-center">{{ $req->quantity_verified }}</td>
                                <td class="border p-2 font-mono text-xs">{{ $req->shipment?->tracking_number ?? '-' }}</td>
                                <td class="border p-2">{{ $req->shipped_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td class="border p-2 text-center">
                                    <a href="{{ route('admin.product_requests.show', $req->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">Detail</a>
                                </td>
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

    <!-- Tab: Delivered -->
    <div id="delivered" class="tab-content hidden">
        <p class="text-sm text-gray-600 mb-4">Permintaan yang sudah diterima oleh toko.</p>
        @if($deliveredRequests->count())
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2 text-left">#</th>
                            <th class="border p-2 text-left">Toko</th>
                            <th class="border p-2 text-left">Produk</th>
                            <th class="border p-2 text-center">Qty</th>
                            <th class="border p-2 text-left">Tanggal Terima</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveredRequests as $req)
                            <tr class="hover:bg-gray-50">
                                <td class="border p-2">{{ $req->id }}</td>
                                <td class="border p-2">{{ $req->store->name }}</td>
                                <td class="border p-2">{{ $req->product->name }}</td>
                                <td class="border p-2 text-center">{{ $req->quantity_verified }}</td>
                                <td class="border p-2">{{ $req->delivered_at?->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $deliveredRequests->links() }}</div>
        @else
            <p class="text-gray-500 text-center py-8">Tidak ada permintaan yang delivered.</p>
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
