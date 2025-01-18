<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TraderTypeResource\Pages;
use App\Filament\Admin\Resources\TraderTypeResource\RelationManagers;
use App\Models\TraderType;
use App\Traits\HasTranslatedLabels;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TraderTypeResource extends Resource
{
    use HasTranslatedLabels;
    protected static ?string $model = TraderType::class;

    protected static ?string $navigationIcon = 'tabler-square-rotated-filled';

    protected static ?int $navigationSort = 9;

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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTraderTypes::route('/'),
            'create' => Pages\CreateTraderType::route('/create'),
            'edit' => Pages\EditTraderType::route('/{record}/edit'),
        ];
    }
}
