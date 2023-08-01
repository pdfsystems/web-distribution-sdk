<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Spatie\DataTransferObject\Attributes\MapFrom;

class Allocation extends AbstractDto
{
    public int $id;

    #[MapFrom('piece.lot')]
    public string $lot;

    #[MapFrom('piece.piece')]
    public string $piece;

    #[MapFrom('piece.warehouse')]
    public Warehouse $warehouse;

    public float $quantity;
}
