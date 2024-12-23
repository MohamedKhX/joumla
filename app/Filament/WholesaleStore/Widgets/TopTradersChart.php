<?php

namespace App\Filament\WholesaleStore\Widgets;

use App\Models\Trader;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopTradersChart extends ChartWidget
{
    protected static ?string $heading = 'أفضل التجار';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $wholesaleStore = Auth::user()->wholesaleStore;
        
        $topTraders = Trader::select('traders.store_name', DB::raw('COUNT(orders.id) as orders_count'))
            ->join('orders', 'traders.id', '=', 'orders.trader_id')
            ->where('orders.wholesale_store_id', $wholesaleStore->id)
            ->groupBy('traders.id', 'traders.store_name')
            ->orderByDesc('orders_count')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'عدد الطلبات',
                    'data' => $topTraders->pluck('orders_count')->toArray(),
                    'backgroundColor' => ['#10B981', '#F59E0B', '#EF4444', '#6366F1', '#EC4899'],
                ],
            ],
            'labels' => $topTraders->pluck('store_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
} 