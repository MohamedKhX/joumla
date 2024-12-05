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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

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
                            ->label('Wholesale Store Name')
                            ->translateLabel()
                            ->required()
                            ->maxLength(100)
                            ->suffixIcon('tabler-building-store')
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
                            ->suffixIcon('tabler-brand-storj')
                            ->columnSpan(1),

                        TextInput::make('phone')
                            ->label('Phone')
                            ->translateLabel()
                            ->required()
                            ->minLength(10)
                            ->maxLength(10)
                            ->suffixIcon('tabler-phone-call')
                            ->columnSpan(1),

                        Fieldset::make('user')
                            ->label('User')
                            ->translateLabel()
                            ->relationship('user')
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email')
                                    ->translateLabel()
                                    ->required()
                                    ->maxLength(100)
                                    ->email()
                                    ->unique('users', ignoreRecord: true)
                                    ->columnSpan(2),

                                TextInput::make('password')
                                    ->label('Password')
                                    ->translateLabel()
                                    ->required()
                                    ->maxLength(100)
                                    ->password()
                                    ->columnSpan(2)
                                    ->disabledOn('edit')
                                    ->hiddenOn('edit'),
                            ]),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
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
