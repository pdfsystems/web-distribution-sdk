<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Rep;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class RepRepository extends AbstractRepository
{
    /**
     * @param Company $company
     * @return Rep[]
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function list(Company $company): array
    {
        return $this->client->getDtoArray('api/rep', Rep::class, [
            'company' => $company->id,
            'sorting[name]' => 'asc',
        ]);
    }
}
