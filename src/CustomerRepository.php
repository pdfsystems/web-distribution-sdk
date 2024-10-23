<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Customer;
use Pdfsystems\WebDistributionSdk\Dtos\Rep;
use Pdfsystems\WebDistributionSdk\Dtos\ResaleCertificate;
use Pdfsystems\WebDistributionSdk\Dtos\ShipTo;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Rpungello\SdkClient\SdkClient;
use RuntimeException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use SplFileInfo;

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
     * @param Company $company
     * @param string $query
     * @param int|null $repId
     * @param bool $masterRep
     * @param int $maxResults
     * @return Customer[]
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function search(Company $company, string $query, ?int $repId = null, bool $masterRep = false, int $maxResults = 50): array
    {
        return $this->client->getDtoArray('api/customer/search', Customer::class, [
            'company_id' => $company->id,
            'q' => $query,
            'rep_id' => $repId,
            'master' => $masterRep,
            'count' => $maxResults,
            'with' => ['employees.emailAddress', 'shipTos.address'],
        ]);
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function findById(int $id): Customer
    {
        return $this->client->getDto('api/customer/' . $id, Customer::class, [
            'with' => ['customFields'],
        ]);
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
            $this->update($customer);
        }

        return $this->findById($response->id);
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
    public function update(Customer $customer): Customer
    {
        return $this->client->putDto("api/customer/$customer->id", $customer);
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     * @throws RuntimeException
     */
    public function addResaleCertificate(Customer $customer, ResaleCertificate $certificate, ?SplFileInfo $document = null): ResaleCertificate
    {
        $request = $certificate->toArray();
        $request['state_id'] = $this->client->states()->find($customer->country, $certificate->state->code)->id;
        $request['customer'] = $customer->id;
        if (! empty($request['expiration_date'])) {
            $request['expiration_date'] = $request['expiration_date']->format('Y-m-d');
        }

        if (empty($document)) {
            return $this->client->postJsonAsDto('api/resale-certificate', $request, ResaleCertificate::class);
        } else {
            $body = SdkClient::convertJsonToMultipart($request);
            $body[] = [
                'name' => 'file',
                'contents' => $document->openFile(),
                'filename' => $document->getFilename(),
            ];

            return $this->client->postMultipartAsDto('api/resale-certificate', $body, ResaleCertificate::class);
        }
    }

    public function addShipTo(Customer $customer, ShipTo $shipTo): ShipTo
    {
        return $this->client->postJsonAsDto('api/ship-to', array_merge([
            'customer' => $customer->id,
            'country_id' => $shipTo->country?->id,
        ], $shipTo->toArray()), ShipTo::class);
    }
}
