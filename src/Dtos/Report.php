<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use DateTimeImmutable;

class Report extends AbstractDto
{
    public ?int $id;

    public string $name;

    public ?DateTimeImmutable $started_at;

    public ?DateTimeImmutable $completed_at;
}
