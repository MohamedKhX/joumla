<?php

namespace App\Filament\WholesaleStore\Resources;

use App\Enums\OrderStateEnum;
use App\Filament\WholesaleStore\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use App\Traits\HasTranslatedLabels;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OrderResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $wholesaleStore = Auth::user()->wholesaleStore;

        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الطلب')
                    ->schema([
                        Forms\Components\Select::make('trader_id')
                            ->relationship(
                                name: 'trader',
                                titleAttribute: 'store_name',
                                modifyQueryUsing: fn (Builder $query) => $query->whereHas('orders', function ($query) use ($wholesaleStore) {
                                    $query->where('wholesale_store_id', $wholesaleStore->id);
                                })->orWhereDoesntHave('orders')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('التاجر')
                            ->translateLabel(),

                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->label('تاريخ الطلب')
                            ->translateLabel()
                            ->default(now()),

                        Forms\Components\Select::make('state')
                            ->options(OrderStateEnum::getTranslations())
                            ->required()
                            ->label('حالة الطلب')
                            ->translateLabel()
                            ->default(OrderStateEnum::Pending),
                    ])->columns(3),

                Forms\Components\Section::make('المنتجات')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('المنتج')
                                    ->translateLabel()
                                    ->options(function () use ($wholesaleStore) {
                                        return Product::where('wholesale_store_id', $wholesaleStore->id)
                                            ->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            $set('unit_price', $product->price);
                                        }
                                    }),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('الكمية')
                                    ->translateLabel()
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1)
                                    ->live(),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('سعر الوحدة')
                                    ->translateLabel()
                                    ->numeric()
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('إضافة منتج')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['product_id']
                                ? Product::find($state['product_id'])?->name
                                : null),
                    ]),

                Forms\Components\Hidden::make('wholesale_store_id')
                    ->default($wholesaleStore->id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('رقم الطلب')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('trader.store_name')
                    ->label('التاجر')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('تاريخ الطلب')
                    ->translateLabel()
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('is_deferred')
                    ->label('بالآجل')
                    ->translateLabel()
                    ->formatStateUsing(fn($state) => $state ? 'نعم' : 'لا'),

                Tables\Columns\TextColumn::make('state')
                    ->label('الحالة')
                    ->translateLabel()
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        OrderStateEnum::Pending => 'warning',
                        OrderStateEnum::Approved => 'success',
                        OrderStateEnum::Rejected => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => $state->translate()),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('عدد المنتجات')
                    ->translateLabel()
                    ->counts('items'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('المبلغ الإجمالي')
                    ->translateLabel()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('state')
                    ->label('الحالة')
                    ->translateLabel()
                    ->options(OrderStateEnum::getTranslations()),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('من')
                            ->translateLabel(),
                        Forms\Components\DatePicker::make('until')
                            ->label('إلى')
                            ->translateLabel(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('approve')
                        ->label('قبول')
                        ->translateLabel()
                        ->icon('heroicon-o-check')
                        ->color(Color::Green)
                        ->requiresConfirmation()
                        ->visible(fn (Order $record) => $record->state === OrderStateEnum::Pending)
                        ->action(fn (Order $record) => $record->update(['state' => OrderStateEnum::Approved])),

                    Tables\Actions\Action::make('reject')
                        ->label('رفض')
                        ->translateLabel()
                        ->icon('heroicon-o-x-mark')
                        ->color(Color::Red)
                        ->requiresConfirmation()
                        ->visible(fn (Order $record) => $record->state === OrderStateEnum::Pending)
                        ->action(fn (Order $record) => $record->update(['state' => OrderStateEnum::Rejected])),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }



































    use HasTranslatedLabels;

    protected static ?string $model = Order::class;

    public static function canCreate(): bool
    {
        return false;
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('wholesale_store_id', Auth::user()->wholesaleStore->id)
            ->latest();
    }
}
