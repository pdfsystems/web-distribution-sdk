<?php

namespace Pdfsystems\WebDistributionSdk;

use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\CustomField;
use Pdfsystems\WebDistributionSdk\Exceptions\ForbiddenException;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Pdfsystems\WebDistributionSdk\Exceptions\ResponseException;
use Rpungello\SdkClient\Exceptions\RequestException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CompanyRepository extends AbstractRepository
{
    /**
     * @return array
     * @throws UnknownProperties
     */
    public function list(): array
    {
        return array_map(function (array $company): Company {
            return new Company($company);
        }, $this->client->getJson('api/company', ['with' => ['defaultLine.defaultSampleTypeCode', 'lines', 'currency']]));
    }

    /**
     * @throws UnknownProperties
     * @throws ResponseException
     */
    public function findById(int $id): Company
    {
        $requestOptions = [
            'with' => [
                'currency',
                'defaultLine.defaultSampleTypeCode',
                'lines',
            ],
        ];

        try {
            $response = $this->client->getJson("api/company/$id", $requestOptions);

            return new Company($response);
        } catch (RequestException $e) {
            if ($e->getHttpStatusCode() === 403) {
                throw new ForbiddenException("You do not have permission to access company with id $id");
            } elseif ($e->getHttpStatusCode() === 404 || $e->getHttpStatusCode() === 500) {
                throw new NotFoundException("Company with id $id not found");
            } else {
                throw $e;
            }
        }
    }

    /**
     * @throws UnknownProperties
     */
    public function customFields(Company $company, string $resourceClass): array
    {
        try {
            return $this->client->getDtoArray('api/custom-field', CustomField::class, [
                'company' => $company->id,
                'resource_class' => $resourceClass,
            ]);
        } catch (RequestException $e) {
            if ($e->getHttpStatusCode() === 403) {
                throw new ForbiddenException("You do not have permission to access company with id $company->id");
            } else {
                throw $e;
            }
        }
    }
}
