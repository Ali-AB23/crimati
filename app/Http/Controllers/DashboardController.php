<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Ticket;
use App\Enums\AssetStatus;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

public function index()
{
    $user = Auth::user();
        $employeeId = $user->employee ? $user->employee->id : null;


        // Variables pour stocker nos statistiques
        $stats =[
            'total_assets' => 0,
            'broken_assets' => 0,
            'active_tickets' => 0,
            'late_tickets' => 0,
        ];

         // 1. LOGIQUE POUR ADMIN & INVENTORISTE (Vue Globale)
        if (in_array($user->role->value, [UserRole::ADMIN_IT->value, UserRole::INVENTORISTE->value])) {
            
            $stats['total_assets'] = Asset::count();
            
            $stats['broken_assets'] = Asset::whereIn('status',[
                AssetStatus::EN_PANNE->value, 
                AssetStatus::EN_REPARATION->value
            ])->count();

            $stats['active_tickets'] = Ticket::whereIn('status',[
                TicketStatus::OUVERT->value, 
                TicketStatus::ASSIGNE->value, 
                TicketStatus::EN_COURS->value
            ])->count();

            // Tickets non fermés et dont la date limite est dépassée
            $stats['late_tickets'] = Ticket::whereNotIn('status',[
                TicketStatus::RESOLU->value, 
                TicketStatus::FERME->value, 
                TicketStatus::ANNULE->value
            ])->where('due_at', '<', now())->count();

        } 
        // 2. LOGIQUE POUR L'EMPLOYÉ (Vue Restreinte à ses données)
        else {
            if ($employeeId) {
                // Matériel qui lui est actuellement affecté
                $stats['total_assets'] = Asset::where('current_employee_id', $employeeId)->count();
                
                $stats['broken_assets'] = Asset::where('current_employee_id', $employeeId)
                    ->whereIn('status',[AssetStatus::EN_PANNE->value, AssetStatus::EN_REPARATION->value])
                    ->count();

                // Ses tickets à lui
                $stats['active_tickets'] = Ticket::where('requester_employee_id', $employeeId)
                    ->whereIn('status',[TicketStatus::OUVERT->value, TicketStatus::ASSIGNE->value, TicketStatus::EN_COURS->value])
                    ->count();

                // L'employé n'a pas forcément besoin de voir la notion de "retard" pour ses propres tickets, 
                // mais on peut lui afficher ceux qui traînent.
                $stats['late_tickets'] = Ticket::where('requester_employee_id', $employeeId)
                    ->whereNotIn('status',[TicketStatus::RESOLU->value, TicketStatus::FERME->value, TicketStatus::ANNULE->value])
                    ->where('due_at', '<', now())->count();
            }
        }

        // On renvoie la vue en lui passant le tableau $stats
        return view('dashboard.dashboard', compact('stats'));
}

}