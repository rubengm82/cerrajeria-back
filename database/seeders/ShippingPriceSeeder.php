<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingPriceSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shippingRules = [
            ['min_subtotal' => 0, 'max_subtotal' => 250, 'price' => 90],
            ['min_subtotal' => 251, 'max_subtotal' => 500, 'price' => 120],
            ['min_subtotal' => 501, 'max_subtotal' => 1000, 'price' => 180],
            ['min_subtotal' => 1001, 'max_subtotal' => null, 'price' => 270],
        ];

        DB::table('commerce_settings')->updateOrInsert(
            ['id' => 1],
            [
                'installation_rules' => json_encode($shippingRules),
                'shipping_price' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
