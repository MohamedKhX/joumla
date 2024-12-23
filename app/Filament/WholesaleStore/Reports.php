<?php

namespace App\Filament\WholesaleStore;

use App\Filament\WholesaleStore\Widgets\WholesaleStoreOverview;
use App\Filament\WholesaleStore\Widgets\WholesaleStoreOrdersChart;
use App\Filament\WholesaleStore\Widgets\TopTradersChart;
use App\Filament\WholesaleStore\Widgets\MonthlyRevenueChart;
use Filament\Pages\Dashboard;

class Reports extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'الإحصائيات';

    public function getWidgets(): array
    {
        return [
            WholesaleStoreOverview::class,
            WholesaleStoreOrdersChart::class,
            TopTradersChart::class,
            MonthlyRevenueChart::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}
