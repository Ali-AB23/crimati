<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Enums\LocationType;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Le Bâtiment principal
        $building = Location::create([
            'name' => 'Bâtiment Principal CRI',
            'type' => LocationType::BUILDING->value,
        ]);

        // Un Étage
        $floor = Location::create([
            'name' => '1er Étage',
            'type' => LocationType::FLOOR->value,
            'parent_id' => $building->id,
        ]);

        // Des pièces spécifiques
        Location::create([
            'name' => 'Magasin Central IT (Stock)',
            'type' => LocationType::STORAGE->value,
            'parent_id' => $floor->id,
        ]);

        Location::create([
            'name' => 'Bureau A101 (Support)',
            'type' => LocationType::OFFICE->value,
            'parent_id' => $floor->id,
        ]);
    }
}