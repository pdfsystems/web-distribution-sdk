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
        $shouldUpdate = $this->shouldRunUpdateAfterCreation($customer);

        if (empty($rep->id)) {
            $rep = $this->client->reps()->findByCode($company, $rep->rep_code);
        }
        $body['rep'] = $rep->id;

        $response = $this->client->postJsonAsDto('api/customer', $body, Customer::class);
        $customer->id = $response->id;

        foreach ($customer->employees as $employee) {
            $employee->country = $response->country;
            $this->client->employees()->create('customer', $response->id, $employee);
        }

        if ($shouldUpdate) {
            return $this->update($customer);
        } else {
            return $this->findById($response->id);
        }
    }

    private function shouldRunUpdateAfterCreation(Customer $customer): bool
    {
        if (! empty($customer->primary_address)) {
            return true;
        } elseif (! empty($customer->primary_phone_number)) {
            return true;
        }

        return false;
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    private function update(Customer $customer): Customer
    {
        return $this->client->putDto("api/customer/$customer->id", $customer);
    }
}
