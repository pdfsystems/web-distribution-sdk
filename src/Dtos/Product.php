<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use DateTimeImmutable;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Casters\ArrayCaster;

class Product extends AbstractDto
{
    public int $id;

    public int $style_id;

    public string $item_number;

    #[MapFrom('style.name')]
    public string $style_name;

    public ?string $color_name;

    #[MapFrom('style.product_category_code.name')]
    public string $category;

    #[MapFrom('style.content')]
    public ?string $content;

    #[MapFrom('style.width')]
    public ?string $width;

    #[MapFrom('style.repeat')]
    public ?string $repeat;

    #[MapFrom('style.selling_unit.name')]
    public string $selling_unit;

    #[MapFrom('style.mill_unit.name')]
    public string $vendor_unit;

    #[MapFrom('style.primary_price.wholesale_price')]
    public ?float $price;

    public ?string $warehouse_location;

    #[MapFrom('sample_warehouse_location')]
    public ?string $warehouse_location_sample;

    #[MapFrom('date_discontinued')]
    public ?DateTimeImmutable $discontinued_date;

    #[MapFrom('discontinue_code.name')]
    public ?string $discontinued_reason;

    public ?DateTimeImmutable $deleted_at;

    public ?Company $company;

    public ?Line $line;

    #[MapFrom('style.vendor')]
    public ?Vendor $vendor;

    public ?Book $primary_book;

    /**
     * @var CustomField[]
     */
    #[CastWith(ArrayCaster::class, CustomField::class)]
    #[MapFrom('custom_fields')]
    public array $custom_fields_item = [];

    /**
     * @var CustomField[]
     */
    #[CastWith(ArrayCaster::class, CustomField::class)]
    #[MapFrom('style.custom_fields')]
    public array $custom_fields_style = [];
}
