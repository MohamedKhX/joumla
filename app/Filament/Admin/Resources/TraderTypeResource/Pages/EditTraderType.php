<?php

namespace App\Filament\Admin\Resources\TraderTypeResource\Pages;

use App\Filament\Admin\Resources\TraderTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTraderType extends EditRecord
{
    protected static string $resource = TraderTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
