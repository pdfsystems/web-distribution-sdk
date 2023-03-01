<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use DateTimeImmutable;
use Spatie\DataTransferObject\Attributes\MapFrom;

class IncomingPurchaseOrder extends AbstractDto
{
    public int $id;

    public int $purchase_order_id;

    public int $purchase_order_number;

    public ?DateTimeImmutable $ship_date;

    public ?string $comment;

    public ?string $confirmation_comment;

    public ?string $internal_notes;

    public string $warehouse_name;

    public string $warehouse_code;

    #[MapFrom('on_order')]
    public float $quantity_on_order;

    #[MapFrom('allocated')]
    public float $quantity_allocated;
}
