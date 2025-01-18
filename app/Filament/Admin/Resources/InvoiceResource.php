<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InvoiceResource\Pages;
use App\Filament\Admin\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use App\Tables\Actions\InvoiceAction;
use App\Traits\HasTranslatedLabels;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    use HasTranslatedLabels;

    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'tabler-file-invoice';

    protected static ?int $navigationSort = 6;


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

                Tables\Columns\TextColumn::make('wholesaleStore.name')
                    ->label('The seller')
                    ->translateLabel()
                    ->searchable(),
            ])
            ->actions([
                InvoiceAction::make('print')
                    ->label('Print')
                    ->translateLabel()
                    ->color(Color::Teal)
                    ->icon('heroicon-o-eye')
                    ->invoiceItems([
                        'number' => 'Invoice Number',
                        'total_amount' => 'Total Amount',
                        'issued_on' => 'Issued On',
                        'trader.store_name' => 'The buyer',
                        'wholesaleStore.name' => 'The seller',
                    ]),

                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->translateLabel()
                    ->color(Color::Green)
                    ->icon('heroicon-o-arrow-down-tray'),
            ]);
    }

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
        return parent::getEloquentQuery()->latest();
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
