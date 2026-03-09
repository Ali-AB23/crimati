<?php

namespace App\Enums;

enum LocationType: string
{
    case BUILDING = 'BUILDING';
    case FLOOR = 'FLOOR';
    case ZONE = 'ZONE';
    case OFFICE = 'OFFICE';
    case ROOM = 'ROOM';
    case MEETING_ROOM = 'MEETING_ROOM';
    case TERRACE = 'TERRACE';
    case STORAGE = 'STORAGE';
}