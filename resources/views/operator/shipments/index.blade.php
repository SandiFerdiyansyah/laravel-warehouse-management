@extends('layouts.operator')

@section('title', 'Pengiriman Stok')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Pengiriman Stok untuk Storage</h1>

    <div class="bg-white p-6 rounded-lg shadow">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
        @endif

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="py-2 text-left">#</th>
                    <th class="py-2 text-left">Produk</th>
                    <th class="py-2 text-left">Toko</th>
                    <th class="py-2 text-left">Qty</th>
                    <th class="py-2 text-left">Lokasi</th>
                    <th class="py-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shipments as $s)
                    <tr class="border-b">
                        <td class="py-2">{{ $s->id }}</td>
                        <td class="py-2">{{ $s->product->name ?? '-' }}</td>
                        <td class="py-2">{{ $s->store_name }}</td>
                        <td class="py-2">{{ $s->quantity }}</td>
                        <td class="py-2">{{ $s->storageLocation->location_code ?? '-' }}</td>
                        <td class="py-2">
                            <form action="{{ route('operator.shipments.receive', $s) }}" method="post" style="display:inline">
                                @csrf
                                <button class="bg-green-500 text-white px-3 py-1 rounded">Terima</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
