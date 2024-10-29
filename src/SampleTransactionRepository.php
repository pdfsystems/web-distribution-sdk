<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\SampleTransaction;
use Pdfsystems\WebDistributionSdk\Dtos\SampleTransactionItem;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class SampleTransactionRepository extends AbstractRepository
{
    /**
     * @param int $id
     * @return SampleTransaction
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function findById(int $id): SampleTransaction
    {
        try {
            $response = $this->client->getJson("api/sample-transaction/$id", ['with' => $this->getRelations()]);

            return new SampleTransaction($response);
        } catch (RequestException) {
            throw new NotFoundException("Transaction with id $id not found");
        }
    }

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

            // We have to run an update to mark the sample transaction as active
            $this->client->putJson("api/sample-transaction/$response->id");
        }

        return $response;
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function iterate(Company $company, callable $callback, array $options = [], int $perPage = 128): void
    {
        if (array_key_exists('with', $options)) {
            $relations = $this->getRelations($options['with']);
            unset($options['with']);
        } else {
            $relations = $this->getRelations();
        }


        $requestOptions = array_merge([
            'count' => $perPage,
            'page' => 1,
            'company' => $company->id,
            'with' => $relations,
        ], $options);

        do {
            $response = $this->client->getDtoArray('api/sample-transaction', SampleTransaction::class, $requestOptions);

            foreach ($response as $transaction) {
                $callback($transaction);
            }

            $requestOptions['page']++;
        } while (! empty($response));
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

        if (! is_null($sampleTransaction->ship_to_country)) {
            $array['ship_to_country_id'] = $sampleTransaction->ship_to_country->id;
        }

        if (! is_null($sampleTransaction->ship_to_state)) {
            $array['ship_to_state_id'] = $sampleTransaction->ship_to_state->id;
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

    /**
     * Gets the relations to eager load when finding a transaction
     *
     * @return string[]
     */
    private function getRelations(array $extras = []): array
    {
        return array_merge($extras, [
            'carrier',
            'customer.country',
            'customer.primaryAddress.country',
            'customer.primaryAddress.state',
            'items.item.style',
            'items.sampleType',
            'line',
            'shipToCountry',
            'shipToState',
            'shippingService',
            'user',
        ]);
    }
}
