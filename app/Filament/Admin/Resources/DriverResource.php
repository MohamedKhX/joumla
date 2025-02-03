<?php

namespace App\Filament\Admin\Resources;

use App\Enums\UserTypeEnum;
use App\Filament\Admin\Resources\DriverResource\Pages;
use App\Filament\Admin\Resources\DriverResource\RelationManagers;
use App\Mail\StoreActive;
use App\Models\Driver;
use App\Models\User;
use App\Traits\HasTranslatedLabels;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;

class DriverResource extends Resource
{
    protected static ?int $navigationSort = 5;


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

                        SpatieMediaLibraryFileUpload::make('car_image')
                            ->label('Car Image')
                            ->translateLabel()
                            ->collection('car_image')
                            ->required(),

                        SpatieMediaLibraryFileUpload::make('target_image')
                            ->label('Target Image')
                            ->translateLabel()
                            ->collection('target_image')
                            ->required(),

                        SpatieMediaLibraryFileUpload::make('license_image')
                            ->label('License Image')
                            ->translateLabel()
                            ->collection('license_image')
                            ->required(),

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
                Tables\Columns\TextColumn::make('is_active')
                    ->label('Is Active')
                    ->translateLabel()
                    ->searchable()
                    ->badge()
                    ->color(function ($state) {
                        if($state) {
                            return Color::Green;
                        }

                        return Color::Red;
                    })
                    ->formatStateUsing(fn($state) => $state ? 'مفعل' : 'ليس مفعل')
                    ->icon('tabler-activity-heartbeat'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Is Active')
                    ->translateLabel()
                    ->options([
                        '1' => 'مفعل',
                        '0' => 'ليس مفعل',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('activate')
                    ->label('Activate')
                    ->translateLabel()
                    ->action(function ($record) {
                        $record->is_active = true;
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->icon('tabler-activity')
                    ->hidden(fn($record) => $record->is_active),

                Tables\Actions\Action::make('de_activate')
                    ->label('De Activate')
                    ->translateLabel()
                    ->icon('tabler-activity')
                    ->action(function ($record) {
                        $record->is_active = false;
                        $record->save();
                    })
                    ->color(Color::Orange)
                    ->hidden(fn($record) => ! $record->is_active),
                Tables\Actions\Action::make('change_password')
                    ->label('Change Password')
                    ->translateLabel()
                    ->icon('tabler-key')
                    ->form([
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->translateLabel()
                            ->required()
                            ->password(),
                    ])
                    ->action(fn (User $user) => $user->update([
                        'password' => bcrypt(request('password')),
                    ])),

            ]);
    }












































    use HasTranslatedLabels;
    protected static ?string $model = User::class;

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
