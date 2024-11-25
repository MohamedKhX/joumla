<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TraderResource\Pages;
use App\Filament\Admin\Resources\TraderResource\RelationManagers;
use App\Models\Trader;
use App\Traits\HasTranslatedLabels;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TraderResource extends Resource
{
    use HasTranslatedLabels;

    protected static ?string $model = Trader::class;

    protected static ?string $navigationIcon = 'tabler-user-hexagon';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Trader Name')
                    ->translateLabel()
                    ->searchable()
                    ->icon('tabler-user-hexagon'),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Trader Email')
                    ->translateLabel()
                    ->icon('heroicon-o-at-symbol'),

                Tables\Columns\TextColumn::make('store_name')
                    ->label('Store Name')
                    ->translateLabel()
                    ->searchable()
                    ->icon('tabler-building-store'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Store Phone')
                    ->translateLabel()
                    ->icon('tabler-phone-call'),

                Tables\Columns\TextColumn::make('store_type')
                    ->label('Store Type')
                    ->translateLabel()
                    ->icon('tabler-brand-storj')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->translate()),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTraders::route('/'),
            'create' => Pages\CreateTrader::route('/create'),
            'edit' => Pages\EditTrader::route('/{record}/edit'),
        ];
    }
}
