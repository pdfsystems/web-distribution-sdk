<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

class ApiKey extends AbstractDto
{
    public int $id;

    public string $name;

    public ?string $description;

    public string $token;
}
