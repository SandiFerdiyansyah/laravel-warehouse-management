@extends('layouts.store')

@section('title', 'Detail Permintaan Barang')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Detail Permintaan #{{ $request->id }}</h1>
        <a href="{{ route('store.product_requests.index') }}" class="text-gray-600 hover:text-gray-800">← Kembali</a>
    </div>

    <!-- Status Progress -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div class="flex-1 text-center">
                <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center mx-auto mb-2">✓</div>
                <p class="text-xs font-semibold">Diminta</p>
                <p class="text-xs text-gray-600">{{ $request->created_at->format('d M') }}</p>
            </div>
            <div class="flex-1 h-1 bg-gray-300 mx-2"></div>

            <div class="flex-1 text-center">
                <div class="w-8 h-8 @if(in_array($request->status, ['verified', 'approved', 'shipped', 'delivered'])) bg-green-500 @else bg-gray-300 @endif text-white rounded-full flex items-center justify-center mx-auto mb-2">
                    @if($request->status === 'rejected') ✗ @else ✓ @endif
                </div>
                <p class="text-xs font-semibold">Diverifikasi</p>
                <p class="text-xs text-gray-600">{{ $request->verified_at?->format('d M') ?? '-' }}</p>
            </div>
            <div class="flex-1 h-1 bg-gray-300 mx-2"></div>

            <div class="flex-1 text-center">
                <div class="w-8 h-8 @if(in_array($request->status, ['approved', 'shipped', 'delivered'])) bg-green-500 @else bg-gray-300 @endif text-white rounded-full flex items-center justify-center mx-auto mb-2">✓</div>
                <p class="text-xs font-semibold">Disetujui</p>
                <p class="text-xs text-gray-600">{{ $request->approved_at?->format('d M') ?? '-' }}</p>
            </div>
            <div class="flex-1 h-1 bg-gray-300 mx-2"></div>

            <div class="flex-1 text-center">
                <div class="w-8 h-8 @if(in_array($request->status, ['shipped', 'delivered'])) bg-green-500 @else bg-gray-300 @endif text-white rounded-full flex items-center justify-center mx-auto mb-2">✓</div>
                <p class="text-xs font-semibold">Dikirim</p>
                <p class="text-xs text-gray-600">{{ $request->shipped_at?->format('d M') ?? '-' }}</p>
            </div>
            <div class="flex-1 h-1 bg-gray-300 mx-2"></div>

            <div class="flex-1 text-center">
                <div class="w-8 h-8 @if($request->status === 'delivered') bg-green-500 @else bg-gray-300 @endif text-white rounded-full flex items-center justify-center mx-auto mb-2">✓</div>
                <p class="text-xs font-semibold">Diterima</p>
                <p class="text-xs text-gray-600">{{ $request->delivered_at?->format('d M') ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-8">
        <!-- Request Details -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Detail Permintaan</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">ID Permintaan</p>
                    <p class="font-semibold">#{{ $request->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Produk</p>
                    <p class="font-semibold">{{ $request->product->name }}</p>
                    <p class="text-xs text-gray-600">SKU: {{ $request->product->sku }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Lokasi Storage</p>
                    <p class="font-semibold">{{ $request->storageLocation->location_code }}</p>
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

        <!-- Status & Timeline -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Status Permintaan</h2>
            <div class="space-y-4">
                <div>
                    <span class="
                        @if($request->status === 'pending') bg-gray-100 text-gray-800
                        @elseif($request->status === 'verified') bg-yellow-100 text-yellow-800
                        @elseif($request->status === 'approved') bg-blue-100 text-blue-800
                        @elseif($request->status === 'shipped') bg-blue-100 text-blue-800
                        @elseif($request->status === 'delivered') bg-green-100 text-green-800
                        @elseif($request->status === 'rejected') bg-red-100 text-red-800
                        @endif
                        px-3 py-1 rounded text-sm font-semibold">
                        @if($request->status === 'pending') 📋 Pending
                        @elseif($request->status === 'verified') ⏳ Terverifikasi
                        @elseif($request->status === 'approved') ✓ Disetujui
                        @elseif($request->status === 'shipped') 📦 Dalam Pengiriman
                        @elseif($request->status === 'delivered') ✅ Diterima
                        @elseif($request->status === 'rejected') ✗ Ditolak
                        @endif
                    </span>
                </div>

                <div>
                    <p class="text-sm text-gray-600">Operator</p>
                    <p class="text-sm">{{ $request->operator->name ?? 'Belum ada' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600">Admin</p>
                    <p class="text-sm">{{ $request->admin->name ?? 'Belum ada' }}</p>
                </div>

                @if($request->rejection_reason)
                    <div class="bg-red-50 border border-red-200 p-3 rounded">
                        <p class="text-sm text-red-700"><strong>Alasan Penolakan:</strong> {{ $request->rejection_reason }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Verification Notes -->
    @if($request->verification_notes)
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Catatan Verifikasi</h2>
            <p class="text-sm">{{ $request->verification_notes }}</p>
        </div>
    @endif

    <!-- Shipment Tracking -->
    @if($request->shipment)
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Informasi Pengiriman</h2>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600">No. Tracking</p>
                    <p class="font-mono font-bold">{{ $request->shipment->tracking_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tanggal Pengiriman</p>
                    <p class="text-sm">{{ $request->shipment->shipped_at?->format('d M Y H:i') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="text-sm">
                        @if($request->shipment->status === 'delivered')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Delivered</span>
                        @else
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">In Transit</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($request->shipment->status === 'in_transit')
                <form action="{{ route('store.product_requests.confirm_delivery', $request->id) }}" method="POST" class="mt-4 pt-4 border-t">
                    @csrf
                    <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 font-semibold">✅ Konfirmasi Diterima</button>
                </form>
            @endif
        </div>
    @endif
@endsection
