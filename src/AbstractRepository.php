<?php

namespace Pdfsystems\WebDistributionSdk;

abstract class AbstractRepository
{
    public function __construct(protected Client $client)
    {
    }
}
