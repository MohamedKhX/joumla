<?php

namespace App\Filament\WholesaleStore\Widgets;

use App\Enums\OrderStateEnum;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\Trader;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class WholesaleStoreOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $wholesaleStore = Auth::user()->wholesaleStore;

        return [
            Stat::make('إجمالي المنتجات', Product::where('wholesale_store_id', $wholesaleStore->id)->count())
                ->description('المنتجات النشطة: ' . Product::where('wholesale_store_id', $wholesaleStore->id)->count())
                ->descriptionIcon('heroicon-m-cube')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('إجمالي التجار', Trader::whereHas('orders', function($query) use ($wholesaleStore) {
                    $query->where('wholesale_store_id', $wholesaleStore->id);
                })->count())
                ->description('التجار النشطين: ' . Trader::whereHas('orders', function($query) use ($wholesaleStore) {
                    $query->where('wholesale_store_id', $wholesaleStore->id)
                        ->whereMonth('created_at', now()->month);
                })->count())
                ->descriptionIcon('heroicon-m-users')
                ->color('warning')
                ->chart([3, 5, 7, 8, 3, 2, 1, 4]),

            Stat::make('إجمالي الإيرادات', number_format(Invoice::where('wholesale_store_id', $wholesaleStore->id)->sum('total_amount'), 2) . ' د.ل')
                ->description('هذا الشهر: ' . number_format(Invoice::where('wholesale_store_id', $wholesaleStore->id)
                    ->whereMonth('created_at', now()->month)
                    ->sum('total_amount'), 2) . ' د.ل')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart([4, 8, 3, 5, 6, 9, 7, 3]),
                
            Stat::make('الطلبات المعلقة', Order::where('wholesale_store_id', $wholesaleStore->id)
                ->where('state', OrderStateEnum::Pending)->count())
                ->description('إجمالي الطلبات: ' . Order::where('wholesale_store_id', $wholesaleStore->id)->count())
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('danger')
                ->chart([2, 4, 6, 3, 5, 7, 4, 2]),
        ];
    }
} 