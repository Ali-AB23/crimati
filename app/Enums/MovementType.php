<?php

namespace App\Enums;

enum MovementType: string
{
    case AFFECTATION = 'AFFECTATION';
    case TRANSFERT = 'TRANSFERT';
    case RETOUR = 'RETOUR';
    case DEPLACEMENT = 'DEPLACEMENT';
}