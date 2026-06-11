<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesChart7Days extends ChartWidget
{
    protected ?string $heading = 'Penjualan (Selesai) 7 Hari Terakhir';

    protected function getData(): array
    {
        $days = 7;

        $labels = collect(range($days - 1, 0))
            ->map(fn ($i) => Carbon::today()->subDays($i)->format('d M'))
            ->values()
            ->toArray();

        $totals = collect(range($days - 1, 0))
            ->map(function ($i) {
                $date = Carbon::today()->subDays($i);

                return (float) Order::query()
                    ->whereDate('created_at', $date)
                    ->where('status', 'selesai')
                    ->sum('total_price');
            })
            ->values()
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total (Rp)',
                    'data'  => $totals,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}