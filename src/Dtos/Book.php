<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

class Book extends AbstractDto
{
    public string $item_number;
    public string $name;
    public ?string $comment;
    public bool $new;
    public bool $limited_stock;
    public bool $inventoried;
    public ?int $weight;
    public ?int $shipping_weight;
    public ?string $harmonized_code;
}
