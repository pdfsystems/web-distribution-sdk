<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;

class Customer extends AbstractDto
{
    public ?int $id;

    public ?string $customer_number;

    public string $name;

    public ?Country $country;

    public ?Address $primary_address;

    public ?Rep $rep;

    /**
     * @var Employee[]
     */
    #[CastWith(ArrayCaster::class, Employee::class)]
    public array $employees = [];
}
