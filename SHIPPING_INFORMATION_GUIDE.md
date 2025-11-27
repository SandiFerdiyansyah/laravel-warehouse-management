# Fitur Shipping Information - Panduan Lengkap

## 📋 Ringkasan Fitur

Supplier kini dapat mengelola informasi pengiriman secara lengkap ketika memproses Purchase Order yang telah di-approve. Sistem akan **otomatis generate tracking number**, **hitung estimasi pengiriman berdasarkan jenis kurir**, dan memungkinkan supplier untuk **edit/update/delete** shipping information.

---

## ✨ Fitur Utama

### 1. **Auto-Generate Tracking Number**
- Format: `TRK[YYYYMMDDHHmmss][6-CHAR-RANDOM]`
- Contoh: `TRK202511171430255A7B8C`
- Otomatis dihasilkan saat modal dibuka
- Ditampilkan read-only untuk referensi

### 2. **Courier Type Selection**
Supplier dapat memilih 2 opsi kurir:

| Kurir | Estimasi | Icon | Deskripsi |
|-------|----------|------|-----------|
| **Truck** | 5 Hari | 🚚 | Pengiriman standar via truk |
| **Express** | 2 Hari | ⚡ | Pengiriman cepat via express |

### 3. **Auto-Calculate Estimated Delivery**
- Sistem otomatis menghitung tanggal estimasi pengiriman
- Berdasarkan jumlah hari dari jenis kurir yang dipilih
- Contoh: Pilih Express → +2 hari dari hari ini
- Ditampilkan read-only, calculated automatically

### 4. **Shipping Notes (Optional)**
- Supplier dapat menambahkan catatan khusus
- Contoh: "Fragile items", "Handle with care", "Special delivery instructions"
- Opsional - bisa kosong

### 5. **Edit/Update Shipping**
- Sebelum shipping final, supplier bisa mengubah courier type
- Estimasi delivery akan recalculate otomatis
- Shipping notes bisa diupdate

### 6. **Delete Shipping**
- Supplier dapat menghapus shipping information sebelum final ship
- Hanya tersedia untuk PO yang approved (belum shipped)
- Setelah shipped, shipping information tidak bisa dihapus

---

## 🏗️ Struktur Database

### Migration: `add_shipping_to_purchase_orders`

```sql
ALTER TABLE purchase_orders ADD COLUMN (
    tracking_number VARCHAR(50) NULLABLE,
    courier_type ENUM('truck', 'express') NULLABLE,
    estimated_delivery DATE NULLABLE,
    shipping_notes TEXT NULLABLE,
    shipped_at DATETIME NULLABLE
);
```

### Purchase Order Model Update

```php
protected $fillable = [
    'po_number',
    'admin_id',
    'supplier_id',
    'status',
    'notes',
    'tracking_number',      // ✨ NEW
    'courier_type',         // ✨ NEW
    'estimated_delivery',   // ✨ NEW
    'shipping_notes',       // ✨ NEW
    'shipped_at',          // ✨ NEW
];
```

---

## 🔄 Workflow

### Status Flow dengan Shipping

```
PENDING (Created)
    ↓
APPROVED_SUPPLIER (Supplier approve)
    ↓
    [SHIPPING MODAL]
    ├─ Auto-generate tracking number
    ├─ Select courier (truck/express)
    ├─ Auto-calculate estimated delivery
    ├─ Add optional shipping notes
    ├─ Can edit courier & notes
    └─ Can delete before final submit
    ↓
SHIPPED (Final submit)
    ├─ tracking_number ✓ stored
    ├─ courier_type ✓ stored
    ├─ estimated_delivery ✓ stored
    ├─ shipping_notes ✓ stored
    └─ shipped_at ✓ stored
    ↓
RECEIVED (Admin confirm)
```

---

## 📱 UI/UX

### Shipping Modal Form

#### **Tracking Number Field**
- **Type:** Text (disabled/read-only)
- **Status:** Auto-generated
- **Value:** `TRK20251117143025...`
- **User Action:** View only

#### **Courier Type Dropdown**
- **Type:** Select dropdown
- **Options:** 
  - `🚚 Truck (5 Days)`
  - `⚡ Express (2 Days)`
- **Required:** Yes
- **On Change:** Trigger `updateEstimatedDelivery()`

#### **Estimated Delivery Date**
- **Type:** Date input (disabled/read-only)
- **Status:** Auto-calculated
- **Value:** Format YYYY-MM-DD
- **Calculation:** Today + N days (berdasarkan courier type)

#### **Shipping Notes**
- **Type:** Textarea (editable)
- **Rows:** 3
- **Placeholder:** "Additional shipping details..."
- **Required:** No
- **Max Length:** No limit

#### **Action Buttons**
- **Cancel:** Close modal without save
- **Delete:** (if in edit mode) Delete shipping information
- **Mark as Shipped:** Submit and finalize

---

## 🛠️ Technical Implementation

### OrderController Methods

#### `ship()` - Initial Shipping
```php
public function ship(Request $request, PurchaseOrder $purchaseOrder)
{
    // Generate tracking number
    $trackingNumber = PurchaseOrder::generateTrackingNumber();
    
    // Calculate estimated delivery
    $estimatedDelivery = $purchaseOrder->calculateEstimatedDelivery(
        $request->courier_type
    );
    
    // Update PO with shipping info
    $purchaseOrder->update([
        'status' => 'shipped',
        'tracking_number' => $trackingNumber,
        'courier_type' => $request->courier_type,
        'estimated_delivery' => $estimatedDelivery,
        'shipping_notes' => $request->shipping_notes,
        'shipped_at' => now(),
    ]);
}
```

#### `updateShipping()` - Edit Existing
```php
public function updateShipping(Request $request, PurchaseOrder $purchaseOrder)
{
    // Recalculate estimated delivery if courier type changed
    $estimatedDelivery = $purchaseOrder->calculateEstimatedDelivery(
        $request->courier_type
    );
    
    $purchaseOrder->update([
        'courier_type' => $request->courier_type,
        'estimated_delivery' => $estimatedDelivery,
        'shipping_notes' => $request->shipping_notes,
    ]);
}
```

#### `deleteShipping()` - Remove
```php
public function deleteShipping(PurchaseOrder $purchaseOrder)
{
    // Only if order is approved but not shipped yet
    $purchaseOrder->update([
        'tracking_number' => null,
        'courier_type' => null,
        'estimated_delivery' => null,
        'shipping_notes' => null,
    ]);
}
```

### PurchaseOrder Model Helpers

```php
public static function generateTrackingNumber()
{
    // TRK20251117143025A7B8CD
    $prefix = 'TRK';
    $timestamp = date('YmdHis');
    $random = strtoupper(substr(md5(uniqid()), 0, 6));
    return $prefix . $timestamp . $random;
}

public static function getEstimatedDays($courierType)
{
    return [
        'truck' => 5,
        'express' => 2,
    ][$courierType] ?? 5;
}

public function calculateEstimatedDelivery($courierType)
{
    $days = self::getEstimatedDays($courierType);
    return now()->addDays($days)->toDateString();
}
```

---

## 📍 Routes

```php
// Supplier Order Routes
Route::post('/orders/{purchaseOrder}/ship', 'ship')->name('orders.ship');
Route::put('/orders/{purchaseOrder}/shipping', 'updateShipping')->name('orders.updateShipping');
Route::delete('/orders/{purchaseOrder}/shipping', 'deleteShipping')->name('orders.deleteShipping');
```

---

## 📺 Views Update

### `supplier/orders/index.blade.php`
- Shipping modal untuk quick shipping dari list view
- Button "Mark as Shipped" untuk PO dengan status approved
- Modal show saat user klik tombol ship

### `supplier/orders/show.blade.php`
- Shipping information section (if exists)
- Edit button untuk modify courier/notes sebelum final ship
- Tampilkan tracking number, courier type, estimasi delivery
- Modal untuk add/edit/delete shipping

---

## 🧪 Testing Steps

### Test 1: Approve PO dan Add Shipping
1. Login sebagai Supplier
2. Buka Purchase Orders
3. Lihat PO dengan status "Approved"
4. Klik "Mark as Shipped" (truck icon)
5. Modal muncul dengan form shipping

### Test 2: Auto-Generate & Calculate
1. Modal terbuka dengan tracking number auto-filled
2. Pilih courier type "Truck"
3. Estimated delivery auto-calculate (5 days)
4. Ubah ke "Express"
5. Estimated delivery update (2 days)

### Test 3: Add Notes & Submit
1. Input shipping notes (optional)
2. Klik "Mark as Shipped"
3. PO status berubah ke "Shipped"
4. Tracking number & courier info tersimpan
5. Estimated delivery visible

### Test 4: Edit Shipping
1. Buka detail PO yang sudah approved
2. Di section "Shipping Information" → klik "Edit"
3. Modal terbuka dengan data sebelumnya
4. Ubah courier type atau notes
5. Submit untuk update

### Test 5: Delete Shipping
1. Buka detail PO yang approved (belum shipped)
2. Klik "Edit" di shipping section
3. Klik button "Delete"
4. Confirm deletion
5. Shipping info cleared

---

## 📊 Fitur Pendampingan

### Shipping Information Display (Detail View)
```
Tracking Number:      TRK20251117...
Courier Type:         🚚 Truck (5 Days) / ⚡ Express (2 Days)
Estimated Delivery:   2025-11-22
Shipped At:          2025-11-17 14:30
Shipping Notes:      [optional notes]
```

### Status Timeline
- Order ditampilkan dengan status "Shipped"
- Timeline memperlihatkan saat shipped

---

## 🔐 Permissions & Security

✅ **Only Supplier Can:**
- Add shipping info (saat approve → ship)
- Edit courier & notes (sebelum final ship)
- Delete shipping (sebelum final ship)

✅ **Protection:**
- Cannot delete shipping setelah final ship
- Cannot change tracking number (read-only, auto-generated)
- Role-based access control via middleware

---

## 💾 Database Constraints

- `tracking_number` - NULLABLE, unique per PO
- `courier_type` - ENUM('truck', 'express'), NULLABLE
- `estimated_delivery` - DATE, NULLABLE
- `shipping_notes` - TEXT, NULLABLE
- `shipped_at` - DATETIME, NULLABLE

---

## 📝 Files Modified/Created

✅ **Created:**
- `/database/migrations/2024_01_01_000010_add_shipping_to_purchase_orders.php`

✅ **Updated:**
- `/app/Models/PurchaseOrder.php` - Added shipping fields & helper methods
- `/app/Http/Controllers/Supplier/OrderController.php` - Added ship, updateShipping, deleteShipping methods
- `/routes/web.php` - Added new shipping routes
- `/resources/views/supplier/orders/index.blade.php` - Updated shipping modal
- `/resources/views/supplier/orders/show.blade.php` - Added shipping section & modal

---

## 🎯 Key Benefits

1. **Otomasi:** Tracking number & estimasi delivery generate otomatis
2. **Fleksibilitas:** Bisa pilih courier sesuai kebutuhan
3. **Transparansi:** Supplier & Admin bisa lihat estimasi pengiriman
4. **Kontrol:** Supplier bisa edit/delete sebelum final ship
5. **Dokumentasi:** Semua shipping info tersimpan di database
6. **User Experience:** Interface intuitif dengan auto-calculation

---

**Status:** ✅ COMPLETE
**Date:** November 17, 2025
**Version:** 1.0
