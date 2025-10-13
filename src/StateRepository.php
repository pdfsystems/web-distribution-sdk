<?php

namespace Pdfsystems\WebDistributionSdk;

use Pdfsystems\WebDistributionSdk\Dtos\Country;
use Pdfsystems\WebDistributionSdk\Dtos\State;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class StateRepository extends AbstractRepository
{
    /**
     * @throws UnknownProperties
     */
    public function list(Country $country): array
    {
        return $this->client->getDtoArray('api/state', State::class, [
            'country' => $country->id,
        ]);
    }

    /**
     * @throws UnknownProperties
     */
    public function find(Country $country, string $code): State
    {
        $states = $this->list($country);

        /**
         * @var State $state
         */
        foreach ($states as $state) {
            if ($state->code === $code) {
                return $state;
            }
        }

        throw new NotFoundException("No state found with code $code");
    }
}
