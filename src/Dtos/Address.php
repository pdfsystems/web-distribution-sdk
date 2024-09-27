<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

class Address extends AbstractDto
{
    public ?int $id;

    public ?string $name;

    public ?string $company_name;

    public ?string $attention;

    public ?string $street;

    public ?string $street2;

    public ?string $city;

    public ?State $state;

    public ?string $postal_code;

    public ?Country $country;

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'state_id' => $this->state?->id,
            'country_id' => $this->country?->id,
        ]);
    }

}
