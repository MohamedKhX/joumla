<?php

namespace App\Enums;

enum UserTypeEnum: string
{
    case Admin = 'Admin';
    case Wholesaler = 'Wholesaler';
    case customer = 'Customer';
}
