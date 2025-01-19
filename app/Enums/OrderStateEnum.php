<?php

namespace App\Enums;

use App\Traits\Enum;

enum OrderStateEnum: string
{
    use Enum;

    case Pending = 'Pending';
    case Rejected  = 'Rejected';
    case Approved = 'Approved';


}
