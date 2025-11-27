<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductRequest;
use App\Models\ProductRequestShipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductRequestController extends Controller
{
    /**
     * Tampilkan daftar semua permintaan barang
     */
    public function index()
    {
        $pendingRequests = ProductRequest::pending()
            ->with(['store', 'product', 'storageLocation', 'operator'])
            ->latest()
            ->paginate(15);

        $awaitingApprovalRequests = ProductRequest::where('status', 'verified')
            ->whereNull('admin_id')
            ->with(['store', 'product', 'storageLocation', 'operator'])
            ->latest()
            ->paginate(15);

        $approvedRequests = ProductRequest::where('status', 'approved')
            ->with(['store', 'product', 'storageLocation', 'operator', 'admin'])
            ->latest()
            ->paginate(15);

        $shippedRequests = ProductRequest::where('status', 'shipped')
            ->with(['store', 'product', 'storageLocation', 'operator', 'admin', 'shipment'])
            ->latest()
            ->paginate(15);

        $deliveredRequests = ProductRequest::where('status', 'delivered')
            ->with(['store', 'product', 'storageLocation', 'operator', 'admin', 'shipment'])
            ->latest()
            ->paginate(15);

        return view('admin.product_requests.index', [
            'pendingRequests' => $pendingRequests,
            'awaitingApprovalRequests' => $awaitingApprovalRequests,
            'approvedRequests' => $approvedRequests,
            'shippedRequests' => $shippedRequests,
            'deliveredRequests' => $deliveredRequests,
        ]);
    }

    /**
     * Tampilkan detail request
     */
    public function show($id)
    {
        $request = ProductRequest::with([
            'store',
            'product',
            'storageLocation',
            'operator',
            'admin',
            'shipment',
        ])->findOrFail($id);

        return view('admin.product_requests.show', [
            'request' => $request,
        ]);
    }

    /**
     * Proses approval atau rejection dari admin
     */
    public function processApproval(Request $request, $id)
    {
        $productRequest = ProductRequest::findOrFail($id);

        if ($productRequest->status !== 'verified') {
            return redirect()->route('admin.product_requests.index')
                ->with('error', 'Request ini harus dalam status verified untuk di-approve.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_notes' => 'required|string|min:5',
        ]);

        $admin = Auth::user();

        if ($validated['action'] === 'approve') {
            $productRequest->update([
                'status' => 'approved',
                'admin_id' => $admin->id,
                'approved_at' => now(),
                'verification_notes' => ($productRequest->verification_notes ?? '') . ' | Admin: ' . $validated['admin_notes'],
            ]);

            // Buat shipment otomatis untuk approved request
            $trackingNumber = 'PR-' . $productRequest->id . '-' . strtoupper(Str::random(6));
            ProductRequestShipment::create([
                'product_request_id' => $productRequest->id,
                'tracking_number' => $trackingNumber,
                'status' => 'in_transit',
                'shipped_at' => now(),
            ]);

            // Update status request menjadi shipped
            $productRequest->update(['status' => 'shipped', 'shipped_at' => now()]);

            return redirect()->route('admin.product_requests.show', $id)
                ->with('success', 'Permintaan barang disetujui dan dalam perjalanan ke toko.');
        } else {
            // Reject
            $productRequest->update([
                'status' => 'rejected',
                'admin_id' => $admin->id,
                'rejection_reason' => $validated['admin_notes'],
            ]);

            return redirect()->route('admin.product_requests.show', $id)
                ->with('success', 'Permintaan barang ditolak oleh admin.');
        }
    }

    /**
     * Update status pengiriman menjadi delivered
     */
    public function markDelivered($id)
    {
        $productRequest = ProductRequest::with('shipment')->findOrFail($id);

        if ($productRequest->status !== 'shipped') {
            return redirect()->route('admin.product_requests.index')
                ->with('error', 'Hanya permintaan dengan status shipped yang bisa dimark delivered.');
        }

        $productRequest->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        if ($productRequest->shipment) {
            $productRequest->shipment->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);
        }

        return redirect()->route('admin.product_requests.show', $id)
            ->with('success', 'Permintaan barang ditandai sebagai delivered.');
    }
}
