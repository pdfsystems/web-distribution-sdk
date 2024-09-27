<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

class Rep extends AbstractDto
{
    public ?int $id = null;

    public ?string $name;

    public ?string $rep_code;

    public ?Rep $master_rep;

    public ?Rep $national_rep;
}
