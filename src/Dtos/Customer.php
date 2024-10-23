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

    public ?string $primary_phone_number;

    public ?Rep $rep;

    #[CastWith(ArrayCaster::class, ShipTo::class)]
    public array $ship_tos = [];

    /**
     * @var ResaleCertificate[]
     */
    #[CastWith(ArrayCaster::class, ResaleCertificate::class)]
    public array $resale_certificates = [];

    /**
     * @var Employee[]
     */
    #[CastWith(ArrayCaster::class, Employee::class)]
    public array $employees = [];

    #[CastWith(ArrayCaster::class, CustomField::class)]
    public array $custom_fields = [];
}
