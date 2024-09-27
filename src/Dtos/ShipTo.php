<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

class ShipTo extends AbstractDto
{
    public ?int $id;

    public ?string $name;

    public ?Country $country;

    public ?string $fax;

    public ?Address $address;

}
