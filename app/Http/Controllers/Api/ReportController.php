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
    private const VALID_STATUSES = ['pending', 'shipped', 'installation_confirmed', 'installation_pending'];

    public function summary(): JsonResponse
    {
        $startDate = Carbon::now()->startOfMonth()->subMonths(5);
        $lowStockProducts = $this->lowStockProducts();

        return response()->json([
            'summary' => [
                'total_sales_amount' => $this->totalSalesAmount(),
                'total_units_sold' => (int) $this->validOrderProductsQuery()->sum('order_products.quantity'),
                'total_orders' => $this->ordersQuery()->count(),
                'low_stock_products_count' => $lowStockProducts->count(),
            ],
            'sales_by_month' => $this->salesByMonth($startDate),
            'top_products' => $this->topProducts(),
            'low_stock_products' => $lowStockProducts,
        ]);
    }

    private function salesByMonth(Carbon $startDate)
    {
        $salesByMonth = $this->pricedOrderProductsQuery()->selectRaw("DATE_FORMAT(orders.created_at, '%Y-%m') as month_key")->selectRaw('COALESCE(SUM(order_products.quantity * products.price), 0) as total_amount')->where('orders.created_at', '>=', $startDate)->groupBy('month_key')->orderBy('month_key')->get()->keyBy('month_key');

        return collect(range(0, 5))->map(function (int $offset) use ($startDate, $salesByMonth) {
            $month = $startDate->copy()->addMonths($offset);
            $monthKey = $month->format('Y-m');
            $monthData = $salesByMonth->get($monthKey);

            return [
                'month' => $monthKey,
                'label' => $month->translatedFormat('M'),
                'total' => round((float) ($monthData->total_amount ?? 0), 2),
            ];
        })->values();
    }

    private function topProducts()
    {
        return $this->pricedOrderProductsQuery()->groupBy('products.id', 'products.name')->select('products.id', 'products.name', DB::raw('SUM(order_products.quantity) as quantity_sold'))->orderByDesc('quantity_sold')->limit(10)->get();
    }

    private function totalSalesAmount(): float
    {
        $total = $this->pricedOrderProductsQuery()->selectRaw('COALESCE(SUM(order_products.quantity * products.price), 0) as total')->value('total');

        return round((float) $total, 2);
    }

    private function lowStockProducts()
    {
        return Product::query()->select('id', 'name', 'stock')->where('stock', '>=', 0)->orderBy('stock')->orderBy('name')->limit(10)->get();
    }

    private function validOrderProductsQuery()
    {
        return DB::table('order_products')->join('orders', 'orders.id', '=', 'order_products.order_id')->whereNull('orders.deleted_at')->whereIn('orders.status', self::VALID_STATUSES);
    }

    private function pricedOrderProductsQuery()
    {
        return $this->validOrderProductsQuery()->join('products', 'products.id', '=', 'order_products.product_id')->whereNull('products.deleted_at');
    }

    private function ordersQuery()
    {
        return Order::query()->whereIn('status', self::VALID_STATUSES);
    }
}
