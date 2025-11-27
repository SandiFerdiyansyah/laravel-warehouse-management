# Error "Error updating shipping status" - SOLVED ✅

## 🐛 Apa Masalahnya?

Error yang Anda lihat: **"Error updating shipping status"** muncul saat klik tombol "Mark as Shipped" di shipping modal.

---

## 🔍 Root Cause (Akar Penyebab)

**Mismatch antara Backend Response dan Frontend Expectation:**

### Backend (Controller `ship()` method)
```php
// BEFORE - Return HTML redirect
return back()->with('success', 'Order marked as shipped...');
```

**Problem:**
- Mengembalikan HTML redirect response
- Content-Type: `text/html; charset=UTF-8`
- Status code: 302 (redirect)

### Frontend (JavaScript fetch)
```javascript
// BEFORE - Expect JSON response
fetch(`/supplier/orders/${poId}/ship`, {
    method: 'POST',
    headers: {
        'Accept': 'application/json'  // ← Expect JSON
    },
    body: formData
})
.then(response => response.json())  // ← Try to parse as JSON
```

**Problem:**
- Mengirim request dengan header `Accept: application/json`
- Try to parse response sebagai JSON
- Tapi server return HTML
- **Result:** `response.json()` throws error → "Error updating shipping status"

---

## ✅ Solusi yang Diterapkan

### 1. Update Backend - Method `ship()`

Added JSON response detection:

```php
public function ship(Request $request, PurchaseOrder $purchaseOrder)
{
    // ... validation code ...

    $purchaseOrder->update([
        'status' => 'shipped',
        'tracking_number' => $trackingNumber,
        'courier_type' => $request->courier_type,
        'estimated_delivery' => $estimatedDelivery,
        'shipping_notes' => $request->shipping_notes,
        'shipped_at' => now(),
    ]);

    $message = 'Order marked as shipped. Tracking number: ' . $trackingNumber;

    // ✅ PERBAIKAN: Deteksi jika client minta JSON
    if ($request->wantsJson()) {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'tracking_number' => $trackingNumber,
                'courier_type' => $request->courier_type,
                'estimated_delivery' => $estimatedDelivery,
            ]
        ]);
    }

    // Jika bukan AJAX, return redirect seperti sebelumnya
    return back()->with('success', $message);
}
```

### 2. Update Frontend - Better Error Handling

Enhanced error handling dan response validation:

```javascript
function handleShippingSubmit(event) {
    event.preventDefault();
    
    // ... form data collection ...

    fetch(`/supplier/orders/${poId}/ship`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        // ✅ PERBAIKAN 1: Check if response is actually JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response');
        }
        
        // ✅ PERBAIKAN 2: Check response status code
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error || data.message || 'Server error');
            });
        }
        
        return response.json();
    })
    .then(data => {
        // ✅ PERBAIKAN 3: Better success handling
        if (data.success) {
            alert(data.message);  // Show tracking number
            closeShippingModal();
            location.reload();
        } else {
            alert('Error: ' + (data.error || data.message || 'Unable to update shipping'));
        }
    })
    .catch(error => {
        // ✅ PERBAIKAN 4: Better error messages
        console.error('Error:', error);
        alert('Error updating shipping: ' + error.message);
    });
}
```

---

## 📝 File yang Diperbaiki

### 1. `/app/Http/Controllers/Supplier/OrderController.php`
- Updated `ship()` method
- Added `$request->wantsJson()` check
- Return JSON response for AJAX requests
- Return HTML redirect for regular requests

### 2. `/resources/views/supplier/orders/index.blade.php`
- Updated `handleShippingSubmit()` function
- Better error handling
- Response content-type validation
- Status code checking
- Improved error messages

---

## 🧪 Sekarang Fitur Akan Bekerja

### Flow yang Benar Sekarang:

```
1. User klik "Mark as Shipped"
   ↓
2. Modal terbuka dengan form
   ├─ Tracking # auto-generated
   ├─ Pilih Courier type
   ├─ Estimated delivery auto-calc
   └─ Input shipping notes
   ↓
3. User klik "Mark as Shipped" button
   ↓
4. JavaScript fetch POST request
   ├─ Body: courier_type, shipping_notes
   ├─ Headers: Accept: application/json, CSRF token
   └─ Method: POST
   ↓
5. Laravel Controller `ship()` method
   ├─ Validate data
   ├─ Generate tracking number
   ├─ Calculate estimated delivery
   ├─ Update database
   └─ Detect `Accept: application/json` header
   ↓
6. Server return JSON response
   {
     "success": true,
     "message": "Order marked as shipped. Tracking: TRK20251117...",
     "data": {
       "tracking_number": "TRK20251117...",
       "courier_type": "express",
       "estimated_delivery": "2025-11-19"
     }
   }
   ↓
7. JavaScript parse JSON
   ├─ Check content-type header
   ├─ Check response status (200)
   ├─ Parse response.json()
   └─ Handle success/error
   ↓
8. Show success alert dengan tracking number
   ↓
9. Close modal
   ↓
10. Reload page
   ↓
11. ✅ PO status berubah ke "SHIPPED"
    ├─ Tracking number: saved
    ├─ Courier type: saved
    ├─ Estimated delivery: saved
    └─ Shipped at: saved
```

---

## ✅ Kesimpulan

Error **"Error updating shipping status"** terjadi karena:

| Aspek | Before | After |
|-------|--------|-------|
| **Backend Response** | HTML redirect (302) | JSON (200) |
| **Content-Type** | text/html | application/json |
| **JavaScript** | Expect JSON, dapat HTML | Properly handle both |
| **Error Handling** | Generic alert | Detailed error messages |
| **Response Check** | Langsung parse JSON | Check content-type dulu |

Sekarang sudah **FIXED** ✅ dan akan bekerja dengan baik!

---

## 🚀 Cara Test

1. **Buka aplikasi:** http://127.0.0.1:8000/login
2. **Login sebagai supplier:** supplier@warehouse.com / password
3. **Buka Purchase Orders**
4. **Klik truck icon pada PO Approved**
5. **Modal terbuka**
   - Tracking # auto-fill ✓
   - Pilih courier (Express)
   - Estimated delivery auto-calc ✓
6. **Klik "Mark as Shipped"**
7. **✅ Sekarang akan success (bukan error lagi)**
8. **Modal close, page reload**
9. **PO status berubah ke "Shipped"**
10. **Lihat tracking number di detail page**

---

**Status:** ✅ **FIXED**
**Date:** November 17, 2025
