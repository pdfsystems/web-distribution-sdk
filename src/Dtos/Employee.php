<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

class Employee extends AbstractDto
{
    public ?int $id;

    public string $name;

    public ?string $email_address;

    public ?Country $country;

    public ?string $notes;

    public bool $receive_accounting = false;

    public bool $receive_marketing = false;

    public bool $customer_service = false;
}
