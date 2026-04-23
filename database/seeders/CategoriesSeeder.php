<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Cilindres',
                'is_important_to_show' => true,
                'image' => null,
            ],
            [
                'name' => 'Escuts',
                'is_important_to_show' => true,
                'image' => null,
            ],
            [
                'name' => 'Segon Pany',
                'is_important_to_show' => true,
                'image' =>  null,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
