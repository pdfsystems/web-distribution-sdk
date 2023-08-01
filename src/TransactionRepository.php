<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Transaction;
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
}
