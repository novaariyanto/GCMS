<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Workflow\Models\Workflow;
use App\Domain\Workflow\Models\WorkflowTransition;
use Illuminate\Database\Eloquent\Collection;

class WorkflowRepository extends BaseRepository
{
    public function __construct(Workflow $model)
    {
        parent::__construct($model);
    }

    public function findDefault(): ?Workflow
    {
        return $this->model
            ->newQuery()
            ->with(['nodes', 'transitions'])
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();
    }

    public function findForCategory(?string $categoryId, ?string $subCategoryId = null): ?Workflow
    {
        if ($subCategoryId !== null) {
            $workflow = $this->model
                ->newQuery()
                ->with(['nodes', 'transitions'])
                ->where('is_active', true)
                ->where('sub_category_id', $subCategoryId)
                ->first();

            if ($workflow instanceof Workflow) {
                return $workflow;
            }
        }

        if ($categoryId === null) {
            return null;
        }

        return $this->model
            ->newQuery()
            ->with(['nodes', 'transitions'])
            ->where('is_active', true)
            ->where('category_id', $categoryId)
            ->whereNull('sub_category_id')
            ->first();
    }

    /**
     * @return Collection<int, WorkflowTransition>
     */
    public function getTransitionsFromNode(string $nodeId): Collection
    {
        return WorkflowTransition::query()
            ->with(['action', 'toNode', 'targetStatus'])
            ->where('from_node_id', $nodeId)
            ->where('is_active', true)
            ->whereHas('action', fn ($query) => $query->where('is_active', true))
            ->get();
    }
}
