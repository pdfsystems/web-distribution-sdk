<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Pdfsystems\WebDistributionSdk\Dtos\AbstractDto;

class SampleTransactionItem extends AbstractDto
{
    public ?int $id;
    public ?Product $item;
    public ?int $quantity_ordered;
    public ?Code $sample_type;
    public ?string $comments;
}
