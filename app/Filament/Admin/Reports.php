<?php

namespace App\Filament\Admin;

use App\Filament\Admin\Widgets\ReportsOverview;
use App\Filament\Admin\Widgets\OrdersChart;
use App\Filament\Admin\Widgets\StoreTypesChart;
use App\Filament\Admin\Widgets\RevenueChart;
use Filament\Pages\Dashboard;

class Reports extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'الإحصائيات';

    public function getWidgets(): array
    {
        return [
            ReportsOverview::class,
            OrdersChart::class,
            StoreTypesChart::class,
            RevenueChart::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}
