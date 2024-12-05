<?php

namespace App\Filament\WholesaleStore\Resources\OrderResource\Pages;

use App\Filament\WholesaleStore\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
