@extends('layouts.admin')

@section('title', 'Create Purchase Order')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Create Purchase Order</h1>
    <a href="{{ route('admin.po.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Back to PO
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.po.store') }}" id="po-form">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Supplier *</label>
                <select id="supplier_id" name="supplier_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        onchange="filterProducts()">
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea id="notes" name="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Additional notes for this purchase order...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- PO Items -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Purchase Order Items</h3>
                <button type="button" onclick="addRow()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    <i class="fas fa-plus mr-2"></i>Add Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full" id="items-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="items-tbody">
                        <!-- Items will be added here dynamically -->
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-4 py-2 text-right font-semibold">Grand Total:</td>
                            <td class="px-4 py-2 font-semibold">Rp. <span id="grand-total">0</span></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- PO Preview -->
        <div class="mt-6 p-4 bg-gray-50 rounded">
            <h3 class="text-sm font-medium text-gray-700 mb-2">PO Preview</h3>
            <div class="bg-white p-4 rounded border">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-600">PO Number:</p>
                        <p class="font-medium">Auto-generated</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Supplier:</p>
                        <p class="font-medium" id="preview-supplier">Not selected</p>
                    </div>
                </div>
                <div class="text-sm text-gray-600">
                    <p>Items: <span id="preview-items" class="font-medium">0</span></p>
                    <p>Total Value: <span id="preview-total" class="font-medium">Rp. 0</span></p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.po.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700" onclick="validateForm(event)">
                <i class="fas fa-save mr-2"></i>Create Purchase Order
            </button>
        </div>
    </form>
</div>

<script>
let rowCounter = 0;
const products = @json($products);

function addRow() {
    rowCounter++;
    const tbody = document.getElementById('items-tbody');
    const row = document.createElement('tr');
    row.id = `row-${rowCounter}`;
    
    row.innerHTML = `
        <td class="px-4 py-2">
            <select name="items[${rowCounter}][product_id]" required class="product-select w-full px-2 py-1 border rounded text-sm" onchange="updateRow(${rowCounter})">
                <option value="">Select Product</option>
                ${products.map(product => `<option value="${product.id}" data-price="${product.price}">${product.name} - ${product.sku}</option>`).join('')}
            </select>
        </td>
        <td class="px-4 py-2">
            <input type="number" name="items[${rowCounter}][quantity]" required min="1" value="1" class="quantity-input w-full px-2 py-1 border rounded text-sm" onchange="updateRow(${rowCounter})">
        </td>
        <td class="px-4 py-2">
            <input type="number" name="items[${rowCounter}][unit_price]" required min="0" step="0.01" class="price-input w-full px-2 py-1 border rounded text-sm" onchange="updateRow(${rowCounter})">
        </td>
        <td class="px-4 py-2">
            <span class="row-total font-medium">Rp. 0</span>
        </td>
        <td class="px-4 py-2">
            <button type="button" onclick="removeRow(${rowCounter})" class="text-red-600 hover:text-red-900">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    updatePreview();
}

function removeRow(id) {
    const row = document.getElementById(`row-${id}`);
    row.remove();
    updateGrandTotal();
    updatePreview();
}

function updateRow(id) {
    const row = document.getElementById(`row-${id}`);
    const productSelect = row.querySelector('.product-select');
    const quantityInput = row.querySelector('.quantity-input');
    const priceInput = row.querySelector('.price-input');
    const totalSpan = row.querySelector('.row-total');
    
    // Auto-fill price when product is selected
    if (productSelect.value && !priceInput.value) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        if (price) {
            priceInput.value = price;
        }
    }
    
    const quantity = parseFloat(quantityInput.value) || 0;
    const price = parseFloat(priceInput.value) || 0;
    const total = quantity * price;
    
    totalSpan.textContent = `Rp. ${total.toLocaleString('id-ID')}`;
    updateGrandTotal();
    updatePreview();
}

function updateGrandTotal() {
    const rows = document.querySelectorAll('#items-tbody tr');
    let grandTotal = 0;
    
    rows.forEach(row => {
        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        grandTotal += quantity * price;
    });
    
    document.getElementById('grand-total').textContent = grandTotal.toLocaleString('id-ID');
}

function updatePreview() {
    const rows = document.querySelectorAll('#items-tbody tr');
    const itemCount = rows.length;
    const grandTotal = document.getElementById('grand-total').textContent;
    
    document.getElementById('preview-items').textContent = itemCount;
    document.getElementById('preview-total').textContent = `Rp. ${grandTotal}`;
    
    // Update supplier preview
    const supplierSelect = document.getElementById('supplier_id');
    const supplierName = supplierSelect.options[supplierSelect.selectedIndex]?.text || 'Not selected';
    document.getElementById('preview-supplier').textContent = supplierName;
}

function filterProducts() {
    const supplierId = document.getElementById('supplier_id').value;
    const productSelects = document.querySelectorAll('.product-select');
    
    productSelects.forEach(select => {
        const currentValue = select.value;
        select.innerHTML = '<option value="">Select Product</option>';
        
        if (supplierId) {
            const filteredProducts = products.filter(product => product.supplier_id == supplierId);
            filteredProducts.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.setAttribute('data-price', product.price);
                option.textContent = `${product.name} - ${product.sku}`;
                if (product.id == currentValue) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        } else {
            products.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.setAttribute('data-price', product.price);
                option.textContent = `${product.name} - ${product.sku}`;
                if (product.id == currentValue) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }
    });
    
    updatePreview();
}

function validateForm(event) {
    const rows = document.querySelectorAll('#items-tbody tr');
    if (rows.length === 0) {
        event.preventDefault();
        alert('Please add at least one item to the purchase order.');
        return false;
    }
    
    let valid = true;
    rows.forEach(row => {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        
        if (!productSelect.value || !quantityInput.value || !priceInput.value) {
            valid = false;
        }
    });
    
    if (!valid) {
        event.preventDefault();
        alert('Please fill in all item details.');
        return false;
    }
    
    return true;
}

// Add initial row
addRow();

// Update supplier preview on change
document.getElementById('supplier_id').addEventListener('change', updatePreview);
</script>
@endsection