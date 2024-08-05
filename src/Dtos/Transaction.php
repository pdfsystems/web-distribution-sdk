<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use DateTimeImmutable;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Casters\ArrayCaster;

class Transaction extends AbstractDto
{
    public ?int $id;

    public ?DateTimeImmutable $created_at;

    #[MapFrom('full_transaction_number')]
    public ?string $transaction_number;

    public ?Rep $rep1;

    public ?Rep $rep2;

    public ?Customer $customer;

    public ?Customer $specifier;

    public ?string $ship_to_name;

    public ?string $ship_to_street;

    public ?string $ship_to_street2;

    public ?string $ship_to_city;

    public ?State $ship_to_state;

    public ?Country $ship_to_country;

    public ?string $ship_to_postal_code;

    public ?string $ship_to_attention;

    public ?string $ship_to_phone;

    public ?string $ship_to_fax;

    public ?string $ship_to_email;

    public ?string $client_auth_key = null;

    public ?GroupDescription $group_description;

    #[CastWith(ArrayCaster::class, itemType: TransactionItem::class)]
    public ?array $items;

    #[CastWith(ArrayCaster::class, itemType: TransactionHold::class)]
    public ?array $holds = [];

    public ?DateTimeImmutable $job_closed_on = null;

    public array $custom_fields = [];

}
