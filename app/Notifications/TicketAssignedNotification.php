<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;

class TicketAssignedNotification extends Notification
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
            'title'        => 'Nouveau ticket assigné',
            'message'      => 'Le ticket ' . $this->ticket->reference . ' vous a été confié.',
            'reference'    => $this->ticket->reference,
            'status_badge' => 'NEW',
            'icon'         => 'blue_chat',
            'url'          => route('notifications.readAndRedirect',[
                'notification' => $this->id, 
                'ticket' => $this->ticket->id
            ])
        ];
    }
}