@extends('layouts.admin')

@section('title', 'Shipments')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Pengiriman Stok ke Toko</h1>

    <a href="{{ route('admin.shipments.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Buat Pengiriman</a>

    <div class="bg-white p-6 rounded-lg shadow">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="py-2 text-left">#</th>
                    <th class="py-2 text-left">Produk</th>
                    <th class="py-2 text-left">Toko</th>
                    <th class="py-2 text-left">Qty</th>
                    <th class="py-2 text-left">Lokasi</th>
                    <th class="py-2 text-left">Status</th>
                    <th class="py-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shipments as $s)
                    <tr class="border-b">
                        <td class="py-2">{{ $s->id }}</td>
                        <td class="py-2">{{ $s->product->name ?? '-' }}</td>
                        <td class="py-2">{{ $s->store->name ?? $s->store_name ?? '-' }}</td>
                        <td class="py-2">{{ $s->quantity }}</td>
                        <td class="py-2">{{ $s->storageLocation->location_code ?? '-' }}</td>
                        <td class="py-2">{{ $s->status }}</td>
                        <td class="py-2">
                            <!-- no actions yet -->
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $shipments->links() }}
        </div>
    </div>
@endsection
