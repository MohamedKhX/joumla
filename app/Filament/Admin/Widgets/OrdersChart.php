<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\OrderStateEnum;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'الطلبات حسب الحالة';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $statuses = OrderStateEnum::cases();
        $data = [];
        
        foreach ($statuses as $status) {
            $data['datasets'][] = Order::where('state', $status)->count();
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