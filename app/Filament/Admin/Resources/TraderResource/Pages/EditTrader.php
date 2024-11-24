<?php

namespace App\Filament\Admin\Resources\TraderResource\Pages;

use App\Filament\Admin\Resources\TraderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrader extends EditRecord
{
    protected static string $resource = TraderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
