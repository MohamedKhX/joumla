<?php

namespace App\Enums;

use App\Traits\Enum;

enum ShipmentStateEnum: string
{
    use Enum;

    case Pending = 'Pending';
    case Cancelled = 'Cancelled';
    case Approved = 'Approved';
    case Shipped = 'Shipped';
}
