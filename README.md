# Warehouse Management System

Aplikasi web berbasis Laravel untuk memonitor dan mengelola penyimpanan gudang, inventaris barang, dan alur pemesanan (PO) antara admin gudang dan supplier.

## Fitur Utama

### 🔐 Multi-Role Authentication
- **Admin**: Akses penuh ke semua fitur
- **Operator**: Akses terbatas untuk operasional gudang
- **Supplier**: Akses vendor untuk mengelola purchase order

### 📊 Dashboard Admin
- Grafik barang masuk vs keluar
- Widget stok menipis
- Peta visual lokasi storage
- Aktivitas terkini

### 📦 Manajemen Inventaris
- CRUD Produk dengan QR Code otomatis
- Kategori produk
- Data supplier
- Lokasi penyimpanan

### 🛒 Purchase Order System
- Membuat PO untuk supplier
- Tracking status PO
- Konfirmasi penerimaan barang

### 📱 Mobile-Friendly Operator Interface
- Scan QR Code dengan kamera
- Filter lokasi storage (kosong/terisi)
- Input barang cepat

### 🏭 Supplier Portal
- View purchase orders
- Update status pengiriman
- Monitoring PO status

## Teknologi

- **Framework**: Laravel 12
- **Database**: MySQL/MariaDB
- **Authentication**: Laravel Breeze
- **QR Code**: Simple QrCode
- **Frontend**: Tailwind CSS
- **Icons**: Font Awesome

## Instalasi

### 1. Prerequisites
- PHP 8.2+
- Composer
- MySQL/MariaDB
- XAMPP (untuk development lokal)

### 2. Clone & Install
```bash
cd laravel-warehouse
composer install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=warehouse
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migration & Seeder
```bash
php artisan migrate
php artisan db:seed
```

### 6. Storage Link
```bash
php artisan storage:link
```

### 7. Start Development Server
```bash
php artisan serve
```

## Default Login

### Admin
- **Email**: admin@warehouse.com
- **Password**: password

### Operator
- **Email**: operator@warehouse.com
- **Password**: password

## Struktur Database

### Tabel Utama:
1. `roles` - Level akses pengguna
2. `users` - Data pengguna dengan role
3. `suppliers` - Data supplier/vendor
4. `categories` - Kategori produk
5. `storage_locations` - Lokasi rak/storage
6. `products` - Data barang dengan QR code
7. `product_movements` - Histori masuk/keluar barang
8. `purchase_orders` - Data PO
9. `purchase_order_items` - Detail item PO

## Struktur Folder

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   ├── Operator/       # Operator controllers
│   │   ├── Supplier/       # Supplier controllers
│   │   └── Auth/           # Authentication controllers
│   └── Middleware/
│       └── CheckRole.php   # Role-based middleware
├── Models/                 # Eloquent models
└── Providers/              # Service providers

database/
├── migrations/             # Database migrations
└── seeders/               # Database seeders

resources/views/
├── layouts/               # Template layouts
├── admin/                 # Admin views
├── operator/              # Operator views
├── supplier/              # Supplier views
└── auth/                  # Authentication views
```

## Routes

### Admin Routes (`/admin/*`)
- `/dashboard` - Dashboard utama
- `/products` - Manajemen produk
- `/categories` - Manajemen kategori
- `/suppliers` - Manajemen supplier
- `/storage` - Manajemen lokasi storage
- `/po` - Manajemen purchase order

### Operator Routes (`/operator/*`)
- `/dashboard` - Dashboard operator
- `/scan` - Scan QR Code

### Supplier Routes (`/supplier/*`)
- `/dashboard` - Dashboard supplier
- `/orders` - Manajemen purchase order

## Fitur QR Code

Setiap produk memiliki QR Code unik yang:
- Digenerate otomatis saat create produk
- Disimpan di `storage/app/public/qr-codes/`
- Digunakan untuk tracking dan input barang

## Security Features

- Role-based access control (RBAC)
- CSRF protection
- Input validation
- SQL injection prevention
- XSS protection

## Development Notes

- Menggunakan Laravel 12 dengan PHP 8.2+
- Responsive design dengan Tailwind CSS
- Mobile-friendly interface untuk operator
- Real-time status tracking
- Comprehensive audit trail

## Contributing

1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## License

MIT License