<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN_IT = 'ADMIN_IT';
    case INVENTORISTE = 'INVENTORISTE';
    case EMPLOYE = 'EMPLOYE';
}