<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ProductRequest;
use App\Models\Product;
use App\Models\StorageLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductRequestController extends Controller
{
    /**
     * Tampilkan daftar permintaan barang yang perlu diverifikasi
     */
    public function index()
    {
        // Use explicit scopes to ensure operator sees only requests that truly
        // need verification (pending with storage_location_id and not yet assigned to any operator).
        $pendingRequests = ProductRequest::needsVerification()
            ->with(['store', 'product', 'warehouse', 'storageLocation'])
            ->latest()
            ->paginate(15);

        // Use awaitingApproval scope for requests that were verified by operator
        // and are waiting for admin approval (status = verified and no admin_id).
        $verifiedRequests = ProductRequest::awaitingApproval()
            ->with(['store', 'product', 'warehouse', 'storageLocation', 'operator', 'admin'])
            ->latest()
            ->paginate(15);

        // Shipped requests (already processed and shipped)
        $shippedRequests = ProductRequest::where('status', 'shipped')
            ->with(['store', 'product', 'warehouse', 'storageLocation', 'operator', 'admin', 'shipment'])
            ->latest()
            ->paginate(15);

        return view('operator.product_requests.index', [
            'pendingRequests' => $pendingRequests,
            'verifiedRequests' => $verifiedRequests,
            'shippedRequests' => $shippedRequests,
        ]);
    }

    /**
     * Tampilkan form verifikasi untuk satu request
     */
    public function verify($id)
    {
        $request = ProductRequest::with(['store', 'product', 'warehouse', 'storageLocation'])
            ->needsVerification()
            ->findOrFail($id);

        // Ambil data real-time dari storage
        $storage = $request->storageLocation;
        $availableQty = $storage->quantity ?? 0;

        return view('operator.product_requests.verify', [
            'request' => $request,
            'availableQty' => $availableQty,
            'product' => $request->product,
            'warehouse' => $request->warehouse,
            'storage' => $storage,
        ]);
    }

    /**
     * Proses verifikasi dari operator
     */
    public function storeVerification(Request $request, $id)
    {
        $productRequest = ProductRequest::needsVerification()->findOrFail($id);

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'quantity_verified' => 'required_if:action,approve|integer|min:1',
            'verification_notes' => 'required|string|min:10',
        ]);

        $operator = Auth::user();

        if ($validated['action'] === 'approve') {
            // Cek apakah quantity_verified <= quantity_requested
            if ($validated['quantity_verified'] > $productRequest->quantity_requested) {
                return back()->withErrors([
                    'quantity_verified' => 'Quantity verified tidak boleh melebihi quantity requested.',
                ])->withInput();
            }

            // Cek apakah storage punya stock cukup
            $storage = $productRequest->storageLocation;
            if ($storage->quantity < $validated['quantity_verified']) {
                return back()->withErrors([
                    'quantity_verified' => 'Stock di storage tidak mencukupi. Stock tersedia: ' . $storage->quantity,
                ])->withInput();
            }

            $productRequest->update([
                'status' => 'verified',
                'operator_id' => $operator->id,
                'quantity_verified' => $validated['quantity_verified'],
                'verification_notes' => $validated['verification_notes'],
                'verified_at' => now(),
            ]);

            return redirect()->route('operator.product_requests.index')
                ->with('success', 'Permintaan barang berhasil diverifikasi. Menunggu approval dari admin.');
        } else {
            // Reject action
            $productRequest->update([
                'status' => 'rejected',
                'operator_id' => $operator->id,
                'verification_notes' => $validated['verification_notes'],
                'verified_at' => now(),
            ]);

            return redirect()->route('operator.product_requests.index')
                ->with('success', 'Permintaan barang ditolak.');
        }
    }
}
