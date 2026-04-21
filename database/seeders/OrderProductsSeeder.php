<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();
        $products = Product::all();

        if ($orders->isEmpty() || $products->isEmpty()) {
            return;
        }

        foreach ($orders as $order) {
            // Cada pedido tiene entre 1 y 4 productos diferentes
            $numProducts = rand(1, 4);
            $selectedProducts = $products->random($numProducts);

            foreach ($selectedProducts as $product) {
                DB::table('order_products')->insert([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'created_at' => $order->created_at,
                    'updated_at' => $order->created_at,
                ]);
            }
        }
    }
}
