<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\FeatureType;
use Illuminate\Database\Seeder;

class FeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener los tipos de características
        $marcaType = FeatureType::where('name', 'Marca')->first();
        $midaInteriorType = FeatureType::where('name', 'Mida Interior')->first();
        $midaExteriorType = FeatureType::where('name', 'Mida Exterior')->first();
        $dobleEmbragatgeType = FeatureType::where('name', 'Doble Embragatge')->first();
        $colorType = FeatureType::where('name', 'Color')->first();
        $tipusClauType = FeatureType::where('name', 'Tipus de Clau')->first();
        $targetaType = FeatureType::where('name', 'Targeta')->first();
        $nivellSeguretatType = FeatureType::where('name', 'Nivell de seguretat')->first();

        $features = [
            // Marcas
            ['type_id' => $marcaType->id, 'value' => 'Securemme'],
            ['type_id' => $marcaType->id, 'value' => 'M&C'],
            ['type_id' => $marcaType->id, 'value' => 'Keso'],
            ['type_id' => $marcaType->id, 'value' => 'Abus'],
            ['type_id' => $marcaType->id, 'value' => 'DMC'],
            ['type_id' => $marcaType->id, 'value' => 'Disec'],

            // Mida Interior
            ['type_id' => $midaInteriorType->id, 'value' => '30 mm'],
            ['type_id' => $midaInteriorType->id, 'value' => '32 mm'],
            ['type_id' => $midaInteriorType->id, 'value' => '40 mm'],

            // Mida Exterior
            ['type_id' => $midaExteriorType->id, 'value' => '30 mm'],
            ['type_id' => $midaExteriorType->id, 'value' => '32 mm'],
            ['type_id' => $midaExteriorType->id, 'value' => '40 mm'],

            // Doble Embragatge
            ['type_id' => $dobleEmbragatgeType->id, 'value' => 'Sí'],
            ['type_id' => $dobleEmbragatgeType->id, 'value' => 'No'],

            // Color
            ['type_id' => $colorType->id, 'value' => 'Plata'],
            ['type_id' => $colorType->id, 'value' => 'Daurat'],

            // Tipus de Clau
            ['type_id' => $tipusClauType->id, 'value' => 'Punts copiables'],
            ['type_id' => $tipusClauType->id, 'value' => 'Punts incopiables'],
            ['type_id' => $tipusClauType->id, 'value' => 'Element mòbil'],
            ['type_id' => $tipusClauType->id, 'value' => 'Codificació Magnètica'],

            // Targeta
            ['type_id' => $targetaType->id, 'value' => 'Sí'],

            // Nivell de seguretat
            ['type_id' => $nivellSeguretatType->id, 'value' => 'Seguretat'],
            ['type_id' => $nivellSeguretatType->id, 'value' => 'Alta seguretat'],
            ['type_id' => $nivellSeguretatType->id, 'value' => 'Molt alta seguretat'],
        ];

        foreach ($features as $feature) {
            Feature::updateOrCreate(
                ['type_id' => $feature['type_id'], 'value' => $feature['value']],
                $feature
            );
        }
    }
}
