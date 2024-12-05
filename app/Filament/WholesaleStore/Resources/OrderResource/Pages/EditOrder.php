<?php

namespace App\Filament\WholesaleStore\Resources\OrderResource\Pages;

use App\Filament\WholesaleStore\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
