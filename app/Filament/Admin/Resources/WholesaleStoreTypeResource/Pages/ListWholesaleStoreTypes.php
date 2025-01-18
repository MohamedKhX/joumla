<?php

namespace App\Filament\Admin\Resources\WholesaleStoreTypeResource\Pages;

use App\Filament\Admin\Resources\WholesaleStoreTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWholesaleStoreTypes extends ListRecords
{
    protected static string $resource = WholesaleStoreTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
