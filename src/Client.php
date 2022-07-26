<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Client as GuzzleClient;

class Client extends GuzzleClient
{
    public function __construct(string $baseUri = 'https://distribution.pdfsystems.com')
    {
        parent::__construct([
            'base_uri' => $baseUri,
            'cookies' => true,
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);
    }
}
