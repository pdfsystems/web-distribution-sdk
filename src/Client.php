<?php

namespace Pdfsystems\WebDistributionSdk;

use Composer\InstalledVersions;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Pdfsystems\WebDistributionSdk\Dtos\ApiKey;
use Pdfsystems\WebDistributionSdk\Dtos\User;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Pdfsystems\WebDistributionSdk\Exceptions\ResponseException;
use Pdfsystems\WebDistributionSdk\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Rpungello\SdkClient\SdkClient;
use Spatie\DataTransferObject\Arr;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class Client extends SdkClient
{
    protected ?GuzzleClient $guzzle;

    /**
     * @throws GuzzleException
     */
    public function __construct(protected array $credentials, protected string $baseUri = 'https://distribution.pdfsystems.com', protected ?HandlerStack $handler = null)
    {
        parent::__construct($this->baseUri, $handler, static::getUserAgent(), cookies: static::requiresCookies($this->credentials));

        if (! empty($this->credentials['email']) && ! empty($this->credentials['password'])) {
            $this->guzzle->post('login', [
                RequestOptions::JSON => $this->credentials,
            ]);
        }
    }

    private static function getUserAgent(): string
    {
        return 'Web Distribution SDK/' . static::getVersion();
    }

    private static function getVersion(): string
    {
        return InstalledVersions::getVersion('pdf-systems-inc/web-distribution-sdk');
    }

    public function get(string $uri, array $query = [], array $headers = []): ResponseInterface
    {
        try {
            return parent::get($uri, $query, $headers);
        } catch (BadResponseException $e) {
            throw $this->handleBadResponseException($e);
        }
    }

    public function post(string $uri, DataTransferObject|array|null $body = null, array $headers = []): ResponseInterface
    {
        try {
            return parent::post($uri, $body, $headers);
        } catch (BadResponseException $e) {
            throw $this->handleBadResponseException($e);
        }
    }

    public function put(string $uri, DataTransferObject|array|null $body = null, array $headers = []): ResponseInterface
    {
        try {
            return parent::put($uri, $body, $headers);
        } catch (BadResponseException $e) {
            throw $this->handleBadResponseException($e);
        }
    }

    protected function handleBadResponseException(BadResponseException $ex): ResponseException
    {
        if ($ex->getCode() === 422) {
            return $this->handleInvalidRequestDataException($ex);
        }

        return new ResponseException($ex->getMessage(), $ex->getCode(), $ex);
    }

    private function handleInvalidRequestDataException(BadResponseException $ex): ResponseException
    {
        $response = json_decode($ex->getResponse()->getBody()->getContents(), true);
        $wdCode = Arr::get($response, 'code');

        if ($wdCode === 1000) {
            return new NotFoundException($response['description'], $ex->getCode(), $ex);
        } elseif ($wdCode === 1002) {
            return new ValidationException("{$response['description']}: {$ex->getRequest()->getUri()->getPath()}", $response['errors'], $ex->getCode(), $ex);
        } else {
            return new ResponseException($response['description'], $ex->getCode());
        }
    }

    private static function requiresCookies(array $credentials): bool
    {
        return ! empty($credentials['email']) && ! empty($credentials['password']);
    }

    protected function getGuzzleClientConfig(): array
    {
        $config = parent::getGuzzleClientConfig();
        if (! empty($this->credentials['token'])) {
            $config['headers']['x-api-key'] = $this->credentials['token'];
        }

        return $config;
    }

    public function getBaseUri(): Uri
    {
        return new Uri($this->baseUri);
    }

    public function getUri(string $path, array $query = []): Uri
    {
        return $this->getBaseUri()->withPath($path)->withQuery(http_build_query($query));
    }

    /**
     * Gets the currently authenticated user
     *
     * @param array $options
     * @return User
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function getAuthenticatedUser(array $options = []): User
    {
        $eagerRelations = [
            'defaultCompany.currency',
            'defaultCompany.lines',
            'defaultCompany.country',
        ];

        if (array_key_exists('include_api_keys', $options) && $options['include_api_keys'] === true) {
            $eagerRelations[] = 'apiKeys';
        }

        return new User(
            json_decode($this->guzzle->get('api/user/me', [
                RequestOptions::QUERY => [
                    'with' => $eagerRelations,
                ],
            ])->getBody()->getContents(), JSON_OBJECT_AS_ARRAY)
        );
    }

    /**
     * @param ApiKey $key
     * @return ApiKey
     * @throws GuzzleException
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
