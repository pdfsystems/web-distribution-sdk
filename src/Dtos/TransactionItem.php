<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Spatie\DataTransferObject\Attributes\MapFrom;

class TransactionItem extends AbstractDto
{
    public int $id;

    public Product $item;

    public float $quantity_ordered;

    public float $customer_quantity_ordered;

    public float $price;

    #[MapFrom('v_extension')]
    public float $extension;
}
