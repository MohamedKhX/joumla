<?php

namespace App\Filament\Admin\Resources;

use App\Enums\UserTypeEnum;
use App\Filament\Admin\Resources\DriverResource\Pages;
use App\Filament\Admin\Resources\DriverResource\RelationManagers;
use App\Models\Driver;
use App\Models\User;
use App\Traits\HasTranslatedLabels;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DriverResource extends Resource
{
    use HasTranslatedLabels;

    protected static ?int $navigationSort = 5;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'tabler-car';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->translateLabel()
                            ->required()
                            ->autofocus(),


                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->translateLabel()
                            ->required()
                            ->unique('users', 'email', ignoreRecord: true)
                            ->email(),

                        Forms\Components\TextInput::make('phone')
                            ->label('Phone')
                            ->translateLabel()
                            ->required()
                            ->numeric(),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->translateLabel()
                            ->required()
                            ->password(),

                        Forms\Components\Hidden::make('type')
                            ->default(UserTypeEnum::Driver->value),
                    ])->columns(1)
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
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
    /*        'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
   */     ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', UserTypeEnum::Driver);
    }

    public static function getModelLabel(): string
    {
        return __('Driver');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Drivers');
    }

    public static function getNavigationLabel(): string
    {
        return __('Drivers');
    }

    public function getHeading(): string|Htmlable
    {
        return __('Drivers');
    }
}
