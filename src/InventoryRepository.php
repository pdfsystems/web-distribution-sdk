<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Pdfsystems\WebDistributionSdk\Dtos\Inventory;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class InventoryRepository extends AbstractRepository
{
    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function findById(int $id): Inventory
    {
        $requestOptions = [
            'with' => [
                'item.style',
            ],
        ];

        try {
            $response = $this->client->getJson("api/inventory/$id", $requestOptions);

            return new Inventory($response);
        } catch (RequestException) {
            throw new NotFoundException("Inventory with id $id not found");
        }
    }
}
