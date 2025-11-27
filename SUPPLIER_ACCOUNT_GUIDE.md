# Panduan Pembuatan Akun Supplier

## Ringkasan Masalah dan Solusi

### Masalah Awal
Supplier tidak bisa login karena akun user supplier dibuat tetapi tidak memiliki supplier profile yang terkait.

### Solusi yang Diterapkan

#### 1. **Perbaikan Database Seeder**
File: `database/seeders/AdminUserSeeder.php`

Ketika membuat user dengan role supplier, sekarang juga membuat supplier profile otomatis:

```php
$supplierUser = User::firstOrCreate([...]);
Supplier::firstOrCreate([
    'user_id' => $supplierUser->id,
    [...profile data...]
]);
```

#### 2. **Form Add Supplier untuk Admin**
File: `resources/views/admin/suppliers/create.blade.php`

Admin sekarang bisa membuat supplier beserta login account-nya dalam satu form:

**Fields yang ditambahkan:**
- **Account Email (Username)** - Email yang digunakan untuk login
- **Account Password** - Password minimal 8 karakter
- **Confirm Password** - Konfirmasi password

**Validasi:**
- Email unik (tidak boleh sama dengan user lain)
- Password minimal 8 karakter
- Password confirmation harus sama

#### 3. **Controller Logic**
File: `app/Http/Controllers/Admin/SupplierController.php`

Method `store()` diupdate untuk:
- Membuat User account dengan role "supplier"
- Hash password secara aman
- Membuat Supplier profile yang terkait dengan User
- Error handling yang baik

#### 4. **Edit Supplier - Account Information**
File: `resources/views/admin/suppliers/edit.blade.php`

Saat edit supplier, admin bisa melihat:
- Email account yang digunakan untuk login
- Role dari user
- Tanggal akun dibuat
- Info bahwa password hanya bisa diubah melalui user management

---

## Cara Penggunaan

### Membuat Supplier Baru dengan Akun

1. **Akses Menu Admin → Suppliers**
2. **Klik "Create New Supplier"**
3. **Isi Form Supplier Data:**
   - Company Name
   - Contact Person
   - Phone Number
   - Address
   - (Optional) Email Supplier

4. **Isi Form Account Creation:**
   - Account Email: `supplier@company.com` (email untuk login)
   - Account Password: `minimum 8 karakter`
   - Confirm Password: `ulangi password`

5. **Klik "Create Supplier"**

### Login Supplier

Supplier bisa login menggunakan:
- **Email:** Email yang dimasukkan di "Account Email"
- **Password:** Password yang dimasukkan saat membuat supplier
- **URL:** `http://localhost:8000/login`

Setelah login, supplier akan diarahkan ke:
- **Dashboard:** `/supplier/dashboard`

---

## Default Supplier Account

Ada 1 default supplier account yang sudah dibuat saat migration:

**Email:** `supplier@warehouse.com`
**Password:** `password`

---

## Database Structure

### User Model
```php
// Relasi ke Role
$user->role() // belongsTo Role

// Relasi ke Supplier
$user->supplier() // hasOne Supplier
```

### Supplier Model
```php
// Relasi ke User
$supplier->user() // belongsTo User

// Relasi ke Product
$supplier->products() // hasMany Product
```

---

## Security Notes

✅ **Password Security:**
- Password di-hash menggunakan bcrypt
- Minimum 8 karakter
- Tidak pernah disimpan dalam plain text

✅ **Email Uniqueness:**
- Setiap email user harus unik
- Sistem mencegah duplikasi email

✅ **Role-Based Access:**
- Middleware `role:supplier` melindungi routes supplier
- Supplier hanya bisa akses supplier routes

---

## Troubleshooting

### Supplier tidak bisa login

**Kemungkinan Penyebab:**
1. Email atau password salah
2. Akun tidak memiliki supplier profile

**Solusi:**
- Verifikasi email dan password di form
- Pastikan supplier profile sudah dibuat (di tabel suppliers)
- Run migration dan seeder ulang jika perlu

### Error "Email already exists"

**Penyebab:** Email sudah digunakan oleh user lain

**Solusi:** Gunakan email yang berbeda saat membuat supplier baru

### Supplier redirect error

**Penyebab:** Supplier profile tidak ditemukan di DashboardController

**Solusi:** Pastikan user memiliki record di tabel suppliers

---

## Fitur Supplier Dashboard

Setelah login, supplier bisa:
1. **Melihat Purchase Orders** - List PO yang ditugaskan
2. **Approve Orders** - Approve PO yang masuk
3. **Ship Orders** - Update status pengiriman
4. **View Statistics** - Melihat statistik pesanan

---

## Modifikasi di File

### 1. `database/seeders/AdminUserSeeder.php`
- Tambah import `use App\Models\Supplier;`
- Update logic untuk membuat supplier profile

### 2. `resources/views/admin/suppliers/create.blade.php`
- Tambah section "Create Login Account for Supplier"
- Tambah fields: account_email, password, password_confirmation

### 3. `resources/views/admin/suppliers/edit.blade.php`
- Tambah section "Login Account Information"
- Tampilkan email account dan role

### 4. `app/Http/Controllers/Admin/SupplierController.php`
- Tambah imports untuk User, Role, Hash, Rule
- Update store() method untuk membuat user dan supplier
- Tambah validasi untuk account_email dan password

---

## Testing

Untuk test fitur baru:

```bash
# 1. Buka aplikasi
php artisan serve

# 2. Login sebagai admin
Email: admin@warehouse.com
Password: password

# 3. Buat supplier baru
Menu Admin → Suppliers → Create New Supplier
Isi semua field termasuk account email dan password

# 4. Login sebagai supplier baru
Logout dan login dengan email supplier yang baru dibuat

# 5. Verifikasi
Supplier bisa akses dashboard dan lihat orders
```

---

**Last Updated:** November 17, 2025
**Status:** ✅ Complete
