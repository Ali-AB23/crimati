<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Asset;
use App\Models\TicketCategory;
use App\Models\User;
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTicketRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Affiche la liste des tickets (Tableau de bord).
     */
    public function index(): View
    {
        // Eager Loading pour éviter le problème N+1
        $tickets = Ticket::with(['asset', 'category', 'requester', 'assignedTo'])
                         ->orderBy('created_at', 'desc')
                         ->paginate(15);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Affiche le formulaire de création d'un ticket.
     */
    public function create(): View
    {
        // On récupère le matériel et les catégories pour les listes déroulantes
        // TODO: Selon le CDC, l'employé ne doit voir QUE son matériel ou celui de son bureau.
        // On filtrera cette requête quand l'Auth sera en place !
        $assets = Asset::orderBy('inventory_code')->get();
        $categories = TicketCategory::orderBy('name')->get();

        return view('tickets.create', compact('assets', 'categories'));
    }

    /**
     * Sauvegarde un nouveau ticket.
     */
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // 1. Calcul automatique de la deadline
        $dueAt = match ($validated['priority']) {
            TicketPriority::URGENT->value => now()->addHours(24),
            TicketPriority::HIGH->value   => now()->addHours(48),
            TicketPriority::MEDIUM->value => now()->addHours(72),
            TicketPriority::LOW->value    => now()->addDays(5),
            default                       => now()->addDays(5),
        };

        // 2. Génération Sécurisée de la référence
        $ticket = DB::transaction(function () use ($validated, $dueAt) {
            $currentYear = now()->year;

            // Verrouillage de la ligne pour éviter les doublons (Race Condition)
            $lastTicket = Ticket::where('ref_year', $currentYear)
                                ->lockForUpdate()
                                ->orderBy('ref_seq', 'desc')
                                ->first();

            $nextSeq = $lastTicket ? $lastTicket->ref_seq + 1 : 1;
            $reference = sprintf('TCK-%d-%04d', $currentYear, $nextSeq);

            // TODO: Remplacer '1' par Auth::user()->employee->id quand l'auth sera codée
            $requesterId = Auth::check() && Auth::user()->employee ? Auth::user()->employee->id : 1;

            return Ticket::create([
                'asset_id'              => $validated['asset_id'],
                'ticket_category_id'    => $validated['ticket_category_id'],
                'requester_employee_id' => $requesterId, 
                'priority'              => $validated['priority'],
                'status'                => TicketStatus::OUVERT->value,
                'description'           => $validated['description'],
                'due_at'                => $dueAt,
                'ref_year'              => $currentYear,
                'ref_seq'               => $nextSeq,
                'reference'             => $reference,
            ]);
        });

        return redirect()->route('tickets.index')
                         ->with('success', 'Ticket créé avec la référence : ' . $ticket->reference);
    }

    /**
     * Affiche les détails d'un ticket spécifique et ses commentaires.
     */
    public function show(Ticket $ticket): View
    {
        // On charge les relations nécessaires pour la page de détails
        $ticket->load(['asset', 'requester', 'assignedTo', 'comments.author', 'category']);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Affiche le formulaire de modification (Principalement pour l'Admin IT).
     */
    public function edit(Ticket $ticket): View
    {
        // Seuls les Admins ou Techniciens peuvent être assignés à un ticket
        $technicians = User::whereIn('role', [UserRole::ADMIN_IT->value])->get();

        return view('tickets.edit', compact('ticket', 'technicians'));
    }

    /**
     * Met à jour le ticket (Changement de statut, Assignation).
     */
    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        // Validation simple inline (on pourrait faire un UpdateTicketRequest)
        $validated = $request->validate([
            'status'              => ['required', 'string'],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
            'due_at'              => ['nullable', 'date'], // L'Admin IT a le droit de modifier la due_at
        ]);

        // Si le statut passe à "Résolu" ou "Fermé", on horodate
        if (in_array($validated['status'],[TicketStatus::RESOLU->value, TicketStatus::FERME->value]) && !$ticket->resolved_at) {
            $validated['resolved_at'] = now();
        }

        if ($validated['status'] === TicketStatus::FERME->value && !$ticket->closed_at) {
            $validated['closed_at'] = now();
        }

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket)
                         ->with('success', 'Le ticket a été mis à jour.');
    }

    /**
     * Supprime un ticket (Annulation forte).
     */
    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()->route('tickets.index')
                         ->with('success', 'Le ticket a été supprimé.');
    }
}