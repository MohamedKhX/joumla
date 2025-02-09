<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AreaResource\Pages;
use App\Filament\Admin\Resources\AreaResource\RelationManagers;
use App\Models\Area;
use App\Traits\HasTranslatedLabels;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Symfony\Contracts\Translation\TranslatorTrait;

class AreaResource extends Resource
{
    use HasTranslatedLabels;

    protected static ?string $model = Area::class;

    protected static ?string $navigationIcon = 'tabler-brand-planetscale';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Area')
                            ->translateLabel()
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('price')
                            ->label('Deliver Price')
                            ->translateLabel()
                            ->required()
                            ->suffix(' د.ل')
                            ->type('number')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Area')
                    ->translateLabel()
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Deliver Price')
                    ->translateLabel()
                    ->badge()
                    ->suffix(' د.ل')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAreas::route('/'),
            'create' => Pages\CreateArea::route('/create'),
            'edit' => Pages\EditArea::route('/{record}/edit'),
        ];
    }
}
