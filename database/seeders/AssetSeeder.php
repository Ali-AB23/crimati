<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Location;
use App\Models\Employee;
use App\Enums\AssetStatus;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        // On récupère les types et lieux créés précédemment
        $typePc = AssetType::where('name', 'Ordinateur Portable')->first();
        $typeChaise = AssetType::where('name', 'Chaise Ergonomique')->first();
        
        $bureauAdmin = Location::where('name', 'Bureau A101 (Support)')->first();
        $magasinStock = Location::where('name', 'Magasin Central IT (Stock)')->first();
        
        // On récupère notre employé de test (Ali)
        $empAli = Employee::where('matricule', 'EMP-0001')->first();

        // 1. Un PC affecté à Ali (En service)
        Asset::create([
            'inventory_code'      => 'PC-2026-0001',
            'asset_type_id'       => $typePc->id,
            'brand'               => 'Dell',
            'model'               => 'XPS 15',
            'serial_number'       => 'SN-DELL-998877',
            'status'              => AssetStatus::EN_SERVICE->value,
            'current_location_id' => $bureauAdmin->id, // Supposons qu'il est dans ce bureau
            'current_employee_id' => $empAli->id, // Affecté à Ali !
            'specs'               =>[
                'ram_gb'       => 16,
                'cpu'          => 'Intel Core i7',
                'storage_gb'   => 512,
                'storage_type' => 'SSD',
            ],
            'notes'               => 'PC de développement.',
        ]);

        // 2. Une chaise en stock (Non affectée)
        Asset::create([
            'inventory_code'      => 'MOB-2026-0001',
            'asset_type_id'       => $typeChaise->id,
            'brand'               => 'Herman Miller',
            'model'               => 'Aeron',
            'serial_number'       => null,
            'status'              => AssetStatus::EN_STOCK->value,
            'current_location_id' => $magasinStock->id, // Au stock
            'current_employee_id' => null, // Affecté à personne
            'specs'               => null, // Pas de specs pour une chaise
            'notes'               => 'Chaise neuve dans son carton.',
        ]);
    }
}