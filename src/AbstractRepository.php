<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\BadResponseException;
use Pdfsystems\WebDistributionSdk\Exceptions\ResponseException;
use Pdfsystems\WebDistributionSdk\Exceptions\ValidationException;
use Spatie\DataTransferObject\Arr;

abstract class AbstractRepository
{
    public function __construct(protected Client $client)
    {
    }
}
