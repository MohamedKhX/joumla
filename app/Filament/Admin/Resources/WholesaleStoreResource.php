<?php

namespace App\Filament\Admin\Resources;

use App\Enums\WholesaleStoreEnum;
use App\Filament\Resources\WholesaleStoreResource\Pages;
use App\Filament\Resources\WholesaleStoreResource\RelationManagers;
use App\Models\WholesaleStore;
use App\Traits\HasTranslatedLabels;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;

class WholesaleStoreResource extends Resource
{
    use HasTranslatedLabels;

    protected static ?string $model = WholesaleStore::class;

    protected static ?string $navigationIcon = 'tabler-building-warehouse';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('wholesale_store_information')
                    ->label('Wholesale Store Information')
                    ->translateLabel()
                    ->schema([
                        TextInput::make('name')
                            ->label('Wholesale Name')
                            ->translateLabel()
                            ->required()
                            ->maxLength(100)
                            ->suffixIcon('heroicon-m-globe-alt')
                            ->columnSpan(2),

                        TextInput::make('city')
                            ->label('City')
                            ->translateLabel()
                            ->required()
                            ->maxLength(100)
                            ->suffixIcon('heroicon-m-globe-alt')
                            ->columnSpan(1),

                        TextInput::make('address')
                            ->label('Address')
                            ->translateLabel()
                            ->required()
                            ->maxLength(100)
                            ->suffixIcon('heroicon-m-globe-alt')
                            ->columnSpan(1),

                        //select box for the type
                        Select::make('type')
                            ->label('Wholesale Store Type')
                            ->translateLabel()
                            ->options(WholesaleStoreEnum::getTranslations())
                            ->required()
                            ->suffixIcon('heroicon-m-globe-alt')
                            ->columnSpan(1),

                        TextInput::make('address')
                            ->label('Address')
                            ->translateLabel()
                            ->required()
                            ->maxLength(100)
                            ->suffixIcon('heroicon-m-globe-alt')
                            ->columnSpan(1),

                        Map::make('location')
                            ->label('Location')
                            ->columnSpanFull()
                            ->defaultLocation(latitude: 40.4168, longitude: -3.7038)
                            ->afterStateUpdated(function (Set $set, ?array $state): void {
                                $set('latitude',  $state['lat']);
                                $set('longitude', $state['lng']);
                            })
                            ->extraStyles([
                                'min-height: 50vh',
                                'border-radius: 5px'
                            ])
                            ->showMarker()
                            ->markerColor("#22c55eff")
                            ->boundaries(true,49,11.1,61.0,2.1)
                            ->geoManPosition('topleft')
                            ->drawCircleMarker()
                            ->rotateMode()
                            ->drawMarker()
                            ->drawPolygon()
                            ->drawPolyline()
                            ->drawCircle()
                            ->dragMode()
                            ->cutPolygon()
                            ->editPolygon()
                            ->deleteLayer()
                            ->setColor('#3388ff')
                            ->setFilledColor('#cad9ec')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Wholesale Store Name')
                    ->translateLabel()
                    ->searchable()
                    ->icon('tabler-building-store'),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Wholesale Store Email')
                    ->translateLabel()
                    ->icon('heroicon-o-at-symbol'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Wholesale Store Phone')
                    ->translateLabel()
                    ->icon('tabler-phone-call'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Wholesale Store Type')
                    ->translateLabel()
                    ->icon('tabler-brand-storj')
                    ->badge()
                    ->color(Color::Red)
                    ->formatStateUsing(fn($state) => $state->translate()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
