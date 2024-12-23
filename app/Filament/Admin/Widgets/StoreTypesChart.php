<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Trader;
use Filament\Widgets\ChartWidget;

class StoreTypesChart extends ChartWidget
{
    protected static ?string $heading = 'توزيع أنواع المتاجر';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $traders = Trader::selectRaw('store_type, count(*) as count')
            ->groupBy('store_type')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'المتاجر',
                    'data' => $traders->pluck('count')->toArray(),
                    'backgroundColor' => [
                        '#10B981', '#F59E0B', '#EF4444', '#6366F1', 
                        '#EC4899', '#8B5CF6', '#14B8A6', '#F97316'
                    ],
                ],
            ],
            'labels' => $traders->pluck('store_type')->map(fn($type) => $type->translate())->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
} 