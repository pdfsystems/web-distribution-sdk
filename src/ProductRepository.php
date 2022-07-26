<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Product;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ProductRepository extends AbstractRepository
{
    /**
     * @param Company $company
     * @param callable $callback
     * @param int $perPage
     * @return void
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function iterate(Company $company, callable $callback, int $perPage = 128): void
    {
        $page = 1;
        do {
            $response = $this->client->getJson('api/item', [
                'with' => [
                    'style.productCategoryCode',
                    'style.primaryPrice',
                ],
                'company' => $company->id,
                'count' => $perPage,
                'page' => $page++,
            ]);

            foreach ($response as $product) {
                $callback(new Product($product));
            }
        } while (! empty($response));
    }
}
