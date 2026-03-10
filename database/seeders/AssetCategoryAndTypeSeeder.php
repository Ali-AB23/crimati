<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetCategory;
use App\Models\AssetType;

class AssetCategoryAndTypeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Catégorie Informatique
        $catIT = AssetCategory::create(['name' => 'Informatique']);

        // Type Ordinateur Portable (avec des specs complexes)
        AssetType::create([
            'asset_category_id' => $catIT->id,
            'name' => 'Ordinateur Portable',
            'spec_schema' => [
                'ram_gb' =>['type' => 'number', 'min' => 4, 'filterable' => true],
                'cpu' =>['type' => 'text', 'filterable' => true],
                'storage_gb' => ['type' => 'number', 'filterable' => true],
                'storage_type' => ['type' => 'select', 'values' =>['SSD', 'HDD'], 'filterable' => true]
            ]
        ]);

        // Type Écran (specs plus simples)
        AssetType::create([
            'asset_category_id' => $catIT->id,
            'name' => 'Écran PC',
            'spec_schema' =>[
                'screen_size_inch' => ['type' => 'number', 'filterable' => true],
                'resolution' =>['type' => 'text', 'filterable' => false]
            ]
        ]);

        // 2. Catégorie Mobilier (Pas besoin de specs techniques poussées)
        $catMobilier = AssetCategory::create(['name' => 'Mobilier de Bureau']);

        AssetType::create([
            'asset_category_id' => $catMobilier->id,
            'name' => 'Chaise Ergonomique',
            'spec_schema' => null // Un mobilier simple n'a pas forcément de specs
        ]);
    }
}