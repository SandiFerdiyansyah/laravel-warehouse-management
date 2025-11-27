# Quick Start - 3-Tier Product Request Workflow

## Status: ✅ FULLY IMPLEMENTED & SEEDED

## 🚀 Start Server
```bash
php artisan serve
```
Visit: http://127.0.0.1:8000

## 👥 Test Accounts (ONLY 4 ROLES)
```
Admin:     admin@example.com / password123
Operator:  operator@example.com / password123
Store:     store@example.com / password123
Supplier:  supplier@example.com / password123
```

## 📋 Workflow Steps

### Step 1: Store Creates Request
- URL: `/store/product-requests/create`
- Select: Product → Warehouse (shows only warehouses with stock) → Quantity
- Submit: Creates request with `warehouse_id` filled, `storage_location_id` = NULL

### Step 2: Admin Selects Storage Location
- URL: `/admin/warehouse/product-requests` (ADMIN ONLY)
- View: "📋 Menunggu Pemilihan Lokasi" tab
- Select: Click "Pilih Lokasi Storage" → Choose location → Submit
- Result: Fills `storage_location_id`, moves to next tab

### Step 3: Operator Verifies
- URL: `/operator/product-requests`
- View: Requests with warehouse & location already selected
- Action: Verify/approve request

## 🎯 Key Features

✅ **Store Cannot See Storage Locations** - Only sees warehouses with product
✅ **Admin Manages Warehouse** - Admin (not separate warehouse user) selects location
✅ **Operator Gets Pre-Filled Location** - No location selection needed
✅ **AJAX Warehouse Filtering** - Dynamic stock loading in real-time
✅ **Test Data Ready** - 3 warehouses, 10 products, 1000+ locations seeded
✅ **Simple Roles** - Only 4 roles: admin, operator, store, supplier

## 📊 Database

- **product_requests**: warehouse_id + storage_location_id (nullable)
- **warehouses**: New table with 3 test warehouses
- **storage_locations**: Updated with warehouse_id + product_id + quantity

## 🔍 Verify Setup

```bash
php artisan tinker
include('test_workflow_diagnostic.php');
```

## 📚 Documentation

- `WORKFLOW_TESTING_GUIDE.md` - Complete testing instructions (UPDATE NEEDED for admin-only warehouse)
- `IMPLEMENTATION_COMPLETE_3TIER_WORKFLOW.md` - Detailed implementation notes

## ⚡ Quick DB Check

```sql
-- Check roles (should be 4 only)
SELECT name FROM roles;

-- Total requests
SELECT COUNT(*) FROM product_requests;

-- Requests by stage
SELECT 
  SUM(CASE WHEN warehouse_id IS NULL THEN 1 ELSE 0 END) as waiting_warehouse,
  SUM(CASE WHEN warehouse_id IS NOT NULL AND storage_location_id IS NULL THEN 1 ELSE 0 END) as needs_location,
  SUM(CASE WHEN storage_location_id IS NOT NULL THEN 1 ELSE 0 END) as needs_verification
FROM product_requests;

-- Warehouse stock
SELECT w.name, COUNT(sl.id) as locations, SUM(sl.quantity) as total_stock
FROM warehouses w
LEFT JOIN storage_locations sl ON w.id = sl.warehouse_id
GROUP BY w.id;
```

## ✅ Everything Ready!

All components implemented:
- 3 Migrations ✓
- 3 Controllers ✓
- 3 Models ✓
- 4 Views ✓
- All Routes ✓
- Test Data Seeded ✓
- AJAX API ✓
- **4 Roles Only** ✓ (admin, operator, store, supplier)

Ready for testing and production!
