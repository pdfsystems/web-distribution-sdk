<?php

namespace Pdfsystems\WebDistributionSdk\Dtos\Simple;

use Pdfsystems\WebDistributionSdk\Dtos\AbstractDto;

class Inventory extends AbstractDto
{
    public float $on_hand = 0;
    public float $available = 0;
}
