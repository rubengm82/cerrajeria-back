<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function summary(): JsonResponse
    {
        $validStatuses = ['pending', 'shipped', 'installation_confirmed'];
        $startDate = Carbon::now()->startOfMonth()->subMonths(5);

        $salesByMonth = DB::table('orders')
            ->join('order_products', 'order_products.order_id', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->selectRaw("DATE_FORMAT(orders.created_at, '%Y-%m') as month_key")
            ->selectRaw('COALESCE(SUM(order_products.quantity * products.price), 0) as total_amount')
            ->whereNull('orders.deleted_at')
            ->whereNull('products.deleted_at')
            ->whereIn('orders.status', $validStatuses)
            ->where('orders.created_at', '>=', $startDate)
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        $months = collect(range(0, 5))->map(function (int $offset) use ($startDate, $salesByMonth) {
            $month = $startDate->copy()->addMonths($offset);
            $monthKey = $month->format('Y-m');
            $monthData = $salesByMonth->get($monthKey);

            return [
                'month' => $monthKey,
                'label' => $month->translatedFormat('M'),
                'total' => round((float) ($monthData->total_amount ?? 0), 2),
            ];
        })->values();

        $topProducts = DB::table('order_products')
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->whereNull('products.deleted_at')
            ->whereNull('orders.deleted_at')
            ->whereIn('orders.status', $validStatuses)
            ->groupBy('products.id', 'products.name')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_products.quantity) as quantity_sold')
            )
            ->orderByDesc('quantity_sold')
            ->limit(10)
            ->get();

        $lowStockProducts = Product::query()
            ->select('id', 'name', 'stock')
            ->where('stock', '>=', 0)
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(10)
            ->get();

        $totalSalesAmount = DB::table('order_products')
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->whereNull('products.deleted_at')
            ->whereNull('orders.deleted_at')
            ->whereIn('orders.status', $validStatuses)
            ->selectRaw('COALESCE(SUM(order_products.quantity * products.price), 0) as total_sales_amount')
            ->value('total_sales_amount');

        $totalUnitsSold = DB::table('order_products')
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->whereNull('orders.deleted_at')
            ->whereIn('orders.status', $validStatuses)
            ->sum('order_products.quantity');

        $totalOrders = Order::query()
            ->whereIn('status', $validStatuses)
            ->count();

        return response()->json([
            'summary' => [
                'total_sales_amount' => round((float) $totalSalesAmount, 2),
                'total_units_sold' => (int) $totalUnitsSold,
                'total_orders' => (int) $totalOrders,
                'low_stock_products_count' => $lowStockProducts->count(),
            ],
            'sales_by_month' => $months,
            'top_products' => $topProducts,
            'low_stock_products' => $lowStockProducts,
        ]);
    }
}
