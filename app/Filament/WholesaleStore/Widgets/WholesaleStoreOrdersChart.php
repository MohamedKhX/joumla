<?php

namespace App\Filament\WholesaleStore\Widgets;

use App\Enums\OrderStateEnum;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class WholesaleStoreOrdersChart extends ChartWidget
{
    protected static ?string $heading = 'الطلبات حسب الحالة';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $wholesaleStore = Auth::user()->wholesaleStore;
        $statuses = OrderStateEnum::cases();
        $data = [];
        
        foreach ($statuses as $status) {
            $data['datasets'][] = Order::where('wholesale_store_id', $wholesaleStore->id)
                ->where('state', $status)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'الطلبات',
                    'data' => $data['datasets'],
                    'backgroundColor' => ['#10B981', '#F59E0B', '#EF4444', '#6366F1'],
                ],
            ],
            'labels' => array_map(fn ($status) => $status->translate(), $statuses),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
} 