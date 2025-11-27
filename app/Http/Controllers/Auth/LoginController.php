<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    // reCAPTCHA v3 Score Thresholds
    const RECAPTCHA_SCORE_BLOCK = 0.3;      // Score < 0.3: Block and log as suspicious
    const RECAPTCHA_SCORE_CHALLENGE = 0.5;  // Score < 0.5: Require additional challenge
    const RECAPTCHA_SCORE_ALLOW = 0.5;      // Score >= 0.5: Allow login attempt

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // OWASP A07:2021 - Identification and Authentication Failures
        // OWASP A04:2021 - Insecure Input Validation
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255',
        ]);

        // OWASP A07:2021 - Rate Limiting (Brute Force Protection)
        $throttleKey = 'login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5, 15)) {
            \Log::warning('Rate limit exceeded - Brute force attempt', [
                'ip' => $request->ip(),
                'email' => $request->email,
            ]);
            return back()->withErrors([
                'email' => 'Terlalu banyak percobaan login. Silakan coba dalam 15 menit.',
            ]);
        }

        // Get reCAPTCHA response
        $recaptchaResponse = $request->input('g-recaptcha-response');
        $secret = config('services.recaptcha.secret');
        $siteKey = config('services.recaptcha.site_key');
        $clientIp = $request->ip();

        // OWASP A09:2021 - Logging and Monitoring Failures
        \Log::info('Login attempt initiated', [
            'email' => $validated['email'],
            'ip_address' => $clientIp,
            'user_agent' => substr($request->userAgent(), 0, 255),
            'has_recaptcha_response' => !empty($recaptchaResponse),
            'recaptcha_response_length' => strlen($recaptchaResponse ?? ''),
        ]);

        // Initialize reCAPTCHA score
        $recaptchaScore = null;
        $recaptchaAction = null;
        $challengeTs = null;
        $recaptchaSkipped = false;

        // Verify reCAPTCHA if token is present and keys are configured
        if (!empty($recaptchaResponse) && !empty($secret) && !empty($siteKey)) {
            try {
                // Verify with Google reCAPTCHA API
                $res = Http::timeout(15)
                    ->withoutVerifying()
                    ->asForm()
                    ->post('https://www.google.com/recaptcha/api/siteverify', [
                        'secret' => $secret,
                        'response' => $recaptchaResponse,
                    ]);

                $body = $res->json();
                $recaptchaScore = floatval($body['score'] ?? 0);
                $recaptchaAction = $body['action'] ?? null;
                $challengeTs = $body['challenge_ts'] ?? null;

                \Log::info('reCAPTCHA verification response', [
                    'success' => $body['success'] ?? false,
                    'score' => $recaptchaScore,
                    'action' => $recaptchaAction,
                    'email' => $validated['email'],
                    'http_status' => $res->status(),
                ]);

                // OWASP A05:2021 - Broken Access Control
                // Validate API response
                if (!isset($body['success']) || $body['success'] !== true) {
                    \Log::warning('reCAPTCHA verification failed', [
                        'email' => $validated['email'],
                        'ip' => $clientIp,
                        'error_codes' => $body['error-codes'] ?? [],
                        'success_value' => $body['success'] ?? null,
                    ]);
                    RateLimiter::hit($throttleKey);
                    return back()->withErrors([
                        'recaptcha' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.',
                    ])->onlyInput('email');
                }

                // ========================================
                // RECAPTCHA V3 SCORE LOGIC
                // ========================================

                // SCORE < 0.3: BLOCK TOTAL (Critical - Likely Bot)
                if ($recaptchaScore < self::RECAPTCHA_SCORE_BLOCK) {
                    \Log::alert('SECURITY: Blocked login - reCAPTCHA score critical', [
                        'email' => $validated['email'],
                        'score' => $recaptchaScore,
                        'ip' => $clientIp,
                        'user_agent' => $request->userAgent(),
                        'reason' => 'Suspected bot or automated attack',
                    ]);
                    
                    // OWASP A01:2021 - Broken Access Control
                    // Don't reveal exact reason (information disclosure protection)
                    return back()->withErrors([
                        'recaptcha' => 'Akses ditolak karena aktivitas mencurigakan.',
                    ])->onlyInput('email');
                }

                // SCORE < 0.5: REQUIRE ADDITIONAL CHALLENGE (Suspicious Behavior)
                if ($recaptchaScore < self::RECAPTCHA_SCORE_CHALLENGE) {
                    \Log::warning('Medium risk detected - Additional challenge required', [
                        'email' => $validated['email'],
                        'score' => $recaptchaScore,
                        'ip' => $clientIp,
                    ]);
                    
                    // OWASP A06:2021 - Cryptographic Failures
                    // Store credentials encrypted for challenge verification
                    session([
                        'challenge_required' => true,
                        'challenge_email' => encrypt($validated['email']),
                        'challenge_password' => encrypt($validated['password']),
                        'challenge_recaptcha_score' => $recaptchaScore,
                        'challenge_initiated_at' => now(),
                        'challenge_ip' => $clientIp,
                    ]);
                    
                    return redirect()->route('login.challenge')
                        ->with('challenge_message', 'Verifikasi tambahan diperlukan untuk keamanan akun Anda.');
                }

                // SCORE >= 0.5: PROCEED WITH LOGIN (Low Risk)
                \Log::info('Low risk detected - Proceeding with authentication', [
                    'email' => $validated['email'],
                    'score' => $recaptchaScore,
                    'ip' => $clientIp,
                ]);

            } catch (\Exception $e) {
                \Log::error('reCAPTCHA verification exception', [
                    'email' => $validated['email'],
                    'ip' => $clientIp,
                    'error_message' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'error_trace' => substr($e->getTraceAsString(), 0, 500),
                ]);
                RateLimiter::hit($throttleKey);
                return back()->withErrors([
                    'recaptcha' => 'Terjadi kesalahan verifikasi. Silakan refresh halaman.',
                ])->onlyInput('email');
            }
        } else {
            // No reCAPTCHA response - either token not sent or keys missing
            \Log::warning('No valid reCAPTCHA data', [
                'email' => $validated['email'],
                'ip' => $clientIp,
                'has_response' => !empty($recaptchaResponse),
                'has_secret' => !empty($secret),
                'has_site_key' => !empty($siteKey),
            ]);
            
            RateLimiter::hit($throttleKey);
            return back()->withErrors([
                'recaptcha' => 'reCAPTCHA token tidak ter-generate. Silakan refresh halaman dan coba lagi.',
            ])->onlyInput('email');
        }

        // OWASP A07:2021 - Attempt Authentication
        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']], false)) {
            // OWASP A04:2021 - Session Fixation Protection
            $request->session()->regenerate();
            
            RateLimiter::clear($throttleKey);

            $user = Auth::user();
            \Log::info('Successful authentication', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $clientIp,
                'recaptcha_score' => $recaptchaScore,
            ]);

            // OWASP A01:2021 - Broken Access Control
            // Check user permissions and redirect appropriately
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif (method_exists($user, 'isOperator') && $user->isOperator()) {
                return redirect()->intended(route('operator.dashboard'));
            } elseif (method_exists($user, 'isSupplier') && $user->isSupplier()) {
                return redirect()->intended(route('supplier.dashboard'));
            } elseif (method_exists($user, 'isStore') && $user->isStore()) {
                return redirect()->intended(route('store.dashboard'));
            }

            return redirect()->intended('/');
        }

        // OWASP A01:2021 - Information Disclosure Prevention
        // Use generic error message (don't reveal if email exists or password wrong)
        RateLimiter::hit($throttleKey);
        \Log::warning('Failed authentication attempt', [
            'email' => $validated['email'],
            'ip' => $clientIp,
            'recaptcha_score' => $recaptchaScore,
        ]);

        return back()->withErrors([
            'email' => 'Email atau password tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Additional Challenge Page
     * Shown when reCAPTCHA score is 0.3-0.5 (Medium Risk)
     */
    public function showChallenge()
    {
        // Verify challenge session exists and not expired
        if (!session('challenge_required')) {
            return redirect()->route('login');
        }

        $challengeInitiatedAt = session('challenge_initiated_at');
        if (now()->diffInMinutes($challengeInitiatedAt) > 10) {
            session()->forget(['challenge_required', 'challenge_email', 'challenge_password', 'challenge_recaptcha_score', 'challenge_initiated_at', 'challenge_ip']);
            \Log::warning('Challenge session expired', ['ip' => request()->ip()]);
            return redirect()->route('login')->withErrors(['challenge' => 'Sesi verifikasi telah expired.']);
        }

        return view('auth.challenge');
    }

    /**
     * Verify Additional Challenge
     * 
     * For scores 0.3-0.5, user must complete additional verification
     * This could be: OTP, Security Question, or Email Verification
     */
    public function verifyChallenge(Request $request)
    {
        // OWASP A07:2021 - Verify Challenge Session Exists
        if (!session('challenge_required')) {
            \Log::warning('Challenge verification without valid session', ['ip' => $request->ip()]);
            return redirect()->route('login');
        }

        // Validate challenge input
        $validated = $request->validate([
            'challenge_code' => 'required|string|max:255',
        ]);

        \Log::info('Challenge verification attempt', [
            'ip' => $request->ip(),
            'session_ip' => session('challenge_ip'),
        ]);

        // OWASP A07:2021 - IP Validation
        // Ensure request comes from same IP as login attempt
        if ($request->ip() !== session('challenge_ip')) {
            \Log::alert('Challenge verification from different IP', [
                'original_ip' => session('challenge_ip'),
                'attempt_ip' => $request->ip(),
            ]);
            session()->forget(['challenge_required', 'challenge_email', 'challenge_password', 'challenge_recaptcha_score', 'challenge_initiated_at', 'challenge_ip']);
            return back()->withErrors(['challenge' => 'Verifikasi dari IP yang berbeda ditolak.']);
        }

        // TODO: Implement your custom challenge logic
        // Examples:
        // 1. OTP verification (send to email/SMS)
        // 2. Security question
        // 3. Email verification link
        // For now, we accept any code as POC

        // Decrypt credentials
        try {
            $email = decrypt(session('challenge_email'));
            $password = decrypt(session('challenge_password'));
        } catch (\Exception $e) {
            \Log::error('Challenge credential decryption failed', ['error' => $e->getMessage()]);
            session()->forget(['challenge_required', 'challenge_email', 'challenge_password', 'challenge_recaptcha_score', 'challenge_initiated_at', 'challenge_ip']);
            return back()->withErrors(['challenge' => 'Kesalahan verifikasi. Silakan login kembali.']);
        }

        // Attempt authentication after challenge
        if (Auth::attempt(['email' => $email, 'password' => $password], false)) {
            $request->session()->regenerate();
            session()->forget(['challenge_required', 'challenge_email', 'challenge_password', 'challenge_recaptcha_score', 'challenge_initiated_at', 'challenge_ip']);

            \Log::info('Successful login after challenge verification', [
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);

            return redirect()->intended('/');
        }

        session()->forget(['challenge_required', 'challenge_email', 'challenge_password', 'challenge_recaptcha_score', 'challenge_initiated_at', 'challenge_ip']);
        \Log::warning('Challenge verification followed by failed authentication', ['ip' => $request->ip()]);
        return back()->withErrors(['challenge' => 'Verifikasi challenge gagal. Silakan login kembali.']);
    }

    public function logout(Request $request)
    {
        \Log::info('User logout', [
            'user_id' => Auth::id() ?? 'Guest',
            'ip' => $request->ip(),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah logout.');
    }

    /**
     * Direct logout (GET request) - Alternative logout method
     * Used when CSRF token expires
     */
    public function directLogout(Request $request)
    {
        \Log::info('Direct logout (GET method)', [
            'user_id' => Auth::id() ?? 'Guest',
            'ip' => $request->ip(),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah logout.');
    }
}
