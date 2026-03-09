<?php

namespace App\Enums;

enum AssetStatus: string
{
    case EN_STOCK = 'en_stock';
    case EN_SERVICE = 'en_service';
    case EN_PANNE = 'en_panne';
    case EN_REPARATION = 'en_reparation';
    case REFORME = 'reforme';
}