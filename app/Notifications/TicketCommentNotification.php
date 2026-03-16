<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;
use App\Models\TicketComment;

class TicketCommentNotification extends Notification
{
    use Queueable;

    public $ticket;
    public $comment;

    // On passe le ticket et le commentaire au moment de la création
    public function __construct(Ticket $ticket, TicketComment $comment)
    {
        $this->ticket = $ticket;
        $this->comment = $comment;
    }

    // On dit à Laravel d'enregistrer ça UNIQUEMENT dans la base de données
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // C'est ici qu'on formate le JSON exactement comme notre vue le demande !
    public function toDatabase(object $notifiable): array
    {
        return[
            'title'        => 'Nouveau commentaire',
            'message'      => 'Mise à jour sur le ticket par ' . ($this->comment->author->employee->full_name ?? $this->comment->author->username),
            'reference'    => $this->ticket->reference,
            'status_badge' => 'INFO',
            'icon'         => 'blue_chat',
            // On crée une route spéciale pour marquer la notif comme lue avant de rediriger vers le ticket
            'url'          => route('notifications.readAndRedirect',[
                'notification' => $this->id, 
                'ticket' => $this->ticket->id
            ])
        ];
    }
}