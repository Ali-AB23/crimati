<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\OrgUnit;
use App\Models\Location;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. On crée le compte de connexion (User)
        $adminUser = User::create([
            'username' => 'admin_cri', // Le login que tu vas taper !
            'password' => Hash::make('password123'), // Le mot de passe
            'role'     => UserRole::ADMIN_IT->value,
            'active'   => true,
        ]);

        // 2. On récupère le Service IT et un Bureau (créés par les autres seeders)
        $serviceIt = OrgUnit::where('name', 'Service Support et Matériel')->first();
        $bureauAdmin = Location::where('name', 'Bureau A101 (Support)')->first();

        // 3. On lui crée son profil d'employé (Obligatoire)
        Employee::create([
            'user_id'            => $adminUser->id,
            'matricule'          => 'IT-0001',
            'full_name'          => 'Administrateur Système',
            'email'              => 'admin.it@cri-guelmim.ma',
            'phone'              => '0600000000',
            'org_unit_id'        => $serviceIt->id ?? 1, // Fallback sécurité
            'office_location_id' => $bureauAdmin->id ?? null,
        ]);
    }
}