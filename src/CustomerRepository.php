<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Customer;
use Pdfsystems\WebDistributionSdk\Dtos\Rep;
use Pdfsystems\WebDistributionSdk\Dtos\ResaleCertificate;
use Pdfsystems\WebDistributionSdk\Dtos\ShipTo;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Pdfsystems\WebDistributionSdk\Exceptions\ValidationException;
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
            'with' => [
                'country',
                'defaultCarrier',
                'defaultSampleCarrier',
                'defaultSampleShippingService',
                'defaultShippingService',
                'primaryAddress',
            ],
        ]);
    }

    /**
     * @param Company $company
     * @param callable $callback
     * @param array $options
     * @param int $perPage
     * @return void
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function iterate(Company $company, callable $callback, array $options = [], int $perPage = 128): void
    {
        $requestOptions = [
            'company' => $company->id,
            'with' => [
                'country',
                'defaultCarrier',
                'defaultSampleCarrier',
                'defaultSampleShippingService',
                'defaultShippingService',
                'primaryAddress',
            ],
            'count' => $perPage,
            'page' => 1,
        ];

        do {
            $response = $this->client->getJson('api/customer', $requestOptions);

            foreach ($response as $customer) {
                $callback(new Customer($customer));
            }

            $requestOptions['page']++;
        } while (! empty($response));
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
            'with' => [
                'employees.emailAddress',
                'shipTos.address',
                'defaultCarrier',
                'defaultSampleCarrier',
                'defaultSampleShippingService',
                'defaultShippingService',
            ],
        ]);
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function findById(int $id): Customer
    {
        return $this->client->getDto('api/customer/' . $id, Customer::class, [
            'with' => ['customFields', 'resaleCertificates.file'],
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
            'with' => [
                'country',
                'customFields',
                'defaultCarrier',
                'defaultSampleCarrier',
                'defaultSampleShippingService',
                'defaultShippingService',
                'employees.emailAddress',
                'primaryAddress',
                'resaleCertificates.file',
            ],
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

        if (! empty($customer->country)) {
            $body['country_id'] = $customer->country->id;
        }

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
     * @throws ValidationException
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

    /**
     * @param Customer $customer
     * @param ShipTo $shipTo
     * @return ShipTo
     * @throws ValidationException
     * @throws GuzzleException
     */
    public function addShipTo(Customer $customer, ShipTo $shipTo): ShipTo
    {
        return $this->client->postJsonAsDto('api/ship-to', array_merge([
            'customer' => $customer->id,
            'country_id' => $shipTo->country?->id,
        ], $shipTo->toArray()), ShipTo::class);
    }
}
