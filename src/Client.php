<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Pdfsystems\WebDistributionSdk\Dtos\User;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class Client
{
    protected ?GuzzleClient $guzzle;

    public function __construct(protected string $baseUri = 'https://distribution.pdfsystems.com', protected ?HandlerStack $handler = null)
    {
    }

    /**
     * Authenticates with Web Distribution using an API key.
     *
     * @param string $apiKey
     * @return User
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function authenticateWithApiKey(string $apiKey): User
    {
        $this->guzzle = static::getGuzzleClient($this->baseUri, $apiKey, $this->handler);

        return $this->getAuthenticatedUser();
    }

    /**
     * Instantiates a new Guzzle client, which will be used when interfacing with the Web Distribution API.
     *
     * @param string $baseUri
     * @param string|null $apiKey
     * @param HandlerStack|null $handler
     * @return GuzzleClient
     */
    protected static function getGuzzleClient(string $baseUri, ?string $apiKey = null, ?HandlerStack $handler = null): GuzzleClient
    {
        $config = [
            'base_uri' => $baseUri,
            'cookies' => true,
            'headers' => [
                'accept' => 'application/json',
            ],
        ];

        if (! is_null($apiKey)) {
            $config['headers']['x-api-key'] = $apiKey;
        }

        if (! is_null($handler)) {
            $config['handler'] = $handler;
        }

        return new GuzzleClient($config);
    }

    /**
     * Gets the currently authenticated user
     *
     * @return User
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function getAuthenticatedUser(): User
    {
        return new User(
            json_decode($this->guzzle->get('api/user/me', [
                RequestOptions::QUERY => [
                    'with' => ['defaultCompany.currency', 'defaultCompany.lines'],
                ],
            ])->getBody()->getContents(), JSON_OBJECT_AS_ARRAY)
        );
    }

    /**
     * Performs a GET request against the Web Distribution API
     *
     * @param string $uri
     * @param array $query
     * @return Response
     * @throws GuzzleException
     */
    public function get(string $uri, array $query = []): Response
    {
        return $this->guzzle->get($uri, [
            RequestOptions::QUERY => $query,
        ]);
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
     * Gets a repository for accessing product information
     *
     * @return ProductRepository
     */
    public function products(): ProductRepository
    {
        return new ProductRepository($this);
    }

    /**
     * Performs a standard GET request, but parses the result as a JSON array
     *
     * @param string $uri
     * @param array $query
     * @return array
     * @throws GuzzleException
     */
    public function getJson(string $uri, array $query = []): array
    {
        return json_decode($this->get($uri, $query)->getBody()->getContents(), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * Authenticates with Web Distribution using regular login credentials (email and password).
     *
     * @param string $email
     * @param string $password
     * @return User
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function authenticateWithCredentials(string $email, string $password): User
    {
        $this->guzzle = static::getGuzzleClient($this->baseUri, handler: $this->handler);

        $this->guzzle->post('login', [
            RequestOptions::JSON => compact('email', 'password'),
        ]);

        return $this->getAuthenticatedUser();
    }
}
