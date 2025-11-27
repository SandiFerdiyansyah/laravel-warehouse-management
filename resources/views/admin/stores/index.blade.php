@extends('layouts.admin')

@section('title', 'Stores')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Toko</h1>
        <a href="{{ route('admin.stores.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>Tambah Toko
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium">#</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Nama Toko</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Telepon</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Alamat</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stores as $store)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $store->id }}</td>
                        <td class="px-6 py-4 font-medium">{{ $store->name }}</td>
                        <td class="px-6 py-4">{{ $store->user->email }}</td>
                        <td class="px-6 py-4">{{ $store->phone ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $store->address ?? '-' }}</td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="{{ route('admin.stores.edit', $store) }}" class="text-blue-500 hover:underline text-sm">Edit</a>
                            <form action="{{ route('admin.stores.destroy', $store) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline text-sm" onclick="return confirm('Delete this store?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">
            {{ $stores->links() }}
        </div>
    </div>
@endsection
