# Google reCAPTCHA Setup Guide

## ✅ Perbaikan yang Dilakukan

Struktur reCAPTCHA telah diperbaiki untuk mengikuti best practices keamanan Laravel:

### 1. **Environment Variables (`.env`)**
```
RECAPTCHA_SITE_KEY=6LdYkw8sAAAAANF1gsfKpVQWtob-kdGs6lISWfjQ
RECAPTCHA_SECRET=6LdYkw8sAAAAAOUGGwndN72Sr9QJzPc2GHQwq6ai
```

**Tujuan**: 
- Merah dalam `.gitignore` (tidak di-commit ke repository)
- Mudah diubah per environment (development/production)
- Aman dari exposure di source code

### 2. **Config File (`config/services.php`)**
```php
'recaptcha' => [
    'site_key' => env('RECAPTCHA_SITE_KEY', ''),
    'secret' => env('RECAPTCHA_SECRET', ''),
],
```

**Tujuan**:
- Centralized configuration management
- Single source of truth untuk aplikasi
- Fallback ke empty string jika env tidak terdefinisi

### 3. **Blade Template (`resources/views/auth/login.blade.php`)**
```blade
<div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
```

**Tujuan**:
- Mengambil nilai dari config (bukan env/hardcoded)
- Scalable dan maintainable
- Best practice Laravel

### 4. **Controller (`app/Http/Controllers/Auth/LoginController.php`)**
```php
$secret = config('services.recaptcha.secret');
```

**Tujuan**:
- Konsisten menggunakan config helper
- Tidak ada hardcoded secrets di code
- Mudah untuk di-override di testing

## 🔄 Best Practices yang Diterapkan

| Aspek | Sebelumnya | Sekarang | Manfaat |
|-------|-----------|---------|---------|
| **Secret Storage** | Hardcoded di config/services.php | Di `.env` | Aman, tidak terekspos |
| **Frontend** | Undefined variable `$siteKey` | `config()` helper | Konsisten & reliable |
| **Backend** | `env()` dengan hardcoded fallback | `config()` helper | Maintainable & clean |
| **Configuration** | Scattered di berbagai file | Centralized di `config/services.php` | Single source of truth |

## 🔐 Security Checklist

- [x] Secrets di `.env` (tidak di source code)
- [x] `.env` di `.gitignore`
- [x] Centralized config management
- [x] No hardcoded API keys di PHP files
- [x] Consistent config access via `config()` helper
- [x] Proper fallback values (empty string, not null)

## 📋 Setup untuk Environment Baru

### Development
1. Copy `.env.example` ke `.env`
2. Tambahkan test keys:
   ```
   RECAPTCHA_SITE_KEY=6LdYkw8sAAAAANF1gsfKpVQWtob-kdGs6lISWfjQ
   RECAPTCHA_SECRET=6LdYkw8sAAAAAOUGGwndN72Sr9QJzPc2GHQwq6ai
   ```
3. Jalankan `php artisan config:cache`

### Production
1. Update `.env` dengan production reCAPTCHA keys
2. Jalankan `php artisan config:cache`
3. Pastikan `.env` TIDAK di version control

## 🧪 Testing

Untuk testing, gunakan reCAPTCHA test keys:
```
RECAPTCHA_SITE_KEY=6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI
RECAPTCHA_SECRET=6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe
```

## 📚 Referensi

- [Google reCAPTCHA Documentation](https://developers.google.com/recaptcha)
- [Laravel Configuration](https://laravel.com/docs/configuration)
- [Environment Configuration](https://laravel.com/docs/env)

## ❌ Error yang Sebelumnya Terjadi

1. **"Undefined variable $siteKey"**
   - Cause: Blade mencari variable yang tidak di-pass dari controller
   - Fix: Menggunakan `config()` helper langsung di blade

2. **"ERROR for site owner: Invalid sitekey"**
   - Cause: Site key tidak terdefinisi (empty string)
   - Fix: Memastikan `.env` memiliki keys yang valid

3. **Hardcoded secrets di source code**
   - Cause: Keys di `config/services.php` dengan fallback values
   - Fix: Pindahkan ke `.env`, gunakan fallback empty string

---

**Status**: ✅ Selesai dan siap digunakan
**Last Updated**: 2025-11-17
