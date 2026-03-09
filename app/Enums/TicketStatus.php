<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OUVERT = 'ouvert';
    case ASSIGNE = 'assigne';
    case EN_COURS = 'en_cours';
    case RESOLU = 'resolu';
    case FERME = 'ferme';
    case ANNULE = 'annule';
}