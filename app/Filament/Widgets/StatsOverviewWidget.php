<?php

namespace App\Filament\Widgets;

use App\Domain\Complaint\Enums\ComplaintStatusCode;
use App\Domain\Complaint\Models\Complaint;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected ?string $heading = 'Ringkasan Pengaduan';

    protected function getStats(): array
    {
        $total = Complaint::query()->count();
        $new = $this->countByStatus(ComplaintStatusCode::NEW->value);
        $inProgress = Complaint::query()
            ->whereHas('status', fn ($query) => $query->whereIn('code', [
                ComplaintStatusCode::VERIFIED->value,
                ComplaintStatusCode::IN_PROGRESS->value,
                ComplaintStatusCode::WAITING_APPROVAL->value,
                ComplaintStatusCode::RETURNED->value,
            ]))
            ->count();
        $completed = $this->countByStatus(ComplaintStatusCode::COMPLETED->value);
        $closed = $this->countByStatus(ComplaintStatusCode::CLOSED->value);
        $resolved = Complaint::query()->whereNotNull('resolved_at')->count();
        $withinSla = Complaint::query()
            ->whereNotNull('resolved_at')
            ->whereColumn('resolved_at', '<=', 'sla_due_at')
            ->count();

        return [
            Stat::make('Total', number_format($total))
                ->description('Seluruh tiket pengaduan')
                ->color('primary'),
            Stat::make('Baru', number_format($new))
                ->color('gray'),
            Stat::make('Dalam Proses', number_format($inProgress))
                ->color('info'),
            Stat::make('Selesai', number_format($completed))
                ->color('success'),
            Stat::make('Ditutup', number_format($closed))
                ->color('gray'),
            Stat::make('SLA %', $resolved > 0 ? number_format(($withinSla / $resolved) * 100, 1) . '%' : '0%')
                ->description('Terselesaikan sebelum jatuh tempo')
                ->color($resolved === 0 || ($withinSla / max($resolved, 1)) >= 0.8 ? 'success' : 'warning'),
        ];
    }

    private function countByStatus(string $statusCode): int
    {
        return Complaint::query()
            ->whereHas('status', fn ($query) => $query->where('code', $statusCode))
            ->count();
    }
}
