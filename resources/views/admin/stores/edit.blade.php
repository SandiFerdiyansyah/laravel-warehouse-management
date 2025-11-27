@extends('layouts.admin')

@section('title', 'Edit Toko')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Edit Toko</h1>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('admin.stores.update', $store) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Nama Toko</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required value="{{ $store->name }}">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Email</label>
                <input type="email" name="email" class="w-full border rounded px-3 py-2 bg-gray-100" disabled value="{{ $store->user->email }}">
                <p class="text-xs text-gray-500 mt-1">Email tidak dapat diubah</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Nama Pemilik</label>
                <input type="text" name="contact_person" class="w-full border rounded px-3 py-2" value="{{ $store->contact_person ?? '' }}">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Telepon</label>
                <input type="text" name="phone" class="w-full border rounded px-3 py-2" value="{{ $store->phone ?? '' }}">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Alamat</label>
                <textarea name="address" class="w-full border rounded px-3 py-2" rows="3">{{ $store->address ?? '' }}</textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan</button>
                <a href="{{ route('admin.stores.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
            </div>
        </form>
    </div>
@endsection
