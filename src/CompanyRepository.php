<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CompanyRepository extends AbstractRepository
{
    /**
     * @return array
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function list(): array
    {
        return array_map(function (array $company): Company {
            return new Company($company);
        }, $this->client->getJson('api/company', ['with' => ['lines', 'currency']]));
    }
}
