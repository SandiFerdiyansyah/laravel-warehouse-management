@extends('layouts.admin')

@section('title', 'Create Supplier')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Create New Supplier</h1>
    <a href="{{ route('admin.suppliers.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Back to Suppliers
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.suppliers.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Company Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g., PT. Supplier Indonesia">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">Contact Person *</label>
                <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g., John Doe">
                @error('contact_person')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g., +62 21 1234 5678">
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g., info@supplier.com">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Account Creation Section -->
        <div class="mt-6 p-4 bg-yellow-50 rounded border border-yellow-200">
            <div class="flex items-center mb-4">
                <i class="fas fa-user-plus text-yellow-600 mr-2"></i>
                <h3 class="text-sm font-semibold text-gray-700">Create Login Account for Supplier</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="account_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Account Email (Username) *
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="account_email" name="account_email" value="{{ old('account_email') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., supplier@example.com"
                           @error('account_email') aria-invalid="true" @enderror>
                    <p class="text-xs text-gray-500 mt-1">This will be used as login email</p>
                    @error('account_email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Account Password *
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Min 8 characters"
                           @error('password') aria-invalid="true" @enderror>
                    <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password *
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Confirm password">
                    @error('password_confirmation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4 p-3 bg-blue-50 rounded">
                <p class="text-xs text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Info:</strong> A login account will be created automatically for this supplier. They can use the provided email and password to access their dashboard.
                </p>
            </div>
        </div>

        <div class="mt-6">
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
            <textarea id="address" name="address" rows="4" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Enter complete address including street, city, postal code...">{{ old('address') }}</textarea>
            @error('address')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Supplier Preview -->
        <div class="mt-6 p-4 bg-gray-50 rounded">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Preview</h3>
            <div class="bg-white p-4 rounded border">
                <div class="flex items-start space-x-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-building text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900" id="preview-name">{{ old('name') ?: 'Company Name' }}</div>
                        <div class="text-sm text-gray-600" id="preview-contact">{{ old('contact_person') ?: 'Contact Person' }}</div>
                        <div class="text-sm text-gray-600" id="preview-phone">{{ old('phone') ?: 'Phone Number' }}</div>
                        <div class="text-sm text-gray-500 mt-1" id="preview-address">{{ old('address') ?: 'Address will appear here...' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.suppliers.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Create Supplier
            </button>
        </div>
    </form>
</div>

<script>
// Live preview
document.getElementById('name').addEventListener('input', function(e) {
    document.getElementById('preview-name').textContent = e.target.value || 'Company Name';
});

document.getElementById('contact_person').addEventListener('input', function(e) {
    document.getElementById('preview-contact').textContent = e.target.value || 'Contact Person';
});

document.getElementById('phone').addEventListener('input', function(e) {
    document.getElementById('preview-phone').textContent = e.target.value || 'Phone Number';
});

document.getElementById('address').addEventListener('input', function(e) {
    document.getElementById('preview-address').textContent = e.target.value || 'Address will appear here...';
});
</script>
@endsection