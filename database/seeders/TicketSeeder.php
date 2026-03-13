<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\Asset;
use App\Models\TicketCategory;
use App\Models\Employee;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $pcAli = Asset::where('inventory_code', 'PC-2026-0001')->first();
        $catPanne = TicketCategory::where('name', 'Problème Matériel (Panne physique)')->first();
        $empAli = Employee::where('matricule', 'EMP-0001')->first();

        // Ali déclare une panne
        Ticket::create([
            'reference'             => 'TCK-2026-0001',
            
            'asset_id'              => $pcAli->id,
            'ticket_category_id'    => $catPanne->id,
            'requester_employee_id' => $empAli->id,
            'assigned_to_user_id'   => null, // Pas encore assigné
            'priority'              => TicketPriority::HIGH->value,
            'status'                => TicketStatus::OUVERT->value,
            'description'           => 'Mon PC chauffe énormément et s\'éteint tout seul au bout de 10 minutes.',
            'due_at'                => now()->addHours(48), // Deadline de 48h (High)
        ]);
    }
}