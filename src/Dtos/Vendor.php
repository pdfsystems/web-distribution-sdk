<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

class Vendor extends AbstractDto
{
    public ?int $id;

    public string $name;

    public ?string $api_name;

    public bool $cut_yardage = false;

    public bool $foreign = false;

    public bool $work_order = false;

    public bool $production_vendor = false;

    public bool $open_line = false;

    public bool $drop_ship = false;

    public int $delivery_days = 1;
}
