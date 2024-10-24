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
    /** @var string[] */
    public ?array $options = [];

    public function toArray(): array
    {
        $array = parent::toArray();

        switch ($this->type) {
            case 2:
                $array['current'] = intval($this->current);
                break;
            case 3:
                $array['current'] = floatval($this->current);
                break;
            case 4:
            case 5:
                $array['current'] = boolval($this->current);
                break;
        }

        return $array;
    }
}
