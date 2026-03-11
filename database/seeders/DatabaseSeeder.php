<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $this->call([
            OrgUnitSeeder::class,               // 1. L'organigramme
            LocationSeeder::class,              // 2. Les lieux
            AssetCategoryAndTypeSeeder::class,  // 3. Le catalogue matériel
            UserSeeder::class,                  // 4. Les utilisateurs (a besoin des lieux et org_units)
        ]);
        
    }
}
