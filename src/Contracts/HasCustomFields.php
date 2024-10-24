<?php

namespace Pdfsystems\WebDistributionSdk\Contracts;

use Pdfsystems\WebDistributionSdk\Dtos\CustomField;

interface HasCustomFields
{
    /**
     * @return CustomField[]
     */
    public function getAllCustomFields(): array;

    /**
     * @param CustomField[] $customFields
     * @return void
     */
    public function setCustomFields(array $customFields): void;
}
