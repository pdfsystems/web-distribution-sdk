<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

class Code extends AbstractDto
{
    public ?int $id;

    public int $type_id;

    public bool $editable = true;

    public bool $hidden = false;

    public string $name;

    public ?string $abbrevation;
}
