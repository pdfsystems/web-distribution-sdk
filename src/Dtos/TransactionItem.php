<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

class TransactionItem extends AbstractDto
{
    public int $id;

    public Product $item;

    public float $quantity_ordered;

    public float $customer_quantity_ordered;
}
