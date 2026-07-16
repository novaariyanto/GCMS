<?php

namespace App\Application\Workflow\Actions;

use App\Application\Workflow\DTOs\TransitionComplaintData;
use App\Application\Workflow\Services\WorkflowEngine;
use App\Domain\Complaint\Models\Complaint;

class TransitionComplaintAction
{
    public function __construct(private readonly WorkflowEngine $workflow) {}

    public function execute(TransitionComplaintData $data): Complaint
    {
        return $this->workflow->transition($data);
    }

    public function __invoke(TransitionComplaintData $data): Complaint
    {
        return $this->execute($data);
    }
}
