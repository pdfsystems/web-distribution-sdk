<?php

namespace Pdfsystems\WebDistributionSdk;

use Pdfsystems\WebDistributionSdk\Dtos\ApiKey;
use Pdfsystems\WebDistributionSdk\Dtos\User;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Pdfsystems\WebDistributionSdk\Exceptions\ResponseException;
use Pdfsystems\WebDistributionSdk\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Rpungello\SdkClient\Exceptions\RequestException;
use Rpungello\SdkClient\SdkClient;
use Spatie\DataTransferObject\Arr;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class Client extends SdkClient
{
    public function get(string $uri, array $query = [], array $headers = []): ResponseInterface
    {
        try {
            return parent::get($uri, $query, $headers);
        } catch (RequestException $e) {
            throw $this->handleRequestException($e);
        }
    }

    public function post(string $uri, DataTransferObject|array|null $body = null, array $headers = []): ResponseInterface
    {
        try {
            return parent::post($uri, $body, $headers);
        } catch (RequestException $e) {
            throw $this->handleRequestException($e);
        }
    }

    public function put(string $uri, DataTransferObject|array|null $body = null, array $headers = []): ResponseInterface
    {
        try {
            return parent::put($uri, $body, $headers);
        } catch (RequestException $e) {
            throw $this->handleRequestException($e);
        }
    }

    protected function handleRequestException(RequestException $ex): ResponseException
    {
        if ($ex->getCode() === 422) {
            return $this->handleInvalidRequestDataException($ex);
        }

        return new ResponseException($ex->getMessage(), $ex->getCode(), $ex);
    }

    private function handleInvalidRequestDataException(RequestException $ex): ResponseException
    {
        $response = $ex->getJsonResponseBody();
        if (empty($response)) {
            return new ResponseException('An unknown error occurred.', $ex->getCode(), $ex);
        }

        return match (Arr::get($response, 'code')) {
            1000 => new NotFoundException($response['description'], $ex->getCode(), $ex),
            1002 => new ValidationException($response['description'], $response['errors'], $ex->getCode(), $ex),
            default => new ResponseException($response['description'], $ex->getCode()),
        };
    }

    /**
     * Gets the currently authenticated user
     *
     * @param array $options
     * @return User
     * @throws UnknownProperties
     */
    public function getAuthenticatedUser(array $options = []): User
    {
        $eagerRelations = [
            'defaultCompany.country',
            'defaultCompany.currency',
            'defaultCompany.defaultLine.defaultSampleTypeCode',
            'defaultCompany.lines',
        ];

        if (array_key_exists('include_api_keys', $options) && $options['include_api_keys'] === true) {
            $eagerRelations[] = 'apiKeys';
        }

        return $this->getDto('api/user/me', User::class, ['with' => $eagerRelations]);
    }

    /**
     * @param ApiKey $key
     * @return ApiKey
     * @throws UnknownProperties
     */
    public function createApiKey(ApiKey $key): ApiKey
    {
        return $this->postDto('api/api-key', $key);
    }

    /**
     * Gets a repository for access company information
     *
     * @return CompanyRepository
     */
    public function companies(): CompanyRepository
    {
        return new CompanyRepository($this);
    }

    /**
     * Gets a repository for access user information
     *
     * @return UserRepository
     */
    public function users(): UserRepository
    {
        return new UserRepository($this);
    }

    /**
     * Gets a repository for accessing product information
     *
     * @return ProductRepository
     */
    public function products(): ProductRepository
    {
        return new ProductRepository($this);
    }

    /**
     * Gets a repository for accessing inventory information
     *
     * @return InventoryRepository
     */
    public function inventory(): InventoryRepository
    {
        return new InventoryRepository($this);
    }

    /**
     * Gets a repository for accessing inventory information
     *
     * @return PurchaseOrderRepository
     */
    public function purchaseOrders(): PurchaseOrderRepository
    {
        return new PurchaseOrderRepository($this);
    }

    /**
     * Gets a repository for accessing sample inventory information
     *
     * @return SampleInventoryRepository
     */
    public function sampleInventory(): SampleInventoryRepository
    {
        return new SampleInventoryRepository($this);
    }

    public function transactions(): TransactionRepository
    {
        return new TransactionRepository($this);
    }

    public function lines(): LineRepository
    {
        return new LineRepository($this);
    }

    public function reps(): RepRepository
    {
        return new RepRepository($this);
    }

    public function customers(): CustomerRepository
    {
        return new CustomerRepository($this);
    }

    public function employees(): EmployeeRepository
    {
        return new EmployeeRepository($this);
    }

    public function countries(): CountryRepository
    {
        return new CountryRepository($this);
    }

    public function states(): StateRepository
    {
        return new StateRepository($this);
    }

    public function sampleTransactions(): SampleTransactionRepository
    {
        return new SampleTransactionRepository($this);
    }
}
