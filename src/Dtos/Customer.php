<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

class Customer extends AbstractDto
{
    public int $id;

    public string $customer_number;

    public string $name;

    public Country $country;

    public ?Address $primary_address;
}
