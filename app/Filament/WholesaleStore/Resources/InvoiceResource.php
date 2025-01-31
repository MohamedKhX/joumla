<?php

namespace App\Filament\WholesaleStore\Resources;

use App\Filament\WholesaleStore\Resources\InvoiceResource\Pages;
use App\Filament\WholesaleStore\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use App\Tables\Actions\InvoiceAction;
use App\Traits\HasTranslatedLabels;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class InvoiceResource extends Resource
{
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'tabler-file-invoice';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Invoice Number')
                    ->translateLabel()
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->translateLabel()
                    ->suffix(' د.ل'),

                Tables\Columns\TextColumn::make('issued_on')
                    ->label('Issued On')
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('trader.store_name')
                    ->label('The buyer')
                    ->translateLabel()
                    ->searchable(),
            ])
            ->actions([
                InvoiceAction::make('view')
                    ->label('View')
                    ->translateLabel()
                    ->color(Color::Teal)
                    ->icon('heroicon-o-eye')
                    ->invoiceItems(fn($record) => $record->items)
                    ->index()
                    ->headersAndColumns([
                        'product.name' => 'المتنج',
                        'unit_price' => 'السعر',
                        'quantity' => 'الكمية',
                    ])
                    ->date(fn($record) => $record->issued_on)
                    ->companyName(fn($record) => $record->wholesaleStore->name)
                    ->companyInfo(function ($record) {
                        return [
                            'العنوان' => $record->wholesaleStore->address,
                            'الهاتف' => $record->wholesaleStore->phone,
                        ];
                    })
                    ->logo(fn($record) => $record->wholesaleStore->getFirstMediaUrl('logo'))
                    ->download(),
            ]);
    }









































    use HasTranslatedLabels;

    protected static ?string $model = Invoice::class;



    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('wholesale_store_id', Auth::user()->wholesaleStore->id)
            ->latest();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
