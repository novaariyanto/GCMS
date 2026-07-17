<?php

namespace App\Domain\Complaint\Events;

use App\Domain\Complaint\Models\Complaint;
use App\Domain\Workflow\Models\WorkflowTransition;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComplaintTransitioned
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Complaint $complaint,
        public readonly WorkflowTransition $transition,
        public readonly ?string $actorId = null,
    ) {}
}
