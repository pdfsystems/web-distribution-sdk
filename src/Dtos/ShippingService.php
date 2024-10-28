<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

class ShippingService extends AbstractDto
{
    public ?int $id;
    public ?string $name;
    public ?Carrier $carrier;
}
