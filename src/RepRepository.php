<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Rep;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class RepRepository extends AbstractRepository
{
    /**
     * @param Company $company
     * @return Rep[]
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function list(Company $company): array
    {
        return $this->client->getDtoArray('api/rep', Rep::class, [
            'company' => $company->id,
            'sorting[name]' => 'asc',
        ]);
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function findById(int $id): Rep
    {
        return $this->client->getDto('api/rep/' . $id, Rep::class, [
            'with' => ['masterRep', 'nationalRep'],
        ]);
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function findByCode(Company $company, string $repCode): Rep
    {
        /** @var Rep[] $reps */
        $reps = $this->client->getDtoArray('api/rep', Rep::class, [
            'company' => $company->id,
            'search' => $repCode,
            'with' => ['masterRep', 'nationalRep'],
        ]);

        foreach ($reps as $rep) {
            if ($rep->rep_code === $repCode) {
                return $rep;
            }
        }

        throw new NotFoundException("No rep found with rep code $repCode");
    }
}
