<?php

namespace App\Filament\Widgets;

use App\Domain\Complaint\Models\Complaint;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class ComplaintTrendChart extends ChartWidget
{
    protected ?string $heading = 'Tren Pengaduan 7 Hari';

    protected function getData(): array
    {
        $period = CarbonPeriod::create(now()->subDays(6)->startOfDay(), now()->startOfDay());
        $labels = [];
        $data = [];

        foreach ($period as $date) {
            $labels[] = $date->format('d M');
            $data[] = Complaint::query()
                ->whereDate('created_at', $date->toDateString())
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pengaduan',
                    'data' => $data,
                    'borderColor' => '#0d9488',
                    'backgroundColor' => 'rgba(13, 148, 136, 0.15)',
                    'fill' => true,
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
