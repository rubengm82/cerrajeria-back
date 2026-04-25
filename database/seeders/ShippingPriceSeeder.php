<?php

namespace Database\Seeders;

use App\Models\CommerceSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        $setting = CommerceSetting::query()->firstOrCreate(['id' => 1], [
            'shipping_price' => 9,
        ]);

        $setting->update([
            'shipping_price' => 9,
        ]);

        $setting->installationPriceRules()->delete();
        $setting->installationPriceRules()->createMany(
            collect($shippingRules)->values()->map(fn (array $rule, int $index) => [
                ...$rule,
                'sort_order' => $index,
            ])->all()
        );
    }
}
