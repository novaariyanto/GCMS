<?php

namespace App\Application\Reporting\Services;

use App\Domain\Complaint\Models\Complaint;
use App\Infrastructure\Persistence\Repositories\ComplaintRepository;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

class ReportService
{
    public function __construct(private readonly ComplaintRepository $complaints) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, Complaint>
     */
    public function complaintsReport(array $filters): Collection
    {
        return $this->complaints
            ->search($filters)
            ->with(['reporter', 'district', 'village'])
            ->get();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function exportCsv(array $filters): string
    {
        $handle = fopen('php://temp', 'r+');

        if ($handle === false) {
            throw new RuntimeException('Unable to open temporary stream for complaint CSV export.');
        }

        fputcsv($handle, [
            'Ticket Number',
            'Status',
            'Category',
            'Sub Category',
            'Priority',
            'Title',
            'Reporter',
            'Reporter Phone',
            'Reporter Email',
            'OPD',
            'PIC',
            'Created At',
            'SLA Due At',
            'Resolved At',
        ]);

        $this->complaintsReport($filters)->each(function (Complaint $complaint) use ($handle): void {
            fputcsv($handle, [
                $complaint->ticket_number,
                $complaint->status?->name,
                $complaint->category?->name,
                $complaint->subCategory?->name,
                $complaint->priority?->name,
                $complaint->title,
                $complaint->reporter_name,
                $complaint->reporter_phone,
                $complaint->reporter_email,
                $complaint->currentOpd?->name,
                $complaint->currentPic?->name,
                $complaint->created_at?->toDateTimeString(),
                $complaint->sla_due_at?->toDateTimeString(),
                $complaint->resolved_at?->toDateTimeString(),
            ]);
        });

        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return $contents === false ? '' : $contents;
    }
}
