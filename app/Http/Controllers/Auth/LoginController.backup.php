<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate basic inputs
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Get reCAPTCHA response
        $recaptchaResponse = $request->input('g-recaptcha-response');
        $secret = config('services.recaptcha.secret');
        $siteKey = config('services.recaptcha.site_key');

        // Log request info
        \Log::info('Login attempt', [
            'email' => $request->email,
            'has_recaptcha_response' => !empty($recaptchaResponse),
            'recaptcha_response_length' => strlen($recaptchaResponse ?? ''),
        ]);

        // Verify reCAPTCHA if token is present and keys are configured
        if (!empty($recaptchaResponse) && !empty($secret) && !empty($siteKey)) {
            try {
                \Log::info('Verifying reCAPTCHA token with Google API');
                
                // Verify with Google reCAPTCHA API
                $res = Http::timeout(15)
                    ->withoutVerifying()  // Disable SSL verification for local development
                    ->asForm()
                    ->post('https://www.google.com/recaptcha/api/siteverify', [
                        'secret' => $secret,
                        'response' => $recaptchaResponse,
                    ]);

                $body = $res->json();
                
                \Log::info('Google reCAPTCHA Response', [
                    'success' => $body['success'] ?? false,
                    'score' => $body['score'] ?? null,
                    'action' => $body['action'] ?? null,
                    'error_codes' => $body['error-codes'] ?? null,
                ]);
                
                // Check if verification was successful
                if (!isset($body['success']) || $body['success'] !== true) {
                    \Log::warning('reCAPTCHA verification failed', [
                        'full_response' => $body,
                    ]);
                    return back()->withErrors(['recaptcha' => 'reCAPTCHA verification failed.'])->onlyInput('email');
                }
                
                // For reCAPTCHA v3, check score
                if (isset($body['score']) && $body['score'] < 0.5) {
                    \Log::warning('reCAPTCHA score too low', ['score' => $body['score']]);
                    return back()->withErrors(['recaptcha' => 'reCAPTCHA score rendah, mungkin bot.'])->onlyInput('email');
                }
                
                \Log::info('reCAPTCHA verification successful');
                
            } catch (\Exception $e) {
                \Log::error('reCAPTCHA verification error', [
                    'error_message' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return back()->withErrors(['recaptcha' => 'Terjadi kesalahan reCAPTCHA: ' . $e->getMessage()])->onlyInput('email');
            }
        } else {
            // Log warning if no token
            if (empty($recaptchaResponse)) {
                \Log::warning('No reCAPTCHA token received from client');
            }
        }

        // Attempt authentication
        $authData = $request->only(['email', 'password']);

        if (Auth::attempt($authData)) {
            $request->session()->regenerate();

            $user = Auth::user();
            \Log::info('User logged in', ['user_id' => $user->id, 'email' => $user->email]);

            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->isOperator()) {
                return redirect()->intended(route('operator.dashboard'));
            } elseif ($user->isSupplier()) {
                return redirect()->intended(route('supplier.dashboard'));
            } elseif ($user->isStore()) {
                return redirect()->intended(route('store.dashboard'));
            }

            return redirect()->intended('/');
        }

        \Log::warning('Failed login attempt', ['email' => $request->email]);
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}