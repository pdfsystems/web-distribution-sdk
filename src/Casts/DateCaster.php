<?php

namespace Pdfsystems\WebDistributionSdk\Casts;

use DateTimeImmutable;
use Exception;
use Spatie\DataTransferObject\Caster;

class DateCaster implements Caster
{
    public function cast(mixed $value): ?DateTimeImmutable
    {
        if (empty($value)) {
            return null;
        } elseif (is_string($value)) {
            try {
                return new DateTimeImmutable($value);
            } catch (Exception) {
                return null;
            }
        } elseif ($value instanceof DateTimeImmutable) {
            return $value;
        }

        return null;
    }
}
