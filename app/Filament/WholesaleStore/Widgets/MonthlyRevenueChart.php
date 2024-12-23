<?php

namespace App\Filament\WholesaleStore\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class MonthlyRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'الإيرادات الشهرية';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        $wholesaleStore = Auth::user()->wholesaleStore;
        $data = collect();
        
        for ($i = 1; $i <= 12; $i++) {
            $data->push(
                Invoice::where('wholesale_store_id', $wholesaleStore->id)
                    ->whereMonth('created_at', $i)
                    ->whereYear('created_at', now()->year)
                    ->sum('total_amount')
            );
        }

        return [
            'datasets' => [
                [
                    'label' => 'الإيرادات د.ل',
                    'data' => $data->toArray(),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
} 