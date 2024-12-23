<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\OrderStateEnum;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Trader;
use App\Models\WholesaleStore;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReportsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي التجار', Trader::count())
                ->description('نشط: ' . Trader::where('is_active', true)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('إجمالي المتاجر بالجملة', WholesaleStore::count())
                ->description('الاشتراكات النشطة: ' . WholesaleStore::has('subscriptions')->count())
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('warning')
                ->chart([3, 5, 7, 8, 3, 2, 1, 4]),

            Stat::make('إجمالي الإيرادات', number_format(Invoice::sum('total_amount'), 2) . ' د.ل')
                ->description('هذا الشهر: ' . number_format(Invoice::whereMonth('created_at', now()->month)->sum('total_amount'), 2) . ' د.ل')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart([4, 8, 3, 5, 6, 9, 7, 3]),

            Stat::make('الطلبات المعلقة', Order::where('state', OrderStateEnum::Pending)->count())
                ->description('إجمالي الطلبات: ' . Order::count())
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('danger')
                ->chart([2, 4, 6, 3, 5, 7, 4, 2]),
        ];
    }
}
