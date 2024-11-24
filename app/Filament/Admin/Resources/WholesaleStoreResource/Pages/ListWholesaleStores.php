<?php

namespace App\Filament\Admin\Resources\WholesaleStoreResource\Pages;

use App\Filament\Admin\Resources\WholesaleStoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWholesaleStores extends ListRecords
{
    protected static string $resource = WholesaleStoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
