<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\ProductRequest;
use App\Models\StorageLocation;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseRequestController extends Controller
{
    /**
     * Tampilkan daftar permintaan yang menunggu pemilihan lokasi storage
     * Admin hanya bisa akses endpoint ini untuk mengelola warehouse requests
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all pending requests yang belum ada storage location
        $pendingRequests = ProductRequest::needsWarehouseSelection()
            ->with(['store', 'product', 'warehouse'])
            ->latest()
            ->paginate(15);

        // Get requests yang sudah di-assign storage location, menunggu operator verification
        $processingRequests = ProductRequest::needsVerification()
            ->with(['store', 'product', 'warehouse', 'storageLocation'])
            ->latest()
            ->paginate(15);

        // Get requests yang sudah verified dan seterusnya
        $verifiedRequests = ProductRequest::awaitingApproval()
            ->with(['store', 'product', 'warehouse', 'storageLocation', 'operator'])
            ->latest()
            ->paginate(15);

        return view('admin.warehouse.product_requests.index', [
            'pendingRequests' => $pendingRequests,
            'processingRequests' => $processingRequests,
            'verifiedRequests' => $verifiedRequests,
        ]);
    }

    /**
     * Tampilkan form untuk memilih lokasi storage dari permintaan
     */
    public function selectLocation($id)
    {
        $request = ProductRequest::with(['store', 'product', 'warehouse'])
            ->needsWarehouseSelection()
            ->findOrFail($id);

        // Get available storage locations dengan stock untuk produk ini
        $storageLocations = StorageLocation::where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $request->product_id)
            ->where('quantity', '>', 0)
            ->get();

        if ($storageLocations->isEmpty()) {
            return redirect()->route('admin.warehouse_product_requests.index')
                ->with('error', 'Tidak ada lokasi storage dengan stok produk ini.');
        }

        return view('admin.warehouse.product_requests.select-location', [
            'request' => $request,
            'storageLocations' => $storageLocations,
        ]);
    }

    /**
     * Simpan pemilihan lokasi storage
     */
    public function storeLocation(Request $request, $id)
    {
        $productRequest = ProductRequest::needsWarehouseSelection()->findOrFail($id);

        $validated = $request->validate([
            'storage_location_id' => 'required|exists:storage_locations,id',
        ]);

        // Verify storage location belongs to warehouse
        $storageLocation = StorageLocation::findOrFail($validated['storage_location_id']);
        if ($storageLocation->warehouse_id !== $productRequest->warehouse_id) {
            return back()->with('error', 'Lokasi storage tidak sesuai dengan gudang.');
        }

        // Verify storage has enough stock
        if ($storageLocation->quantity < $productRequest->quantity_requested) {
            return back()->with('error', 'Stock di lokasi storage tidak mencukupi.');
        }

        $productRequest->update([
            'storage_location_id' => $validated['storage_location_id'],
        ]);

        return redirect()->route('admin.warehouse_product_requests.index')
            ->with('success', 'Lokasi storage berhasil dipilih. Menunggu verifikasi dari operator.');
    }
}
