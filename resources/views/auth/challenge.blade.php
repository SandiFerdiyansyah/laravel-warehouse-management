@extends('layouts.auth')

@section('title', 'Verifikasi Tambahan - Login')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md p-5 mx-auto">
        <div class="bg-white shadow-md rounded-lg p-8">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Verifikasi Keamanan Tambahan</h2>
                <p class="text-gray-600 text-sm">Kami mendeteksi aktivitas tidak biasa. Silakan verifikasi untuk melanjutkan.</p>
            </div>

            <!-- Status Alert -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <p class="text-sm text-blue-700">
                    <span class="font-semibold">Catatan:</span> Sesi Anda akan kadaluarsa dalam 
                    <span id="countdown" class="font-bold">10:00</span> menit.
                </p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Challenge Form -->
            <form method="POST" action="{{ route('login.verify-challenge') }}" class="space-y-6">
                @csrf

                <!-- Challenge Code Input -->
                <div>
                    <label for="challenge_code" class="block text-gray-700 font-medium mb-2">
                        Kode Verifikasi
                    </label>
                    <input 
                        type="text" 
                        id="challenge_code" 
                        name="challenge_code"
                        placeholder="Masukkan kode yang dikirim"
                        maxlength="10"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                        autofocus
                    >
                    <p class="text-xs text-gray-500 mt-1">
                        Kode verifikasi telah dikirim ke email terdaftar Anda.
                    </p>
                </div>

                <!-- IP Validation Notice -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3">
                    <p class="text-xs text-yellow-700">
                        <span class="font-semibold">Keamanan:</span> 
                        Verifikasi harus dilakukan dari perangkat dan lokasi yang sama.
                    </p>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full bg-blue-600 text-white font-medium py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Verifikasi
                </button>

                <!-- Alternative Actions -->
                <div class="flex gap-3 text-sm">
                    <a 
                        href="{{ route('login') }}"
                        class="flex-1 text-center text-blue-600 hover:text-blue-800 font-medium py-2 px-4 rounded-lg border border-blue-300 transition duration-200"
                    >
                        Kembali ke Login
                    </a>
                    <button 
                        type="button"
                        onclick="resendCode()"
                        class="flex-1 text-center text-green-600 hover:text-green-800 font-medium py-2 px-4 rounded-lg border border-green-300 transition duration-200"
                    >
                        Kirim Ulang Kode
                    </button>
                </div>
            </form>

            <!-- Security Information -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    🔒 <span class="font-semibold">Informasi Keamanan:</span>
                    <br>
                    Halaman ini terenkripsi dan aman. Jangan pernah bagikan kode verifikasi Anda.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-600 text-xs mt-6">
            Butuh bantuan? <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">Hubungi Support</a>
        </p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Countdown Timer
    function startCountdown() {
        let timeLeft = 10 * 60; // 10 minutes in seconds
        const countdownEl = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                // Session expired
                alert('Sesi verifikasi Anda telah kadaluarsa. Silakan login kembali.');
                window.location.href = '{{ route("login") }}';
            }
            
            // Change color to red when less than 2 minutes
            if (timeLeft < 120) {
                countdownEl.parentElement.classList.add('text-red-600');
                countdownEl.parentElement.classList.remove('text-gray-700');
            }
        }, 1000);
    }

    // Resend Code Function
    function resendCode() {
        const button = event.target;
        button.disabled = true;
        button.textContent = 'Mengirim...';
        
        // Simulate API call
        setTimeout(() => {
            alert('Kode verifikasi telah dikirim kembali ke email Anda.');
            button.disabled = false;
            button.textContent = 'Kirim Ulang Kode';
        }, 1000);
    }

    // Start countdown on page load
    document.addEventListener('DOMContentLoaded', startCountdown);

    // Form submission logging
    document.querySelector('form').addEventListener('submit', function(e) {
        console.log('[Challenge Form] Submitting challenge code verification');
    });

    // IP Address Display (for demonstration)
    console.log('[Challenge Page] Loaded. Expecting verification from same IP/location.');
</script>
@endsection
