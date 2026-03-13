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
        if (in_array($user->role->value,[UserRole::ADMIN_IT->value, UserRole::INVENTORISTE->value])) {
            
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

            $stats['late_tickets'] = Ticket::whereNotIn('status',[
                TicketStatus::RESOLU->value, 
                TicketStatus::FERME->value, 
                TicketStatus::ANNULE->value
            ])->where('due_at', '<', now())->count();

            // --- NOUVEAU : Récupération des données pour les tableaux (Global) ---
            $recentTickets = Ticket::with(['asset', 'assignedTo.employee'])
                                   ->orderBy('created_at', 'desc')
                                   ->limit(5)->get();

            $attentionAssets = Asset::with(['type.category', 'currentLocation', 'currentEmployee'])
                                    ->whereIn('status',[AssetStatus::EN_PANNE->value, AssetStatus::EN_REPARATION->value])
                                    ->limit(5)->get();

        } 
        // 2. LOGIQUE POUR L'EMPLOYÉ (Vue Restreinte à ses données)
        else {
            if ($employeeId) {
                $stats['total_assets'] = Asset::where('current_employee_id', $employeeId)->count();
                
                $stats['broken_assets'] = Asset::where('current_employee_id', $employeeId)
                    ->whereIn('status',[AssetStatus::EN_PANNE->value, AssetStatus::EN_REPARATION->value])
                    ->count();

                $stats['active_tickets'] = Ticket::where('requester_employee_id', $employeeId)
                    ->whereIn('status',[TicketStatus::OUVERT->value, TicketStatus::ASSIGNE->value, TicketStatus::EN_COURS->value])
                    ->count();

                $stats['late_tickets'] = Ticket::where('requester_employee_id', $employeeId)
                    ->whereNotIn('status',[TicketStatus::RESOLU->value, TicketStatus::FERME->value, TicketStatus::ANNULE->value])
                    ->where('due_at', '<', now())->count();

                // --- NOUVEAU : Récupération des données pour les tableaux (Restreint à l'employé) ---
                $recentTickets = Ticket::with(['asset', 'assignedTo.employee'])
                                       ->where('requester_employee_id', $employeeId)
                                       ->orderBy('created_at', 'desc')
                                       ->limit(5)->get();

                $attentionAssets = Asset::with(['type.category', 'currentLocation', 'currentEmployee'])
                                        ->where('current_employee_id', $employeeId)
                                        ->whereIn('status',[AssetStatus::EN_PANNE->value, AssetStatus::EN_REPARATION->value])
                                        ->limit(5)->get();
            } else {
                // Sécurité si l'employé n'a pas d'ID
                $recentTickets = collect();
                $attentionAssets = collect();
            }
        }

        // On renvoie la vue EXACTE avec les 3 variables !
        return view('dashboard.dashboard', compact('stats', 'recentTickets', 'attentionAssets'));
    }
}