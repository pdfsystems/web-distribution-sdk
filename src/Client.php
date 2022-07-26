<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use Pdfsystems\WebDistributionSdk\Dtos\User;

class Client
{
    protected ?GuzzleClient $guzzle;

    public function __construct(protected string $baseUri = 'https://distribution.pdfsystems.com', protected ?HandlerStack $handler = null)
    {
    }

    /**
     * @throws GuzzleException
     */
    public function authenticateWithApiKey(string $apiKey): User
    {
        $this->guzzle = static::getGuzzleClient($this->baseUri, $apiKey, $this->handler);

        return $this->getAuthenticatedUser();
    }

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
     * @throws GuzzleException
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
