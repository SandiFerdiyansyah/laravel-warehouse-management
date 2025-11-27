@extends('layouts.admin')

@section('title', 'Tambah Toko')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Tambah Toko Baru</h1>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('admin.stores.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Nama Toko</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name') }}">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Email</label>
                <input type="email" name="email" class="w-full border rounded px-3 py-2" required value="{{ old('email') }}">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Password</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Nama Pemilik</label>
                <input type="text" name="contact_person" class="w-full border rounded px-3 py-2" value="{{ old('contact_person') }}">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Telepon</label>
                <input type="text" name="phone" class="w-full border rounded px-3 py-2" value="{{ old('phone') }}">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Alamat</label>
                <textarea name="address" class="w-full border rounded px-3 py-2" rows="3">{{ old('address') }}</textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan</button>
                <a href="{{ route('admin.stores.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
            </div>
        </form>
    </div>
@endsection
