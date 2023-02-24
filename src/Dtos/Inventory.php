<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Spatie\DataTransferObject\Attributes\MapFrom;

class Inventory extends AbstractDto
{
    public int $id;

    #[MapFrom('item.item_number')]
    public string $item_number;

    #[MapFrom('item.style.name')]
    public string $style_name;

    #[MapFrom('item.color_name')]
    public string $color_name;

    public string $lot;

    public string $piece;

    public ?string $warehouse_location;

    public bool $active;

    public bool $approved;

    public bool $pre_receipt;

    public bool $seconds;

    public ?string $comment;

    #[MapFrom('mill_piece')]
    public ?string $vendor_piece;

    public float $quantity_on_hand;

    public float $quantity_available;
}
