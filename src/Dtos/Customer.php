<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Pdfsystems\WebDistributionSdk\Contracts\HasCustomFields;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;

class Customer extends AbstractDto implements HasCustomFields
{
    public ?int $id;

    public ?string $customer_number;

    public ?string $name;

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

    /**
     * @var CustomField[]
     */
    #[CastWith(ArrayCaster::class, CustomField::class)]
    public array $custom_fields = [];

    public ?Carrier $default_carrier;
    public ?ShippingService $default_shipping_service;
    public ?Carrier $default_sample_carrier;
    public ?ShippingService $default_sample_shipping_service;

    public function getAllCustomFields(): array
    {
        return $this->custom_fields;
    }

    public function setCustomFields(array $customFields): void
    {
        $this->custom_fields = $customFields;
    }
}
