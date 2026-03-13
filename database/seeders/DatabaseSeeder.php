<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            OrgUnitSeeder::class,               // 1. L'organigramme
            LocationSeeder::class,              // 2. Les lieux
            AssetCategoryAndTypeSeeder::class,  // 3. Le catalogue matériel
            UserSeeder::class,                  // 4. Les acteurs (Users/Employees)
            TicketCategorySeeder::class,        // 5. Les catégories de réclamations
            AssetSeeder::class,                 // 6. Le matériel physique
            TicketSeeder::class,                // 7. Les réclamations
        ]);
    }
}