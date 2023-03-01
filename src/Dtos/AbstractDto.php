<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use DateTimeImmutable;
use Pdfsystems\WebDistributionSdk\Casts\DateCaster;
use Spatie\DataTransferObject\Attributes\DefaultCast;
use Spatie\DataTransferObject\DataTransferObject;

#[
    DefaultCast(DateTimeImmutable::class, DateCaster::class)
]
abstract class AbstractDto extends DataTransferObject
{
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
