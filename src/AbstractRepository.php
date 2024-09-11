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

        if ($wdCode === 1002) {
            return new ValidationException($response['description'], $response['errors'], $ex->getCode(), $ex);
        } else {
            return new ResponseException($response['description'], $ex->getCode());
        }
    }
}
