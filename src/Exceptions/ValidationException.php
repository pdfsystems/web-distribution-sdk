<?php

namespace Pdfsystems\WebDistributionSdk\Exceptions;

use Throwable;

class ValidationException extends ResponseException
{
    public function __construct(string $message = "", protected array $errors = [], int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(static::convertErrorsToMessage($this->errors), $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private static function convertErrorsToMessage(array $errors): string
    {
        return 'Validation failed: ' . implode('; ', array_map(fn ($array) => implode(', ', $array), $errors));
    }
}
