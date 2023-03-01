<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Exceptions\ForbiddenException;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Pdfsystems\WebDistributionSdk\Exceptions\ResponseException;
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

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     * @throws ResponseException
     */
    public function findById(int $id): Company
    {
        $requestOptions = [
            'with' => [
                'lines',
                'currency',
            ],
        ];

        try {
            $response = $this->client->getJson("api/company/$id", $requestOptions);

            return new Company($response);
        } catch (RequestException $e) {
            if ($e->getCode() === 403) {
                throw new ForbiddenException("You do not have permission to access company with id $id");
            } elseif ($e->getCode() === 404 || $e->getCode() === 500) {
                throw new NotFoundException("Company with id $id not found");
            } else {
                throw $e;
            }
        }
    }
}
