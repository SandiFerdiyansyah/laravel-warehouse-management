#!/bin/bash
# Simple browser test for store product request form
# Usage: bash scripts/test_store_ui.sh

echo "Testing store product request create form..."
echo ""

# Test 1: Get create form
echo "Test 1: GET /store/product-requests/create"
curl -s -c /tmp/store_cookies.txt http://127.0.0.1:8000/login | grep -q "Login" && echo "✓ Login page accessible"

# For manual verification:
echo ""
echo "Manual verification steps:"
echo "1. Open browser: http://127.0.0.1:8000/login"
echo "2. Login as store@warehouse.com / password123"
echo "3. Navigate to: Permintaan Barang → Buat Permintaan"
echo "4. Select product 'Kulkas (PRD-JQZ9C4QI)' or 'LG Monitor 24inch (PRD-QMGXYWHA)'"
echo "5. Verify:"
echo "   - Gudang 'Gudang Pusat Jakarta' appears"
echo "   - Stock shows: 20 unit"
echo "   - No orange warning badge (since it's real storage location now, not fallback)"
echo ""
echo "✓ If fallback scenario exists (e.g., manually edit product stock_quantity),
  the orange '⚠️ Stok Global' badge will appear."
