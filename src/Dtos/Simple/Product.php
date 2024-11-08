<?php

namespace Pdfsystems\WebDistributionSdk\Dtos\Simple;

use Pdfsystems\WebDistributionSdk\Dtos\AbstractDto;

class Product extends AbstractDto
{
    public ?int $id;
    public ?string $sku;
    public ?string $style;
    public ?string $color;
    public ?string $width;
    public ?string $repeat;
    public ?string $content;
    public ?string $tests;
    public ?Inventory $inventory;
    public ?Inventory $sample_inventory;
    public ?Inventory $vendor_inventory;
    public ?string $image_url;
    public ?string $label_message;
    public ?string $product_category;
    public ?string $origin_country;
    public ?string $discontinued;
    public ?float $price;
    public ?float $discounted_price;
}
