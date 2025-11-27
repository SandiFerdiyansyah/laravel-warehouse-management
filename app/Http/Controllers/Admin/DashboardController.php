<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductMovement;
use App\Models\StorageLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrderItem;

class DashboardController extends Controller
{
    public function index()
    {
        // Get product movement statistics
        $productMovements = ProductMovement::select(
            DB::raw('type'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(quantity) as total_quantity')
        )
        ->groupBy('type')
        ->get();

        $inCount = $productMovements->where('type', 'in')->first();
        $outCount = $productMovements->where('type', 'out')->first();

        $stats = [
            'total_in' => $inCount ? $inCount->total_quantity : 0,
            'total_out' => $outCount ? $outCount->total_quantity : 0,
            'low_stock_products' => Product::where('stock_quantity', '<=', 10)->count(),
            'total_products' => Product::count(),
            'filled_locations' => StorageLocation::where('is_filled', true)->count(),
            'empty_locations' => StorageLocation::where('is_filled', false)->count(),
        ];

        // Get low stock products
        $lowStockProducts = Product::with(['category', 'supplier'])
            ->where('stock_quantity', '<=', 10)
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        // Get storage locations
        $storageLocations = StorageLocation::orderBy('location_code')->get();

        // Get recent product movements
        $recentMovements = ProductMovement::with(['product', 'user'])
            ->orderBy('timestamp', 'desc')
            ->limit(10)
            ->get();

        // Compute monetary totals and profit
        $totalInValue = 0; // cost value of incoming
        $totalOutValue = 0; // revenue value of outgoing
        $totalProfit = 0;
        $totalInQty = 0;
        $totalOutQty = 0;

        $products = Product::all();
        foreach ($products as $product) {
            // average cost from PO items
            $avgCost = PurchaseOrderItem::where('product_id', $product->id)->avg('unit_price') ?? 0;

            $inQty = ProductMovement::where('product_id', $product->id)->where('type', 'in')->sum('quantity');
            $outQty = ProductMovement::where('product_id', $product->id)->where('type', 'out')->sum('quantity');

            $inValue = $inQty * $avgCost;
            $outValue = $outQty * ($product->price ?? 0);
            $profit = ($product->price - $avgCost) * $outQty;

            $totalInValue += $inValue;
            $totalOutValue += $outValue;
            $totalProfit += $profit;
            $totalInQty += $inQty;
            $totalOutQty += $outQty;
        }

        $moneyStats = [
            'total_in_value' => $totalInValue,
            'total_out_value' => $totalOutValue,
            'total_profit' => $totalProfit,
            'total_in_qty' => $totalInQty,
            'total_out_qty' => $totalOutQty,
        ];

        // Build per-product profit summary to show top contributors
        $profitByProduct = [];
        foreach ($products as $product) {
            $avgCost = PurchaseOrderItem::where('product_id', $product->id)->avg('unit_price') ?? 0;
            $outQty = ProductMovement::where('product_id', $product->id)->where('type', 'out')->sum('quantity');
            $profit = ($product->price - $avgCost) * $outQty;
            $profitByProduct[] = [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'price' => $product->price,
                'avg_cost' => $avgCost,
                'out_qty' => $outQty,
                'profit' => $profit,
            ];
        }

        // sort desc by profit and take top 5
        usort($profitByProduct, function ($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });
        $topProfitProducts = array_slice($profitByProduct, 0, 5);

        return view('admin.dashboard', compact('stats', 'lowStockProducts', 'storageLocations', 'recentMovements', 'moneyStats', 'topProfitProducts'));
    }
}