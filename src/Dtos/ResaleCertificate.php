<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use DateTimeImmutable;

class ResaleCertificate extends AbstractDto
{
    public ?int $id;

    public ?State $state;

    public ?string $resale_number;

    public ?DateTimeImmutable $expiration_date;
}
