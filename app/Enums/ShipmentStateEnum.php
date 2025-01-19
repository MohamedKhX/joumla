<?php

namespace App\Enums;

use App\Traits\Enum;

enum ShipmentStateEnum: string
{
    use Enum;
    case WaitingForShipping = 'Waiting For Shipping';
    case Shipping = 'Shipping';
    case Shipped = 'Shipped';
}
