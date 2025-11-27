@extends('layouts.admin')

@section('title', 'Buat Pengiriman')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Buat Pengiriman Stok ke Toko</h1>

    <div class="bg-white p-6 rounded-lg shadow">
        <form action="{{ route('admin.shipments.store') }}" method="post" id="shipment-form">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold">Produk</label>
                <select id="product-select" name="product_id" class="w-full border p-2" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                    @endforeach
                </select>
            </div>

            <div id="locations-list" class="mb-4 hidden">
                <h3 class="text-sm font-semibold mb-2">Lokasi yang menyimpan produk ini</h3>
                <div class="bg-gray-50 border rounded p-3 text-sm">
                    <ul id="locations-ul" class="space-y-1"></ul>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold">Toko</label>
                <div class="relative">
                    <input type="text" 
                           id="store-search" 
                           placeholder="Cari nama toko..." 
                           class="w-full border p-2 rounded"
                           autocomplete="off">
                    <input type="hidden" id="store-id" name="store_id" required>
                    <div id="store-dropdown" class="absolute top-full left-0 right-0 border border-t-0 bg-white rounded-b hidden max-h-48 overflow-y-auto z-10">
                        @foreach($stores as $store)
                            <div class="store-option p-2 hover:bg-blue-100 cursor-pointer" 
                                 data-id="{{ $store->id }}" 
                                 data-name="{{ $store->name }}">
                                <div class="font-semibold">{{ $store->name }}</div>
                                <div class="text-xs text-gray-600">{{ $store->user->email ?? 'N/A' }} - {{ $store->phone ?? '-' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div id="store-selected" class="mt-2 text-sm text-green-600 hidden">
                    ✓ Toko: <strong id="selected-store-name"></strong>
                </div>
                @error('store_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Tujuan Lokasi & Quantity</label>
                <div id="locations-container" class="space-y-3">
                    <div class="location-row flex gap-2">
                        <select name="locations[0][storage_location_id]" class="flex-1 border p-2" required>
                            <option value="">-- Pilih Lokasi --</option>
                        </select>
                        <input type="number" name="locations[0][quantity]" class="w-24 border p-2" min="1" placeholder="Qty" required>
                        <button type="button" class="remove-location bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" style="display:none;">Hapus</button>
                    </div>
                </div>
                <button type="button" id="add-location" class="mt-3 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">+ Tambah Lokasi</button>
            </div>

            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Kirim</button>
        </form>
    </div>

    <script>
        // Store search functionality
        const storeSearch = document.getElementById('store-search');
        const storeDropdown = document.getElementById('store-dropdown');
        const storeId = document.getElementById('store-id');
        const storeSelected = document.getElementById('store-selected');
        const selectedStoreName = document.getElementById('selected-store-name');
        const storeOptions = document.querySelectorAll('.store-option');

        storeSearch.addEventListener('focus', function() {
            storeDropdown.classList.remove('hidden');
        });

        storeSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            storeOptions.forEach(option => {
                const name = option.dataset.name.toLowerCase();
                option.style.display = name.includes(searchTerm) ? 'block' : 'none';
            });
        });

        storeOptions.forEach(option => {
            option.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                storeId.value = id;
                storeSearch.value = name;
                storeDropdown.classList.add('hidden');
                selectedStoreName.textContent = name;
                storeSelected.classList.remove('hidden');
            });
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#store-search') && !e.target.closest('#store-dropdown')) {
                storeDropdown.classList.add('hidden');
            }
        });

        // Existing product and location logic
        const productSelect = document.getElementById('product-select');
        const locationsContainer = document.getElementById('locations-container');
        const addLocationBtn = document.getElementById('add-location');
        const locationsList = document.getElementById('locations-list');
        const locationsUl = document.getElementById('locations-ul');
        let productLocationsData = [];

        async function loadLocationsForProduct(productId) {
            productLocationsData = [];
            locationsUl.innerHTML = '';
            locationsList.classList.add('hidden');

            if (!productId) return;

            try {
                const res = await fetch(`{{ url('/admin/shipments/product') }}/${productId}/locations`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error('Gagal memuat');
                productLocationsData = await res.json();

                if (!Array.isArray(productLocationsData) || productLocationsData.length === 0) {
                    return;
                }

                locationsList.classList.remove('hidden');
                productLocationsData.forEach(loc => {
                    const li = document.createElement('li');
                    li.textContent = `${loc.location_code} — Qty: ${loc.qty} — Kapasitas: ${loc.capacity ?? '-'}`;
                    locationsUl.appendChild(li);
                });

                // Update all location selects
                updateAllLocationSelects();
            } catch (err) {
                console.error(err);
            }
        }

        function updateAllLocationSelects() {
            const rows = document.querySelectorAll('.location-row');
            const selectedLocationIds = new Set();

            // Collect already selected locations
            rows.forEach(row => {
                const select = row.querySelector('select[name$="[storage_location_id]"]');
                if (select && select.value) {
                    selectedLocationIds.add(parseInt(select.value));
                }
            });

            // Update all selects with filtered options
            rows.forEach(row => {
                const select = row.querySelector('select[name$="[storage_location_id]"]');
                const currentValue = select.value;
                select.innerHTML = '<option value="">-- Pilih Lokasi --</option>';
                
                productLocationsData.forEach(loc => {
                    // Skip if this location is already selected in another row
                    if (selectedLocationIds.has(loc.id) && currentValue !== String(loc.id)) {
                        return;
                    }

                    const opt = document.createElement('option');
                    opt.value = loc.id;
                    opt.dataset.qty = loc.qty;
                    opt.textContent = `${loc.location_code} (qty: ${loc.qty})`;
                    select.appendChild(opt);
                });
                select.value = currentValue;
            });
        }

        function validateQuantity(row) {
            const locSelect = row.querySelector('select[name$="[storage_location_id]"]');
            const qtyInput = row.querySelector('input[name$="[quantity]"]');
            const errorDiv = row.querySelector('.qty-error') || (() => {
                const div = document.createElement('div');
                div.className = 'qty-error text-red-500 text-xs mt-1';
                row.appendChild(div);
                return div;
            })();

            const selectedOpt = locSelect.options[locSelect.selectedIndex];
            const maxQty = selectedOpt && selectedOpt.dataset.qty ? parseInt(selectedOpt.dataset.qty) : 0;
            const inputQty = parseInt(qtyInput.value || 0);

            if (inputQty > maxQty) {
                errorDiv.textContent = `Quantity tidak boleh melebihi stok yang tersedia (${maxQty})`;
                qtyInput.classList.add('border-red-500');
                return false;
            } else {
                errorDiv.textContent = '';
                qtyInput.classList.remove('border-red-500');
                return true;
            }
        }

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.location-row');
            rows.forEach(row => {
                const btn = row.querySelector('.remove-location');
                if (btn) {
                    btn.style.display = rows.length > 1 ? 'block' : 'none';
                }
            });
        }

        addLocationBtn.addEventListener('click', function() {
            const rows = document.querySelectorAll('.location-row');
            const newIndex = rows.length;
            const newRow = document.createElement('div');
            newRow.className = 'location-row flex gap-2';
            newRow.innerHTML = `
                <select name="locations[${newIndex}][storage_location_id]" class="flex-1 border p-2" required>
                    <option value="">-- Pilih Lokasi --</option>
                </select>
                <input type="number" name="locations[${newIndex}][quantity]" class="w-24 border p-2" min="1" placeholder="Qty" required>
                <button type="button" class="remove-location bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</button>
            `;
            locationsContainer.appendChild(newRow);

            // Populate the new select with locations
            const newSelect = newRow.querySelector('select');
            productLocationsData.forEach(loc => {
                const opt = document.createElement('option');
                opt.value = loc.id;
                opt.dataset.qty = loc.qty;
                opt.textContent = `${loc.location_code} (qty: ${loc.qty})`;
                newSelect.appendChild(opt);
            });

            // Add change and input listeners for quantity validation
            newSelect.addEventListener('change', function() {
                validateQuantity(newRow);
                updateAllLocationSelects(); // Update all dropdowns when selection changes
            });
            newRow.querySelector('input[name$="[quantity]"]').addEventListener('input', function() {
                validateQuantity(newRow);
            });

            // Add remove listener
            newRow.querySelector('.remove-location').addEventListener('click', function() {
                newRow.remove();
                updateRemoveButtons();
                updateAllLocationSelects(); // Update dropdowns after removal
            });

            updateRemoveButtons();
            updateAllLocationSelects(); // Update all dropdowns after adding new row
        });

        // Initial remove button setup
        document.querySelectorAll('.remove-location').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.location-row').remove();
                updateRemoveButtons();
            });
        });

        // Add validation listeners to initial row
        document.querySelectorAll('.location-row').forEach(row => {
            const locSelect = row.querySelector('select[name$="[storage_location_id]"]');
            const qtyInput = row.querySelector('input[name$="[quantity]"]');
            if (locSelect) {
                locSelect.addEventListener('change', function() {
                    validateQuantity(row);
                    updateAllLocationSelects(); // Update all dropdowns when selection changes
                });
            }
            if (qtyInput) {
                qtyInput.addEventListener('input', function() {
                    validateQuantity(row);
                });
            }
        });

        // Form submission validation
        document.getElementById('shipment-form').addEventListener('submit', function(e) {
            // Validate store is selected
            if (!storeId.value) {
                e.preventDefault();
                alert('Mohon pilih toko terlebih dahulu');
                return;
            }

            const rows = document.querySelectorAll('.location-row');
            let isValid = true;

            rows.forEach(row => {
                if (!validateQuantity(row)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Mohon periksa kembali input quantity yang tidak valid');
            }
        });

        productSelect.addEventListener('change', function() {
            loadLocationsForProduct(this.value);
        });

        // If product pre-selected, load locations
        if (productSelect.value) loadLocationsForProduct(productSelect.value);
    </script>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Tujuan Lokasi & Quantity</label>
                <div id="locations-container" class="space-y-3">
                    <div class="location-row flex gap-2">
                        <select name="locations[0][storage_location_id]" class="flex-1 border p-2" required>
                            <option value="">-- Pilih Lokasi --</option>
                        </select>
                        <input type="number" name="locations[0][quantity]" class="w-24 border p-2" min="1" placeholder="Qty" required>
                        <button type="button" class="remove-location bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" style="display:none;">Hapus</button>
                    </div>
                </div>
                <button type="button" id="add-location" class="mt-3 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">+ Tambah Lokasi</button>
            </div>

            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Kirim</button>
        </form>
    </div>

    <script>
        const productSelect = document.getElementById('product-select');
        const locationsContainer = document.getElementById('locations-container');
        const addLocationBtn = document.getElementById('add-location');
        const locationsList = document.getElementById('locations-list');
        const locationsUl = document.getElementById('locations-ul');
        let productLocationsData = [];

        async function loadLocationsForProduct(productId) {
            productLocationsData = [];
            locationsUl.innerHTML = '';
            locationsList.classList.add('hidden');

            if (!productId) return;

            try {
                const res = await fetch(`{{ url('/admin/shipments/product') }}/${productId}/locations`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error('Gagal memuat');
                productLocationsData = await res.json();

                if (!Array.isArray(productLocationsData) || productLocationsData.length === 0) {
                    return;
                }

                locationsList.classList.remove('hidden');
                productLocationsData.forEach(loc => {
                    const li = document.createElement('li');
                    li.textContent = `${loc.location_code} — Qty: ${loc.qty} — Kapasitas: ${loc.capacity ?? '-'}`;
                    locationsUl.appendChild(li);
                });

                // Update all location selects
                updateAllLocationSelects();
            } catch (err) {
                console.error(err);
            }
        }

        function updateAllLocationSelects() {
            const rows = document.querySelectorAll('.location-row');
            const selectedLocationIds = new Set();

            // Collect already selected locations
            rows.forEach(row => {
                const select = row.querySelector('select[name$="[storage_location_id]"]');
                if (select && select.value) {
                    selectedLocationIds.add(parseInt(select.value));
                }
            });

            // Update all selects with filtered options
            rows.forEach(row => {
                const select = row.querySelector('select[name$="[storage_location_id]"]');
                const currentValue = select.value;
                select.innerHTML = '<option value="">-- Pilih Lokasi --</option>';
                
                productLocationsData.forEach(loc => {
                    // Skip if this location is already selected in another row
                    if (selectedLocationIds.has(loc.id) && currentValue !== String(loc.id)) {
                        return;
                    }

                    const opt = document.createElement('option');
                    opt.value = loc.id;
                    opt.dataset.qty = loc.qty;
                    opt.textContent = `${loc.location_code} (qty: ${loc.qty})`;
                    select.appendChild(opt);
                });
                select.value = currentValue;
            });
        }

        function validateQuantity(row) {
            const locSelect = row.querySelector('select[name$="[storage_location_id]"]');
            const qtyInput = row.querySelector('input[name$="[quantity]"]');
            const errorDiv = row.querySelector('.qty-error') || (() => {
                const div = document.createElement('div');
                div.className = 'qty-error text-red-500 text-xs mt-1';
                row.appendChild(div);
                return div;
            })();

            const selectedOpt = locSelect.options[locSelect.selectedIndex];
            const maxQty = selectedOpt && selectedOpt.dataset.qty ? parseInt(selectedOpt.dataset.qty) : 0;
            const inputQty = parseInt(qtyInput.value || 0);

            if (inputQty > maxQty) {
                errorDiv.textContent = `Quantity tidak boleh melebihi stok yang tersedia (${maxQty})`;
                qtyInput.classList.add('border-red-500');
                return false;
            } else {
                errorDiv.textContent = '';
                qtyInput.classList.remove('border-red-500');
                return true;
            }
        }

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.location-row');
            rows.forEach(row => {
                const btn = row.querySelector('.remove-location');
                if (btn) {
                    btn.style.display = rows.length > 1 ? 'block' : 'none';
                }
            });
        }

        addLocationBtn.addEventListener('click', function() {
            const rows = document.querySelectorAll('.location-row');
            const newIndex = rows.length;
            const newRow = document.createElement('div');
            newRow.className = 'location-row flex gap-2';
            newRow.innerHTML = `
                <select name="locations[${newIndex}][storage_location_id]" class="flex-1 border p-2" required>
                    <option value="">-- Pilih Lokasi --</option>
                </select>
                <input type="number" name="locations[${newIndex}][quantity]" class="w-24 border p-2" min="1" placeholder="Qty" required>
                <button type="button" class="remove-location bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</button>
            `;
            locationsContainer.appendChild(newRow);

            // Populate the new select with locations
            const newSelect = newRow.querySelector('select');
            productLocationsData.forEach(loc => {
                const opt = document.createElement('option');
                opt.value = loc.id;
                opt.dataset.qty = loc.qty;
                opt.textContent = `${loc.location_code} (qty: ${loc.qty})`;
                newSelect.appendChild(opt);
            });

            // Add change and input listeners for quantity validation
            newSelect.addEventListener('change', function() {
                validateQuantity(newRow);
                updateAllLocationSelects(); // Update all dropdowns when selection changes
            });
            newRow.querySelector('input[name$="[quantity]"]').addEventListener('input', function() {
                validateQuantity(newRow);
            });

            // Add remove listener
            newRow.querySelector('.remove-location').addEventListener('click', function() {
                newRow.remove();
                updateRemoveButtons();
                updateAllLocationSelects(); // Update dropdowns after removal
            });

            updateRemoveButtons();
            updateAllLocationSelects(); // Update all dropdowns after adding new row
        });

        // Initial remove button setup
        document.querySelectorAll('.remove-location').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.location-row').remove();
                updateRemoveButtons();
            });
        });

        // Add validation listeners to initial row
        document.querySelectorAll('.location-row').forEach(row => {
            const locSelect = row.querySelector('select[name$="[storage_location_id]"]');
            const qtyInput = row.querySelector('input[name$="[quantity]"]');
            if (locSelect) {
                locSelect.addEventListener('change', function() {
                    validateQuantity(row);
                    updateAllLocationSelects(); // Update all dropdowns when selection changes
                });
            }
            if (qtyInput) {
                qtyInput.addEventListener('input', function() {
                    validateQuantity(row);
                });
            }
        });

        // Form submission validation
        document.getElementById('shipment-form').addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('.location-row');
            let isValid = true;

            rows.forEach(row => {
                if (!validateQuantity(row)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Mohon periksa kembali input quantity yang tidak valid');
            }
        });

        productSelect.addEventListener('change', function() {
            loadLocationsForProduct(this.value);
        });

        // If product pre-selected, load locations
        if (productSelect.value) loadLocationsForProduct(productSelect.value);
    </script>
@endsection