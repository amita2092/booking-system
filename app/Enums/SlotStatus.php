<?php

namespace App\Enums;

enum SlotStatus:string
{
    case AVAILABLE = 'available';

    case BOOKED = 'booked';

    case BLOCKED = 'blocked';

    case EXPIRED = 'expired';
}