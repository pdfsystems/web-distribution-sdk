<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;

class User extends AbstractDto
{
    public int $id;

    public string $initials;

    public string $name;

    public string $email;

    public ?Company $default_company;

    #[CastWith(ArrayCaster::class, ApiKey::class)]
    public array $api_keys = [];
}
