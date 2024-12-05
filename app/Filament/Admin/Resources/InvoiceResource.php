<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InvoiceResource\Pages;
use App\Filament\Admin\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use App\Traits\HasTranslatedLabels;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
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

    protected static ?int $navigationSort = 3;


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Number')
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->translateLabel()
                    ->suffix(' د.ل'),

                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('trader.name')
                    ->label('The buyer')
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('wholesale_store.name')
                    ->label('The seller')
                    ->translateLabel(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
