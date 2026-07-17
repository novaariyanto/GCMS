<?php

namespace App\Application\Complaint\Actions;

use App\Application\Complaint\DTOs\CreateComplaintData;
use App\Application\Complaint\Services\ComplaintService;
use App\Domain\Complaint\Models\Complaint;

class CreateComplaintAction
{
    public function __construct(private readonly ComplaintService $complaints) {}

    public function execute(CreateComplaintData $data): Complaint
    {
        return $this->complaints->create($data);
    }

    public function __invoke(CreateComplaintData $data): Complaint
    {
        return $this->execute($data);
    }
}
