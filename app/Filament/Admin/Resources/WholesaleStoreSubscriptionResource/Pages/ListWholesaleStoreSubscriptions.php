<?php

namespace App\Filament\Admin\Resources\WholesaleStoreSubscriptionResource\Pages;

use App\Filament\Admin\Resources\WholesaleStoreSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWholesaleStoreSubscriptions extends ListRecords
{
    protected static string $resource = WholesaleStoreSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
