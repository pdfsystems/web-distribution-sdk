<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
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

    /**
     * @param string $token
     * @return Uri
     */
    public function getSsoLoginUri(string $token): Uri
    {
        return $this->client->getUri("login-sso", compact('token'));
    }

    /**
     * @throws GuzzleException
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
