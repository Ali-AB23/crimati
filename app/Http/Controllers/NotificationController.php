<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Affiche la page "Notification inbox" avec filtres et pagination.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // On récupère la requête de base pour les notifications de l'utilisateur
        $query = $user->notifications();

        // FILTRE : Recherche textuelle (dans la colonne JSON 'data')
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            // On cherche dans le JSON (Attention, syntaxe spécifique selon MySQL/MariaDB)
            // Pour faire simple et compatible, on peut faire un LIKE sur toute la colonne data castée en texte
            $query->whereRaw("LOWER(CAST(data AS CHAR)) LIKE ?", ['%'.$search.'%']);
        }

        // FILTRE : Type (Onglets)
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'unread':
                    $query->whereNull('read_at');
                    break;
                case 'read':
                    $query->whereNotNull('read_at');
                    break;
                // Si tes types de notifs sont bien nommés (ex: App\Notifications\TicketOverdue)
                case 'tickets':
                    $query->where('type', 'like', '%Ticket%');
                    break;
                case 'assets':
                    $query->where('type', 'like', '%Asset%');
                    break;
                case 'imports':
                    $query->where('type', 'like', '%Import%');
                    break;
            }
        }

        // TRI : Ordre
        $sort = $request->get('sort', 'newest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // PAGINATION (10 par page selon ta maquette)
        $notifications = $query->paginate(10)->withQueryString();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Marque toutes les notifications comme lues.
     */
    public function markAllAsRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * Supprime toutes les notifications DÉJÀ LUES (Clear read).
     */
    public function clearRead(): RedirectResponse
    {
        Auth::user()->readNotifications()->delete();
        return back()->with('success', 'Les notifications lues ont été supprimées.');
    }
    /**
     * Marque une notification spécifique comme lue, puis redirige vers l'URL voulue.
     */
    public function readAndRedirect($notificationId, $ticketId): RedirectResponse
    {
        // 1. On cherche la notification dans celles de l'utilisateur
        $notification = Auth::user()->notifications()->find($notificationId);

        // 2. Si elle existe et n'est pas lue, on la marque comme lue
        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        // 3. On redirige vers le ticket concerné
        return redirect()->route('tickets.show', $ticketId);
    }
}