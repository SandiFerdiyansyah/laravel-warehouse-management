@extends('layouts.admin')

@section('title', 'Edit Supplier')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Edit Supplier: {{ $supplier->name }}</h1>
    <a href="{{ route('admin.suppliers.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Back to Suppliers
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Company Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $supplier->name) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">Contact Person *</label>
                <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('contact_person')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                <input type="email" id="email" name="email" value="{{ old('email', $supplier->email ?? '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Account Information -->
        @if($supplier->user)
        <div class="mt-6 p-4 bg-blue-50 rounded border border-blue-200">
            <div class="flex items-center mb-4">
                <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                <h3 class="text-sm font-semibold text-gray-700">Login Account Information</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-3 rounded">
                    <p class="text-xs text-gray-600">Account Email</p>
                    <p class="text-sm font-medium text-gray-900">{{ $supplier->user->email }}</p>
                </div>
                <div class="bg-white p-3 rounded">
                    <p class="text-xs text-gray-600">Role</p>
                    <p class="text-sm font-medium">
                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">{{ $supplier->user->role->name }}</span>
                    </p>
                </div>
                <div class="bg-white p-3 rounded md:col-span-2">
                    <p class="text-xs text-gray-600">Account Created</p>
                    <p class="text-sm font-medium text-gray-900">{{ $supplier->user->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            <div class="mt-3 p-2 bg-yellow-50 rounded">
                <p class="text-xs text-yellow-800">
                    <i class="fas fa-lock mr-1"></i>
                    <strong>Note:</strong> To change the account password, please use the password reset feature in user management.
                </p>
            </div>
        </div>
        @else
        <div class="mt-6 p-4 bg-red-50 rounded border border-red-200">
            <p class="text-sm text-red-800">
                <i class="fas fa-exclamation-circle mr-1"></i>
                This supplier does not have a login account. Please contact the administrator.
            </p>
        </div>
        @endif

        <div class="mt-6">
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
            <textarea id="address" name="address" rows="4" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address', $supplier->address) }}</textarea>
            @error('address')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Supplier Statistics -->
        <div class="mt-6 p-4 bg-blue-50 rounded">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Supplier Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-3 rounded">
                    <p class="text-sm text-gray-600">Products Count</p>
                    <p class="text-lg font-semibold">{{ $supplier->products_count ?? 0 }}</p>
                </div>
                <div class="bg-white p-3 rounded">
                    <p class="text-sm text-gray-600">Created At</p>
                    <p class="text-lg font-semibold">{{ $supplier->created_at->format('Y-m-d') }}</p>
                </div>
                <div class="bg-white p-3 rounded">
                    <p class="text-sm text-gray-600">Last Updated</p>
                    <p class="text-lg font-semibold">{{ $supplier->updated_at->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        @if($supplier->products_count > 0)
            <div class="mt-6 p-4 bg-green-50 rounded">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Recent Products</h3>
                <div class="space-y-2">
                    @foreach($supplier->products->take(3) as $product)
                        <div class="flex justify-between items-center bg-white p-2 rounded">
                            <span class="text-sm">{{ $product->name }}</span>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Stock: {{ $product->stock_quantity }}</span>
                        </div>
                    @endforeach
                    @if($supplier->products_count > 3)
                        <p class="text-xs text-gray-600 text-center">... and {{ $supplier->products_count - 3 }} more products</p>
                    @endif
                </div>
            </div>
        @endif

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.suppliers.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Update Supplier
            </button>
        </div>
    </form>
</div>
@endsection