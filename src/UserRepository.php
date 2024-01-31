<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\ProjectUser;
use Pdfsystems\WebDistributionSdk\Dtos\User;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UserRepository extends AbstractRepository
{
    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function findById(int $id): User
    {
        return new User($this->client->getJson("api/user/$id"));
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function listForCompany(Company $company): array
    {
        return User::arrayOf($this->client->getJson("api/user", ['company' => $company->id]));
    }

    /**
     * @param string $emailAddress
     * @param string $name
     * @param string $repCode
     * @return ProjectUser
     * @throws GuzzleException
     */
    public function projectUser(string $emailAddress, string $name, string $repCode): ProjectUser
    {
        return $this->client->postJsonAsDto("api/user/project-user", [
            'email_address' => $emailAddress,
            'rep_code' => $repCode,
            'name' => $name,
        ], ProjectUser::class);
    }
}
