<?php

namespace App\Filament\Admin\Resources\WholesaleStoreResource\Pages;

use App\Filament\Admin\Resources\WholesaleStoreResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateWholesaleStore extends CreateRecord
{
    protected static string $resource = WholesaleStoreResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $this->data['user']['email'],
            'password' => Hash::make($this->data['user']['password']),
        ]);

        $data['user_id'] = $user->id;

        return $data;
    }
}
