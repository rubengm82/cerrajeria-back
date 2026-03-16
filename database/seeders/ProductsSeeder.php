<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::query()
            ->whereIn('name', [
                'Bombines de seguridad',
                'Escudos protectores',
                'Accesorios de cerrajeria',
            ])
            ->pluck('id', 'name');

        $products = [
            [
                'name' => 'Bombin antibumping europerfil premium',
                'description' => 'Bombin de alta seguridad con proteccion antibumping, antitaladro y cinco llaves reversibles.',
                'price' => 89.90,
                'stock' => 18,
                'code' => 'BOM-SEG-001',
                'discount' => 10.00,
                'category_id' => $categories['Bombines de seguridad'],
                'is_installable' => true,
                'is_important_to_show' => true,
                'installation_price' => 35.00,
                'extra_keys' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Bombin de seguridad leva larga reforzada',
                'description' => 'Cilindro reforzado para puertas acorazadas con resistencia avanzada frente a ganzuado y extrusion.',
                'price' => 104.50,
                'stock' => 12,
                'code' => 'BOM-SEG-002',
                'discount' => 12.50,
                'category_id' => $categories['Bombines de seguridad'],
                'is_installable' => true,
                'is_important_to_show' => true,
                'installation_price' => 40.00,
                'extra_keys' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Escudo magnetico acorazado defender',
                'description' => 'Escudo protector magnetico para cerraduras de alta seguridad con cuerpo macizo y defensa antirotura.',
                'price' => 129.99,
                'stock' => 9,
                'code' => 'ESC-SEG-001',
                'discount' => 15.00,
                'category_id' => $categories['Escudos protectores'],
                'is_installable' => true,
                'is_important_to_show' => true,
                'installation_price' => 45.00,
                'extra_keys' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Escudo de seguridad antiextraccion compacto',
                'description' => 'Protector compacto de acero endurecido para incrementar la resistencia del bombin frente a ataques violentos.',
                'price' => 96.75,
                'stock' => 14,
                'code' => 'ESC-SEG-002',
                'discount' => 8.00,
                'category_id' => $categories['Escudos protectores'],
                'is_installable' => true,
                'is_important_to_show' => true,
                'installation_price' => 38.00,
                'extra_keys' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Kit de mantenimiento para bombines y cerraduras',
                'description' => 'Pack de lubricacion tecnica y utiles basicos para prolongar la vida util de bombines, escudos y cerraduras.',
                'price' => 24.90,
                'stock' => 30,
                'code' => 'ACC-CER-001',
                'discount' => 5.00,
                'category_id' => $categories['Accesorios de cerrajeria'],
                'is_installable' => false,
                'is_important_to_show' => true,
                'installation_price' => null,
                'extra_keys' => null,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['code' => $product['code']],
                $product
            );
        }
    }
}
