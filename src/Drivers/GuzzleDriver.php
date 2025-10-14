<?php

namespace Pdfsystems\WebDistributionSdk\Drivers;

use GuzzleHttp\HandlerStack;

class GuzzleDriver extends \Rpungello\SdkClient\Drivers\GuzzleDriver
{
    use WebDistributionDriver;

    public function __construct(protected string $apiKey, string $baseUri = 'https://distribution.pdfsystems.com', ?HandlerStack $handler = null, ?string $userAgent = null)
    {
        parent::__construct($baseUri, $handler, $userAgent ?: static::getUserAgent());
    }

    protected function getGuzzleClientConfig(): array
    {
        $config = parent::getGuzzleClientConfig();
        $config['headers']['x-api-key'] = $this->apiKey;

        return $config;
    }
}
