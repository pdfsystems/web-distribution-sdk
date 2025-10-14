<?php

namespace Pdfsystems\WebDistributionSdk;

use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\ProjectUser;
use Pdfsystems\WebDistributionSdk\Dtos\Report;
use Pdfsystems\WebDistributionSdk\Dtos\User;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use SplFileInfo;

class UserRepository extends AbstractRepository
{
    /**
     * @throws UnknownProperties
     */
    public function findById(int $id): User
    {
        return new User($this->client->getJson("api/user/$id"));
    }

    /**
     * @throws UnknownProperties
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
     */
    public function projectUser(string $emailAddress, string $name, string $repCode): ProjectUser
    {
        return $this->client->postJsonAsDto("api/user/project-user", [
            'email_address' => $emailAddress,
            'rep_code' => $repCode,
            'name' => $name,
        ], ProjectUser::class);
    }

    /**
     * @param string $token
     * @return string
     */
    public function getSsoLoginUri(string $token): string
    {
        return $this->client->getRelativeUri("login-sso", compact('token'));
    }

    /**
     */
    public function createReport(Company $company, string $name, SplFileInfo $file = null, User $user = null): Report
    {
        $body = [
            [
                'name' => 'name',
                'contents' => $name,
            ],
            [
                'name' => 'company_id',
                'contents' => $company->id,
            ],
        ];

        if (! is_null($file)) {
            $body[] = [
                'name' => 'file',
                'contents' => fopen($file->getPathname(), 'r'),
                'filename' => $file->getFilename(),
            ];
        }

        if (! is_null($user)) {
            $body[] = [
                'name' => 'user_id',
                'contents' => $user->id,
            ];
        }

        return $this->client->postMultipartAsDto(
            "api/report",
            $body,
            Report::class
        );
    }
}
