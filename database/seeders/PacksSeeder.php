<?php

namespace Database\Seeders;

use App\Models\Pack;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PacksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packs = [
            [
                'name' => 'Pack Familia Cilindres Securemme',
                'total_price' => 35, // 45.00 in cents, cheaper than 53.00
                'description' => 'Pack con 2 cilindres básicos Securemme K1: 1 Níquel 30x30 y 1 Llautó 30x30',
                'products' => [
                    'Cilindre 30x30 Níquel Securemme K1',
                    'Cilindre 30x30 Llautó Securemme K1',
                ],
            ],
            [
                'name' => 'Pack Seguridad Alta KESO',
                'total_price' => 199, // 220.00, cheaper than 256.74
                'description' => 'Pack con 1 cilindre KESO Omega 2 MASTER Níquel y 1 Escut DMC Boxer Plata',
                'products' => [
                    'Cilindre 30x30 mm Níquel KESO Omega 2 MASTER',
                    'Escut DMC Boxer Plata',
                ],
            ],
            [
                'name' => 'Pack Escudos Seguros',
                'total_price' => 105, // 120.00, cheaper than 150.00
                'description' => 'Pack con 4 escuts: 2 ABUS y 2 DMC BASSET',
                'products' => [
                    'Escut ABUS Plata',
                    'Escut ABUS Daurat',
                    'Escut DMC BASSET Plata',
                    'Escut DMC BASSET Daurat',
                ],
            ],
            [
                'name' => 'Pack Completo Puerta',
                'total_price' => 125, // 170.00, cheaper than 201.50
                'description' => 'Pack completo para puerta: 1 Cilindre Securemme K1 30x30 Níquel, 1 Escut ABUS Plata, 1 Segon Pany M&C',
                'products' => [
                    'Cilindre 30x30 Níquel Securemme K1',
                    'Escut ABUS Plata',
                    'OFERTA segon pany M&C EZC',
                ],
            ],
        ];

        foreach ($packs as $packData) {
            $products = $packData['products'] ?? [];
            unset($packData['products']);

            // Usar name como clave única
            $pack = Pack::updateOrCreate(
                ['name' => $packData['name']],
                $packData
            );

            // Adjuntar los productos al pack
            if (! empty($products)) {
                $productIds = Product::whereIn('name', $products)->pluck('id');
                $pack->products()->sync($productIds);
            }
        }
    }
}
