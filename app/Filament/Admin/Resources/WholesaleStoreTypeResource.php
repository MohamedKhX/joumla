<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WholesaleStoreTypeResource\Pages;
use App\Filament\Admin\Resources\WholesaleStoreTypeResource\RelationManagers;
use App\Models\WholesaleStoreType;
use App\Traits\HasTranslatedLabels;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WholesaleStoreTypeResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-s-queue-list';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->translateLabel()
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }



























    use HasTranslatedLabels;
    protected static ?string $model = WholesaleStoreType::class;


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWholesaleStoreTypes::route('/'),
            'create' => Pages\CreateWholesaleStoreType::route('/create'),
            'edit' => Pages\EditWholesaleStoreType::route('/{record}/edit'),
        ];
    }
}
