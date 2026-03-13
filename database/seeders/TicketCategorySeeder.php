<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketCategory;

class TicketCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories =[
            'Problème Matériel (Panne physique)',
            'Problème Logiciel (OS, Logiciels métiers)',
            'Problème Réseau / Internet',
            'Demande de nouveau matériel',
            'Mobilier ou Aménagement',
        ];

        foreach ($categories as $category) {
            TicketCategory::create(['name' => $category]);
        }
    }
}