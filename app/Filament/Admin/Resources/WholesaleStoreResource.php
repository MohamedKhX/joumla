<?php

namespace App\Filament\Admin\Resources;

use App\Enums\WholesaleStoreEnum;
use App\Filament\Resources\WholesaleStoreResource\Pages;
use App\Filament\Resources\WholesaleStoreResource\RelationManagers;
use App\Models\Trader;
use App\Models\WholesaleStore;
use App\Models\WholesaleStoreType;
use App\Traits\HasTranslatedLabels;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class WholesaleStoreResource extends Resource
{
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
                        Select::make('wholesale_store_type_id')
                            ->label('Wholesale Store Type')
                            ->translateLabel()
                            ->options(WholesaleStoreType::all()->pluck('name', 'id')->toArray())
                            ->required()
                            ->suffixIcon('tabler-brand-storj')
                            ->columnSpan(2),

                        SpatieMediaLibraryFileUpload::make('logo')
                            ->label('Logo')
                            ->translateLabel()
                            ->collection('logo')
                            ->columnSpan(2),

                        SpatieMediaLibraryFileUpload::make('license')
                            ->label('License')
                            ->translateLabel()
                            ->collection('license')
                            ->columnSpan(2),

                        TextInput::make('phone')
                            ->label('Phone')
                            ->translateLabel()
                            ->required()
                            ->minLength(10)
                            ->maxLength(10)
                            ->suffixIcon('tabler-phone-call')
                            ->columnSpan(2),

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
                SpatieMediaLibraryImageColumn::make('logo')
                    ->collection('logo')
                    ->label("Logo")
                    ->translateLabel()
                    ->circular(),

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

                Tables\Columns\TextColumn::make('wholesaleStoreType.name')
                    ->label('Wholesale Store Type')
                    ->translateLabel()
                    ->icon('tabler-brand-storj')
                    ->badge()
                    ->color(Color::Red),
                ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                Tables\Actions\Action::make('license')
                    ->label('License')
                    ->translateLabel()
                    ->icon('tabler-license')
                    ->url(fn($record) => $record->licenseUrl(), true)
                    ->color(Color::Indigo),

                Tables\Actions\Action::make('change_password')
                    ->label('Change Password')
                    ->translateLabel()
                    ->icon('tabler-key')
                    ->form([
                        TextInput::make('password')
                            ->label('Password')
                            ->translateLabel()
                            ->required()
                            ->password(),
                    ])
                    ->action(function (WholesaleStore $wholesaleStore) {
                        $wholesaleStore->user->password = bcrypt(request('password'));
                        $wholesaleStore->user->save();
                    }),
            ]);
    }





























    use HasTranslatedLabels;

    protected static ?string $model = WholesaleStore::class;


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
