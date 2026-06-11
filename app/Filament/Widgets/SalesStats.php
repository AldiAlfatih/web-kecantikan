<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class SalesStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // Status final penjualan
        $doneStatus = 'selesai';

        // Status order yang masih berjalan
        $progressStatuses = ['menunggu_verifikasi', 'diproses', 'dikirim'];

        $today  = Carbon::today();
        $start7 = Carbon::now()->subDays(6)->startOfDay();

        // ========== PENJUALAN SELESAI ==========
        $totalRevenueDone = Order::where('status', $doneStatus)->sum('total_price');
        $totalOrdersDone  = Order::where('status', $doneStatus)->count();

        $revenueDoneToday = Order::where('status', $doneStatus)
            ->whereDate('created_at', $today)
            ->sum('total_price');

        $ordersDoneToday = Order::where('status', $doneStatus)
            ->whereDate('created_at', $today)
            ->count();

        $revenueDone7d = Order::where('status', $doneStatus)
            ->whereBetween('created_at', [$start7, Carbon::now()])
            ->sum('total_price');

        $ordersDone7d = Order::where('status', $doneStatus)
            ->whereBetween('created_at', [$start7, Carbon::now()])
            ->count();

        $avgOrderDone = $totalOrdersDone > 0 ? ($totalRevenueDone / $totalOrdersDone) : 0;

        // ========== ORDER DALAM PROSES ==========
        $ordersInProgress = Order::whereIn('status', $progressStatuses)->count();

        return [
            Stat::make('Total Pendapatan (Selesai)', 'Rp ' . number_format($totalRevenueDone, 0, ',', '.'))
                ->description('Hanya order status selesai')
                ->descriptionIcon('heroicon-m-banknotes'),

            Stat::make('Order Selesai', number_format($totalOrdersDone, 0, ',', '.'))
                ->description('Status: selesai')
                ->descriptionIcon('heroicon-m-check-badge'),

            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($revenueDoneToday, 0, ',', '.'))
                ->description($ordersDoneToday . ' order selesai hari ini')
                ->descriptionIcon('heroicon-m-calendar-days'),

            Stat::make('Pendapatan 7 Hari', 'Rp ' . number_format($revenueDone7d, 0, ',', '.'))
                ->description($ordersDone7d . ' order selesai (7 hari)')
                ->descriptionIcon('heroicon-m-chart-bar'),

            Stat::make('Rata-rata Order', 'Rp ' . number_format($avgOrderDone, 0, ',', '.'))
                ->description('AOV (order selesai)')
                ->descriptionIcon('heroicon-m-calculator'),

            Stat::make('Order Dalam Proses', number_format($ordersInProgress, 0, ',', '.'))
                ->description('Menunggu verifikasi / diproses / dikirim')
                ->descriptionIcon('heroicon-m-truck'),
        ];
    }
}