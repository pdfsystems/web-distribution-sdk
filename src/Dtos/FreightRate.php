<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Spatie\DataTransferObject\Attributes\MapFrom;

class FreightRate extends AbstractDto
{
    #[MapFrom("service_id")]
    public int $serviceId;

    #[MapFrom("service")]
    public string $serviceName;

    public float $rate;
}
