<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;

class SampleTransaction extends AbstractDto
{
    public ?int $id;
    public ?string $sample_transaction_number;
    public ?string $customer_email;
    public ?Customer $customer;
    public ?Line $line;
    public ?string $ordered_by;
    public ?string $project_name;
    public ?string $rep_email;
    public ?string $ship_to_attention;
    public ?string $source_key;
    public ?int $sample_usage;
    public ?string $ship_to_name;
    public ?string $ship_to_street;
    public ?string $ship_to_street2;
    public ?string $ship_to_city;
    public ?State $ship_to_state;
    public ?Country $ship_to_country;
    public ?string $ship_to_postal_code;
    public ?Carrier $carrier;
    public ?ShippingService $shipping_service;

    /**
     * @var SampleTransactionItem[]
     */
    #[CastWith(ArrayCaster::class, itemType: SampleTransactionItem::class)]
    public ?array $items;
}
