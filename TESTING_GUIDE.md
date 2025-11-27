# Search Functionality Testing Guide

## Quick Test Checklist

### 1. Admin Shipment Create Form
**URL**: `http://localhost:8000/admin/shipments/create`

**Test Store Search**:
1. ✅ Click in the "Cari nama toko..." field
2. ✅ Verify dropdown appears with list of stores
3. ✅ Type a partial store name (e.g., "Test")
4. ✅ Verify list filters to show only matching stores
5. ✅ Click on a store name
6. ✅ Verify store name appears in the search field
7. ✅ Verify green confirmation message shows "✓ Toko: [Store Name]"
8. ✅ Click outside dropdown
9. ✅ Verify dropdown closes

**Test Product Selection**:
1. ✅ Select a product from "Pilih Produk" dropdown
2. ✅ Verify locations list appears showing available storage locations
3. ✅ Verify location selects populate with available locations
4. ✅ Can add multiple locations with "+ Tambah Lokasi" button
5. ✅ Quantity validation works (shows error if exceeds available stock)

---

### 2. Admin Products Index
**URL**: `http://localhost:8000/admin/products`

**Test Search**:
1. ✅ Type in "Search products..." field
2. ✅ Verify table filters by product SKU or name
3. ✅ Try searching "TEST" - case insensitive
4. ✅ Partial match works (search "test" finds "test-product")

**Test Category Filter**:
1. ✅ Select a category from first dropdown
2. ✅ Verify table shows only products from that category
3. ✅ Select "All Categories"
4. ✅ Verify all products show again

**Test Supplier Filter**:
1. ✅ Select a supplier from second dropdown
2. ✅ Verify table shows only products from that supplier

**Test Combined Filters**:
1. ✅ Search for a product AND select a category AND select a supplier
2. ✅ Verify all three filters work together

---

### 3. Admin Storage Index
**URL**: `http://localhost:8000/admin/storage`

**Test Location Search**:
1. ✅ Type in "Search locations..." field
2. ✅ Verify table filters by location code
3. ✅ Try searching "A-01" - partial match
4. ✅ Clear search - all locations show again

**Test Status Filter**:
1. ✅ Click "Filled" button
2. ✅ Verify only filled locations show in grid
3. ✅ Verify only filled locations show in table
4. ✅ Click "Empty" button
5. ✅ Verify only empty locations show
6. ✅ Click "All"
7. ✅ Verify all locations show

**Test Combined Search + Status Filter**:
1. ✅ Search for location code AND select "Filled"
2. ✅ Verify both filters work together

---

### 4. Admin Suppliers Index
**URL**: `http://localhost:8000/admin/suppliers`

**Test Supplier Search**:
1. ✅ Type in "Search suppliers..." field
2. ✅ Verify table filters by supplier name
3. ✅ Try searching by contact person name
4. ✅ Try searching by phone number
5. ✅ Clear search - all suppliers show again

---

### 5. Admin Purchase Orders Index
**URL**: `http://localhost:8000/admin/po`

**Test PO Search**:
1. ✅ Type in "Search PO..." field
2. ✅ Verify table filters by PO number
3. ✅ Partial search works (e.g., "PO-2025" finds all POs in 2025)

**Test Status Filter**:
1. ✅ Select each status option from first dropdown
2. ✅ Verify table filters by selected status
3. ✅ Select "All Status"
4. ✅ Verify all POs show

**Test Supplier Filter**:
1. ✅ Select a supplier from second dropdown
2. ✅ Verify table shows only POs from that supplier

**Test Combined Filters**:
1. ✅ Search by PO number AND filter by status AND filter by supplier
2. ✅ Verify all three filters work together

---

### 6. Admin Categories Index
**URL**: `http://localhost:8000/admin/categories`

**Test Category Search**:
1. ✅ Type in "Search categories..." field
2. ✅ Verify table filters by category name
3. ✅ Try searching by description
4. ✅ Clear search - all categories show again

---

### 7. Supplier Purchase Orders Index
**URL**: `http://localhost:8000/supplier/orders`

**Test PO Search**:
1. ✅ Type in "Search PO numbers..." field
2. ✅ Verify table filters by PO number
3. ✅ Try searching by creator name
4. ✅ Verify shipping modal still works

---

## Expected Behavior

### All Search Features Should:
- ✅ Filter instantly as user types (no button click needed)
- ✅ Be case-insensitive
- ✅ Support partial string matching
- ✅ Show/hide rows based on match
- ✅ Work without page reload
- ✅ Preserve other form values (don't clear dropdowns, etc.)

### Dropdowns Should:
- ✅ Show matching options
- ✅ Hide non-matching options
- ✅ Show all options when search is cleared
- ✅ Highlight on hover
- ✅ Close on outside click

### Combined Filters Should:
- ✅ Use AND logic (row must match ALL filters)
- ✅ Work together without conflicts
- ✅ Update correctly when any filter changes

---

## Common Issues & Fixes

| Issue | Solution |
|-------|----------|
| Search not working | Check browser console for JS errors, verify HTML elements exist |
| Dropdown won't close | Check that click-outside listener is attached |
| Filter shows wrong results | Verify case sensitivity handling |
| Page feels slow | This is normal - client-side filters may process thousands of rows |

---

## Browser Compatibility

- ✅ Chrome/Edge (Latest)
- ✅ Firefox (Latest)  
- ✅ Safari (Latest)
- ⚠️ IE11 (not supported - uses modern JS features)

---

## Notes

- All searches are instant (no server calls)
- Searches only filter visible page (respect pagination)
- Dropdown functionality uses vanilla JavaScript (no jQuery)
- All filters are additive (clicking doesn't remove other filters)

---

**Created**: 2025-11-17
**Last Updated**: 2025-11-17
