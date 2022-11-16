<?php

namespace Pdfsystems\WebDistributionSdk\Requests;

use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class Request
{
    protected ValidatorInterface $validator;

    public function __construct()
    {
        $this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return count($this->validator->validate($this)) === 0;
    }

    /**
     * @throws ValidationFailedException
     */
    public function validate(): void
    {
        $errors = $this->validator->validate($this);

        if ($errors->count() > 0) {
            throw new ValidationFailedException($this, $errors);
        }
    }
}
