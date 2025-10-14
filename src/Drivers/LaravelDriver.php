<?php

namespace Pdfsystems\WebDistributionSdk\Drivers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\PendingRequest;

class LaravelDriver extends \Rpungello\SdkClient\Drivers\LaravelDriver
{
    use WebDistributionDriver;

    public function __construct(Application $app, private readonly string $apiKey, private readonly ?string $userAgent = null, string $baseUri = 'https://distribution.pdfsystems.com')
    {
        parent::__construct($app, $baseUri);
    }

    protected function pendingRequest(array $headers = []): PendingRequest
    {
        return parent::pendingRequest($headers)
            ->withUserAgent($this->userAgent ?: static::getUserAgent())
            ->withHeader('X-Api-Key', $this->apiKey);
    }
}
