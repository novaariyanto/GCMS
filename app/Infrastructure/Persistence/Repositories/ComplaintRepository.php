<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Complaint\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ComplaintRepository extends BaseRepository
{
    public function __construct(Complaint $model)
    {
        parent::__construct($model);
    }

    public function findByTicket(string $ticketNumber): ?Complaint
    {
        return $this->model
            ->newQuery()
            ->with(['category', 'subCategory', 'priority', 'status', 'currentNode'])
            ->where('ticket_number', $ticketNumber)
            ->first();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<Complaint>
     */
    public function search(array $filters): Builder
    {
        return $this->model
            ->newQuery()
            ->with(['category', 'subCategory', 'priority', 'status', 'currentOpd', 'currentPic'])
            ->when($filters['status_id'] ?? null, fn (Builder $query, string $statusId) => $query->where('status_id', $statusId))
            ->when($filters['status_code'] ?? null, function (Builder $query, string $statusCode): void {
                $query->whereHas('status', fn (Builder $statusQuery) => $statusQuery->where('code', $statusCode));
            })
            ->when($filters['category_id'] ?? null, fn (Builder $query, string $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['sub_category_id'] ?? null, fn (Builder $query, string $subCategoryId) => $query->where('sub_category_id', $subCategoryId))
            ->when($filters['priority_id'] ?? null, fn (Builder $query, string $priorityId) => $query->where('priority_id', $priorityId))
            ->when($filters['district_id'] ?? null, fn (Builder $query, string $districtId) => $query->where('district_id', $districtId))
            ->when($filters['village_id'] ?? null, fn (Builder $query, string $villageId) => $query->where('village_id', $villageId))
            ->when($filters['current_opd_id'] ?? null, fn (Builder $query, string $opdId) => $query->where('current_opd_id', $opdId))
            ->when($filters['current_pic_id'] ?? null, fn (Builder $query, string $picId) => $query->where('current_pic_id', $picId))
            ->when($filters['reporter_id'] ?? null, fn (Builder $query, string $reporterId) => $query->where('reporter_id', $reporterId))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $dateFrom) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $dateTo) => $query->whereDate('created_at', '<=', $dateTo))
            ->when($filters['sla_overdue'] ?? false, fn (Builder $query) => $query->whereNotNull('sla_due_at')->where('sla_due_at', '<', now())->whereNull('resolved_at'))
            ->when($filters['q'] ?? $filters['search'] ?? null, function (Builder $query, string $term): void {
                $query->where(function (Builder $nested) use ($term): void {
                    $nested
                        ->where('ticket_number', 'like', "%{$term}%")
                        ->orWhere('title', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhere('reporter_name', 'like', "%{$term}%");
                });
            })
            ->latest();
    }

    /**
     * @return Builder<Complaint>
     */
    public function forPic(User|string $pic): Builder
    {
        $picId = $pic instanceof User ? $pic->id : $pic;

        return $this->model
            ->newQuery()
            ->with(['category', 'priority', 'status', 'currentNode'])
            ->where('current_pic_id', $picId)
            ->latest();
    }

    /**
     * @return Builder<Complaint>
     */
    public function forOpd(string $opdId): Builder
    {
        return $this->model
            ->newQuery()
            ->with(['category', 'priority', 'status', 'currentNode', 'currentPic'])
            ->where('current_opd_id', $opdId)
            ->latest();
    }
}
