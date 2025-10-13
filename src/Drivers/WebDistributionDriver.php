<?php

namespace Pdfsystems\WebDistributionSdk\Drivers;

use Composer\InstalledVersions;

trait WebDistributionDriver
{
    protected static function getUserAgent(): string
    {
        return 'Web Distribution SDK/' . static::getVersion();
    }

    protected static function getVersion(): string
    {
        return InstalledVersions::getVersion('pdfsystems/web-distribution-sdk');
    }
}
