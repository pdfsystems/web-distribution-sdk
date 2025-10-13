<?php

namespace Pdfsystems\WebDistributionSdk;

use Pdfsystems\WebDistributionSdk\Dtos\Employee;
use Pdfsystems\WebDistributionSdk\Exceptions\ValidationException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class EmployeeRepository extends AbstractRepository
{
    /**
     * @throws UnknownProperties
     */
    public function findById(int $id): Employee
    {
        return $this->client->getDto('api/employee/' . $id, Employee::class);
    }

    /**
     * @throws ValidationException
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
