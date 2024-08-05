<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Line;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class LineRepository extends AbstractRepository
{
    /**
     * @param Company $company
     * @return Line[]
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function list(Company $company): array
    {
        return $this->client->getDtoArray('api/line', Line::class, [
            'company' => $company->id,
            'sorting[name]' => 'asc',
        ]);
    }
}
