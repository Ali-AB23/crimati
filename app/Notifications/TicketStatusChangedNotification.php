<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;

class TicketStatusChangedNotification extends Notification
{
    use Queueable;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return[
            'title'        => 'Statut du ticket mis à jour',
            'message'      => 'Votre ticket est passé en statut : ' . str_replace('_', ' ', $this->ticket->status->value),
            'reference'    => $this->ticket->reference,
            'status_badge' => strtoupper($this->ticket->status->value),
            'icon'         => 'blue_chat',
            'url'          => route('notifications.readAndRedirect',[
                'notification' => $this->id, 
                'ticket' => $this->ticket->id
            ])
        ];
    }
}