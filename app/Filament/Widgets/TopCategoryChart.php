<?php

namespace App\Filament\Widgets;

use App\Domain\Complaint\Models\Complaint;
use Filament\Widgets\ChartWidget;

class TopCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Kategori Teratas';

    protected function getData(): array
    {
        $rows = Complaint::query()
            ->selectRaw('category_id, count(*) as aggregate')
            ->with('category')
            ->groupBy('category_id')
            ->orderByDesc('aggregate')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pengaduan',
                    'data' => $rows->pluck('aggregate')->map(fn ($value): int => (int) $value)->all(),
                    'backgroundColor' => ['#0d9488', '#0284c7', '#22c55e', '#f59e0b', '#ef4444'],
                ],
            ],
            'labels' => $rows
                ->map(fn (Complaint $complaint): string => $complaint->category?->name ?? 'Tanpa kategori')
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
