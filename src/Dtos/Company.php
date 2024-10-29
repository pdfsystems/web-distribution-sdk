<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;

class Company extends AbstractDto
{
    public int $id;

    public string $name;

    public ?string $ordertrack_username;

    public ?Currency $currency;

    public ?Country $country;

    public ?Line $default_line;

    #[CastWith(ArrayCaster::class, Line::class)]
    public array $lines = [];
}
