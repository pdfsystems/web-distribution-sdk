<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Employee;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class EmployeeRepository extends AbstractRepository
{
    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function findById(int $id): Employee
    {
        return $this->client->getDto('api/employee/' . $id, Employee::class);
    }

    /**
     * @throws GuzzleException
     */
    public function create(string $modelKey, int $modelId, Employee $employee): Employee
    {
        $request = $employee->toArray();
        $request[$modelKey] = $modelId;

        if (empty($request['country_id'])) {
            $request['country_id'] = $employee->country?->id;
        }

        return $this->client->postJsonAsDto("api/employee", $request, Employee::class);
    }
}
