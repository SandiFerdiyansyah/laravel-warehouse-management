@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Warehouse Management</h1>
        <p class="text-gray-600 mt-2">Silakan login untuk melanjutkan</p>
    </div>

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Google reCAPTCHA v3 --}}
        <input type="hidden" id="recaptchaToken" name="g-recaptcha-response">
        @error('g-recaptcha-response')
            <p class="text-red-500 text-xs mt-1 mb-2">{{ $message }}</p>
        @enderror
        @error('recaptcha')
            <p class="text-red-500 text-xs mt-1 mb-2">{{ $message }}</p>
        @enderror

        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Login
        </button>
    </form>

@endsection

@section('scripts')
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    <script>
        const loginForm = document.getElementById('loginForm');
        const siteKey = '{{ config("services.recaptcha.site_key") }}';
        
        console.log('=== reCAPTCHA Initialization ===');
        console.log('Site Key:', siteKey);
        console.log('grecaptcha available?', typeof grecaptcha !== 'undefined');

        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('=== Login Submit Handler ===');
            
            // Check if grecaptcha is loaded
            if (typeof grecaptcha === 'undefined') {
                console.error('❌ grecaptcha object not found');
                alert('reCAPTCHA tidak ter-load. Silakan refresh halaman.');
                return;
            }
            console.log('✓ grecaptcha object found');

            // Use grecaptcha.ready to ensure it's loaded
            grecaptcha.ready(function() {
                console.log('✓ grecaptcha.ready() executed');
                console.log('Executing grecaptcha.execute() with action: login');
                
                grecaptcha.execute(siteKey, {action: 'login'})
                    .then(function(token) {
                        console.log('✓ Token received from Google API');
                        console.log('Token length:', token.length);
                        console.log('Token prefix:', token.substring(0, 30));
                        
                        // Validate token is not empty
                        if (!token || token.trim() === '') {
                            console.error('❌ Token is empty!');
                            alert('reCAPTCHA token tidak valid. Silakan refresh halaman.');
                            return;
                        }
                        
                        const tokenInput = document.getElementById('recaptchaToken');
                        tokenInput.value = token;
                        console.log('✓ Token set to hidden input, value length:', tokenInput.value.length);
                        console.log('✓ Submitting form...');
                        
                        // Submit form immediately
                        loginForm.submit();
                    })
                    .catch(function(error) {
                        console.error('❌ grecaptcha.execute() error:', error);
                        console.error('Error type:', typeof error);
                        console.error('Error message:', error.message);
                        console.error('Full error:', error);
                        alert('Terjadi kesalahan dengan reCAPTCHA: ' + error.message);
                    });
            });
        });
    </script>
@endsection