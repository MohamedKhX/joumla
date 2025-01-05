<?php

namespace App\Enums;

use App\Traits\Enum;

enum UserTypeEnum: string
{
    use Enum;

    case Admin = 'Admin';
    case Wholesaler = 'Wholesaler';
    case Trader = 'Trader';

    case Driver = 'Driver';
}
