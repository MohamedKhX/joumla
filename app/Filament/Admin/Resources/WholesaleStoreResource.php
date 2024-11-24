<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Resources\WholesaleStoreResource\Pages;
use App\Filament\Resources\WholesaleStoreResource\RelationManagers;
use App\Models\WholesaleStore;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WholesaleStoreResource extends Resource
{
    protected static ?string $model = WholesaleStore::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\WholesaleStoreResource\Pages\ListWholesaleStores::route('/'),
            'create' => \App\Filament\Admin\Resources\WholesaleStoreResource\Pages\CreateWholesaleStore::route('/create'),
            'edit' => \App\Filament\Admin\Resources\WholesaleStoreResource\Pages\EditWholesaleStore::route('/{record}/edit'),
        ];
    }
}
