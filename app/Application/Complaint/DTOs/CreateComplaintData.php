<?php

namespace App\Application\Complaint\DTOs;

use App\Domain\Shared\DTOs\BaseDto;

class CreateComplaintData extends BaseDto
{
    public ?string $category_id = null;

    public ?string $sub_category_id = null;

    public ?string $priority_id = null;

    public ?string $title = null;

    public ?string $description = null;

    public ?string $address = null;

    public null|float|string $latitude = null;

    public null|float|string $longitude = null;

    public ?string $reporter_name = null;

    public ?string $reporter_phone = null;

    public ?string $reporter_email = null;

    public bool $is_anonymous = false;

    public ?string $district_id = null;

    public ?string $village_id = null;

    public ?string $reporter_id = null;
}
