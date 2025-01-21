<?php

namespace App\Enums;

use App\Traits\Enum;

enum ShipmentStateEnum: string
{
    use Enum;
    case Pending = 'Pending';
    case WaitingForShipping = 'Waiting For Shipping';
    case WaitingForReceiving = 'Waiting For Receiving';
    case Received = 'Received';
    case Shipping = 'Shipping';
    case Shipped = 'Shipped';
}
