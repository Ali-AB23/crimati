<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

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
        $validated = $request->validate([
            'body' =>['required', 'string', 'min:2', 'max:2000'],
        ]);

        // 2. Création du commentaire lié au ticket
        // NB: Auth::id() renverra l'ID du User connecté. (On simule l'ID 1 pour l'instant)
        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::check() ? Auth::id() : 1, 
            'body'      => $validated['body'],
        ]);

        // 3. Optionnel métier : Si l'Admin répond, on peut passer le ticket en "En cours"
        // if (Auth::user()->role === UserRole::ADMIN_IT->value && $ticket->status === TicketStatus::OUVERT->value) {
        //     $ticket->update(['status' => TicketStatus::EN_COURS->value]);
        // }

        // 4. On redirige vers la page du ticket sur laquelle on était
        return redirect()->route('tickets.show', $ticket)
                         ->with('success', 'Votre commentaire a été ajouté.');
    }
}