# reCAPTCHA v3 Debugging Guide

## Perubahan Terbaru

### Perbaikan yang Dilakukan:

1. **Login Blade Template** - `resources/views/auth/login.blade.php`
   - ✅ Tambah console.log untuk debugging
   - ✅ Cek apakah grecaptcha object sudah loaded
   - ✅ Gunakan `grecaptcha.ready()` wrapper
   - ✅ Better error handling dengan .catch()

2. **LoginController** - `app/Http/Controllers/Auth/LoginController.php`
   - ✅ Hapus required validation pada g-recaptcha-response
   - ✅ Tambah check apakah token diterima dari client
   - ✅ Add detailed logging untuk debugging
   - ✅ Better error messages

3. **Layout** - `resources/views/layouts/auth.blade.php`
   - ✅ Sudah support @yield('scripts')

---

## 🔍 How to Debug

### Step 1: Open Browser Console (F12)

Ketika login page di-load, Anda akan melihat:
```
reCAPTCHA Site Key: 6Lf_mg8sAAAAAHThSkpyV42j-UeBmSL1fvbGhb_0
```

### Step 2: Submit Login Form

Lihat di console apakah ada messages:
```
reCAPTCHA token received: eyJhbGciOiJSUzI1NiIs...
```

Jika tidak ada, berarti grecaptcha.execute() gagal.

### Step 3: Check Network Tab

1. Buka DevTools → Network tab
2. Filter: `siteverify`
3. Cari POST request ke `google.com/recaptcha/api/siteverify`
4. Lihat response body untuk status

### Step 4: Check Logs

Server logs ada di: `storage/logs/laravel.log`

Cari entries dengan "reCAPTCHA" untuk melihat apa error di server.

---

## ⚠️ Common Issues & Solutions

### Issue 1: "reCAPTCHA tidak ter-load"
**Cause**: grecaptcha object tidak ter-load  
**Solution**:
- Refresh halaman
- Check browser console untuk network errors
- Pastikan internet connection stabil
- Cek apakah Google CDN accessible

### Issue 2: Token tidak ter-generate
**Cause**: grecaptcha.execute() error  
**Solution**:
- Cek console untuk error message
- Verify Site Key di .env sudah benar
- Pastikan @section('scripts') ter-load

### Issue 3: "reCAPTCHA verification error"
**Cause**: Token tidak ter-kirim atau Secret Key salah  
**Solution**:
- Check Network tab untuk POST ke siteverify
- Verify Secret Key di .env sudah benar
- Check server logs di storage/logs/laravel.log

### Issue 4: "reCAPTCHA score rendah"
**Cause**: Behavior score < 0.5  
**Solution**:
- Score 0 = likely bot, 1 = likely human
- Cek response di Network tab untuk score value
- Bisa lower threshold di LoginController (line 55)

---

## 🧪 Quick Test

### Test 1: Check Configuration
```bash
# Di terminal, run:
php artisan tinker

# Masukkan:
config('services.recaptcha.site_key')
config('services.recaptcha.secret')

# Pastikan keduanya return value, bukan NULL/empty
```

### Test 2: Manual API Call
```bash
# Ganti TOKEN dengan token dari console, ganti SECRET dengan secret key

curl -X POST https://www.google.com/recaptcha/api/siteverify \
  -d secret=YOUR_SECRET_KEY \
  -d response=TOKEN_FROM_CONSOLE
```

### Test 3: Check Logs
```bash
# Lihat log real-time:
tail -f storage/logs/laravel.log | grep reCAPTCHA
```

---

## 📋 Checklist Debugging

- [ ] reCAPTCHA Site Key di .env sudah benar
- [ ] reCAPTCHA Secret Key di .env sudah benar
- [ ] Config cache ter-update: `php artisan config:cache`
- [ ] Layout auth.blade.php punya `@yield('scripts')`
- [ ] Login blade punya `@section('scripts')`
- [ ] Browser console tidak ada error
- [ ] Network tab menunjukkan request ke siteverify
- [ ] Response dari siteverify punya "success": true
- [ ] Server logs tidak punya error

---

## 📊 Expected Flow

```
1. User input email & password
   ↓
2. User click "Login" button
   ↓
3. JavaScript intercept form submit
   ↓
4. Check if grecaptcha loaded
   ↓
5. grecaptcha.execute() get token
   ↓
6. Set token ke hidden input
   ↓
7. Submit form ke /login
   ↓
8. LoginController verify token
   ↓
9. Http POST ke Google API
   ↓
10. Check response success=true
   ↓
11. Check score >= 0.5
   ↓
12. Auth::attempt() check credentials
   ↓
13. Redirect ke dashboard atau back with error
```

---

## 🔐 Important Notes

- Site Key bisa public (sudah di HTML)
- Secret Key JANGAN pernah expose (hanya server-side)
- reCAPTCHA v3 selalu return token, tidak ada visible checkbox
- Score interpretation:
  - 0.9+ = very likely legitimate
  - 0.5 = neutral
  - 0.1 = very likely bot

---

**Last Updated**: 2025-11-17  
**For Issues**: Check browser console first, then server logs
