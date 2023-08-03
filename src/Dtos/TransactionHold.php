<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use DateTimeImmutable;
use Spatie\DataTransferObject\Attributes\MapFrom;

class TransactionHold extends AbstractDto
{
    #[MapFrom('hold.name')]
    public string $name;

    #[MapFrom('hold.abbreviation')]
    public ?string $abbreviation;

    #[MapFrom('hold.internal_name')]
    public ?string $internal_name;

    public ?string $release_comment;

    public ?DateTimeImmutable $released_at;
}
