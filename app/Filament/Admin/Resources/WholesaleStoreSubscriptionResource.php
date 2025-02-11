<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WholesaleStoreSubscriptionResource\Pages;
use App\Filament\Admin\Resources\WholesaleStoreSubscriptionResource\RelationManagers;
use App\Models\WholesaleStore;
use App\Models\WholesaleStoreSubscription;
use App\Traits\HasTranslatedLabels;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WholesaleStoreSubscriptionResource extends Resource
{
    protected static ?string $navigationIcon = 'tabler-cash';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('subscription info')
                    ->label(__('Subscription Information'))
                    ->schema([
                        Forms\Components\Select::make('wholesale_store_id')
                            ->label('Wholesale Store')
                            ->translateLabel()
                            ->options(WholesaleStore::all()->pluck('name', 'id'))
                            ->searchable()
                            ->prefixIcon('tabler-building-warehouse')
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Price')
                            ->translateLabel()
                            ->required()
                            ->numeric()
                            ->prefixIcon('tabler-cash'),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date')
                            ->translateLabel()
                            ->required()
                            ->prefixIcon('tabler-calendar')
                            ->minDate(now()),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date')
                            ->translateLabel()
                            ->required()
                            ->prefixIcon('tabler-calendar')
                            ->afterOrEqual('start_date')
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wholesaleStore.name')
                    ->label('Wholesale Store')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Price')
                    ->translateLabel()
                    ->sortable()
                    ->color(Color::Green)
                    ->badge()
                    ->suffix(' د.ل'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->translateLabel()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->translateLabel()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }




























    use HasTranslatedLabels;

    protected static ?string $model = WholesaleStoreSubscription::class;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWholesaleStoreSubscriptions::route('/'),
            'create' => Pages\CreateWholesaleStoreSubscription::route('/create'),
            'edit' => Pages\EditWholesaleStoreSubscription::route('/{record}/edit'),
        ];
    }
}
