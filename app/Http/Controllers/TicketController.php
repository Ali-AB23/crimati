<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Asset;
use App\Models\TicketCategory;
use App\Models\User;
use App\Enums\LocationType;
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
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // 1. Initialisation de la requête (Eager Loading massif)
        $query = Ticket::with(['asset', 'requester', 'assignedTo.employee']);

        // 2. RÈGLES DE VISIBILITÉ (L'employé ne voit que SES tickets)
        if ($user->role->value === UserRole::EMPLOYE->value) {
            $employeeId = $user->employee ? $user->employee->id : 0;
            $query->where('requester_employee_id', $employeeId);
        }

        // 3. GESTION DES FILTRES (Moteur de recherche)
        if ($request->filled('reference')) {
            $query->where('reference', 'like', '%' . $request->reference . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filtre "Late" (En retard)
        if ($request->filled('late')) {
            if ($request->late === 'yes') {
                $query->where('due_at', '<', now())
                      ->whereNotIn('status',[
                          TicketStatus::RESOLU->value, 
                          TicketStatus::FERME->value, 
                          TicketStatus::ANNULE->value
                      ]);
            } elseif ($request->late === 'no') {
                $query->where(function($q) {
                    $q->where('due_at', '>=', now())
                      ->orWhereIn('status',[
                          TicketStatus::RESOLU->value, 
                          TicketStatus::FERME->value, 
                          TicketStatus::ANNULE->value
                      ]);
                });
            }
        }

        if ($request->filled('assigned_to_user_id')) {
            $query->where('assigned_to_user_id', $request->assigned_to_user_id);
        }

        // 4. Exécution avec pagination
        $tickets = $query->orderBy('created_at', 'desc')->paginate(7);

        // 5. Données pour les listes déroulantes (Selects)
        $statuses = TicketStatus::cases();
        $priorities = TicketPriority::cases();
        $technicians = User::with('employee')->whereIn('role',[UserRole::ADMIN_IT->value])->get();

        return view('tickets.index', compact('tickets', 'statuses', 'priorities', 'technicians'));
    }

    /**
     * Affiche le formulaire de création d'un ticket.
     */
    public function create(): View
    {
        $user = Auth::user();
        $assetsQuery = Asset::query();

        // APPLICATION DU CDC : L'employé ne peut sélectionner QUE le matériel autorisé
        if ($user->role->value === UserRole::EMPLOYE->value && $user->employee) {
            $employee = $user->employee;
            
            $assetsQuery->where(function ($q) use ($employee) {
                // RÈGLE 1 : Affecté directement à l'employé
                $q->where('current_employee_id', $employee->id)
                  // RÈGLES 2 & 3 : Non affecté à quelqu'un d'autre ET dans un bon lieu
                  ->orWhere(function ($qUnassigned) use ($employee) {
                      $qUnassigned->whereNull('current_employee_id')
                                  ->whereHas('currentLocation', function ($qLoc) use ($employee) {
                                      $qLoc->where(function ($qConditions) use ($employee) {
                                          // 2: Bureau perso
                                          $qConditions->where('id', $employee->office_location_id)
                                                      // 3a: Service
                                                      ->orWhere('org_unit_id', $employee->org_unit_id)
                                                      // 3b: Espace public (Pas un bureau ni stock)
                                                      ->orWhere(function ($qPublic) {
                                                          $qPublic->whereNull('org_unit_id')
                                                                  ->whereNotIn('type',[
                                                                      LocationType::OFFICE->value,
                                                                      LocationType::STORAGE->value
                                                                  ]);
                                                      });
                                      });
                                  });
                  });
            });
        }

        $assets = $assetsQuery->with(['currentLocation', 'currentEmployee'])->orderBy('inventory_code')->get();
        $categories = TicketCategory::orderBy('name')->get();

        return view('tickets.create', compact('assets', 'categories'));
    }
    /**
     * Sauvegarde un nouveau ticket.
     */
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = Auth::user();

        // =========================================================
        // 🛑 SÉCURITÉ BACKEND : Empêcher le piratage du formulaire (F12)
        // =========================================================
        if ($user->role->value === UserRole::EMPLOYE->value && $user->employee) {
            $employee = $user->employee;
            $assetId = $validated['asset_id'];

            // On revérifie côté Serveur si l'Asset soumis fait bien partie du périmètre autorisé
            $isAuthorized = Asset::where('id', $assetId)
                ->where(function ($q) use ($employee) {
                    $q->where('current_employee_id', $employee->id)
                      ->orWhere(function ($qUnassigned) use ($employee) {
                          $qUnassigned->whereNull('current_employee_id')
                                      ->whereHas('currentLocation', function ($qLoc) use ($employee) {
                                          $qLoc->where(function ($qConditions) use ($employee) {
                                              $qConditions->where('id', $employee->office_location_id)
                                                          ->orWhere('org_unit_id', $employee->org_unit_id)
                                                          ->orWhere(function ($qPublic) {
                                                              $qPublic->whereNull('org_unit_id')
                                                                      ->whereNotIn('type',[
                                                                          LocationType::OFFICE->value,
                                                                          LocationType::STORAGE->value
                                                                      ]);
                                                          });
                                          });
                                      });
                      });
                })->exists();

            if (!$isAuthorized) {
                return back()->withErrors(['asset_id' => 'Alerte Sécurité : Vous n\'êtes pas autorisé à créer une réclamation pour ce matériel.'])->withInput();
            }
        }

        // =========================================================
        // SUITE NORMALE DE LA CRÉATION
        // =========================================================

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
        $ticket->load(['asset', 'requester', 'assignedTo', 'comments.author.employee', 'category']);

        $technicians = User::with('employee')->whereIn('role',[\App\Enums\UserRole::ADMIN_IT->value])->get();

        // LOGIQUE MÉTIER : Les statuts SUIVANTS autorisés selon le statut actuel
        $allowedStatuses = match ($ticket->status->value) {
            \App\Enums\TicketStatus::OUVERT->value   => [\App\Enums\TicketStatus::EN_COURS->value], // L'assignation se fait via le bouton Assign
            \App\Enums\TicketStatus::ASSIGNE->value  =>[\App\Enums\TicketStatus::EN_COURS->value],
            \App\Enums\TicketStatus::EN_COURS->value => [\App\Enums\TicketStatus::RESOLU->value],
            \App\Enums\TicketStatus::RESOLU->value   =>[\App\Enums\TicketStatus::FERME->value],
            default =>[], // Si Fermé ou Annulé, on ne peut plus rien changer
        };

        return view('tickets.show', compact('ticket', 'technicians', 'allowedStatuses'));
    }

    /**
     * Affiche le formulaire de modification (Principalement pour l'Admin IT).
     */
    public function edit(Ticket $ticket): View
    {
        $technicians = User::whereIn('role', [UserRole::ADMIN_IT->value])->get();

        return view('tickets.edit', compact('ticket', 'technicians'));
    }

    /**
     * Met à jour le ticket (Changement de statut, Assignation).
     */
    /**
     * Met à jour le ticket (Changement de statut, Assignation, Date).
     */
    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        // 🛑 TA SÉCURITÉ CONSERVÉE : Interdire la modification sur les tickets clos/annulés
        if (in_array($ticket->status->value,[\App\Enums\TicketStatus::FERME->value, \App\Enums\TicketStatus::ANNULE->value])) {
            return back()->withErrors(['update_error' => 'Action impossible : ce ticket est définitivement clôturé ou annulé.']);
        }
        
        $validated = $request->validate([
            'status'              => ['required', 'string'],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
            'due_at'              =>['nullable', 'date'],
        ]);

        // 1. MÉMORISER L'ÉTAT AVANT MODIFICATION (Pour les notifications)
        $oldStatus = $ticket->status->value;
        $oldAssignedTo = $ticket->assigned_to_user_id;

        // ⚙️ TA LOGIQUE MÉTIER CONSERVÉE : Passage auto à "Assigné"
        if ($request->has('assigned_to_user_id') && $validated['assigned_to_user_id']) {
            if ($ticket->status->value === \App\Enums\TicketStatus::OUVERT->value) {
                $validated['status'] = \App\Enums\TicketStatus::ASSIGNE->value;
            }
        }

        // ⚙️ TON HORODATAGE CONSERVÉ
        if (in_array($validated['status'],[\App\Enums\TicketStatus::RESOLU->value, \App\Enums\TicketStatus::FERME->value]) && !$ticket->resolved_at) {
            $validated['resolved_at'] = now();
        }

        if ($validated['status'] === \App\Enums\TicketStatus::FERME->value && !$ticket->closed_at) {
            $validated['closed_at'] = now();
        }

        // 2. APPLICATION DE LA MISE À JOUR
        $ticket->update($validated);

        // =========================================================
        // 🚀 DÉCLENCHEMENT DES NOTIFICATIONS (Le nouvel ajout)
        // =========================================================
        
        // A. Le statut a-t-il changé ? -> On prévient l'employé qui a créé le ticket
        if ($oldStatus !== $validated['status']) {
            if ($ticket->requester && $ticket->requester->user) {
                $ticket->requester->user->notify(new \App\Notifications\TicketStatusChangedNotification($ticket));
            }
        }

        // B. L'assignation a-t-elle changé ? -> On prévient le technicien concerné
        if ($oldAssignedTo !== $ticket->assigned_to_user_id && $ticket->assignedTo) {
            $ticket->assignedTo->notify(new \App\Notifications\TicketAssignedNotification($ticket));
        }

        return redirect()->route('tickets.show', $ticket)
                         ->with('success', 'Le ticket a été mis à jour avec succès.');
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