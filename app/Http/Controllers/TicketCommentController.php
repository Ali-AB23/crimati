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
        
        $userToNotify = null;

        if (Auth::id() === $ticket->requester->user_id) {
            // C'est le demandeur qui écrit, on notifie l'assigné
            if ($ticket->assignedTo) {
                $userToNotify = $ticket->assignedTo;
            }
        } else {
            // C'est un admin/tech qui écrit, on notifie le demandeur
            $userToNotify = $ticket->requester->user; // Attention : Assure-toi que la relation "user" existe dans le modèle Employee
        }

        // On envoie la notification !
        if ($userToNotify) {
            $userToNotify->notify(new \App\Notifications\TicketCommentNotification($ticket, $comment));
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Votre commentaire a été ajouté.');
    }
}