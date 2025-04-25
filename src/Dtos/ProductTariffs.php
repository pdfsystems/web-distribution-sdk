<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Pdfsystems\WebDistributionSdk\Dtos\AbstractDto;

class ProductTariffs extends AbstractDto
{
    public float $percent_price = 0.0;
    public float $percent_cost = 0.0;
    /** @var string[]  */
    public array $countries = [];
}
