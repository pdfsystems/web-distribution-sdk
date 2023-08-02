<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Inventory;
use Pdfsystems\WebDistributionSdk\Dtos\Transaction;
use Pdfsystems\WebDistributionSdk\Dtos\TransactionItem;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class TransactionRepository extends AbstractRepository
{
    /**
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function findByTransactionNumber(Company $company, string $transactionNumber): Transaction
    {
        $requestOptions = [
            'with' => [
                'customer.country',
                'customer.primaryAddress.state',
                'customer.primaryAddress.country',
                'specifier.country',
                'specifier.primaryAddress.state',
                'specifier.primaryAddress.country',
                'rep1',
                'items.item.style.productCategoryCode',
                'items.item.style.sellingUnit',
                'items.item.style.millUnit',
                'items.allocatedPieces.piece.warehouse',
                'shipToCountry',
                'shipToState',
            ],
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
     * @throws GuzzleException
     */
    public function unallocate(TransactionItem $item): void
    {
        $this->client->post("api/transaction-item/$item->id/unallocate");
    }

    /**
     * @throws GuzzleException
     */
    public function allocateSingle(TransactionItem $item, Inventory $piece): void
    {
        $this->client->post("api/transaction-item/$item->id/reallocate", [
            $piece->id => $item->quantity_ordered,
        ]);
    }
}
