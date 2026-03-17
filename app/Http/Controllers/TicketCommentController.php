<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Notifications\TicketCommentNotification;

class TicketCommentController extends Controller
{
    /**
     * Ajoute un commentaire à un ticket spécifique.
     * 
     * @param Request $request
     * @param Ticket $ticket (Injecté via l'URL : /tickets/{ticket}/comments)
     */
    public function store(Request $request, Ticket $ticket): RedirectResponse
    {
        if (in_array($ticket->status->value,[\App\Enums\TicketStatus::FERME->value, \App\Enums\TicketStatus::ANNULE->value])) {
            return back()->withErrors(['comment_error' => 'Impossible d\'ajouter un commentaire : ce ticket est clôturé ou annulé.']);
        }

        $validated = $request->validate([
            'body' =>['required', 'string', 'min:2', 'max:2000'],
        ]);

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::id(), 
            'body'      => $validated['body'],
        ]);

        // =========================================================
        // 🚀 LA MAGIE DES NOTIFICATIONS
        // =========================================================
        // Qui doit recevoir la notification ?
        // Si c'est l'employé qui écrit, on notifie le technicien (s'il y en a un).
        // Si c'est le technicien qui écrit, on notifie l'employé.
        
        $currentUserId = \Illuminate\Support\Facades\Auth::id();
        $notification = new \App\Notifications\TicketCommentNotification($ticket, $comment);

        // A. Est-ce que l'auteur du commentaire est le demandeur (l'Employé) ?
        if ($ticket->requester && $currentUserId === $ticket->requester->user_id) {
            
            if ($ticket->assigned_to_user_id) {
                // Cas 1 : Le ticket est assigné -> On notifie spécifiquement le technicien en charge
                if ($ticket->assignedTo) {
                    $ticket->assignedTo->notify($notification);
                }
            } else {
                // Cas 2 : Le ticket n'est pas encore assigné -> On notifie TOUS les Admins IT !
                $admins = \App\Models\User::where('role', \App\Enums\UserRole::ADMIN_IT->value)->get();
                \Illuminate\Support\Facades\Notification::send($admins, $notification);
            }

        } 
        // B. L'auteur du commentaire est un Admin/Technicien
        else {
            // On notifie le demandeur (l'Employé)
            if ($ticket->requester && $ticket->requester->user) {
                $ticket->requester->user->notify($notification);
            }
        }

        return redirect()->route('tickets.show', $ticket)
                         ->with('success', 'Votre commentaire a été ajouté.');
    }
}