<?php

namespace Pdfsystems\WebDistributionSdk\Requests;

use Symfony\Component\Validator\Constraints as Assert;

class FreightRequest extends Request
{
    #[Assert\NotBlank]
    public string $postalCode;

    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(1)]
    public float $quantity;

    #[Assert\NotBlank]
    #[Assert\Length(2)]
    public string $country;

    public function __construct(string $postalCode, float $quantity = 1, string $country = 'US')
    {
        parent::__construct();
        $this->postalCode = $postalCode;
        $this->quantity = $quantity;
        $this->country = $country;
    }
}
