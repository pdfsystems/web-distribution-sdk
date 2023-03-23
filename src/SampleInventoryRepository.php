<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\Dtos\Product;
use Pdfsystems\WebDistributionSdk\Dtos\SampleInventory;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class SampleInventoryRepository extends AbstractRepository
{
    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function getOnHand(Product $product, int $warehouseId, int $sampleTypeId): SampleInventory
    {
        $requestOptions = [
            'warehouse_id' => $warehouseId,
            'sample_type_id' => $sampleTypeId,
        ];
        $response = $this->client->getJson("api/item/$product->id/sample-inventory-on-hand", $requestOptions);

        return new SampleInventory($response);
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function receive(Product $product, int $warehouseId, int $sampleTypeId, int $quantity = 1, string $adjustmentType = 'R'): SampleInventory
    {
        $requestOptions = [
            'item_id' => $product->id,
            'warehouse_id' => $warehouseId,
            'sample_type_id' => $sampleTypeId,
            'quantity' => $quantity,
            'adjustment_type' => $adjustmentType,
            'line_id' => $product->line->id,
        ];
        $this->client->postJson("api/sample-inventory", $requestOptions);

        return $this->getOnHand($product, $warehouseId, $sampleTypeId);
    }
}
