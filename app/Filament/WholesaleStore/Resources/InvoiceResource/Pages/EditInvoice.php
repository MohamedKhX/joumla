<?php

namespace App\Filament\WholesaleStore\Resources\InvoiceResource\Pages;

use App\Filament\WholesaleStore\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
