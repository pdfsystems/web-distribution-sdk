<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\AbstractRepository;
use Pdfsystems\WebDistributionSdk\Dtos\Country;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CountryRepository extends AbstractRepository
{
    /**
     * @throws UnknownProperties
     */
    public function list(): array
    {
        return $this->client->getDtoArray('api/country', Country::class);
    }

    /**
     * @throws UnknownProperties
     */
    public function find(string $code): Country
    {
        $countries = $this->list();

        /**
         * @var Country $country
         */
        foreach ($countries as $country) {
            if ($country->code === $code) {
                return $country;
            }
        }

        throw new NotFoundException("No country found with code $code");
    }
}
