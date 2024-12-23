<?php

namespace App\Filament\WholesaleStore\Resources\OrderResource\Pages;

use App\Filament\WholesaleStore\Resources\OrderResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make(3)
                    ->schema([
                        Section::make('معلومات الطلب')
                            ->schema([
                                TextEntry::make('number')
                                    ->label('رقم الطلب')
                                    ->translateLabel(),

                                TextEntry::make('date')
                                    ->label('تاريخ الطلب')
                                    ->translateLabel()
                                    ->date(),

                                TextEntry::make('state')
                                    ->label('الحالة')
                                    ->translateLabel()
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state->translate()),
                            ])->columnSpan(2),

                        Section::make('معلومات التاجر')
                            ->schema([
                                TextEntry::make('trader.store_name')
                                    ->label('اسم المتجر')
                                    ->translateLabel(),

                                TextEntry::make('trader.phone')
                                    ->label('رقم الهاتف')
                                    ->translateLabel(),

                                TextEntry::make('trader.address')
                                    ->label('العنوان')
                                    ->translateLabel(),
                            ])->columnSpan(1),
                    ]),

                Section::make('المنتجات')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('items')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('المنتج')
                                    ->translateLabel(),

                                TextEntry::make('quantity')
                                    ->label('الكمية')
                                    ->translateLabel(),

                                TextEntry::make('unit_price')
                                    ->label('سعر الوحدة')
                                    ->translateLabel()
                                    ->money('LYD'),

                                TextEntry::make('total')
                                    ->label('المجموع')
                                    ->translateLabel()
                                    ->money('LYD')
                                    ->state(fn ($record) => $record->quantity * $record->unit_price),
                            ])
                            ->columns(4),
                    ]),

                Section::make('الإجمالي')
                    ->schema([
                        TextEntry::make('total_amount')
                            ->label('المبلغ الإجمالي')
                            ->translateLabel()
                            ->money('LYD')
                            ->size('lg')
                            ->weight('bold'),
                    ])
            ]);
    }
} 