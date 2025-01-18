<?php

namespace App\Filament\Admin\Resources\TraderTypeResource\Pages;

use App\Filament\Admin\Resources\TraderTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTraderTypes extends ListRecords
{
    protected static string $resource = TraderTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
