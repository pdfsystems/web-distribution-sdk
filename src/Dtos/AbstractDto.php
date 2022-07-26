<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

abstract class AbstractDto extends DataTransferObject
{
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
