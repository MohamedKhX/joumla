<?php

namespace App\Filament\Admin\Resources\TraderResource\Pages;

use App\Filament\Admin\Resources\TraderResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateTrader extends CreateRecord
{
    protected static string $resource = TraderResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = User::create([
            'name'     => $data['store_name'],
            'email'    => $this->data['user']['email'],
            'password' => Hash::make($this->data['user']['password']),
        ]);

        $data['user_id'] = $user->id;

        return $data;
    }
}
