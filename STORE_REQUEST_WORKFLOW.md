# Workflow Permintaan Barang Toko (Store Product Request) - Updated

## Overview
Workflow baru untuk permintaan barang toko dipermudah:
- **Toko** membuat permintaan → langsung ke gudang utama admin
- **Admin** melihat permintaan pending → approve/reject
- **Operator** mengambil permintaan approved → fulfillment (packing & shipment)

## Status Enum Values
```
enum('pending','verified','approved','rejected','shipped','delivered')
```

## Workflow Step-by-Step

### 1. Store User - Buat Permintaan
**URL:** `GET/POST /store/product-requests/create`

**Flow:**
- Toko login
- Buka menu "Permintaan Barang" → "Buat Permintaan"
- **Pilih Produk** (required)
- **Input Jumlah** (required)
- **Gudang** otomatis diset ke "Gudang Pusat Jakarta" (WH-001 / admin's warehouse)
- Submit → Permintaan dibuat dengan status `pending`

**Controller Logic:**
- `ProductRequestController::store()` menerima: `product_id`, `quantity_requested`
- Gudang diambil otomatis via `resolveMainWarehouse()` (env-aware)
- Stock check: lakukan validasi terhadap storage_locations + fallback
- Jika valid → create ProductRequest dengan `status='pending'`
- Success message: "Permintaan barang berhasil dibuat. Admin akan memproses..."

**Database entry:**
```
INSERT INTO product_requests (
  store_id, 
  product_id, 
  warehouse_id,        # auto-set to admin warehouse
  quantity_requested, 
  status               # 'pending'
) VALUES (...)
```

### 2. Admin User - Review & Approve Permintaan
**URL:** `GET /admin/product-requests` (index view)
**Detail:** `GET /admin/product-requests/{id}`

**Flow:**
- Admin login
- Buka "Permintaan Barang" (dari Product Requests menu)
- Lihat daftar permintaan dengan status `pending`
- Klik permintaan untuk detail:
  - Store: Test Store
  - Produk: Laptop lenovo
  - Jumlah: 5 unit
  - Gudang: Gudang Pusat Jakarta
  - Status: pending
- Admin dapat:
  - **Approve** → update status ke `approved` (akan diteruskan ke operator)
  - **Reject** → update status ke `rejected` (notifikasi ke toko)

**Controller Logic (Admin):**
- `ProductRequestController::index()` query `status IN ('pending', 'approved', ...)`
- `ProductRequestController::processApproval()` → update status='approved'
- Bisa juga maintain `operator_id` jika admin assign ke operator spesifik

### 3. Operator - Fulfillment Permintaan
**URL:** `GET /operator/product-requests` (index view)
**Detail:** `GET /operator/product-requests/{id}/verify`

**Flow:**
- Operator login
- Buka "Permintaan Barang" di operator dashboard
- Lihat daftar permintaan dengan status `approved`
- Klik permintaan untuk detail & verification:
  - Verifikasi stok di storage locations
  - Buat shipment
  - Update status ke `verified` atau `shipped`
- Operator menggunakan page "Verify Permintaan" untuk:
  - Pilih storage location (lokasi penyimpanan spesifik)
  - Konfirmasi quantity
  - Generate picking list
  - Update status → `verified`/`shipped`

**Controller Logic (Operator):**
- `ProductRequestController::verify()` show form untuk storage location selection
- `ProductRequestController::storeVerification()` save selected location + update status
- Integrate dengan shipment creation

### 4. End State - Delivered to Store
- Operator membuat shipment untuk permintaan approved
- Shipment status: `shipped`
- Store receive confirmation
- Status → `delivered`

## Key Changes vs Previous Version

| Aspek | Sebelum | Sesudah |
|-------|---------|---------|
| **Pilih Gudang** | Toko pilih gudang | Auto ke gudang utama |
| **Form Field** | Harus ada warehouse_id input | Tidak ada (auto di backend) |
| **Workflow** | Warehouse select location → ... | Toko submit → Admin approve → Operator fulfill |
| **Status awal** | Pending | Pending |
| **Admin role** | Hanya view | Review & approve/reject |
| **Operator role** | Verify + select location | Fulfill approved requests |

## Configuration
```env
# Main warehouse selection (priority order)
MAIN_WAREHOUSE_ID=        # Option 1: by ID
MAIN_WAREHOUSE_CODE=WH-001 # Option 2: by code (recommended)
# Fallback: warehouse name = 'gudang utama kota serang'
# Last resort: first warehouse
```

## Stock Sync
- Semua produk dengan `stock_quantity > 0` otomatis disinkronisasi ke `storage_locations` di gudang utama
- Location code: `MAIN-{SKU_SUFFIX}` (contoh: `MAIN-GXYWHA`)
- Capacity: 2x stock_quantity
- Fallback: jika storage_locations kosong, gunakan `products.stock_quantity`

## Test & Verification
```bash
# Test request flow
php scripts/test_request_flow.php

# Verify stock sync
php scripts/verify_sync.php

# Debug product info
php scripts/debug_product_info.php PRD-SKU-HERE [warehouse_id]
```
