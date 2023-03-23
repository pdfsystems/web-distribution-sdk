<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Spatie\DataTransferObject\Attributes\MapFrom;

class SampleInventory extends AbstractDto
{
    public int $item_id;

    #[MapFrom('on_hand')]
    public int $quantity_on_hand;
}
