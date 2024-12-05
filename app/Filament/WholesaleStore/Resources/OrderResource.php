<?php

namespace App\Filament\WholesaleStore\Resources;

use App\Filament\WholesaleStore\Resources\OrderResource\Pages;
use App\Filament\WholesaleStore\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Traits\HasTranslatedLabels;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    use HasTranslatedLabels;

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'tabler-shopping-cart-bolt';


    public static function table(Table $table): Table
    {
        return $table
            ->columns([

            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }


    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
