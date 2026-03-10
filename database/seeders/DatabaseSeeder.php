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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            OrgUnitSeeder::class,               // indépendant
            LocationSeeder::class,               // indépendant
            AssetCategoryAndTypeSeeder::class,   // indépendant
            // Plus tard, tu ajouteras ici les seeders dépendants :
            // EmployeeSeeder::class,
            // AssetSeeder::class,
            // TicketSeeder::class,
        ]);
        
    }
}
