<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\Dtos\IncomingPurchaseOrder;
use Pdfsystems\WebDistributionSdk\Dtos\Product;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PurchaseOrderRepository extends AbstractRepository
{
    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function listByProduct(Product $product): array
    {
        $response = $this->client->getJson("api/item/{$product->id}/purchase-orders");

        return array_map(fn (array $purchaseOrder) => new IncomingPurchaseOrder($purchaseOrder), $response);
    }
}
