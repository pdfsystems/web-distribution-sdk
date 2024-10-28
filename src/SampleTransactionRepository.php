<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\Dtos\SampleTransaction;
use Pdfsystems\WebDistributionSdk\Dtos\SampleTransactionItem;

class SampleTransactionRepository extends AbstractRepository
{
    /**
     * @throws GuzzleException
     */
    public function create(SampleTransaction $sampleTransaction): SampleTransaction
    {
        $response = $this->client->postJsonAsDto('api/sample-transaction', $this->mapDtoToArray($sampleTransaction), SampleTransaction::class);

        if (! empty($sampleTransaction->items)) {
            $this->client->postJson('api/sample-transaction-item', [
                'id' => $response->id,
                'data' => array_map(fn (SampleTransactionItem $item) => $this->mapItemDtoToArray($item), $sampleTransaction->items),
            ]);
        }

        return $response;
    }

    private function mapDtoToArray(SampleTransaction $sampleTransaction): array
    {
        $array = $sampleTransaction->toArray();

        if (! is_null($sampleTransaction->line)) {
            $array['line'] = $sampleTransaction->line->id;
        }

        if (! is_null($sampleTransaction->customer)) {
            $array['customer_id'] = $sampleTransaction->customer->id;
        }

        if (! is_null($sampleTransaction->carrier)) {
            $array['carrier_id'] = $sampleTransaction->carrier->id;
        }

        if (! is_null($sampleTransaction->shipping_service)) {
            $array['shipping_service_id'] = $sampleTransaction->shipping_service->id;
        }

        return $array;
    }

    private function mapItemDtoToArray(SampleTransactionItem $item): array
    {
        return [
            'id' => $item->item?->id,
            'comments' => $item->comments,
            'quantityOrdered' => $item->quantity_ordered,
            'sampleTypeChosen' => $item->sample_type?->id,
        ];
    }
}
