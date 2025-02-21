<?php

namespace App\Filament\Admin\Resources\WholesaleStoreResource\Pages;

use App\Filament\Admin\Resources\WholesaleStoreResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditWholesaleStore extends EditRecord
{
    protected static string $resource = WholesaleStoreResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['location_latitude']  = $data['map'][0];
        $data['location_longitude'] = $data['map'][1];

        unset($data['map']);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
