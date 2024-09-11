<?php

namespace Pdfsystems\WebDistributionSdk\Exceptions;

use Throwable;

class ValidationException extends ResponseException
{
    public function __construct(string $message = "", protected array $errors = [], int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
