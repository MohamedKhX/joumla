<?php

namespace App\Filament\WholesaleStore\Resources;

use App\Filament\WholesaleStore\Resources\ProductResource\Pages;
use App\Filament\WholesaleStore\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Traits\HasTranslatedLabels;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    use HasTranslatedLabels;

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'tabler-box';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make()
                    ->label('Product')
                    ->translateLabel()
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->translateLabel()
                            ->required()
                            ->maxLength(100),

                        SpatieMediaLibraryFileUpload::make('thumbnail')
                            ->label('Thumbnail')
                            ->translateLabel()
                            ->collection('thumbnail')
                            ->required(),

                        TextArea::make('description')
                            ->label('Description')
                            ->translateLabel()
                            ->required(),

                        TextInput::make('price')
                            ->label('Price')
                            ->translateLabel()
                            ->required()
                            ->numeric()
                            ->minValue(0),

                        DatePicker::make('expire_date')
                            ->label('Expire Date')
                            ->translateLabel()
                            ->nullable()
                            ->date(),

                        SpatieMediaLibraryFileUpload::make('images')
                            ->label('Images')
                            ->translateLabel()
                            ->collection('images')
                            ->multiple(),

                        Forms\Components\Hidden::make('wholesale_store_id')
                            ->default(1)
                    ])
                    ->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->translateLabel()
                    ->collection('thumbnail')
                    ->circular()
                    ->height(80)
                    ->width(80),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->translateLabel()
                    ->description(fn (Product $record): string => str($record->description)->limit(80))
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->translateLabel()
                    ->badge()
                    ->suffix(' د.ل'),

                Tables\Columns\TextColumn::make('expire_date')
                    ->label('Expire Date')
                    ->translateLabel()
                    ->date(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
