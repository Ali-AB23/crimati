<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;

class NewTicketCreatedNotification extends Notification
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
        $demandeur = optional($this->ticket->requester)->full_name ?? 'Un employé';

        return[
            'title'        => 'Nouveau ticket déclaré',
            'message'      => $demandeur . ' a déclaré un problème sur le matériel : ' . optional($this->ticket->asset)->inventory_code,
            'reference'    => $this->ticket->reference,
            'status_badge' => 'NEW',
            'icon'         => 'blue_chat', // Utilisera l'icône bleue dans ta vue
            'url'          => route('notifications.readAndRedirect',[
                'notification' => $this->id, 
                'ticket'       => $this->ticket->id
            ])
        ];
    }
}