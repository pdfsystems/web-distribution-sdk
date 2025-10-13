<?php

namespace Pdfsystems\WebDistributionSdk;

use Pdfsystems\WebDistributionSdk\Dtos\Product;
use Pdfsystems\WebDistributionSdk\Dtos\SampleInventory;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class SampleInventoryRepository extends AbstractRepository
{
    /**
     * @param Product|int $product
     * @param int $warehouseId
     * @param int $sampleTypeId
     *
     * @return SampleInventory
     * @throws UnknownProperties
     */
    public function getOnHand(Product|int $product, int $warehouseId, int $sampleTypeId): SampleInventory
    {
        if (is_int($product)) {
            $product = $this->client->products()->findById($product);
        }

        $requestOptions = [
            'warehouse_id' => $warehouseId,
            'sample_type_id' => $sampleTypeId,
        ];
        $response = $this->client->getJson("api/item/$product->id/sample-inventory-on-hand", $requestOptions);

        return new SampleInventory($response);
    }

    /**
     * @param Product|int $product
     * @param int $warehouseId
     * @param int $sampleTypeId
     * @param int $quantity
     * @param string $adjustmentType R = Receive, A = Adjust, P = Physical, S = Shipment, L = Release
     * @param int|null $userId
     * @return SampleInventory
     * @throws UnknownProperties
     */
    public function receive(Product|int $product, int $warehouseId, int $sampleTypeId, int $quantity = 1, string $adjustmentType = 'R', ?int $userId = null): SampleInventory
    {
        if (is_int($product)) {
            $product = $this->client->products()->findById($product);
        }

        $requestOptions = [
            'item_id' => $product->id,
            'warehouse_id' => $warehouseId,
            'sample_type_id' => $sampleTypeId,
            'quantity' => $quantity,
            'adjustment_type' => $adjustmentType,
            'line_id' => $product->line->id,
        ];
        if (! empty($userId)) {
            $requestOptions['user_id'] = $userId;
        }
        $this->client->postJson("api/sample-inventory", $requestOptions);

        return $this->getOnHand($product, $warehouseId, $sampleTypeId);
    }
}
