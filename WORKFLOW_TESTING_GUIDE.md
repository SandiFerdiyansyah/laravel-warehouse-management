# Product Request Workflow - Testing Guide

## Overview
The product request menu now implements a 3-tier workflow:
1. **Store** - Requests product from specific warehouse (sees only warehouses with stock)
2. **Warehouse** - Receives request and selects storage location
3. **Operator** - Verifies and executes the shipment

## Test Data Available

### Users (4 Roles Only)
- **Admin**: `admin@example.com` / `password123`
- **Operator**: `operator@example.com` / `password123`
- **Store User**: `store@example.com` / `password123`
- **Supplier**: `supplier@example.com` / `password123`

### Products
- 10 products seeded across 5 categories (Electronics, Furniture, Clothing, Food, Books)

### Warehouses
- **Warehouse 1** (WH001) - Jakarta
- **Warehouse 2** (WH002) - Surabaya
- **Warehouse 3** (WH003) - Bandung

### Storage Locations
- 1000+ storage locations distributed across warehouses
- Each location has random stock quantities for different products

---

## End-to-End Workflow Test

### Step 1: Store Creates Request

**URL**: `http://127.0.0.1:8000/store/product-requests/create`

**Action**:
1. Login as: `store@example.com`
2. Click "Buat Permintaan Barang"
3. Select Product: `Electronics - Laptop`
4. View available warehouses (should show only warehouses with this product in stock)
5. Select Warehouse: `Warehouse 1 - Jakarta` (shows available stock)
6. Enter Quantity: `5`
7. Click "Buat Permintaan"

**Expected Result**:
- Request created with:
  - `status` = pending
  - `warehouse_id` = filled
  - `storage_location_id` = NULL (not selected yet)
- Redirected to `/store/product-requests` with success message

**Database Check**:
```php
// In Tinker:
$req = App\Models\ProductRequest::latest()->first();
// Should show:
// - warehouse_id: 1
// - storage_location_id: null
// - quantity_requested: 5
// - status: 'pending'
```

---

### Step 2: Warehouse Selects Storage Location

**URL**: `http://127.0.0.1:8000/admin/warehouse/product-requests`

**Action**:
1. Login as: `admin@example.com`
2. View "📋 Menunggu Pemilihan Lokasi" tab
3. Should see the request created in Step 1
4. Click "Pilih Lokasi Storage →"
5. View available storage locations (only shows locations for this product in this warehouse)
6. Select a location with sufficient stock
7. Click "✓ Pilih Lokasi"

**Expected Result**:
- Storage location selected
- Redirected back with success: "Lokasi storage berhasil dipilih..."
- Request now appears in "⏳ Menunggu Verifikasi Operator" tab

**Database Check**:
```php
// In Tinker:
$req = App\Models\ProductRequest::latest()->first();
// Should show:
// - warehouse_id: 1
// - storage_location_id: [filled with ID]
// - quantity_requested: 5
// - status: 'pending'
```

---

### Step 3: Operator Verifies Request

**URL**: `http://127.0.0.1:8000/operator/product-requests`

**Action**:
1. Login as: `operator@example.com`
2. View the list of requests
3. Should see the request with storage location pre-filled
4. Click on request to verify
5. Review details (warehouse, storage location, quantity)
6. Click "Verifikasi" button

**Expected Result**:
- Request status updated to `verified`
- `quantity_verified` field populated
- Request moves to "Terverifikasi" section

**Database Check**:
```php
// In Tinker:
$req = App\Models\ProductRequest::latest()->first();
// Should show:
// - status: 'verified'
// - quantity_verified: 5
// - operator_id: [filled with operator user ID]
```

---

## Workflow State Validation

### Query to Check All Stages

```php
// Requests pending warehouse selection (warehouse_id NULL, storage_location_id NULL)
$stage1 = App\Models\ProductRequest::whereNull('warehouse_id')
    ->whereNull('storage_location_id')
    ->count();

// Requests awaiting warehouse location selection (warehouse_id filled, storage_location_id NULL)
$stage2 = App\Models\ProductRequest::whereNotNull('warehouse_id')
    ->whereNull('storage_location_id')
    ->count();

// Requests awaiting operator verification (warehouse_id filled, storage_location_id filled)
$stage3 = App\Models\ProductRequest::whereNotNull('warehouse_id')
    ->whereNotNull('storage_location_id')
    ->where('status', 'pending')
    ->count();

// Requests verified by operator
$stage4 = App\Models\ProductRequest::where('status', 'verified')->count();
```

---

## Key Features

### ✅ Store View (create.blade.php)
- Warehouse selection instead of storage location
- AJAX-based warehouse filtering (only shows warehouses with product stock)
- Dynamic quantity max based on warehouse stock
- Form submits: `product_id`, `warehouse_id`, `quantity_requested`

### ✅ Warehouse View (index.blade.php)
- Tab 1: Requests pending location selection
- Tab 2: Requests awaiting operator verification
- Tab 3: Completed/verified requests
- "Pilih Lokasi Storage" button links to `select-location.blade.php`

### ✅ Warehouse View (select-location.blade.php)
- Shows request summary
- Lists all storage locations with available stock for product
- Radio button selection with location details
- Shows total stock across all locations
- Form submits: `storage_location_id`

### ✅ Warehouse Controller
- `index()` - Uses scopes: `needsWarehouseSelection()`, `needsVerification()`, `awaitingApproval()`
- `selectLocation($id)` - Validates request is in correct state
- `storeLocation($request, $id)` - Validates storage location before saving

### ✅ Operator View (verify.blade.php)
- Shows requests with storage location pre-filled
- Displays warehouse and storage location info
- Operator only sees requests in `needsVerification()` state

---

## Troubleshooting

### Issue: No warehouses showing in store view
**Cause**: Product has no stock in any warehouse
**Fix**: Check `warehouse_stock` API endpoint returns stock > 0

### Issue: Storage location selection fails
**Cause**: Selected location doesn't belong to warehouse or product mismatch
**Fix**: Check validation in `storeLocation()` method

### Issue: Request not appearing in warehouse view
**Cause**: Request status or warehouse_id not set correctly
**Fix**: Check store controller `store()` method sets correct fields

### Query to verify data:
```php
$req = App\Models\ProductRequest::find(1);
$req->load('product', 'warehouse', 'storageLocation', 'store');
dd($req);
```

---

## API Endpoints

### GET `/store/product-requests/warehouse-stock`
**Purpose**: Get available stock for product in specific warehouse

**Parameters**:
- `product_id` (required)
- `warehouse_id` (required)

**Response**:
```json
{
  "stock": 100,
  "warehouse_id": 1,
  "product_id": 1
}
```

---

## Database Schema Reference

### product_requests table
```
- id
- product_id (FK)
- warehouse_id (FK) → NULL until warehouse selects
- storage_location_id (FK) → NULL until warehouse selects storage
- store_id (FK)
- operator_id (FK) → NULL until operator verifies
- admin_id (FK) → NULL until admin approves
- quantity_requested
- quantity_verified → NULL until operator verifies
- quantity_approved → NULL until admin approves
- status (pending, verified, approved, shipped, delivered, cancelled)
- created_at, updated_at
```

### Scopes in ProductRequest model
- `pending()` - Where status = 'pending'
- `needsWarehouseSelection()` - warehouse_id filled, storage_location_id NULL
- `needsVerification()` - storage_location_id filled, status pending
- `awaitingApproval()` - status = 'verified'

---

## Success Criteria

✅ Store cannot see storage location details (only warehouses with stock)
✅ Warehouse sees pending requests and selects storage location
✅ Operator sees requests with storage location pre-filled
✅ Request flow: Store → Warehouse → Operator
✅ Database properly tracks each stage with correct foreign keys
✅ AJAX warehouse filtering works dynamically
✅ All validations prevent invalid state transitions
