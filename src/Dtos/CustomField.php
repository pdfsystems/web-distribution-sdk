<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

class CustomField extends AbstractDto
{
    public ?int $type;
    public ?string $key;
    public ?string $name;
    public ?string $current;
    public ?string $formatted;
    public ?string $default;
    public bool $display_on_transaction = false;
    public bool $display_on_request_account = false;
    public bool $ordertrack_visible = false;
    public bool $ordertrack_editable = false;
    public bool $ordertrack_filterable = false;
}
