<?php

namespace App\Domain\Complaint\Events;

use App\Domain\Complaint\Models\Complaint;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComplaintCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly Complaint $complaint) {}
}
