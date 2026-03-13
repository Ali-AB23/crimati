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
        // Récupération des Unités et Lieux créés par les autres Seeders
        $serviceIt = OrgUnit::where('name', 'Service Support et Matériel')->first();
        $serviceDev = OrgUnit::where('name', 'Service Développement')->first();
        
        $bureauAdmin = Location::where('name', 'Bureau A101 (Support)')->first();
        $magasinStock = Location::where('name', 'Magasin Central IT (Stock)')->first();

        // ==========================================
        // 1. COMPTE ADMIN IT (Super Admin)
        // ==========================================
        $adminUser = User::create([
            'username' => 'admin_cri',
            'password' => Hash::make('password123'),
            'role'     => UserRole::ADMIN_IT->value,
            'active'   => true,
        ]);

        Employee::create([
            'user_id'            => $adminUser->id,
            'matricule'          => 'IT-0001',
            'full_name'          => 'Administrateur Système',
            'email'              => 'admin.it@cri-guelmim.ma',
            'phone'              => '0600000001',
            'org_unit_id'        => $serviceIt->id ?? 1,
            'office_location_id' => $bureauAdmin->id ?? null,
        ]);


        // ==========================================
        // 2. COMPTE INVENTORISTE (Gestionnaire Parc)
        // ==========================================
        $invUser = User::create([
            'username' => 'inv_cri', // Login : inv_cri
            'password' => Hash::make('password123'),
            'role'     => UserRole::INVENTORISTE->value,
            'active'   => true,
        ]);

        Employee::create([
            'user_id'            => $invUser->id,
            'matricule'          => 'INV-0001',
            'full_name'          => 'Gestionnaire de Parc',
            'email'              => 'inventaire@cri-guelmim.ma',
            'phone'              => '0600000002',
            'org_unit_id'        => $serviceIt->id ?? 1, // Il fait partie du support
            'office_location_id' => $magasinStock->id ?? null, // Il travaille au stock
        ]);


        // ==========================================
        // 3. COMPTE EMPLOYE (Utilisateur Final)
        // ==========================================
        $empUser = User::create([
            'username' => 'emp_cri', // Login : emp_cri
            'password' => Hash::make('password123'),
            'role'     => UserRole::EMPLOYE->value,
            'active'   => true,
        ]);

        Employee::create([
            'user_id'            => $empUser->id,
            'matricule'          => 'EMP-0001',
            'full_name'          => 'Ali Développeur', // Un clin d'œil à toi !
            'email'              => 'ali.dev@cri-guelmim.ma',
            'phone'              => '0600000003',
            'org_unit_id'        => $serviceDev->id ?? 1, // Il est dans le service Dev
            'office_location_id' => null, // Pas de bureau physique spécifique renseigné
        ]);
    }
}