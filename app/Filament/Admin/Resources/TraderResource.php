<?php

namespace App\Filament\Admin\Resources;

use App\Enums\StoreTypeEnum;
use App\Enums\WholesaleStoreEnum;
use App\Filament\Admin\Resources\TraderResource\Pages;
use App\Filament\Admin\Resources\TraderResource\RelationManagers;
use App\Mail\StoreActive;
use App\Models\Trader;
use App\Models\TraderType;
use App\Models\User;
use App\Traits\HasTranslatedLabels;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;

class TraderResource extends Resource
{
    use HasTranslatedLabels;

    protected static ?string $model = Trader::class;

    protected static ?string $navigationIcon = 'tabler-user-hexagon';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Fieldset::make('trader_information')
                    ->label('Trader Information')
                    ->translateLabel()
                    ->schema([
                        TextInput::make('store_name')
                            ->label('Store Name')
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
                        Select::make('trader_type_id')
                            ->label('Trader Type')
                            ->translateLabel()
                            ->options(TraderType::all()->pluck('name', 'id')->toArray())
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

                        \Filament\Forms\Components\Fieldset::make('user')
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
                                    ->minLength(8)
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

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Trader Name')
                    ->translateLabel()
                    ->searchable()
                    ->icon('tabler-user-hexagon'),

                /*Tables\Columns\TextColumn::make('user.email')
                    ->label('Trader Email')
                    ->translateLabel()
                    ->icon('heroicon-o-at-symbol'),*/

                Tables\Columns\TextColumn::make('store_name')
                    ->label('Store Name')
                    ->translateLabel()
                    ->searchable()
                    ->icon('tabler-building-store'),

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

                Tables\Columns\TextColumn::make('phone')
                    ->label('Store Phone')
                    ->translateLabel()
                    ->icon('tabler-phone-call'),

                Tables\Columns\TextColumn::make('traderType.name')
                    ->label('Store Type')
                    ->translateLabel()
                    ->icon('tabler-brand-storj')
                    ->badge(),
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
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('activate')
                    ->label('Activate')
                    ->translateLabel()
                    ->action(function ($record) {
                        $record->is_active = true;
                        $record->save();
                        Mail::to($record->user->email)
                            ->send(new StoreActive());
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
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->translateLabel()
                            ->required()
                            ->password(),
                    ])
                    ->action(function (Trader $trader) {
                        $trader->user->password = bcrypt(request('password'));
                        $trader->user->save();
                    }),
            ]);
    }





































    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Overview')
                            ->translateLabel()
                            ->icon('heroicon-m-eye')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                SpatieMediaLibraryImageEntry::make('user.avatar')
                                    ->label('Avatar')
                                    ->translateLabel()
                                    ->collection('avatar')
                                    ->conversion('thumb')
                                    ->circular()
                                    ->url(fn($record) => $record->user->getFirstMediaUrl('avatar'), true),

                                Fieldset::make('Info')
                                    ->translateLabel()
                                    ->schema([
                                        TextEntry::make('user.name')
                                            ->label('Trader Name')
                                            ->translateLabel(),

                                        TextEntry::make('store_name')
                                            ->label('Store Name')
                                            ->translateLabel(),

                                        TextEntry::make('user.email')
                                            ->label('Trader Email')
                                            ->translateLabel(),

                                        TextEntry::make('tags.name')
                                            ->label('Tags')
                                            ->translateLabel()
                                            ->badge()
                                            ->color('success'),
                                    ])
                                    ->columns(4),
                            ])
                    ])
            ])
            ->columns(1);
    }


    public static function canView(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTraders::route('/'),
            'create' => Pages\CreateTrader::route('/create'),
            'edit' => Pages\EditTrader::route('/{record}/edit'),
            'view' => Pages\ViewTrader::route('/{record}/view'),
        ];
    }
}
