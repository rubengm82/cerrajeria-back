<?php

namespace Database\Seeders;

use App\Models\CommerceSetting;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::with(['products', 'packs'])->get();
        $settings = CommerceSetting::current();
        $installationStatuses = ['installation_confirmed', 'installation_pending', 'installation_finished'];

        foreach ($orders as $order) {
            $subtotal = 0;

            foreach ($order->products as $product) {
                $subtotal += (float) $product->price * (int) $product->pivot->quantity;
            }

            foreach ($order->packs as $pack) {
                $subtotal += (float) $pack->total_price * (int) $pack->pivot->quantity;
            }

            $installationPrice = 0;

            if (in_array($order->status, $installationStatuses)) {
                $rules = $settings->installation_rules ?? [];
                usort($rules, fn($a, $b) => $a['min_subtotal'] <=> $b['min_subtotal']);

                foreach ($rules as $rule) {
                    $max = $rule['max_subtotal'];
                    if ($max === null || $subtotal <= $max) {
                        $installationPrice = (float) ($rule['price'] ?? 0);
                        break;
                    }
                }
            }

            $order->update([
                'subtotal' => $subtotal,
                'installation_price' => $installationPrice,
            ]);
        }
    }
}
