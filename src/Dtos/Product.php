<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use DateTimeImmutable;
use Pdfsystems\WebDistributionSdk\Contracts\HasCustomFields;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Casters\ArrayCaster;

class Product extends AbstractDto implements HasCustomFields
{
    public ?int $id;

    public ?int $style_id;

    public ?string $item_number;

    #[MapFrom('style.name')]
    public ?string $style_name;

    public ?string $color_name;

    #[MapFrom('style.product_category_code.name')]
    public ?string $category;

    #[MapFrom('style.content')]
    public ?string $content;

    #[MapFrom('style.width')]
    public ?string $width;

    #[MapFrom('style.repeat')]
    public ?string $repeat;

    #[MapFrom('style.selling_unit.name')]
    public ?string $selling_unit;

    #[MapFrom('style.mill_unit.name')]
    public ?string $vendor_unit;

    #[MapFrom('style.primary_price.wholesale_price')]
    public ?float $price;

    #[MapFrom('style.inventoried')]
    public bool $inventoried = true;

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

    public function getAllCustomFields(): array
    {
        return array_merge($this->custom_fields_item, $this->custom_fields_style);
    }

    public function setCustomFields(array $customFields): void
    {
        $this->custom_fields_item = $customFields;
        $this->custom_fields_style = $customFields;
    }
}
