@extends('layouts.admin')

@section('title', 'Detail Permintaan Barang')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Detail Permintaan Barang #{{ $request->id }}</h1>
        <a href="{{ route('admin.product_requests.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">← Kembali</a>
    </div>

    <!-- Status Badge -->
    <div class="mb-6">
        @php
            $statusColor = match($request->status) {
                'pending' => 'bg-gray-100 text-gray-800',
                'verified' => 'bg-yellow-100 text-yellow-800',
                'approved' => 'bg-green-100 text-green-800',
                'shipped' => 'bg-blue-100 text-blue-800',
                'delivered' => 'bg-green-100 text-green-800',
                'rejected' => 'bg-red-100 text-red-800',
                default => 'bg-gray-100 text-gray-800',
            };
            $statusLabel = match($request->status) {
                'pending' => '📋 Pending',
                'verified' => '⏳ Terverifikasi',
                'approved' => '✓ Disetujui',
                'shipped' => '📦 Dalam Pengiriman',
                'delivered' => '✅ Diterima',
                'rejected' => '✗ Ditolak',
                default => ucfirst($request->status),
            };
        @endphp
        <span class="{{ $statusColor }} px-4 py-2 rounded text-sm font-semibold">{{ $statusLabel }}</span>
    </div>

    <div class="grid grid-cols-3 gap-6 mb-8">
        <!-- Toko Info -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Informasi Toko</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Nama Toko</p>
                    <p class="font-semibold">{{ $request->store->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Kontak Person</p>
                    <p class="text-sm">{{ $request->store->contact_person ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Telepon</p>
                    <p class="text-sm">{{ $request->store->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Alamat</p>
                    <p class="text-xs">{{ $request->store->address ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Produk Info -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Informasi Produk</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Nama Produk</p>
                    <p class="font-semibold">{{ $request->product->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">SKU</p>
                    <p class="text-sm font-mono">{{ $request->product->sku }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Kategori</p>
                    <p class="text-sm">{{ $request->product->category->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Qty Diminta</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $request->quantity_requested }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Qty Terverifikasi</p>
                    <p class="text-2xl font-bold text-green-600">{{ $request->quantity_verified ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Storage Info -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Lokasi Storage</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Kode Lokasi</p>
                    <p class="font-semibold text-lg">{{ $request->storageLocation->location_code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tipe</p>
                    <p class="text-sm">{{ $request->storageLocation->location_type ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Kapasitas</p>
                    <p class="text-sm">{{ $request->storageLocation->capacity ?? '-' }} unit</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline & Notes -->
    <div class="grid grid-cols-2 gap-6 mb-8">
        <!-- Operator Verification -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Verifikasi Operator</h2>
            @if($request->operator)
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Operator</p>
                        <p class="font-semibold">{{ $request->operator->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tanggal Verifikasi</p>
                        <p class="text-sm">{{ $request->verified_at?->format('d M Y H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Catatan</p>
                        <p class="text-sm bg-gray-50 p-3 rounded">{{ $request->verification_notes ?? '-' }}</p>
                    </div>
                </div>
            @else
                <p class="text-gray-500 text-sm">Belum diverifikasi operator.</p>
            @endif
        </div>

        <!-- Admin Approval -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Approval Admin</h2>
            @if($request->admin)
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Admin</p>
                        <p class="font-semibold">{{ $request->admin->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tanggal Approval</p>
                        <p class="text-sm">{{ $request->approved_at?->format('d M Y H:i') ?? '-' }}</p>
                    </div>
                    @if($request->rejection_reason)
                        <div>
                            <p class="text-sm text-gray-600">Alasan Penolakan</p>
                            <p class="text-sm bg-red-50 p-3 rounded text-red-700">{{ $request->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-gray-500 text-sm">Belum di-approve admin.</p>
            @endif
        </div>
    </div>

    <!-- Shipment Info -->
    @if($request->shipment)
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Informasi Pengiriman</h2>
            <div class="grid grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-600">No. Tracking</p>
                    <p class="font-mono text-sm font-semibold">{{ $request->shipment->tracking_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status Pengiriman</p>
                    <p class="text-sm">
                        @if($request->shipment->status === 'delivered')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Delivered</span>
                        @else
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">In Transit</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tanggal Kirim</p>
                    <p class="text-sm">{{ $request->shipment->shipped_at?->format('d M Y H:i') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tanggal Terima</p>
                    <p class="text-sm">{{ $request->shipment->delivered_at?->format('d M Y H:i') ?? '-' }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    @if($request->status === 'verified' && !$request->admin_id)
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Proses Approval</h2>
            <form action="{{ route('admin.product_requests.approve', $request->id) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold mb-2">Catatan Admin *</label>
                    <textarea name="admin_notes" rows="3" class="w-full border p-3 rounded @error('admin_notes') border-red-500 @enderror" placeholder="Masukkan catatan atau alasan approval..." required>{{ old('admin_notes') }}</textarea>
                    @error('admin_notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" name="action" value="approve" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 font-semibold">✓ Setujui Pengiriman</button>
                    <button type="submit" name="action" value="reject" class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600 font-semibold">✗ Tolak Permintaan</button>
                </div>
            </form>
        </div>
    @elseif($request->status === 'shipped' && $request->shipment && $request->shipment->status !== 'delivered')
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Update Status</h2>
            <form action="{{ route('admin.product_requests.delivered', $request->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 font-semibold">✅ Mark as Delivered</button>
            </form>
        </div>
    @endif
@endsection
