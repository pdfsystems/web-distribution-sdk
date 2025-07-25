<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Uri;
use Pdfsystems\WebDistributionSdk\Dtos\Allocation;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Inventory;
use Pdfsystems\WebDistributionSdk\Dtos\Transaction;
use Pdfsystems\WebDistributionSdk\Dtos\TransactionFreightResponse;
use Pdfsystems\WebDistributionSdk\Dtos\TransactionItem;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Pdfsystems\WebDistributionSdk\Exceptions\ResponseException;
use Psr\Http\Message\ResponseInterface;
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
            $response = $this->client->getJson("api/transaction/$id", ['with' => $this->getRelations()]);

            return new Transaction($response);
        } catch (RequestException) {
            throw new NotFoundException("Transaction with id $id not found");
        }
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
            $response = $this->client->getDtoArray('api/transaction', Transaction::class, $requestOptions);

            foreach ($response as $transaction) {
                $callback($transaction);
            }

            $requestOptions['page']++;
        } while (! empty($response));
    }

    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function findByTransactionNumber(Company $company, string $transactionNumber): Transaction
    {
        $requestOptions = [
            'with' => $this->getRelations(),
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
     * @throws ResponseException|GuzzleException
     */
    public function getNotificationResponse(Transaction $transaction): ResponseInterface
    {
        return $this->client->get("export/transaction/notification/$transaction->id");
    }

    /**
     * @throws ResponseException|GuzzleException
     */
    public function saveNotification(Transaction $transaction, string $path): bool
    {
        if (! is_writable(dirname($path))) {
            return false;
        }

        $fh = fopen($path, 'w');

        return stream_copy_to_stream($this->getNotificationResponse($transaction)->getBody()->detach(), $fh) !== false;
    }

    /**
     * @throws ResponseException|GuzzleException
     */
    public function getPickTicketResponse(Transaction $transaction): ResponseInterface
    {
        return $this->client->get("export/transaction/pick-ticket/$transaction->id");
    }

    /**
     * @throws ResponseException|GuzzleException
     */
    public function savePickTicket(Transaction $transaction, string $path): bool
    {
        if (! is_writable(dirname($path))) {
            return false;
        }

        $fh = fopen($path, 'w');

        return stream_copy_to_stream($this->getPickTicketResponse($transaction)->getBody()->detach(), $fh) !== false;
    }

    /**
     * @throws ResponseException|GuzzleException
     */
    public function getInvoiceResponse(Transaction $transaction): ResponseInterface
    {
        return $this->client->get("export/transaction/invoice/$transaction->id");
    }

    /**
     * @throws ResponseException|GuzzleException
     */
    public function saveInvoice(Transaction $transaction, string $path): bool
    {
        if (! is_writable(dirname($path))) {
            return false;
        }

        $fh = fopen($path, 'w');

        return stream_copy_to_stream($this->getInvoiceResponse($transaction)->getBody()->detach(), $fh) !== false;
    }

    /**
     * Instructs Web Distribution to refresh the data on Order-Track for the specified transaction
     *
     * @param Transaction|int $transaction
     * @return bool
     */
    public function refreshOrderTrack(Transaction|int $transaction): bool
    {
        if ($transaction instanceof Transaction) {
            $transaction = $transaction->id;
        }

        try {
            $response = $this->client->post("api/transaction/$transaction/refresh-order-track");

            return $response->getStatusCode() === 202;
        } catch (GuzzleException) {
            return false;
        }
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
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function freight(Transaction|int $transaction): TransactionFreightResponse
    {
        if ($transaction instanceof Transaction) {
            $transaction = $transaction->id;
        }

        return $this->client->getDto("api/transaction/$transaction/freight", TransactionFreightResponse::class);
    }

    /**
     * Gets the relations to eager load when finding a transaction
     *
     * @return string[]
     */
    private function getRelations(array $extras = []): array
    {
        return array_merge($extras, [
            'customer.country',
            'customer.primaryAddress.country',
            'customer.primaryAddress.state',
            'holds.hold',
            'items.allocatedPieces.piece.warehouse',
            'items.item.style.millUnit',
            'items.item.style.productCategoryCode',
            'items.item.style.sellingUnit',
            'rep1',
            'rep2',
            'shipToCountry',
            'shipToState',
            'specifier.country',
            'specifier.primaryAddress.country',
            'specifier.primaryAddress.state',
            'user',
        ]);
    }
}
