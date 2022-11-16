<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Casters\ArrayCaster;

class FreightResponse extends AbstractDto
{
    public int $packages;

    #[MapFrom('packing_charge')]
    public int $packingCharge;

    public int $weight;

    #[CastWith(ArrayCaster::class, itemType: FreightRate::class)]
    public array $rates;
}
