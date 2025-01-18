<?php

namespace App\Filament\Admin\Resources\WholesaleStoreTypeResource\Pages;

use App\Filament\Admin\Resources\WholesaleStoreTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWholesaleStoreType extends EditRecord
{
    protected static string $resource = WholesaleStoreTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
