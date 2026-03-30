<?php

namespace Database\Seeders;

use App\Models\FeatureType;
use Illuminate\Database\Seeder;

class FeatureTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $featureTypes = [
            [
                'name' => 'Marca',
            ],
            [
                'name' => 'Mida Interior',
            ],
            [
                'name' => 'Mida Exterior',
            ],
            [
                'name' => 'Doble Embragatge',
            ],
            [
                'name' => 'Color',
            ],
            [
                'name' => 'Tipus de Clau',
            ],
            [
                'name' => 'Targeta',
            ],
            [
                'name' => 'Nivell de seguretat',
            ],
        ];

        foreach ($featureTypes as $featureType) {
            FeatureType::updateOrCreate(
                ['name' => $featureType['name']],
                $featureType
            );
        }
    }
}
