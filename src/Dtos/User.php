<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

class User extends AbstractDto
{
    public int $id;

    public string $initials;

    public string $name;

    public string $email;

    public ?Company $default_company;
}
