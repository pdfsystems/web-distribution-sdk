<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Pdfsystems\WebDistributionSdk\Dtos\AbstractDto;

class TransactionFreightResponse extends AbstractDto
{
    public float $rate = 0.0;

    public int $packages = 1;
}
