<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Workflow\Models\Workflow;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\WorkflowResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WorkflowController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $workflows = Workflow::query()
            ->with(['category', 'subCategory'])
            ->where('is_active', true)
            ->latest()
            ->paginate((int) $request->integer('per_page', 15));

        return WorkflowResource::collection($workflows);
    }

    public function show(string $id): WorkflowResource
    {
        $workflow = Workflow::query()
            ->with(['category', 'subCategory', 'nodes', 'transitions.fromNode', 'transitions.toNode', 'transitions.action', 'transitions.targetStatus'])
            ->findOrFail($id);

        return new WorkflowResource($workflow);
    }
}
