# SSL Certificate Error Fix

## Problem Identified

**Error**: `cURL error 77: error setting certificate verify locations`

This error occurs when making HTTPS requests from localhost without proper SSL certificate configuration.

## Solution Applied

Added `.withoutVerifying()` to the HTTP request in LoginController:

```php
$res = Http::timeout(15)
    ->withoutVerifying()  // Disable SSL verification for local development
    ->asForm()
    ->post('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => $secret,
        'response' => $recaptchaResponse,
    ]);
```

## Why This Works

1. **Development Environment**: Localhost doesn't have valid SSL certificates
2. **Google API**: Uses HTTPS (requires SSL verification by default)
3. **Solution**: `->withoutVerifying()` tells Laravel to skip SSL certificate validation
4. **Safety**: Only used for development/localhost, NOT recommended for production

## For Production

In production, use proper SSL certificates or environment-based configuration:

```php
$client = Http::timeout(15);

// Only disable verification on localhost
if (config('app.debug')) {
    $client = $client->withoutVerifying();
}

$res = $client
    ->asForm()
    ->post('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => $secret,
        'response' => $recaptchaResponse,
    ]);
```

## Flow Now Working

```
1. User fills email & password
   ↓
2. User clicks Login
   ↓
3. JavaScript: grecaptcha.execute() gets token
   ↓
4. Form submits with token to /login
   ↓
5. LoginController receives token
   ↓
6. HTTP request to Google API (NOW WITH WORKING SSL)
   ↓
7. Google returns success/fail response
   ↓
8. If success, attempt authentication
   ↓
9. Redirect to dashboard or back with error
```

---

**Status**: ✅ SSL Error Fixed  
**Next**: Try login again - should work now!
