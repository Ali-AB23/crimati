<?php

namespace App\Enums;

enum OrgUnitType: string
{
    case DIRECTEUR = 'DIRECTEUR';
    case SECRETAIRE_GENERAL = 'SECRETAIRE_GENERAL';
    case POLE = 'POLE';
    case DIVISION = 'DIVISION';
    case SERVICE = 'SERVICE';
    case CELLULE = 'CELLULE';
}