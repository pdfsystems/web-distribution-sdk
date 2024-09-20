<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Customer;
use Pdfsystems\WebDistributionSdk\Dtos\Rep;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CustomerRepository extends AbstractRepository
{
    /**
     * @param Company $company
     * @return Customer[]
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function list(Company $company): array
    {
        return $this->client->getDtoArray('api/customer', Customer::class, [
            'company' => $company->id,
            'sorting[name]' => 'asc',
            'with' => ['country', 'primaryAddress'],
        ]);
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function findById(int $id): Customer
    {
        return $this->client->getDto('api/customer/' . $id, Customer::class);
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function findByCustomerNumber(Company $company, string $customerNumber): Customer
    {
        /** @var Customer[] $customers */
        $customers = $this->client->getDtoArray('api/customer', Customer::class, [
            'company' => $company->id,
            'search' => "#$customerNumber",
            'with' => ['country', 'primaryAddress', 'employees.emailAddress'],
        ]);

        foreach ($customers as $customer) {
            if ($customer->customer_number === $customerNumber) {
                return $customer;
            }
        }

        throw new NotFoundException("No customer found with customer number $customerNumber");
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function create(Company $company, Rep $rep, Customer $customer): Customer
    {
        $body = $customer->toArray();

        if (empty($rep->id)) {
            $rep = $this->client->reps()->findByCode($company, $rep->rep_code);
        }
        $body['rep'] = $rep->id;

        $response = $this->client->postJsonAsDto('api/customer', $body, Customer::class);

        foreach ($customer->employees as $employee) {
            $employee->country = $response->country;
            $this->client->employees()->create('customer', $response->id, $employee);
        }

        return $response;
    }
}
