@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Monetary totals: total rupiah masuk / keluar and quantities -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-green-500 rounded-full">
                    <i class="fas fa-wallet text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Rupiah Masuk</p>
                    <p class="text-2xl font-bold">{{ isset($moneyStats) ? 'Rp ' . number_format($moneyStats['total_in_value'],0,',','.') : 'Rp 0' }}</p>
                    <p class="text-sm text-gray-400">Qty: {{ $moneyStats['total_in_qty'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-red-500 rounded-full">
                    <i class="fas fa-cash-register text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Rupiah Keluar (Pendapatan)</p>
                    <p class="text-2xl font-bold">{{ isset($moneyStats) ? 'Rp ' . number_format($moneyStats['total_out_value'],0,',','.') : 'Rp 0' }}</p>
                    <p class="text-sm text-gray-400">Qty: {{ $moneyStats['total_out_qty'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-500 rounded-full">
                    <i class="fas fa-chart-line text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Estimasi Keuntungan</p>
                    @php $profit = $moneyStats['total_profit'] ?? 0; @endphp
                    <p class="text-2xl font-bold text-{{ $profit >= 0 ? 'green' : 'red' }}-600">{{ isset($moneyStats) ? 'Rp ' . number_format($profit,0,',','.') : 'Rp 0' }}</p>
                    <p class="text-sm text-gray-400">(Berdasarkan harga jual & rata-rata biaya)</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-green-500 rounded-full">
                    <i class="fas fa-arrow-down text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Masuk</p>
                    <p class="text-2xl font-bold">{{ $stats['total_in'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-red-500 rounded-full">
                    <i class="fas fa-arrow-up text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Keluar</p>
                    <p class="text-2xl font-bold">{{ $stats['total_out'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-500 rounded-full">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Stok Menipis</p>
                    <p class="text-2xl font-bold">{{ $stats['low_stock_products'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-blue-500 rounded-full">
                    <i class="fas fa-box text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Produk</p>
                    <p class="text-2xl font-bold">{{ $stats['total_products'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Storage Locations</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $stats['filled_locations'] }}</p>
                    <p class="text-gray-600">Terisi</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-400">{{ $stats['empty_locations'] }}</p>
                    <p class="text-gray-600">Kosong</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.products.create') }}" class="bg-blue-500 text-white p-3 rounded text-center hover:bg-blue-600">
                    <i class="fas fa-plus mb-2"></i>
                    <p>Tambah Produk</p>
                </a>
                <a href="{{ route('admin.products.scan') }}" class="bg-green-500 text-white p-3 rounded text-center hover:bg-green-600">
                    <i class="fas fa-qrcode mb-2"></i>
                    <p>Scan QR Code</p>
                </a>
                <a href="{{ route('admin.movements.index') }}" class="bg-indigo-500 text-white p-3 rounded text-center hover:bg-indigo-600">
                    <i class="fas fa-history mb-2"></i>
                    <p>Movements</p>
                </a>
                <a href="{{ route('admin.shipments.index') }}" class="bg-teal-500 text-white p-3 rounded text-center hover:bg-teal-600">
                    <i class="fas fa-truck mb-2"></i>
                    <p>Pengiriman Stok</p>
                </a>
                <a href="{{ route('admin.po.create') }}" class="bg-purple-500 text-white p-3 rounded text-center hover:bg-purple-600">
                    <i class="fas fa-file-invoice mb-2"></i>
                    <p>Buat PO</p>
                </a>
                <a href="{{ route('admin.storage.create') }}" class="bg-orange-500 text-white p-3 rounded text-center hover:bg-orange-600">
                    <i class="fas fa-warehouse mb-2"></i>
                    <p>Tambah Lokasi</p>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Produk Stok Menipis</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">SKU</th>
                            <th class="text-left py-2">Nama</th>
                            <th class="text-left py-2">Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $product)
                            <tr class="border-b">
                                <td class="py-2">{{ $product->sku }}</td>
                                <td class="py-2">{{ $product->name }}</td>
                                <td class="py-2">
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($lowStockProducts->isEmpty())
                    <p class="text-gray-500 text-center py-4">Tidak ada produk dengan stok menipis</p>
                @endif
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Aktivitas Terkini</h2>
            <div class="space-y-3">
                @foreach($recentMovements as $movement)
                    <div class="flex items-center justify-between py-2 border-b">
                        <div>
                            <p class="font-medium">{{ $movement->product->name }}</p>
                            <p class="text-sm text-gray-500">{{ $movement->user->name }} • {{ $movement->timestamp->diffForHumans() }}</p>
                        </div>
                        <div class="text-right">
                            @if($movement->type === 'in')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                    +{{ $movement->quantity }}
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">
                                    -{{ $movement->quantity }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if($recentMovements->isEmpty())
                    <p class="text-gray-500 text-center py-4">Belum ada aktivitas</p>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-6 bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Top Produk (Kontribusi Keuntungan)</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="py-2 text-left">SKU</th>
                        <th class="py-2 text-left">Nama</th>
                        <th class="py-2 text-left">Harga Jual</th>
                        <th class="py-2 text-left">Rata-rata Biaya</th>
                        <th class="py-2 text-left">Qty Keluar</th>
                        <th class="py-2 text-left">Estimasi Keuntungan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProfitProducts as $p)
                        <tr class="border-b">
                            <td class="py-2">{{ $p['sku'] }}</td>
                            <td class="py-2">{{ $p['name'] }}</td>
                            <td class="py-2">{{ 'Rp ' . number_format($p['price'],0,',','.') }}</td>
                            <td class="py-2">{{ 'Rp ' . number_format($p['avg_cost'],0,',','.') }}</td>
                            <td class="py-2">{{ $p['out_qty'] }}</td>
                            <td class="py-2">{{ 'Rp ' . number_format($p['profit'],0,',','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if(empty($topProfitProducts))
                <p class="text-gray-500 text-center py-4">Belum ada data keuntungan</p>
            @endif
        </div>
    </div>
    </div>
@endsection