<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use DateTimeImmutable;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Casters\ArrayCaster;

class TransactionItem extends AbstractDto
{
    public int $id;

    public Product $item;

    public float $quantity_ordered;

    public float $customer_quantity_ordered;

    public float $price;

    #[MapFrom('v_extension')]
    public float $extension;

    #[MapFrom('allocated_pieces')]
    #[CastWith(ArrayCaster::class, itemType: Allocation::class)]
    public array $pieces;

    public ?DateTimeImmutable $job_item_closed_date = null;
}
