<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Uri;
use Pdfsystems\WebDistributionSdk\Dtos\Allocation;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Inventory;
use Pdfsystems\WebDistributionSdk\Dtos\Transaction;
use Pdfsystems\WebDistributionSdk\Dtos\TransactionItem;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class TransactionRepository extends AbstractRepository
{
    /**
     * @param int $id
     * @return Transaction
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function findById(int $id): Transaction
    {
        try {
            $response = $this->client->getJson("api/transaction/$id", ['with' => $this->getFindRelations()]);

            return new Transaction($response);
        } catch (RequestException) {
            throw new NotFoundException("Transaction with id $id not found");
        }
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function findByTransactionNumber(Company $company, string $transactionNumber): Transaction
    {
        $requestOptions = [
            'with' => $this->getFindRelations(),
            'company' => $company->id,
            'transaction_number' => $transactionNumber,
            'transaction_number_exact' => true,
        ];

        try {
            $response = $this->client->getJson("api/transaction", $requestOptions);

            return new Transaction($response[0]);
        } catch (RequestException) {
            throw new NotFoundException("Transaction with number $transactionNumber not found");
        }
    }

    /**
     * URI that can be used to redirect the user to the payment page for the supplied transaction.
     * Note that this requires the company in question to have a Web Distribution payment process configured.
     * If they do not, when attempting to load the URL an error will be displayed.
     *
     * @param Transaction $transaction
     * @return Uri
     */
    public function getPaymentUri(Transaction $transaction): Uri
    {
        return $this->client->getBaseUri()->withPath("/client/payment")->withQuery(http_build_query([
            'transaction' => $transaction->id,
            'key' => $transaction->client_auth_key,
        ]));
    }

    /**
     * URI that can be used to redirect the user to the customer portal for the supplied transaction.
     * Note that this requires the company in question to subscribe to the portal pages in Web Distribution.
     *
     * @param Transaction $transaction
     * @return Uri
     */
    public function getPortalUri(Transaction $transaction): Uri
    {
        return $this->client->getBaseUri()->withPath("/client/transaction/landing")->withQuery(http_build_query([
            'id' => $transaction->id,
            'key' => $transaction->client_auth_key,
        ]));
    }

    /**
     * @throws GuzzleException
     */
    public function unallocate(TransactionItem|int $item): void
    {
        if (is_int($item)) {
            $this->client->post("api/transaction-item/$item/unallocate");
        } else {
            $this->client->post("api/transaction-item/$item->id/unallocate");
        }
    }

    /**
     * @throws GuzzleException
     */
    public function allocateSingle(TransactionItem $item, Inventory $piece): void
    {
        $this->allocateSingleId($item->id, $piece->id, $item->quantity_ordered);
    }

    /**
     * @throws GuzzleException
     */
    public function allocateSingleId(int $itemId, int $pieceId, float $quantity): void
    {
        $this->allocateId($itemId, [
            $pieceId => $quantity,
        ]);
    }

    /**
     * @param TransactionItem $item
     * @param Allocation[] $allocations
     * @return void
     * @throws GuzzleException
     */
    public function allocate(TransactionItem $item, array $allocations): void
    {
        $allocationMap = [];

        foreach ($allocations as $allocation) {
            $allocationMap[$allocation->inventory_id] = $allocation->quantity;
        }

        $this->allocateId($item->id, $allocationMap);
    }

    /**
     * @param int $itemId
     * @param array $allocations
     * @return void
     * @throws GuzzleException
     */
    public function allocateId(int $itemId, array $allocations): void
    {
        $this->client->post("api/transaction-item/$itemId/reallocate", $allocations);
    }

    /**
     * Gets the relations to eager load when finding a transaction
     *
     * @return string[]
     */
    private function getFindRelations(): array
    {
        return [
            'customer.country',
            'customer.primaryAddress.country',
            'customer.primaryAddress.state',
            'holds.hold',
            'items.allocatedPieces.piece.warehouse',
            'items.item.style.millUnit',
            'items.item.style.productCategoryCode',
            'items.item.style.sellingUnit',
            'rep1',
            'shipToCountry',
            'shipToState',
            'specifier.country',
            'specifier.primaryAddress.country',
            'specifier.primaryAddress.state',
        ];
    }
}
