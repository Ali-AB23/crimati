<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrgUnit;
use App\Enums\OrgUnitType;

class OrgUnitSeeder extends Seeder
{
    public function run(): void
    {
        // On crée la racine (Le Directeur)
        $direction = OrgUnit::create([
            'name' => 'Direction Générale CRI',
            'type' => OrgUnitType::DIRECTEUR->value,
            'parent_id' => null,
        ]);

        // On crée un Pôle rattaché au Directeur
        $poleTech = OrgUnit::create([
            'name' => 'Pôle Système d\'Information',
            'type' => OrgUnitType::POLE->value,
            'parent_id' => $direction->id,
        ]);

        // On crée des Services rattachés au Pôle
        OrgUnit::create([
            'name' => 'Service Support et Matériel',
            'type' => OrgUnitType::SERVICE->value,
            'parent_id' => $poleTech->id,
        ]);

        OrgUnit::create([
            'name' => 'Service Développement',
            'type' => OrgUnitType::SERVICE->value,
            'parent_id' => $poleTech->id,
        ]);
    }
}