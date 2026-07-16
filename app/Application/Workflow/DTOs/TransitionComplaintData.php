<?php

namespace App\Application\Workflow\DTOs;

use App\Domain\Shared\DTOs\BaseDto;

class TransitionComplaintData extends BaseDto
{
    public ?string $complaint_id = null;

    public ?string $action_code = null;

    public ?string $remark = null;

    public ?string $to_user_id = null;

    public ?string $attachment_path = null;
}
